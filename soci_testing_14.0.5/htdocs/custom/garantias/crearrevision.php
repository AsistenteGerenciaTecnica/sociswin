<?php include("db.php"); ?>
<!-- Se definen variables-->
<?php
$id='';
$orden='';
// Se obtiene el ID de la hoja de vida
if  (isset($_GET['id'])) {
  $id = $_GET['id'];
}
//Se carga los datos al momento que se presiona el boton Grabar
if (isset($_POST['update'])) {
  $fecha_actual=date("Y-m-d H:i:s");
  $orden = $_POST['orden'];
  //Se cargan los datos en la variable $query para ser enviada a la base de datos
  //STATUS =1 NO ELIMINADA STATUS=0 ELIMINADA
  //STATUT =1 ABIERTA STATUT=0 CERRADA
  $query = "INSERT INTO llx_revision (date_send, orden, fk_equipment_history, status, statut) 
  VALUES ('$fecha_actual', '$orden', '$id', 1, 1)";
  mysqli_query($conn, $query);//se actualizan en la base de datos
  header("Location: intervenciones.php?id=$id");
}

//al presionar el boton cancelar se carga la pagina principal
if (isset($_POST['cancelar'])) {
  header("Location: intervenciones.php?id=$id");
}
//Lineas importantes para la correcta visualizacion del modulo
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) { $i--; $j--; }
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) $res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) $res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) $res = @include "../main.inc.php";
if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (!$res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

// Load translation files required by the page
$langs->loadLangs(array("garantias@garantias"));

$action = GETPOST('action', 'aZ09');

// Security check
$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0)
{
	$action = '';
	$socid = $user->socid;
}
//HEADER MODULO
llxHeader("", $langs->trans("Area Garantias"));
//TITULO MODULO
print load_fiche_titre($langs->trans("Area Garantias"), '', 'garantias.png@garantias');
?>
<html>
  <head>
      <link rel="stylesheet" href="styles.css">
  </head>
  <body>
      <!-- Se el ID  de la hoja de vida del equipo-->
      <form action="crearrevision.php?id=<?php echo $id?>" method="POST" enctype="multipart/form-data">
        <h4>INTERVENCION INTERNA</h4></br></br>
        <!-- Se imprime el nombre del cliente de la hoja de vida-->
        <label>Nombre Cliente:</label><?php
        //Se consulta el nombre del cliente agregado a la hoja de vida en la base de datos
        $query="SELECT llx_societe.nom as nom FROM llx_societe, llx_equipment_history WHERE llx_societe.rowid=llx_equipment_history.fk_client AND llx_equipment_history.rowid=$id";
        $result = mysqli_fetch_assoc(mysqli_query($conn, $query));?>
        <input type="text" name="cliente" class="form-control" value="<?php echo utf8_encode($result['nom']); ?>" disabled></br></br>
        <!-- Se imprime un menu desplegable con referencia y descripcion de las ordenes de servicio-->
        <label>Orden de Servicio </label>
        <select name="orden"><?php
        //Se consultan las referencias y descripciones de las ordenes de servicio creadas en el modulo de comercio para este cliente
        $query="SELECT llx_fichinter.rowid as rowid, llx_fichinter.ref as ref, llx_fichinter.description as des FROM llx_societe, llx_fichinter, llx_equipment_history WHERE llx_equipment_history.rowid=$id AND llx_societe.rowid=llx_equipment_history.fk_client AND llx_fichinter.fk_soc = llx_societe.rowid AND llx_fichinter.fk_statut!=3"; 
        $result = mysqli_query($conn, $query);
        foreach ($result as $op){?>
            <option value="<?php echo $op['rowid']?>"><?php echo utf8_encode($op['ref']); echo " - "; echo utf8_encode($op['des']); ?></option>
        <?php } ?>
        </select></br></br>
        </br></br>
        <!-- Boton para guardar lo modificado-->
        <button class="btn btn-secondary" name="update">Grabar</button>
        <!-- Boton para regresar a la hoja de vida-->
        <button class="btn btn-secondary" name="cancelar">Anular</button>
      </form>
  </body>
</html>
<!-- Footer Esqueleto Dolibarr IMPORTANTE-->
<?php
llxFooter();
?>