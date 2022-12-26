<?php
define("CONFIG_PATH", $argv[1]);

$lineas = file(CONFIG_PATH, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lineas as $linea) {
	if (strpos(htmlspecialchars($linea), '$dolibarr_main_db_host') !== false) {
		$data = explode('=',htmlspecialchars($linea));
		define("DB_SERVER", trim(str_replace(";","",str_replace("'","",$data[1]))));
	}
	if (strpos(htmlspecialchars($linea), '$dolibarr_main_db_user') !== false) {
		$data = explode('=',htmlspecialchars($linea));
		define("DB_USER", trim(str_replace(";","",str_replace("'","",$data[1]))));
	}
	if (strpos(htmlspecialchars($linea), '$dolibarr_main_db_pass') !== false) {
		$data = explode('=',htmlspecialchars($linea));
		define("DB_PASS", trim(str_replace(";","",str_replace("'","",$data[1]))));
	}
	if (strpos(htmlspecialchars($linea), '$dolibarr_main_db_prefix') !== false) {
		$data = explode('=',htmlspecialchars($linea));
		define("PREFIJO", trim(str_replace(";","",str_replace("'","",$data[1]))));
	}
}

require_once "config/myDBC3.php";

//Eliminar todas las tareas actuales de at
$ejecutar0 = "atrm $(atq | cut -f1)";
shell_exec($ejecutar0);

$cadena="SHOW DATABASES";
$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASS) or die ('Error connecting to mysql: ' . mysqli_error($link).'\r\n');

if (!($result=mysqli_query($link,$cadena))) {
        printf("Error: %s\n", mysqli_error($link));
    }

while( $row = mysqli_fetch_row( $result ) ){
	if (($row[0]!="information_schema") && ($row[0]!="mysql") && ($row[0]!="tmp") && ($row[0]!="innodb") && ($row[0]!="performance_schema")) {
	//if ($row[0]=="infofacturadiancom") {
		echo "Programando AT sobre la base de datos: ".$row[0]."\r\n";
		$dolibarr = new Dolibarr(DB_SERVER,DB_USER,DB_PASS,$row[0]);

		//Elimina las tareas detalles de esa base de datos
		$cadenaCronDel = "TRUNCATE TABLE ".PREFIJO."facturadian_cronjobs";
		$dolibarr->grabardatos($cadenaCronDel);
		echo "Eliminadas las tareas de la base de datos: ".$row[0]."\r\n";
		
		//leer si tiene tareas esta base de datos
		$cadenaCron = "SELECT * FROM ".PREFIJO."facturadian_cron WHERE 1 ";
		$resultCron = $dolibarr->leerdatosarray($cadenaCron);

		if($resultCron) {

			//Leemos parametros
			$cadenaParametros = "SELECT * FROM ".PREFIJO."facturadian_credenciales WHERE 1 LIMIT 1";
			$rowParametros = $dolibarr->leerdatos($cadenaParametros);

			while($rowCron = $resultCron->fetch_array(MYSQLI_ASSOC)) {
				$arrayDias = explode (",", $rowCron['dias']);

				if (in_array(date("N"), $arrayDias)) {

					$arrayPrefijos = explode (",", $rowCron['prefijo']);
					$tiempo=0;
					foreach ($arrayPrefijos as $prefijo) {

						//Envia
						$ejecutar = "echo 'php /var/www/html/dolibarr/htdocs/custom/facturadian/scripts/dian3enviar_".$rowCron['documento'].".php ".$rowParametros['username']." ".$rowParametros['password']." ".$rowCron['ambiente']." ".$rowCron['cantidad']." ".$prefijo." ".CONFIG_PATH."' | at ".$rowCron['hora'].":".$rowCron['minuto']." + ".++$tiempo." minutes 2>&1";
						echo "\n".$ejecutar;
						$retval = NULL;
						$output = NULL;
						exec($ejecutar, $output, $retval);
						$job = explode(" ",$output[1]);
						$cadenaDetalle = "INSERT INTO ".PREFIJO."facturadian_cronjobs (cron, job, prefijo, detalle,accion) 
											VALUES ('$rowCron[rowid]','$job[1]','$prefijo','$output[1]','enviar') ";
						$dolibarr->grabardatos($cadenaDetalle);

						//Actualiza
						++$tiempo;
						$ejecutar2= "echo 'php /var/www/html/dolibarr/htdocs/custom/facturadian/scripts/update.php ".$rowParametros['username']." ".$rowParametros['password']." ".CONFIG_PATH."' | at ".$rowCron['hora'].":".$rowCron['minuto']." + ".++$tiempo." minutes 2>&1";
						echo "\n".$ejecutar2;
						$retval = NULL;
						$output = NULL;
						exec($ejecutar2, $output, $retval);
						$job = explode(" ",$output[1]);
						$cadenaDetalle = "INSERT INTO ".PREFIJO."facturadian_cronjobs (cron, job, prefijo, detalle,accion) 
											VALUES ('$rowCron[rowid]','$job[1]','$prefijo','$output[1]','actualizar') ";
						$dolibarr->grabardatos($cadenaDetalle);

						//Eventos
						++$tiempo;
						$ejecutar7= "echo 'php /var/www/html/dolibarr/htdocs/custom/facturadian/scripts/eventos.php ".$rowParametros['username']." ".$rowParametros['password']." ".CONFIG_PATH."' | at ".$rowCron['hora'].":".$rowCron['minuto']." + ".++$tiempo." minutes 2>&1";
						echo "\n".$ejecutar7;
						$retval = NULL;
						$output = NULL;
						exec($ejecutar7, $output, $retval);
						$job = explode(" ",$output[1]);
						$cadenaDetalle = "INSERT INTO ".PREFIJO."facturadian_cronjobs (cron, job, prefijo, detalle,accion) 
											VALUES ('$rowCron[rowid]','$job[1]','$prefijo','$output[1]','eventos') ";
						$dolibarr->grabardatos($cadenaDetalle);

						$tiempo = $tiempo + 3;
					}

				}


			}
		}


	}
}

?>


