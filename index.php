<?php
header('Content-Type: application/json; charset=utf-8');
include("autoload.php");
ob_start();
use core\Log as Log;
use carbazar\parse\VIN as VIN;
use carbazar\Auth as Auth;
use carbazar\Request as Request;
$resp = [
    "code"=>200,
    "status"=>"success",
    "message"=>"",
    "request"=>$_REQUEST,
    "response"=>[]
];
function setErrorResponse($code,$message){
    return [
        "code"=>$code,
        "status"=>($code==200)?"success":"failed",
        "message"=>$message,
        "request"=>$_REQUEST,
        "response"=>[]
    ];
}
while($resp["code"]==200){
    // check requered fields
    if(!isset($_REQUEST["action"]) || !in_array($_REQUEST["action"],["request","auth","status"]) ){$resp = setErrorResponse(500,"Unknown request type.");break;}
    $action = $_REQUEST["action"];
    if($action == "auth"){
        if(!isset($_REQUEST["apikey"])){$resp = setErrorResponse(500,"No required field apikey");break;}
        if(!isset($_REQUEST["login"])){$resp = setErrorResponse(500,"No required field login");break;}
        if(!isset($_REQUEST["password"])){$resp = setErrorResponse(500,"No required field password");break;}
        $auth = new Auth([
            "apikey"=>$_REQUEST["apikey"],
            "login"=>$_REQUEST["login"],
            "password"=>$_REQUEST["password"]
        ]);
        $resp = $auth->getResp();
        if($resp["code"]!=200)break;
    }
    elseif($action == "request"){
        if(!isset($_REQUEST["session"])){$resp = setErrorResponse(401,"No required field session");break;}
        $auth = new Auth([
            "session"=>$_REQUEST["session"]
        ]);
        $resp = $auth->getResp();
        if($resp["code"]!=200)break;
        $request = new Request(["session_id"=>$auth->getSession()->id,"vin"=>"","code"=>$resp["code"],"status"=>"progress","data"=>""]);
        if(!isset($_REQUEST["vin"])){$resp = setErrorResponse(500,"No required field vin");$request->update(["code"=>$resp["code"],"status"=>$resp["status"],"message"=>$resp["message"]]);break;}
        $vin = strtoupper($_REQUEST["vin"]);
        $request->update(["vin"=>$vin]);
        $rq = ["vin"=>$vin];
        if(!VIN::validate($vin)){$resp = setErrorResponse(500,"Incorrect field value vin");$request->update(["code"=>$resp["code"],"status"=>$resp["status"],"message"=>$resp["message"]]);break;}

        $result = [];
        $rca = new carbazar\parse\Rca();
        $zalog = new carbazar\parse\Zalog();
        $gibdd = new carbazar\parse\Gibdd();
        $cp = new carbazar\parse\Carprice();
        $osago = new carbazar\parse\Osago();
        $decodeVIN = new carbazar\parse\VIN;
        if(!isset($result["history"])||count($result["history"])==0){
            $result["history"]=json_decode($gibdd->history($rq),true);
            if(isset($result["RequestResult"]) && isset($result["RequestResult"]["vehiclePassport"]) && isset($result["RequestResult"]["vehiclePassport"]["issue"]))
                $result["RequestResult"]["vehiclePassport"]["issue"] = preg_replace('/"/m',"'",$result["RequestResult"]["vehiclePassport"]["issue"]);
        }
        if(!isset($result["dtp"])||count($result["dtp"])==0){$result["dtp"]=json_decode($gibdd->dtp($rq),true);}
        if(!isset($result["wanted"])||count($result["history"])==0){$result["wanted"]=json_decode($gibdd->wanted($rq),true);}
        if(!isset($result["restrict"])||count($result["history"])==0){$result["restrict"]=json_decode($gibdd->restrict($rq),true);}
        if(!isset($result["rca"])||count($result["history"])==0){$result["rca"]=json_decode($rca->get($rq),true);}
        if(!isset($result["zalog"])||count($result["history"])==0){$result["zalog"]=json_decode($zalog->get($rq),true);}
        if(!isset($result["vin"])){$result["vin"]=$decodeVIN->get($rq["vin"]);}
        if(!isset($result["osago"]["price"])&&isset($result["history"]["RequestResult"]["vehicle"]["powerHp"])){$result["osago"]=$osago->get($result["history"]["RequestResult"]["vehicle"]["powerHp"]);}
        if(!isset($result["carprice"]["car_price_from"])&&isset($result["vin"])){
            $mark = $result["vin"]["brand"];
            $model = isset($result["vin"]["model"])?$result["vin"]["model"]:preg_replace("/".preg_quote($mark,'/')."/im","",$result["history"]["RequestResult"]["vehicle"]["model"]);
            $year = $result["history"]["RequestResult"]["vehicle"]["year"];
            $cpdata = [
                "mark"=>$mark,
                "model"=>$model,
                "year"=>$result["history"]["RequestResult"]["vehicle"]["year"]
            ];
            //print_r($cpdata);exit;
            $result["carprice"]=json_decode($cp->get($cpdata),true);
        }
        $request->update(["code"=>$resp["code"],"status"=>$resp["status"],"data"=>json_encode($resp,JSON_UNESCAPED_UNICODE)]);
        $resp["response"] = $result;
        if($result["history"]["status"]=="200")$auth->getApikey()->decrease();
    }
    elseif($action == "status"){}


    if($resp["code"]==200){
        break;
    }
}
Log::debug(ob_get_clean());
http_response_code($resp["code"]);
echo json_encode($resp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
exit;
?>
