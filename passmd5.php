<?php
$pass = "test";
include("autoload.php");
use carbazar\Session as Session;
$pass = (count($argv)>1)?$argv[1]:"test";
$hash = password_hash($pass, PASSWORD_BCRYPT);
echo $pass."\t".md5($pass)."\t".$hash."\t".Session::generate()."\n";
?>
