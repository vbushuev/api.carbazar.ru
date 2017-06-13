<?php
$pass = "test";
include("autoload.php");
use carbazar\Session as Session;
$pass = (count($argv)>1)?$argv[1]:"test";
echo $pass."\t".md5($pass)."\t".Session::generate()."\n";
?>
