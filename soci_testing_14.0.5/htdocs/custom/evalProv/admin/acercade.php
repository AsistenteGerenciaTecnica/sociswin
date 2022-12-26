<?php
require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/custom/evalProv/lib/evalProv.lib.php';

$langs->load("admin");
$langs->load("install");
$langs->load("errors");
$langs->load("evalProv@evalProv");

if (!$user->admin) accessforbidden();

$value = GETPOST('value','alpha');
$action = GETPOST('action','alpha');

$title = $langs->trans("evalProv");
$helpurl = "ES:AIU";
llxHeader("",$title,$helpurl);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($title,$linkback,'setup');
$head = evalprov_admin_prepare_head();

dol_fiche_head($head, 'acercade',$langs->trans('evalProv'), 0, 'logo@evalProv');

$form=new Form($db);


print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
		print '<td>'.$langs->trans("Description").'</td>'."\n";
		print '<td align="right">'.$langs->trans("Value").'</td>'."\n";
	print '</tr>'."\n";
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
		print '<td>';
			print '<img src="../img/object_logo.png" width="35px"/> ';
			print $langs->trans("evalProv");
		print '</td>';
		print '<td width="60%" align="right">';			
			print '1.0.0';	
		print '</td>';
	print '</tr>';
	$var=!$var;

	print '<tr '.$bc[$var].'>';
		print '<td>';
			print '<img src="'.dol_buildpath('/theme/'.$conf->theme.'/img/favicon.ico',1).'"/>';
			print '&nbsp;';
			print 'Dolibarr';
		print '</td>';
		print '<td align="right">';
			print '14.0.5';					
		print '</td>';
	print '</tr>';		
print '</table>';

print '<br/>';
print '<br/>';

print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
		print '<td colspan="3">'.$langs->trans("Company").'</td>'."\n";
	print '</tr>'."\n";
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
		print '<td rowspan="8" width="20%"><img src="../img/ultimate.png" width="250"/></td>';
		print '<td width="20%">'.$langs->trans("Developer").'</td>';
		print '<td width="70%" align="left">';			
			print 'Mateo Hernández López';	
		print '</td>';
	print '</tr>';
	$var=!$var;
	print '<tr '.$bc[$var].'>';
		print '<td>'.$langs->trans("Phone").'</td>';
		print '<td align="left">';			
			print '(6) 3419039';	
		print '</td>';
	print '</tr>';
	$var=!$var;
	print '<tr '.$bc[$var].'>';
		print '<td>'.$langs->trans("Email").'</td>';
		print '<td align="left">';			
			print 'servicioalcliente@ultimate.com.co';	
		print '</td>';
	print '</tr>';
	$var=!$var;
	print '<tr '.$bc[$var].'>';
		print '<td>'.$langs->trans("Web").'</td>';
		print '<td align="left">';			
			print '<a href="https://ultimate.com.co">https://ultimate.com.co</a>';	
		print '</td>';
	print '</tr>';
	$var=!$var;
	print '<tr '.$bc[$var].'>';
		print '<td>LinkedIn</td>';
		print '<td align="left">';			
			print '<a href="https://co.linkedin.com/company/ultimate-technology-sas" target="_blank"><img src="../img/linkedin.png" width="20"/></a>';	
		print '</td>';
	print '</tr>';
	$var=!$var;
	print '<tr '.$bc[$var].'>';
		print '<td>Facebook</td>';
		print '<td align="left">';			
			print '<a href="https://www.facebook.com/ultimatetechnologysas/" target="_blank"><img src="../img/facebook.png" width="20"></a>';	
		print '</td>';
	print '</tr>';	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
		print '<td>Instagram</td>';
		print '<td align="left">';			
			print '<a href="https://www.instagram.com/ultimatetechnology/?hl=es-la" target="_blank"><img src="../img/instagram.png" width="20"></a>';	
		print '</td>';
	print '</tr>';
	$var=!$var;
	print '<tr '.$bc[$var].'>';
		print '<td>Youtube</td>';
		print '<td align="left">';			
			print '<a href="https://www.youtube.com/channel/UCec3ccuoZSl5yLS433qObDA" target="_blank"><img src="../img/youtube.png" width="20"></a>';	
		print '</td>';
	print '</tr>';
print '</table>';

print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/aiu/lib/js/aiu.js"></script>';

dol_fiche_end();
$db->close();
llxFooter();
?>