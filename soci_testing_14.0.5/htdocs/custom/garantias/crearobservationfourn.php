<?php include("db.php"); ?>
<!-- Se definen variables-->
<?php
ini_set("default_charset", "UTF-8");
mb_internal_encoding('UTF-8');
 
// Esto le dice a PHP que generaremos cadenas UTF-8
mb_http_output('UTF-8');

$observation='';
$id='';
$idh='';
$destino='';

// Se obtiene el ID de la garantia en la que vamos a agregar una observacion
if  (isset($_GET['id'])) {
  $id = $_GET['id'];
}

// Se obtiene el ID de la hoja de vida donde estamos
if  (isset($_GET['idh'])) {
    $idh = $_GET['idh'];
}

//Se carga los datos al momento que se preciona el boton Grabar
if (isset($_POST['update'])) {
    $observation = utf8_decode($_POST['observation']);
    $fecha_actual=date("Y-m-d H:i:s");
    //Se evalua si el array "archivo" no esta vacia
    if (!empty($_FILES["archivo"])){
        $reporte = null;
        //se recorre todas las imagenes que se cargaron en el array archivo
        for($x=0; $x<count($_FILES["archivo"]["name"]); $x++){
            $file = $_FILES["archivo"];
            $nombre = $file["name"][$x];
            $tipo = $file["type"][$x];
            $ruta_provisional = $file["tmp_name"][$x];
            //se evalua si es un tipo de archivo diferente a una imagen
            if ($tipo != 'image/jpeg' && $tipo != 'image/jpg' && $tipo != 'image/png' && $tipo != 'image/gif' && $tipo != 'image/bmp' && $tipo != 'image/svg' && $tipo != 'image/tiff'){
                $reporte .= "<p style='color: red'>Error $nombre, el archivo no es una imagen.</p>";
            } else {
                //Se crea la ruta de destino donde se guardaran las imagenes
                $destino="images/".$nombre;
                //se copia la imagen de la ruta provisional a la ruta destino
                copy($ruta_provisional, $destino);
                //Sentencia SQL
                //Se cargan los datos en la variable $query para ser enviada a la base de datos
                $query = "INSERT INTO llx_observation_fourn (date, observation, imagen, fk_garantia, status) 
                VALUES ('$fecha_actual', '$observation', '$destino', '$id', 1)";
                mysqli_query($conn, $query);//se actualizan en la base de datos
                //Redireccionamos a la hoja de vida que estamos trabajando
                header("Location: enviogarantia.php?id=$idh");
            }
        }
        echo $reporte;
    } else {
        //Sentencia SQL
        //Se cargan los datos en la variable $query para ser enviada a la base de datos
        $query = "INSERT INTO llx_observation_fourn (date, observation, fk_garantia, status) 
        VALUES ('$fecha_actual', '$observation', '$id', 1)";
        mysqli_query($conn, $query);//se actualizan en la base de datos
        //Redireccionamos a la hoja de vida que estamos trabajando
        header("Location: enviogarantia.php?id=$idh");

    }
}
//al presionar el boton cancelar se carga la hoja de vida
if (isset($_POST['cancelar'])) {
    header("Location: enviogarantia.php?id=$idh");
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
//header('Content-Type: text/html; charset=UTF-8');
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <!-- Se el ID  de la hoja de vida y de la intervencion interna del equipo el atributo "enctype" es para subir archivos-->
        <form action="crearobservationfourn.php?id=<?php echo $id?>&idh=<?php echo $idh?>" method="POST" enctype="multipart/form-data">
            <h4>OBSERVACIONES DEL PROVEEDOR</h4></br></br>
            <!-- Se imprime un textarea para escribir la observacion del proveedor-->
            <label>Observacion del Proveedor</label>
            <textarea name="observation" rows="6" cols="40" type="text" class="form-control" value=""></textarea></br></br>
            <!-- Se imprime un input/file con el atributo multiple para subir varias imagenes del procedimiento a realizada-->
            <label>Imagen</label>
            <input type="file" class="form-control" id="archivo[]" name="archivo[]" multiple="">
            </br></br>
            <!-- Boton para guardar lo modificado-->
            <button class="btn btn-secondary" name="update">Grabar</button>
            <!-- Boton para cancelar y regresar a la visualizacion de la hoja ed vida-->
            <button class="btn btn-secondary" name="cancelar">Anular</button>
        </form>
    </body>
</html>
<!-- Footer Esqueleto Dolibarr IMPORTANTE-->
<?php
llxFooter();
?>