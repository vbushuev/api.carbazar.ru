<?php
chdir(dirname(__FILE__));
set_time_limit(300);
include("autoload.php");
use core\Log as Log;
use carbazar\parse\VIN as VIN;
use carbazar\Auth as Auth;
use carbazar\Apikey as Apikey;
use carbazar\Account as Account;
use carbazar\Session as Session;
use carbazar\Request as Request;
//Log::$console= true;
$resp = [
    "code"=>200,
    "status"=>"success",
    "message"=>"",
    "request"=>$_REQUEST,
    "response"=>[]
];
$currentRequest = new Request;
$request = new Request;
try{
    $lostRequests = $currentRequest->get(['status'=>"progress","updated_at"=>"<date_add(now(),INTERVAL -1 MINUTE)"]);
    // $request = $currentRequest->get(['status'=>"progress","created_at"=>"<date_add(now(),INTERVAL -3 MINUTE)"]);

    foreach($lostRequests as $lostData){
        // print_r($lostData);exit;
        $request->find($lostData["id"]);
        $request->update();
        if(empty($request->vin))continue;
        //print_r($request->toArray());exit;
        $resp = json_decode($request->data,true);
        $rq = ["vin"=>$request->vin];
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
        array_walk_recursive($result,function(&$v,$k){$v = preg_replace('/"/m','',$v);});
        $resp["response"] = $result;

        if($result["history"]["status"]=="200"){
            try{
                $apikey = new Apikey;
                $session = new Session(["session_id"=>$request->session_id]);
                $apikey->find($session->apikey_id);
                $account = new Account;
                $account->find($apikey->account_id);
                $account->decrease();
            }
            catch(\Exception $e){
                Log::debug($e->getMessage());
            }
            $resp["code"]="200";
            $resp["status"] = "success";
        }
        else{
            $resp["code"]=$result["history"]["status"];
            $resp["message"] = $result["history"]["message"];
            $resp["status"] = "failed";
        }
        $request->update(["code"=>$resp["code"],"status"=>$resp["status"],"data"=>json_encode($resp,JSON_UNESCAPED_UNICODE)]);
    }

}
catch(\Exception $e){
    Log::debug($e->getMessage());
}
?>
