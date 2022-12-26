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
  $type = utf8_decode($_POST['type']); //label
  $type = strtoupper($type);
  $num_fac = ("" != trim($_POST['num_fac']) ? $_POST['num_fac'] : null);
  $num_fac = strtoupper($num_fac);
  $orden = ("" != trim($_POST['orden']) ? $_POST['orden'] : null);
  $cliente = ("" != trim($_POST['cliente']) ? $_POST['cliente'] : null);
  $proveedor = ("" != trim($_POST['proveedor']) ? $_POST['proveedor'] : null);
  $dater = ("" != trim($_POST['date_reception']) ? $_POST['date_reception'] : null);
  $date = ("" != trim($_POST['date_operation']) ? $_POST['date_operation'] : null);
  $date_fac = ("" != trim($_POST['date_facture']) ? $_POST['date_facture'] : null);
  $verify_code = ("" != trim($_POST['verify_code']) ? $_POST['verify_code'] : null);
  $verify_code = strtoupper($verify_code);
  $control_port= ("" != trim($_POST['control_port']) ? $_POST['control_port'] : null);
  $mac_address = ("" != trim($_POST['mac_address']) ? $_POST['mac_address'] : null);
  $mac_address = strtoupper($mac_address);
  $mark = ("" != trim($_POST['mark']) ? utf8_decode($_POST['mark']) : null);
  $mark = strtoupper($mark);
  $modelo = ("" != trim($_POST['modelo']) ? utf8_decode($_POST['modelo']) : null);
  $modelo = strtoupper($modelo);
  $serial = ("" != trim($_POST['serial']) ? $_POST['serial'] : null);
  $serial = strtoupper($serial);
  $project = ("" != trim($_POST['projet']) ? $_POST['projet'] : null);
  $nacionalidad = ("" != trim($_POST['nacionalidad']) ? $_POST['nacionalidad'] : null);
  $puerto_web = ("" != trim($_POST['web_port']) ? $_POST['web_port'] : null);
  $catalogo = ("" != trim($_POST['catalogo']) ? $_POST['catalogo'] : null);
  $warranty_time = ("" != trim($_POST['warranty_time']) ? $_POST['warranty_time'] : null);


  $query = "SELECT * FROM llx_product WHERE llx_product.label= '$type'";
	$result = mysqli_fetch_assoc(mysqli_query($conn, $query));
  $reference = $result['ref'];
//  echo $reference . "<br>";

  $query = "SELECT rowid FROM llx_commande_fournisseur WHERE llx_commande_fournisseur.ref= '$orden'";
	$result = mysqli_fetch_assoc(mysqli_query($conn, $query));
  $orden2 = $result['rowid'];
 // echo $orden . "<br>";
 // echo $orden2 . "<br>";


 
$query3 = "INSERT INTO llx_equipment_history ( type, reference,ref,fk_facture ,fk_commande_fournisseur, fk_client,fk_fournisseur,  date_reception, date_operation, date_facture,
verify_code, control_port,mac_address, mark, modelo, serial, fk_projet, national_type, web_port,category_operation,warranty_time,status, statut) 
VALUES ('$type','$reference','$ref', '$num_fac','$orden2','$cliente', '$proveedor', '$dater', '$date', '$date_fac', '$verify_code', '$control_port',
'$mac_address','$mark','$modelo','$serial','$project', '$nacionalidad','$puerto_web','$catalogo','$warranty_time', 1, 0)";

$result3 = mysqli_query($conn, $query3);
if(!$result3) {
  die("check: $project". mysqli_error($conn));
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
      <!-- jQuery library -->


  </head>
  <body>
    <!-- Se imprimen todos los inputs editables y no editables-->
    <div>
      <!--Se recibe el ID de la hoja de vida del equipo -->
      <form action="i_manual_ultm.php" method="POST">
        <!-- Se Imprime la referencia de la hoja de vida-->
        <h3><?php?></h3></br></br>
        <!-- Se imprime si ya fue guardada la referencia del equipo y el input para ser modificada-->
        <table>
        <tr>
          <td>
        <label>Referencia </label>
        </td>
        <td>
        <!-- Se imprimen el tipo de equipo y el input para ser modificado-->
        <div id="autocomplete">
            <input name="type" type="text" id="city-box" class="form-control" autocomplete="off" required></br></br>
            <div id="cityList"></div>
          </div>
          </td>
        </tr>
          
       
        <!-- Se imprimen el numero de factura y el input para ser modificado-->
        <tr>
          <td>
        <label>Numero de Factura </label>
        </td>
        <td>
        <input name="num_fac" type="text" class="form-control" value="" required></br></br>
        </td>
        </tr>

        <!-- Se imprimen la orden de compra y el input para ser modificado-->
        <tr>
          <td>
        <label>Orden de Compra </label>
        </td>
        <td>
        <div id="autocomplete2">
            <input name="orden" type="text" id="city-box2" class="form-control" autocomplete="off" required></br></br>
            <div id="cityList2"></div>
          </div>
          </td>
        </tr>
        <!-- Se imprimen el cliente y un menu desplegable para ser modificado-->
        <tr>
          <td>
        <label>Cliente </label>
        </td>
        <td>
        <select name="cliente" required><?php
          //se consulta en la base de datos todos los nombres de clientes (fournisseur=0)
          $query="SELECT * FROM llx_societe WHERE fournisseur=0 ORDER BY nom DESC"; 
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
        </td>
        </tr>
        <!-- Se imprimen el proveedor y el input para ser modificado-->
        <tr>
          <td>
        <label>Proveedor </label>
        </td>
        <td>
        <select name="proveedor" required><?php
          //se consulta en la base de datos todos los nombres de proveedores (fournisseur=0)
          $query="SELECT * FROM llx_societe WHERE fournisseur=1 ORDER BY nom DESC"; 
          $result = mysqli_query($conn, $query);
          foreach ($result as $op){
            //Si el id de un cliente  es igual al que ya tenemos registrado en la hoja de vida, se marca seleccionado en el menu desplegable
            ?>
              <option selected value="<?php echo $op['rowid']?>"><?php echo utf8_encode($op['nom'])?></option>   
              <?php } ?>
        </select></br></br>
        </td>
        </tr>
        <!-- Se imprimen la fecha de recepcion y el input para ser modificado-->
        <tr>
          <td>
        <label>Fecha de Recepción </label>
        </td>
        <td>
        <input name="date_reception" type="date" class="form-control" value="" required></br></br>
        </td>
        </tr>
        <!-- Se imprimen la fecha de operacion del equipo y el input para ser modificado-->
        <tr>
          <td>
        <label>Fecha de Operación </label>
        </td>
        <td>
        <input name="date_operation" type="date" class="form-control" value="" placeholder="aaaa-mm-dd"></br></br>
        </td>
        </tr>
        <!-- Se imprimen la fecha de Facturación del equipo y el input para ser modificado-->
        <tr>
          <td>
        <label>Fecha de Facturación </label>
        </td>
        <td>
        <input name="date_facture" type="date" class="form-control" value="" placeholder="aaaa-mm-dd"></br></br>
        </td>
        </tr>
        <!-- Se imprimen el codigo de verificacion y el input para ser modificado-->
        <tr>
          <td>
        <label>Codigo de Verificación </label>
        </td>
        <td>
        <input name="verify_code" type="text" class="form-control" value=""></br></br>
        </td>
        </tr>
        <!-- Se imprimen el puerto de control y el input para ser modificado-->
        <tr>
          <td>
        <label>Puerto de Control </label>
        </td>
        <td>
        <input name="control_port" type="text" class="form-control" value="0"></br></br>
        </td>
        </tr>
        <!-- Se imprimen la direccion MAC y el input para ser modificado-->
        <tr>
          <td>
        <label>Mac Address </label>
        </td>
        <td>
        <input name="mac_address" type="text" class="form-control" value="0"></br></br>
        </td>
        </tr>
        <!-- Se imprimen la marca del equipo y el input para ser modificado-->
        <tr>
          <td>
        <label>Marca </label>
        </td>
        <td>
        <input name="mark" type="text" class="form-control" value=""></br></br>
        </td>
        </tr>
        <!-- Se imprimen el modelo del equipo y el input para ser modificado-->
        <tr>
          <td>
        <label>Modelo</label>
        </td>
        <td>
        <input name="modelo" type="text" class="form-control" value=""></br></br>
        </td>
        </tr>
        <!-- Se imprimen el serial del equipo y el input para ser modificado-->
        <tr>
          <td>
        <label>Serial </label>
        </td>
        <td>
        <input name="serial" type="text" class="form-control" value="0"></br></br>
        </td>
        </tr>
        <!-- Se imprimen el proyecto al que pertenece el equipo y unmenu desplegable para ser modificado-->

        <tr>
          <td>
        <label>Proyecto</label>
        </td>
        <td>
        <select name="projet" required><?php
          //se consulta en la base de datos los titulos de los proyectos
          $query="SELECT * FROM llx_projet ORDER BY ref DESC"; 
          $result = mysqli_query($conn, $query);?>
          <!-- Si no se asigna a ningun proyecto, automaticamente se asigna como proyecto de ventas-->
          <!-- <option value="0"><?php echo "ventas";?></option> -->
          <?php
          foreach ($result as $op){
            //Si el id de un proyecto es igual al que ya tenemos registrado en la hoja de vida, se marca seleccionado en el menu desplegable
            if($op['rowid']==$row['fk_projet']){ ?>
              <option selected value=""><?php echo $op['ref'] . ' - '  . utf8_encode($op['title'])?></option>
            <?php } else { ?>
              <option value=""><?php echo $op['ref'] . ' - ' . utf8_encode($op['title'])?></option>
            <?php }
          } ?>
        </select></br></br>
        </td>
        </tr>
        <!-- Se imprimen el tipo de nacionalidad y el input para ser modificado-->
        <tr>
          <td>
        <label>Nacionalidad </label>
        </td>
        <td>
        <select name="nacionalidad">

              				<option selected value="na">NACIONAL</option>
    				<option value="in">INTERNACIONAL</option>
            				
               </select></br></br>
               </td>
        </tr>
        <!-- Se imprimen el puerto web y el input para ser modificado-->
        <tr>
          <td>
        <label>Puerto Web </label>
        </td>
        <td>
        <input name="web_port" type="text" class="form-control" value=" "></br></br>
        </td>
        </tr>
        <!-- Se imprimen el catalogo de manejo SI tiene o NO tiene y el menu desplegable para ser modificado-->
        <tr>
          <td>
        <label>Catalogo de Manejo </label>
        </td>
        <td>
        <select name="catalogo">
              				<option selected value="SI">SI</option>
    				          <option value="NO">NO</option>

               </select></br></br>
               </td>
        </tr>
        <!-- Se imprimen el tiempo de garantia de fabricante y el menu desplegable para ser modificado-->
        <tr>
          <td>
        <label>Tiempo de Garantia </label>
        </td>
        <td>
        <select name="warranty_time"><?php
          for($i=1; $i<=10; $i++){
            //El menu desplegable de tiempo de garantia sera de 1 a 10 años
            ?>
              <option selected value="<?php echo $i; ?>"><?php echo "$i"." años"; ?></option>
           
              <?php
          } ?>
        </select></br></br>
        </td>
        </tr>
        </table>
        <!-- Boton para guardar lo modificado-->
        <button class="btn btn-secondary" name="ingresar"> Grabar</button>
        <!-- Boton para volver a la hoja de vida del equipo-->
        <button class="btn btn-secondary" name="cancelar"> Anular</button>
      </form>
    </div>
    <script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
  $(document).ready(function(){

    // Autocomplete Textbox
    $("#city-box").keyup(function(){
      var city = $(this).val();

      if(city != ''){
         $.ajax({
            url: "search.php",
            method: "POST",
            data: { city: city},
            success: function(data){
              console.log(data);
              $("#cityList").fadeIn("fast").html(data);
            }
         }); 
      }else{
        $("#cityList").fadeOut();
        $("#table-data").html("");
      }
    });

    // Autocomplete List Click Code
    $(document).on('click','#cityList li',function(){
      $('#city-box').val($(this).text());
      $("#cityList").fadeOut();
    });

       // Autocomplete Textbox2
       $("#city-box2").keyup(function(){
      var city2 = $(this).val();

      if(city2 != ''){
         $.ajax({
            url: "search2.php",
            method: "POST",
            data: { city2: city2},
            success: function(data){
              console.log(data);
              $("#cityList2").fadeIn("fast").html(data);
            }
         }); 
      }else{
        $("#cityList2").fadeOut();
        $("#table-data").html("");
      }
    });

    // Autocomplete List Click Code
    $(document).on('click','#cityList2 li',function(){
      $('#city-box2').val($(this).text());
      $("#cityList2").fadeOut();
    });

    // Search Button Code
    $("#search-btn").on('click', function(e){
      e.preventDefault();

      var city = $('#city-box').val();

      if(city == ""){
        alert("Please enter the city Name.");
        $("#table-data").html("");
      }else{
        $.ajax({
            url: "load-table.php",
            method: "POST",
            data: { city: city},
            success: function(data){
              $("#table-data").html(data);
            }
         }); 
      }
    })
  });
</script>
  </body>
</html>
<!-- Footer Esqueleto Dolibarr IMPORTANTE-->
<?php 

llxFooter();
?>

