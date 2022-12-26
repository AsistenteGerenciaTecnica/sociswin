<?php
define("USERAPP", $_REQUEST['userapp']);
define("PASSWORDAPP", $_REQUEST['passwordapp']);
define("CONFIG_PATH", $_REQUEST['configpath']);
define("CONFIG_ENTITY", $_REQUEST['entity']);

require_once "config/myDBC3.php";
require_once "config/dbconfig3.php";

$dolibarr = new Dolibarr(DB_SERVER,DB_USER,DB_PASS,DB_NAME);

//Elimina prefijos actuales
$cadena = "DELETE FROM ".PREFIJO."facturadian_cargarprefijos WHERE entidad = ".CONFIG_ENTITY;
$dolibarr->grabardatos($cadena);

echo "************ OBTENIENDO TOKEN *************** \n\n <br />";
$token = shell_exec("curl --request POST -d '{ \"username\":\"".USERAPP."\", \"password\":\"".PASSWORDAPP."\"}' ".URLTOKEN);
echo "\n\n";

$curl = 'curl --request POST -H "Authorization: '.$token.'" '.URLPREFIJOS;

$json = shell_exec($curl);
$json2 = json_decode($json,true);
$obj = json_decode($json2);

for($i=0; $i< count($obj); $i++){

	$entidad = CONFIG_ENTITY;
	$cadena2 = "
	INSERT INTO ".PREFIJO."facturadian_cargarprefijos 
	(prefijo,clavetecnica,pindian,nit,ambiente,entidad) VALUES (
	'{$obj[$i]->Prefijo}',
	'{$obj[$i]->ClaveTecnica}',
	'{$obj[$i]->PinDian}',
	'{$obj[$i]->Nit}',
	'{$obj[$i]->Ambiente}',
	'$entidad'
	)";
	//echo $cadena2;
	$dolibarr->grabardatos($cadena2);

	echo "<br />===> Insertado prefijo : ".$obj[$i]->Prefijo;

}

?>


