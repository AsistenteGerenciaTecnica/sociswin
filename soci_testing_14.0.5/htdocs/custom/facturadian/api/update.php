<?php
define("INVOICES", $_REQUEST[invoices]);
define("USERAPP", $_REQUEST[userapp]);
define("PASSWORDAPP", $_REQUEST[passwordapp]);
define("CONFIG_PATH", $_REQUEST[configpath]);

require_once "config/dbconfig3.php";
require_once "config/myDBC3.php";

$dolibarr = new Dolibarr(DB_SERVER,DB_USER,DB_PASS,DB_NAME);;

$nohayfacturas = true;
$primeravez = true;

$invoices = explode(",", INVOICES);
foreach ($invoices as $invoice) {
    if($primeravez) {
		echo "************ OBTENIENDO TOKEN *************** \n\n <br /><br />";
		$token = shell_exec("curl --request POST -d '{ \"username\":\"".USERAPP."\", \"password\":\"".PASSWORDAPP."\"}' ".URLTOKEN);
		echo "\n\n";
		$primeravez = false;
	}
	$nohayfacturas = false;
	//VARIABLES CALCULADAS
	$prefijonumero = explode("-",$invoice);
	$prefijo = $prefijonumero[0];
	$numero = $prefijonumero[1];
	$numero = (int) $numero;
	
	$fecha = new DateTime();
	$Timedate=$fecha->format('YmdHis');
	$nameStepFunction="UpdateOnly-".$Timedate."-".$prefijo.$numero;

	//llama la api de update
	$curl = 'curl --request POST -H "Authorization: '.$token.'" -H "Content-Type:application/json" -d \'{';
	$curl.= '"Invoice":"'.$prefijo.$numero;
	$curl.= '","tablaNombre":"'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '', USERAPP));
	$curl.= '","nameStepFunction":"'.$nameStepFunction;
	$curl.='" }\' '.DIAN4URLUPDATE;

	//echo $curl;
	$json = shell_exec($curl." > /dev/null 2>/dev/null &");
	echo "===> Update: ".$prefijo.$numero." \n <br />";
}
if($nohayfacturas) { echo "<br /><font color='red'>**** NO SE PROCESO NINGUN DOCUMENTO **** </font> "; }

?>


