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

$curl = 'curl --request POST -H "Authorization: '.$token.'" '.URLUPDATE;

$json = shell_exec($curl);
$json2 = json_decode($json,true);
$obj = json_decode($json2);

for($i=0; $i< count($obj); $i++){
	if(strlen($obj[$i]->MessageId) > 20) { $messageid = 'true'; }
	$cadena2 = " 
	UPDATE ".PREFIJO."facture_extrafields SET 
	cufe = '{$obj[$i]->Uuid}',
	zipkey='{$obj[$i]->ZipKey}',
	isvalid='{$obj[$i]->IsValid}',
	statuscode='{$obj[$i]->StatusCode}',
	statusdescription='{$obj[$i]->StatusDescription}',
	errormessage='".str_replace( '\"', '', json_encode($obj[$i]->ErrorMessage))."',
	success='{$obj[$i]->Success}',
	processedmessage='{$obj[$i]->ProcessedMessage}',
	pdf21='{$obj[$i]->pdf}',
	messageid='$messageid'
	WHERE fk_object = '{$obj[$i]->facturasistema}'
	";
	echo $cadena2;
	$dolibarr->grabardatos($cadena2);	

	echo "===> Actualizando desde la DIAN datos del documento: ".$obj[$i]->invoice."\n";
	
}


?>


