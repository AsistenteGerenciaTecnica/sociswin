<?php
define("USERAPP", $argv[1]);
define("PASSWORDAPP", $argv[2]);
define("CONFIG_PATH", $argv[3]);
require_once "config/myDBC3.php";
require_once "config/dbconfig3.php";

$dolibarr = new Dolibarr(DB_SERVER,DB_USER,DB_PASS,DB_NAME);

echo "************ OBTENIENDO TOKEN *************** \n\n";
$token = shell_exec("curl --request POST -d '{ \"username\":\"".USERAPP."\", \"password\":\"".PASSWORDAPP."\"}' ".URLTOKEN);
echo "\n\n";

$curl = 'curl --request POST -H "Authorization: '.$token.'" '.URLEVENTOS;

$json = shell_exec($curl);
$json2 = json_decode($json,true);
$obj = json_decode($json2);

for($i=0; $i< count($obj); $i++){
	$cadena2 = " 
	UPDATE ".PREFIJO."facture_extrafields SET 
	evento = '{$obj[$i]->evento}',
	dateevento='{$obj[$i]->eventTime}',
	condicion='{$obj[$i]->condicion}',
	condicionmsg='{$obj[$i]->condicionmsg}'
	WHERE fk_object = '{$obj[$i]->facturasistema}'
	";
	$dolibarr->grabardatos($cadena2);	

	echo "===> Actualizando EVENTOS desde dynamodb del documento: ".$obj[$i]->invoice."\n";
	
}


?>


