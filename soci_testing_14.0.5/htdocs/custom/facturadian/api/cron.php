<?php
define("CONFIG_PATH", '/var/www/html/********/conf/conf.php');
require_once "config/dbconfig3.php";
require_once "config/myDBC3.php";

$dolibarr = new Dolibarr(DB_SERVER,DB_USER,DB_PASS,DB_NAME);

define("FACTURADIAN_USERAPP", "************");
define("FACTURADIAN_PASSWORDAPP", "********");

$cadena="SELECT fac.rowid AS rowid FROM  ".PREFIJO."facture AS fac
LEFT JOIN ".PREFIJO."facture_extrafields AS extra
ON fac.rowid = extra.fk_object
WHERE (NOT extra.envio OR extra.envio is NULL) AND LEFT(fac.ref,1) <> '(' AND (extra.isvalid <> 'true' OR extra.isvalid is NULL)
AND fac.entity='**********' ";

$result = $dolibarr->leerdatosarray($cadena);

$rowidfacturas = "";
while($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $rowidfacturas = $rowidfacturas.$row['rowid'].",";
}

$rowidfacturas = trim($rowidfacturas, ',');
$ejecutar = "php /var/www/html/********/custom/facturadian/api/enviar.php ".FACTURADIAN_USERAPP." ".FACTURADIAN_PASSWORDAPP." ".CONFIG_PATH." ".$rowidfacturas;

echo shell_exec($ejecutar);

?>

