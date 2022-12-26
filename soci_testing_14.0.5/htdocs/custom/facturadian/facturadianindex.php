<style> 
.descargas  {
  background-color: #4CAF50;
  border: none;
  color: white;
  padding: 16px 32px;
  text-decoration: none;
  margin: 4px 2px;
  cursor: pointer;
}
</style>
<?php
// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

// Load translation files required by the page
$langs->loadLangs(array("facturadian@facturadian"));

$action=GETPOST('action', 'alpha');


// Security check
//if (! $user->rights->facturadian->myobject->read) accessforbidden();
$socid=GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0)
{
	$action = '';
	$socid = $user->socid;
}

$max=5;
$now=dol_now();


/*
 * Actions
 */

// None


/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader("", $langs->trans("FacturaDianArea"));

print load_fiche_titre($langs->trans("Bienvenido al Modulo FacturaDian"), '', 'facturadian.png@facturadian');

print "<center><h2><a class='descargas' href='https://facturadian-descargas.s3.us-east-2.amazonaws.com/dolibarr_11.0.2/index.html' target='_new'>Descargar la ultima version AQUI (No olvides apagar y prender el modulo para ver los cambios)</a> </h2></center>";		
		
print "
	<br /><br />
	<font size='3'>
		Coloca las credenciales que uses en <a href ='https://portal.facturadian.com' target='_blank'><font color='red'>portal.facturadian.com</font></a> en la opcion <b>Credenciales FacturaDian</b>,  
		actualiza los prefijos desde la opcion <b>Lista de prefijos</b>, ajusta los datos de su empresa desde la opcion  <b>Empresa/organizacion</b>, ajusta los diccionarios desde la opcion <b>Diccionarios</b>
		y por ultimo verifica que todo este bien desde la opcion <b>Verificar FacturaDian</b>, no olvide ingresar los datos en los <b>parametros del modulo</b> y por ultimo no olvide que las facturas se envian desde la grilla de lista de facturas del modulo de Facturacion de Dolibarr.
	</font>";
	
print "<br />";
print "<img src='https://facturadian.s3.amazonaws.com/flujo.png' width='95%' height='100%'>";

print '<div class="fichetwothirdright"><div class="ficheaddleft">';
print '</div></div></div>';

// End of page
llxFooter();
$db->close();
