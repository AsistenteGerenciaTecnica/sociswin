<?php
define("USERAPP", $argv[1]);
define("PASSWORDAPP", $argv[2]);
define("CONFIG_PATH", $argv[3]);
define("ROWIDS", $argv[4]);

require_once "config/dbconfig3.php";
require_once "config/myDBC3.php";

$dolibarr = new Dolibarr(DB_SERVER,DB_USER,DB_PASS,DB_NAME);;

$cadena = "
SELECT fac.rowid AS rowid,
fac.type AS type,
fac.ref AS facnumber,
fac.datef AS datef,
extra.cufe AS cufe
FROM ".PREFIJO."facture AS fac
LEFT JOIN ".PREFIJO."facture_extrafields AS extra
ON fac.rowid = extra.fk_object
WHERE fac.rowid IN (".ROWIDS.") ORDER BY fac.ref ";

$nohayfacturas = true;
$primeravez = true;
$result = $dolibarr->leerdatosarray($cadena);

while($row = $result->fetch_array(MYSQLI_ASSOC)) {
	
	if($primeravez) {
		echo "************ OBTENIENDO TOKEN *************** \n\n <br />";
		$token = shell_exec("curl --request POST -d '{ \"username\":\"".USERAPP."\", \"password\":\"".PASSWORDAPP."\"}' ".URLTOKEN);
		echo "\n\n";
		$primeravez = false;
	}
	
	$nohayfacturas = false;

	//VARIABLES CALCULADAS
	$prefijonumero = explode("-",$row[facnumber]);
	$prefijo = $prefijonumero[0];
	$numero = $prefijonumero[1];
	$numero = (int) $numero;
	
	$fecha = new DateTime();
	$Timedate=$fecha->format('YmdHis');
	$nameStepFunction="VerificarDian4-".$Timedate."-".$prefijo.$numero;

	//OBTENE EL AMBIENTE
	$cadenaPRE = "SELECT * FROM ".PREFIJO."facturadian_cargarprefijos WHERE prefijo = '$prefijo' LIMIT 1 ";
	$rowPRE = $dolibarr->leerdatos($cadenaPRE);
	
	//llama la api de verificar
	$curl = 'curl --request POST -H "Authorization: '.$token.'" -H "Content-Type:application/json" -d \'{';
	$curl.= '"Invoice":"'.$prefijo.$numero;
	$curl.= '","Cufe":"'.$row['cufe'];
	$curl.= '","IssueDate":"'.$row['datef'];
	$curl.= '","Ambiente":"'.$rowPRE['ambiente'];
	$curl.= '","nameStepFunction":"'.$nameStepFunction;
	$curl.='" }\' '.DIAN4URLVERIFICAR;

	//echo $curl;
	$json = shell_exec($curl." > /dev/null 2>/dev/null &");
	echo "===> Verificando en DIAN: ".$prefijo.$numero." Cufe:".$row['cufe']." \n <br />";
}
if($nohayfacturas) { echo "<br /><font color='red'>**** NO SE PROCESO NINGUN DOCUMENTO **** </font> "; }


?>


