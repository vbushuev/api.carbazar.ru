<?php
include("autoload.php");
use core\Log as Log;
use core\HTTPConnector as Http;
use carbazar\parse\Zalog as Zalog;
Log::$console=true;
$vin = "TMAJ3813DGJ215139";
$vin = "Z8NBEAB1754384931";
// $vin = "WDD2120801A633860";

// $zalog = new Zalog;
// $data = $zalog->get(["vin"=>$vin]);
// print_r($data);
// exit;

$apihost = "http://api.carbazar.bs2/";
$rq = [
    "login"=>"test",
    "password"=>"$2y$10$/uU6MmtQ7V0vaz3AjFzNtu4mYhTksHD1Xao2aljO/E9BwZG8.xP4O",
    "apikey"=>"7685659CB6D840C78713A5EAA976C8D7"
];

$apihost = "http://api.cars-bazar.ru/";
$rq = [
    "login"=>"test@inbox.ru",
    "password"=>'$2y$10$WYTlWdZlagAWdrdVj33RQujnAxk92T99c2wzS001NR0GRB3rEIJYq',
    "apikey"=>"0B13BB74DA3A4F6F88B19A72DF50DCEA"
];

$http=new Http(["proxy"=>false]);
$rs = json_decode($http->fetch($apihost."auth","GET",$rq),true);
print_r($rs);
if(!isset($rs["response"]["session"]))exit;
$rq = [
    "vin"=>$vin,
    "session"=>$rs["response"]["session"]
];
$rs = json_decode($http->fetch($apihost."request","GET",$rq),true);
print_r($rs);
?>
