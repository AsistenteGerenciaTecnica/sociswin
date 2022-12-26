<?php

$lineas = file(CONFIG_PATH, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lineas as $linea) {
	if (strpos(htmlspecialchars($linea), '$dolibarr_main_db_host') !== false) {
		$data = explode('=',htmlspecialchars($linea));
		define("DB_SERVER", trim(str_replace(";","",str_replace("'","",$data[1]))));
	}
	if (strpos(htmlspecialchars($linea), '$dolibarr_main_db_name') !== false) {
		if (strpos(htmlspecialchars($linea), '$data[0]') !== false) {
			define("DB_NAME", strtolower(preg_replace('/[^a-zA-Z0-9]/', '', USERAPP)));
		} else {
			$data = explode('=',htmlspecialchars($linea));
			define("DB_NAME", trim(str_replace(";","",str_replace("'","",$data[1]))));
		}
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

$lineas = file(CONFIG_PATH, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lineas as $linea) {
	if (strpos(htmlspecialchars($linea), '$dolibarr_main_data_root') !== false) {
		if (strpos(htmlspecialchars($linea), '$data[0]') !== false) {
			define("DOCUMENT", "@/documents/".DB_NAME);
		} else {
			$data = explode('=',htmlspecialchars($linea));
			define("DOCUMENT", "@".trim(str_replace(";","",str_replace("'","",$data[1]))));
		}
	}
}

define("URLTOKEN", "https://2jr7q76fbi.execute-api.us-east-1.amazonaws.com/prod/token" );
define("URLUPDATE", "https://2jr7q76fbi.execute-api.us-east-1.amazonaws.com/prod/update" );
define("DIAN4URLUPDATE", "https://jtcw9uxrdc.execute-api.us-east-1.amazonaws.com/prod/update" );
define("URLEXECUTE", "https://2jr7q76fbi.execute-api.us-east-1.amazonaws.com/prod/execution" );
define("URLVERIFICAR", "https://2jr7q76fbi.execute-api.us-east-1.amazonaws.com/prod/verificar" );
define("URLSUBIRS3", "https://2jr7q76fbi.execute-api.us-east-1.amazonaws.com/prod/subes3" );
define("DIAN4URLSUBIRS3", "https://jtcw9uxrdc.execute-api.us-east-1.amazonaws.com/prod/subes3" );
define("URLEMAIL", "https://2jr7q76fbi.execute-api.us-east-1.amazonaws.com/prod/enviapdf" );
define("DIAN4URLEMAIL", "https://jtcw9uxrdc.execute-api.us-east-1.amazonaws.com/prod/enviapdf" );
define("URLEVENTOS", "https://2jr7q76fbi.execute-api.us-east-1.amazonaws.com/prod/updateeventos" );
define("URLPREFIJOS", "https://2jr7q76fbi.execute-api.us-east-1.amazonaws.com/prod/prefijos" );
define("URLPAGOS", "https://2jr7q76fbi.execute-api.us-east-1.amazonaws.com/prod/pagos" );
define("DIAN3URLEXECUTE", "https://11kddnl6de.execute-api.us-east-1.amazonaws.com/prod/todoenuno" );
define("DIAN4URLEXECUTE", "https://jtcw9uxrdc.execute-api.us-east-1.amazonaws.com/prod/restapi" );
define("DIAN4URLVERIFICAR", "https://jtcw9uxrdc.execute-api.us-east-1.amazonaws.com/prod/verificar" );

?>
