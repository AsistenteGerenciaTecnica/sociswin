<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *	\file       socinexus/socinexusindex.php
 *	\ingroup    socinexus
 *	\brief      Home page of socinexus top menu
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT .'/societe/class/societe.class.php';

// Load translation files required by the page
$langs->loadLangs(array("socinexus@socinexus"));

$action = GETPOST('action', 'aZ09');


// Security check
// if (! $user->rights->socinexus->myobject->read) {
// 	accessforbidden();
// }
$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0) {
	$action = '';
	$socid = $user->socid;
}

$max = 5;
$now = dol_now();


/*
 * Actions
 */

// None


/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader("", $langs->trans("SociNexusArea"));

?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Heebo:wght@700&display=swap" rel="stylesheet">
<style type="text/css">

	.socititle {
		margin-bottom: 20px;
		display: flex;
		
		font-family: 'Heebo', sans-serif;
		color: #2D82C4;
		font-weight: bold;
		font-size: 32pt;
	}

	.socititle > img{
		width: 50px;
		margin-right: 5px;
	}

</style>

<div class='socititle'>
	<img src='https://drive.google.com/uc?id=1D8kTJQW-EmxD1EE63iB09-9RDp2FQ43r&export=download'></img>
	<div>SOCI NEXUS</div>
</div>

<?php


//print load_fiche_titre($langs->trans("SociNexusArea"), '', 'socinexus.png@socinexus');

//print '<div class="fichecenter"><div class="fichethirdleft">';


//BEGIN MODULEBUILDER DRAFT MYOBJECT
// Draft MyObject
/* if (! empty($conf->socinexus->enabled) && $user->rights->socinexus->read)
{
	$langs->load("orders");

	$sql = "SELECT c.rowid, c.ref, c.ref_client, c.total_ht, c.tva as total_tva, c.total_ttc, s.rowid as socid, s.nom as name, s.client, s.canvas";
	$sql.= ", s.code_client";
	$sql.= " FROM ".MAIN_DB_PREFIX."commande as c";
	$sql.= ", ".MAIN_DB_PREFIX."societe as s";
	if (! $user->rights->societe->client->voir && ! $socid) $sql.= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
	$sql.= " WHERE c.fk_soc = s.rowid";
	$sql.= " AND c.fk_statut = 0";
	$sql.= " AND c.entity IN (".getEntity('commande').")";
	if (! $user->rights->societe->client->voir && ! $socid) $sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = ".((int) $user->id);
	if ($socid)	$sql.= " AND c.fk_soc = ".((int) $socid);

	$resql = $db->query($sql);
	if ($resql)
	{
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="3">'.$langs->trans("DraftMyObjects").($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th></tr>';

		$var = true;
		if ($num > 0)
		{
			$i = 0;
			while ($i < $num)
			{

				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven"><td class="nowrap">';

				$imp->id=$obj->rowid;
				$imp->ref=$obj->ref;
				$imp->ref_client=$obj->ref_client;
				$imp->total_ht = $obj->total_ht;
				$imp->total_tva = $obj->total_tva;
				$imp->total_ttc = $obj->total_ttc;

				print $imp->getNomUrl(1);
				print '</td>';
				print '<td class="nowrap">';
				print '</td>';
				print '<td class="right" class="nowrap">'.price($obj->total_ttc).'</td></tr>';
				$i++;
				$total += $obj->total_ttc;
			}
			if ($total>0)
			{

				print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price($total)."</td></tr>";
			}
		}
		else
		{

			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("NoOrder").'</td></tr>';
		}
		print "</table><br>";

		$db->free($resql);
	}
	else
	{
		dol_print_error($db);
	}
} */
//END MODULEBUILDER DRAFT MYOBJECT


print '<div class="fichecenter"><div class="fichethirdleft">';


$NBMAX = $conf->global->MAIN_SIZE_SHORTLIST_LIMIT;
$max = $conf->global->MAIN_SIZE_SHORTLIST_LIMIT;

//BEGIN MODULEBUILDER LASTMODIFIED MYOBJECT
// Last modified myobject
if (! empty($conf->socinexus->enabled) && $user->rights->socinexus->implementacion->read)
{
	/*
	SQL Para obtener todas las implementaciones
	*/
	$sql = "SELECT *";
	$sql.= " FROM ".MAIN_DB_PREFIX."socinexus_implementacion as s";
	$sql .= " WHERE ref LIKE '%". GETPOST('search') ."%'";	

	if (trim(GETPOST('estado')) != ""){
		$sql .= " AND estado = " . GETPOST('estado');
	}

	$sql .= " ORDER BY s.tms DESC";

	$resql = $db->query($sql);
	
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		print '<table class="noborder centpercent">';

		print '<tr class="liste_titre">';
		
		print '<th>';
		print 'Logo';
		print '</th>';

		print '<th>';
		print 'Nombre';

		print '<form method="GET" style="display:flex">';
		print '<input type="text" name="search" value="'. GETPOST('search') .'"></input>';
		print '<button type="submit">Buscar</button>';
		print '</form>';

		print '</th>';

		print '<th>';
		print 'Tercero';
		print '</th>';

		print '<th>';
		print 'URL';
		print '</th>';

		print '<th >Creación</th>';

		print '<th>';
		print 'Estado';
		print '</th>';

		print '</tr>';

		if ($num)
		{
			while ($i < $num)
			{
				$objp = $db->fetch_object($resql);

				$imp->id = $objp->rowid;
				$imp->ref = $objp->ref;
				$imp->tms = $objp->tms;
				$imp->fk_soc = $objp->fk_soc;
				$imp->logo_url = $objp->logo_url;
				$imp->soci_url = $objp->soci_url;
				$imp->estado = $objp->estado;
				$imp->creacion = $objp->soci_creacion;

				$third = new Societe($db);

				if (!empty($imp->fk_soc)) {
					$third->fetch($imp->fk_soc);
				}

				$nombre = str_replace("_", " ", $imp->ref);
				
				print '<tr class="oddeven">';

				print '<td class="nowrap"><img src="'. $imp->logo_url .'" style="width:100px"></img></td>';

				print '<td class="nowrap"><a href="implementacion_card.php?id='. $imp->id .'">'. $nombre .'</a></td>';
				
				print '<td class="nowrap">';
				if (!empty($third->nom)) {
					print '<a href="../../societe/card.php?socid='. $third->id .'">'. $third->nom .'</a>';
				} else {
					print 'Ningún tercero vinculado';
				}
				print '</td>';
				
				print '<td class="nowrap"><a href="'. $imp->soci_url .'">'. $imp->soci_url .'</a></td>';

				print '<td class="nowrap">'. $imp->creacion ."</td>";
				
				if ($imp->estado == 0){
					print '<td class="nowrap" style="background-color:rgba(89, 226, 74, 1)">';
					print 'Activo';
				} elseif ($imp->estado == 1){
					print '<td class="nowrap" style="background-color:rgba(210, 100, 100, 1)">';
					print 'Inactivo';
				} elseif ($imp->estado == 2) {
					print '<td class="nowrap" style="background-color:rgba(255, 186, 80, 1)">';
					print 'Período de prueba';
				}
				print '</td>';

				print '</tr>';

				$i++;
			}

			$db->free($resql);
		} else {
			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("None").'</td></tr>';
		}
		print "</table><br>";
	}
} else {
	print "<div>Qué hace acá manín?</div>";
}


print '</div></div></div>';

// End of page
llxFooter();
$db->close();
