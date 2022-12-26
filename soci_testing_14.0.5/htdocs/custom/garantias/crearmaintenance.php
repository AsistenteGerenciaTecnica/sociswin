<?php include("db.php"); ?>

<!-- Se definen variables-->
<?php
$actividad='';
$frecuencia='';
$destino='';
$id='';

// Se obtiene el ID del elemento donde estamos
if  (isset($_GET['id'])) {
  $id = $_GET['id'];
  // Se extrae la hoja de vida con este ID en la base de datos
  $query = "SELECT * FROM llx_equipment_history WHERE rowid=$id";
  $result = mysqli_query($conn, $query);
  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    //se definen variables con los datos de la hoja de vida extraidos de la base de datos

    $idproduct=$row['fk_product'];
  }
}

//Se carga los datos al momento que se preciona el boton Grabar
if (isset($_POST['update'])) {
  $actividad = utf8_decode($_POST['actividad']);
  $frecuencia = utf8_decode($_POST['frecuencia']);
  $fecha_actual=date("Y-m-d H:i:s");
  /*
  $foto=$_FILES['image']['name'];
  $ruta=$_FILES['image']['tmp_name'];
  $destino="images/".$foto;
  copy($ruta, $destino);
  */
  
  if (!empty($_POST["archivo"])){
    $reporte = null;
    for($x=0; $x<count($_FILES["archivo"]["name"]); $x++){
      $file = $_FILES["archivo"];
      $nombre = $file["name"][$x];
      $tipo = $file["type"][$x];
      $ruta_provisional = $file["tmp_name"][$x];

      if ($tipo != 'image/jpeg' && $tipo != 'image/jpg' && $tipo != 'image/png' && $tipo != 'image/gif' && $tipo != 'image/bmp' && $tipo != 'image/svg' && $tipo != 'image/tiff'){
        $reporte .= "<p style='color: red'>Error $nombre, el archivo no es una imagen.</p>";
      }

      else{
        $destino="images/".$nombre;
        copy($ruta_provisional, $destino);      

        //Codigo para insertar imagenes a tu Base de datos.
        //Sentencia SQL
        //Se cargan los datos en la variable $query para ser enviada a la base de datos
        $query = "INSERT INTO llx_maintenance (date, activities, frecuency, imagen, fk_equipment_history, fk_product, status) 
        VALUES ('$fecha_actual', '$actividad', '$frecuencia', '$destino', '$id', '$idproduct', 1)";
        $result= mysqli_query($conn, $query);//se actualizan en la base de datos
        
        header("Location: procedimientos.php?id=$id");
      }
    }
    //echo $reporte;
  } else {
    //Codigo para insertar imagenes a tu Base de datos.
    //Sentencia SQL
    //Se cargan los datos en la variable $query para ser enviada a la base de datos
    $query = "INSERT INTO llx_maintenance (date, activities, frecuency, fk_equipment_history, fk_product, status) 
    VALUES ('$fecha_actual', '$actividad', '$frecuencia', '$id', '$idproduct', 1)";
    $result= mysqli_query($conn, $query);//se actualizan en la base de datos
    
    header("Location: procedimientos.php?id=$id");
  }
}

//al presionar el boton cancelar se carga la pagina principal
if (isset($_POST['cancelar'])) {
  header("Location: procedimientos.php?id=$id");
}

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

<head>
    <link rel="stylesheet" href="styles.css">
</head>

<div>
    <form action="crearmaintenance.php?id=<?php echo $id?>" method="POST" enctype="multipart/form-data">
    <h4>PROCEDIMIENTO DE MANTENIMIENTO</h4></br></br>
      <label>Actividad</label>
      <textarea name="actividad" rows="6" cols="40" type="text" class="form-control" value=""></textarea></br></br>
      
      <label>Frecuencia </label>
      <input name="frecuencia" type="text" class="form-control" value=""></br></br>

      <label>Imagen</label>
      <input type="file" class="form-control" id="archivo[]" name="archivo[]" multiple="">
     
    </br></br>
      <!-- Boton para guardar lo modificado-->
      <button class="btn btn-secondary" name="update">Grabar</button>
      <button class="btn btn-secondary" name="cancelar">Anular</button>
    </form>
</div>

<!-- Footer Esqueleto Dolibarr IMPORTANTE-->
<?php
llxFooter();
?>