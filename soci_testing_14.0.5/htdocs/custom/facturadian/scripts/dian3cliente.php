<?php
define("USERAPP", $argv[1]);
define("PASSWORDAPP", $argv[2]);
define("AMBIENTE", $argv[3]);
define("DOCUMENT", $argv[4]);
define("CONFIG_PATH", $argv[5]);
require_once "config/myDBC3.php";
require_once "config/dbconfig3.php";

$dolibarr = new Dolibarr(DB_SERVER,DB_USER,DB_PASS,DB_NAME);

//FACTURAS A ENVIAR POR EMAIL
$cadena = "
SELECT fac.ref AS facnumber,fac.datef AS datef,extra.zipkey AS zipkey,soc.email AS ReceptorEmail
FROM ".PREFIJO."facture AS fac
INNER JOIN ".PREFIJO."societe AS soc
ON fac.fk_soc = soc.rowid
INNER JOIN ".PREFIJO."facture_extrafields AS extra
ON fac.rowid = extra.fk_object
WHERE extra.envio AND extra.isvalid = 'true' AND extra.pdf21 = 'true' AND fac.ref = '".DOCUMENT."' ORDER BY fac.ref
 LIMIT 1";

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
	
	//Subir el PDF para asegurar que sea la ultima version del pdf
	$ejecutar = 'curl --request POST -H "Authorization: '.$token.'" -H "Content-Type:application/octet-stream" --data-binary "'.DOCUMENTOS.'"'.$row['facnumber'].'"/"'.$row['facnumber'].'".pdf" "'.URLSUBIRS3.'?invoice="'.$prefijo.$numero.'"&carpeta="'.substr($row['datef'],0,7).'" " ';
	shell_exec($ejecutar." > /dev/null 2>/dev/null &");
	echo "<br />Documento: (".$prefijo.$numero.") subiendo nuevo pdf,  ";
	
	//llama el envio del pdf al cliente trayendo el email de nuevo del cliente
	$curl = 'curl --request POST -H "Authorization: '.$token.'" -H "Content-Type:application/json" -d \'{';
	$curl.= '"Invoice":"'.$prefijo.$numero;
	$curl.= '","IssueDate":"'.$row['datef'];
	$curl.= '","Ambiente":"'.AMBIENTE;
	$curl.= '","ReceptorEmail":"'.$row['ReceptorEmail'];
	$curl.='" }\' '.URLEMAIL;
	
	//echo $curl;
	$json = shell_exec($curl." > /dev/null 2>/dev/null &");
	echo " y enviando al email: ".$row['ReceptorEmail']."\n";
}
if($nohayfacturas) { echo "<br /><font color='red'>**** NO SE ENVIO NINGUN DOCUMENTO **** </font> "; } else {
	echo "<br /><font color='blue'><h3>Los resultados se reflejaran en 3 minutos en Dolibarr...</h3></font>";
}
	
?>


