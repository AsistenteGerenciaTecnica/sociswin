<?php
define("ROWID", $_REQUEST[rowid]);
define("USERAPP", $_REQUEST[userapp]);
define("PASSWORDAPP", $_REQUEST[passwordapp]);
define("CONFIG_PATH", $_REQUEST[configpath]);

require_once "config/dbconfig3.php";
require_once "config/myDBC3.php";

$dolibarr = new Dolibarr(DB_SERVER,DB_USER,DB_PASS,DB_NAME);

//FACTURAS A ENVIAR POR EMAIL
$cadena = "
SELECT fac.ref AS facnumber,fac.datef AS datef,extra.zipkey AS zipkey,soc.email AS ReceptorEmail
FROM ".PREFIJO."facture AS fac
INNER JOIN ".PREFIJO."societe AS soc
ON fac.fk_soc = soc.rowid
INNER JOIN ".PREFIJO."facture_extrafields AS extra
ON fac.rowid = extra.fk_object
WHERE extra.isvalid = 'true' AND extra.pdf21 = 'true' AND fac.rowid='".ROWID."'
";

$nohayfacturas = true;
$primeravez = true;

$result = $dolibarr->leerdatosarray($cadena);
while($row = $result->fetch_array(MYSQLI_ASSOC)) {
	if($primeravez) {
		echo "************ OBTENIENDO TOKEN *************** \n\n <br /><br />";
		$token = shell_exec("curl --request POST -d '{ \"username\":\"".USERAPP."\", \"password\":\"".PASSWORDAPP."\"}' ".URLTOKEN);
		echo "\n\n";
		$primeravez = false;
	}

	$nohayfacturas = false;
	//VARIABLES CALCULADAS
	$prefijonumero = explode("-",$row['facnumber']);
	$prefijo = $prefijonumero[0];
	$numero = $prefijonumero[1];
	$numero = (int) $numero;
	
	//Dian4
	$fecha = new DateTime();
	$Timedate=$fecha->format('YmdHis');
	$nameStepFunction=$Timedate."-".$prefijo.$numero;
	
	//OBTENE EL AMBIENTE
	$cadenaPRE = "SELECT * FROM ".PREFIJO."facturadian_cargarprefijos WHERE prefijo = '$prefijo' LIMIT 1 ";
	$rowPRE = $dolibarr->leerdatos($cadenaPRE);

	//llama el envio del pdf al cliente trayendo el email de nuevo del cliente
	$curl = 'curl --request POST -H "Authorization: '.$token.'" -H "Content-Type:application/json" -d \'{';
	$curl.= '"Invoice":"'.$prefijo.$numero;
	$curl.= '","nameStepFunction":"'.$nameStepFunction;
	$curl.= '","IssueDate":"'.$row['datef'];
	$curl.= '","Ambiente":"'.$rowPRE['ambiente'];
	$curl.= '","ReceptorEmail":"'.$row['ReceptorEmail'];
	$curl.='" }\' '.DIAN4URLEMAIL;
	
	//echo $curl;
	$json = shell_exec($curl." > /dev/null 2>/dev/null &");
    echo "===> Enviando: ".$prefijo.$numero." a: ".$row['ReceptorEmail']."\n";
}
if($nohayfacturas) { echo "<br /><font color='red'>**** NO SE ENVIO NINGUN DOCUMENTO **** </font> "; }
	
?>


