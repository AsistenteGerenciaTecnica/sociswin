<?php 
include("db.php");

// Se obtiene el ID del elemento donde estamos

//Se carga los datos al momento que se preciona el boton Grabar

     



//al presionar el boton cancelar se carga la pagina principal
//if para vaidar si es enviado
if (isset($_POST['ingresar'])) {

      $mes=date("m");
			$dia=date("d");
			$query = "SELECT COUNT(*) FROM llx_equipment_history";
			$result = mysqli_fetch_assoc(mysqli_query($conn, $query));
			$num=$result['COUNT(*)']+1;
			

  $ref="HV" . $dia . $mes . "-" . $num;
  $type = utf8_decode($_POST['type']);
  $reference = utf8_decode($_POST['reference']);
  $num_fac = $_POST['num_fac'];
  $orden = $_POST['orden'];
  $cliente = $_POST['cliente'];
  $proveedor = $_POST['proveedor'];
  $dater = $_POST['date_reception'];
  $date = $_POST['date_operation'];
  $date_fac = $_POST['date_facture'];
  $verify_code=$_POST['verify_code'];
  $control_port=$_POST['control_port'];
  $mac_address=$_POST['mac_address'];
  $mark=utf8_decode($_POST['mark']);
  $modelo=utf8_decode($_POST['modelo']);
  $serial=$_POST['serial'];
  $project=$_POST['projet'];
  $nacionalidad=$_POST['nacionalidad'];
  $puerto_web=$_POST['web_port'];
  $catalogo=$_POST['catalogo'];
  $warranty_time=$_POST['warranty_time'];

  $query3 = "INSERT INTO llx_equipment_history ( type, reference,ref,fk_facture ,fk_commande_fournisseur, fk_client,fk_fournisseur,  date_reception, date_operation, date_facture,
  verify_code, control_port,mac_address, mark, modelo, serial, fk_projet, national_type, web_port,category_operation,warranty_time,status, statut) 
  VALUES ('$type','$reference','$ref', '$num_fac','$orden','$cliente', '$proveedor', '$dater', '$date', '$date_fac', '$verify_code', '$control_port',
  '$mac_address','$mark','$modelo','$serial','$projet', '$nacionalidad','$puerto_web','$catalogo','$warranty_time', 1, 0)";

$result3 = mysqli_query($conn, $query3);
if(!$result3) {
  die("Query Failed.");
}

 
  //header("Location: garantiasindex.php");
//  header("Location: hojagarantia.php?id=$id");
}


//Lineas importnates para la correcta visualizacion del modulo
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
      <!-- Hoja de estilos CSS -->
      <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <!-- Se imprimen todos los inputs editables y no editables-->
    <div>
      <!--Se recibe el ID de la hoja de vida del equipo -->
      <form action="i_manual.php" method="POST">
        <!-- Se Imprime la referencia de la hoja de vida-->
        <h3><?php?></h3></br></br>
        <!-- Se imprime si ya fue guardada la referencia del equipo y el input para ser modificada-->
        <label>Referencia </label>
        <input name="reference" type="text" class="form-control" value=""></br></br>
        <!-- Se imprimen el tipo de equipo y el input para ser modificado-->
        <label>Tipo </label>
        <input name="type" type="text" class="form-control" value=""></br></br>
        <!-- Se imprimen el numero de factura y el input para ser modificado-->
        <label>Numero de Factura </label>
        <input name="num_fac" type="text" class="form-control" value="" ></br></br>
        <!-- Se imprimen la orden de compra y el input para ser modificado-->
        <label>Orden de Compra </label>
        <input name="orden" type="text" class="form-control" value="" ></br></br>
        <!-- Se imprimen el cliente y un menu desplegable para ser modificado-->
        <label>Cliente </label>
        <select name="cliente"><?php
          //se consulta en la base de datos todos los nombres de clientes (fournisseur=0)
          $query="SELECT * FROM llx_societe WHERE fournisseur=0"; 
          $result = mysqli_query($conn, $query);
          foreach ($result as $op){
            //Si el id de un cliente  es igual al que ya tenemos registrado en la hoja de vida, se marca seleccionado en el menu desplegable
            if($op['rowid']==$row['fk_client']){?>
              <option selected value="<?php echo $op['rowid']?>"><?php echo utf8_encode($op['nom'])?></option>
            <?php } else { ?>
              <option value="<?php echo $op['rowid']?>"><?php echo utf8_encode($op['nom'])?></option>
            <?php }
          } ?>
        </select></br></br>
        <!-- Se imprimen el proveedor y el input para ser modificado-->
        <label>Proveedor </label>
        <select name="proveedor"><?php
          //se consulta en la base de datos todos los nombres de proveedores (fournisseur=0)
          $query="SELECT * FROM llx_societe WHERE fournisseur=1"; 
          $result = mysqli_query($conn, $query);
          foreach ($result as $op){
            //Si el id de un cliente  es igual al que ya tenemos registrado en la hoja de vida, se marca seleccionado en el menu desplegable
            ?>
              <option selected value="<?php echo $op['rowid']?>"><?php echo utf8_encode($op['nom'])?></option>
           
         
              <?php } ?>
        </select></br></br>
        <!-- Se imprimen la fecha de recepcion y el input para ser modificado-->
        <label>Fecha de Recepción </label>
        <input name="date_reception" type="date" class="form-control" value="" ></br></br>
        <!-- Se imprimen la fecha de operacion del equipo y el input para ser modificado-->
        <label>Fecha de Operación </label>
        <input name="date_operation" type="date" class="form-control" value="" placeholder="aaaa-mm-dd"></br></br>
        <!-- Se imprimen la fecha de Facturación del equipo y el input para ser modificado-->
        <label>Fecha de Facturación </label>
        <input name="date_facture" type="date" class="form-control" value="" placeholder="aaaa-mm-dd"></br></br>
        <!-- Se imprimen el codigo de verificacion y el input para ser modificado-->
        <label>Codigo de Verificación </label>
        <input name="verify_code" type="text" class="form-control" value=""></br></br>
        <!-- Se imprimen el puerto de control y el input para ser modificado-->
        <label>Puerto de Control </label>
        <input name="control_port" type="text" class="form-control" value=""></br></br>
        <!-- Se imprimen la direccion MAC y el input para ser modificado-->
        <label>Mac Address </label>
        <input name="mac_address" type="text" class="form-control" value=""></br></br>
        <!-- Se imprimen la marca del equipo y el input para ser modificado-->
        <label>Marca </label>
        <input name="mark" type="text" class="form-control" value=""></br></br>
        <!-- Se imprimen el modelo del equipo y el input para ser modificado-->
        <label>Modelo</label>
        <input name="modelo" type="text" class="form-control" value=""></br></br>
        <!-- Se imprimen el serial del equipo y el input para ser modificado-->
        <label>Serial </label>
        <input name="serial" type="text" class="form-control" value=""></br></br>
        <!-- Se imprimen el proyecto al que pertenece el equipo y unmenu desplegable para ser modificado-->
        <label>Proyecto</label>
        <select name="projet"><?php
          //se consulta en la base de datos los titulos de los proyectos
          $query="SELECT * FROM llx_projet"; 
          $result = mysqli_query($conn, $query);?>
          <!-- Si no se asigna a ningun proyecto, automaticamente se asigna como proyecto de ventas-->
          <option value="0"><?php echo "ventas";?></option>
          <?php
          foreach ($result as $op){
            //Si el id de un proyecto es igual al que ya tenemos registrado en la hoja de vida, se marca seleccionado en el menu desplegable
            if($op['rowid']==$row['fk_projet']){ ?>
              <option selected value=""><?php echo utf8_encode($op['title'])?></option>
            <?php } else { ?>
              <option value=""><?php echo utf8_encode($op['title'])?></option>
            <?php }
          } ?>
        </select></br></br>
        <!-- Se imprimen el tipo de nacionalidad y el input para ser modificado-->
        <label>Nacionalidad </label>
        <select name="nacionalidad">
              				<option selected value="na">NACIONAL</option>
    				<option value="in">INTERNACIONAL</option>
            				
               </select></br></br>
        <!-- Se imprimen el puerto web y el input para ser modificado-->
        <label>Puerto Web </label>
        <input name="web_port" type="text" class="form-control" value=" "></br></br>
        <!-- Se imprimen el catalogo de manejo SI tiene o NO tiene y el menu desplegable para ser modificado-->
        <label>Catalogo de Manejo </label>
        <select name="catalogo">
              				<option selected value="SI">SI</option>
    				          <option value="NO">NO</option>

               </select></br></br>
        <!-- Se imprimen el tiempo de garantia de fabricante y el menu desplegable para ser modificado-->
        <label>Tiempo de Garantia </label>
        <select name="warranty_time"><?php
          for($i=1; $i<=10; $i++){
            //El menu desplegable de tiempo de garantia sera de 1 a 10 años
            ?>
              <option selected value="<?php echo $i; ?>"><?php echo "$i"." años"; ?></option>
           
              <?php
          } ?>
        </select></br></br></br></br>
        <!-- Boton para guardar lo modificado-->
        <button class="btn btn-secondary" name="ingresar"> Grabar</button>
        <!-- Boton para volver a la hoja de vida del equipo-->
        <button class="btn btn-secondary" name="cancelar"> Anular</button>
      </form>
    </div>
  </body>
</html>
<!-- Footer Esqueleto Dolibarr IMPORTANTE-->
<?php   
echo "ingreso cliente";
llxFooter();
?>