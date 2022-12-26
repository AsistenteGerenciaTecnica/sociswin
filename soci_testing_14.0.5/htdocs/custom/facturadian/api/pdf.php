<?php
define("ROWID", $_REQUEST[rowid]);
define("USERAPP", $_REQUEST[userapp]);
define("PASSWORDAPP", $_REQUEST[passwordapp]);
define("CONFIG_PATH", $_REQUEST[configpath]);

require_once "config/dbconfig3.php";
require_once "config/myDBC3.php";

$dolibarr = new Dolibarr(DB_SERVER,DB_USER,DB_PASS,DB_NAME);

//PDF DE FACTURAS PARA SUBIR A S3
$cadena = " 
SELECT fac.datef AS datef, fac.rowid, fac.ref AS facnumber, fac.entity AS entity
FROM ".PREFIJO."facture AS fac
INNER JOIN ".PREFIJO."facture_extrafields AS extra
ON fac.rowid = extra.fk_object
WHERE extra.isvalid = 'true' AND fac.rowid='".ROWID."'
";

$primeravez = true;
$result = $dolibarr->leerdatosarray($cadena);
while($row = $result->fetch_array(MYSQLI_ASSOC)) {
	
	
	if($primeravez) {
        echo "************ OBTENIENDO TOKEN *************** \n\n";
        $token = shell_exec("curl --request POST -d '{ \"username\":\"".USERAPP."\", \"password\":\"".PASSWORDAPP."\"}' ".URLTOKEN);
        echo "\n\n";
		$primeravez = false;
	}
	
	
	//VARIABLES CALCULADAS
	$prefijonumero = explode("-",$row['facnumber']);
	$prefijo = $prefijonumero[0];
	$numero = $prefijonumero[1];
	$numero = (int) $numero;
	
	if($row['entity'] == 1) { $origenpdf = DOCUMENT."/facture/"; }
	else { $origenpdf = DOCUMENT."/".$row['entity']."/facture/"; }
	
	$curl = 'curl --request POST -H "Authorization: '.$token.'" -H "Content-Type:application/octet-stream" --data-binary "'.$origenpdf.'"'.$row['facnumber'].'"/"'.$row['facnumber'].'".pdf" "'.DIAN4URLSUBIRS3.'?invoice="'.$prefijo.$numero.'"&carpeta="'.substr($row['datef'],0,7).'" " ';
	
	//echo $curl;

	$json = shell_exec($curl." > /dev/null 2>/dev/null &");
	echo "===> Subiendo pdf a S3 del documento: ".$prefijo.$numero."\n";
	//echo $json;
	
	$cadenaf = " UPDATE ".PREFIJO."facture_extrafields SET pdf21='Si' WHERE fk_object = '$row[rowid]' ";
	$dolibarr->grabardatos($cadenaf);

}

?>


