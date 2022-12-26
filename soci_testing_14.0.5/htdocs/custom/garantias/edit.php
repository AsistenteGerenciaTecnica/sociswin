<?php 
//se incluye la base de datos
include("db.php");

//consulta a todos los nombres de clientes de la base de datos
$query="SELECT * FROM llx_societe WHERE client>0 ORDER BY nom"; 

$result = mysqli_query($conn, $query);
// creo un array vacio para ir agregando los diferentes elementos (clientes al arreglo)
$arraycl= array();
if($result){
   //while para recorre la consulta
  while($cl = mysqli_fetch_array($result)){
    //codificacion utf8 del campo nombre de la consulta
    $cliente = utf8_encode($cl['nom']);
    //agrego elemento por elemento a el arreglo
    array_push($arraycl, $cliente);
  } 

}         


// Se obtiene el ID del elemento donde estamos
if  (isset($_GET['id'])) {
  $id = $_GET['id'];
  // Se extrae la hoja de vida con este ID en la base de datos
  $query = "SELECT * FROM llx_equipment_history WHERE rowid=$id";
  $result = mysqli_query($conn, $query);
  //Se en la variable $result hay 1 elemento quiere decir que si lo encontro
  //Se procede guardar en la variables creadas los datos de la base de datos
  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    $varcompcliente = $row['fk_client'];
    //se definen variables con los datos de la hoja de vida extraidos de la base de datos
    $proveedor=$row['fk_fournisseur'];
    $cf=$row['fk_commande_fournisseur'];
    //Se extrae las referencias de los campos conociendo el ID
    $query2="SELECT nom FROM llx_societe WHERE rowid=$proveedor";
    $proveedor = mysqli_fetch_assoc(mysqli_query($conn, $query2));
    //se extrae la referencia de la orden de compra
    $query2="SELECT ref FROM llx_commande_fournisseur WHERE rowid=$cf";
    $cf = mysqli_fetch_assoc(mysqli_query($conn, $query2));
  }
}

//Se carga los datos al momento que se preciona el boton Grabar
if (isset($_POST['update'])) {
  $id = $_GET['id'];
  //$referencia = utf8_decode($_POST['reference']);
  //$type = utf8_decode($_POST['type']);
  $num_fac = ("" != trim($_POST['num_fac']) ? "'".$_POST['num_fac']."'" : "NULL");
  $n_cliente = ("" != trim($_POST['cliente']) ? "'".$_POST['cliente']."'" : "NULL");
  $newDateop = ("" != trim($_POST['date_operation']) ? "'".$_POST['date_operation']."'" : "NULL");
  $newDatefc = ("" != trim($_POST['date_facture']) ? "'".$_POST['date_facture']."'" : "NULL");
  $n_project = ("" != trim($_POST['project']) ? "'".$_POST['project']."'" : "NULL");
  $verify_code = ("" != trim($_POST['verify_code']) ? "'".$_POST['verify_code']."'" : "NULL");
  $control_port = ("" != trim($_POST['reference']) ? "'".$_POST['control_port']."'" : "NULL");
  $mac_address = ("" != trim($_POST['mac_address']) ? "'".$_POST['mac_address']."'" : "NULL");
  $mark = ("" != trim($_POST['mark']) ? "'".$_POST['mark']."'" : "NULL");
  $modelo = ("" != trim($_POST['modelo']) ? "'".utf8_decode($_POST['modelo'])."'" : "NULL");
  $serial = ("" != trim($_POST['serial']) ? "'".$_POST['serial']."'" : "NULL");
  $puerto_web = ("" != trim($_POST['web_port']) ? "'".$_POST['web_port']."'" : "NULL");
  $categoria_operacion = ("" != trim($_POST['category']) ? "'".$_POST['category']."'" : "NULL");
  $warranty_time = ("" != trim($_POST['warranty_time']) ? "'".$_POST['warranty_time']."'" : "NULL");
  
/*impresiones de prueba
  echo gettype($newDateop);
  echo "<br>";
  echo gettype($newDatefc);
  echo "<br>";
*/
$date = $newDateop;
$date_fac = $newDatefc;
$varfechaop = strlen($date);
$varfechafc = strlen($date_fac);

/* impresiones de prueba
echo "<br>";
echo $date;
echo "<br>";
echo $varfechaop;
echo "<br>";
echo $date_fac;
echo "<br>";
echo $varfechafc;
 */
  //consulta para extraer id de cliente

  //$querync="SELECT llx_societe.rowid as idc, llx_projet.rowid as idp FROM llx_projet, llx_societe WHERE llx_societe.rowid=llx_projet.fk_soc AND llx_societe.nom = '$n_cliente' AND llx_projet.title='$n_project' " ; 

  $querync="SELECT llx_societe.rowid as idc FROM llx_commande, llx_societe WHERE llx_societe.rowid=llx_commande.fk_soc AND llx_societe.nom = $n_cliente" ; 
//$querync="SELECT rowid FROM llx_societe,  WHERE nom='$n_cliente'";
  $consulta = mysqli_fetch_assoc(mysqli_query($conn, $querync));
  
  $cliente = $consulta['idc'];
  
  //$project = $consulta['idp'];

  $querynp="SELECT llx_projet.rowid as idp FROM llx_projet, llx_societe WHERE llx_societe.rowid=llx_projet.fk_soc AND llx_societe.nom = $n_cliente AND llx_projet.title=$n_project " ; 
  $consulta2 = mysqli_fetch_assoc(mysqli_query($conn, $querynp));
  if($consulta2>0){
  $project = $consulta2['idp'];
  }else{
    $project =0;
  }
/*
  echo "<br>";
  echo "cliente ".$cliente;
  echo "<br>";
  echo "proyecto ".$project;
  echo "<br>";
echo $cliente['idc'];   
echo "<br>";
echo "update";
echo "<br>";
echo $n_cliente;
echo "<br>";
echo " id  ".  $id;
echo "<br>";
echo $cliente['idp'];   
echo "<br>";
echo $n_project;
*/

  //Se cargan los datos en la variable $query para ser enviada a la base de datos
//if($varfechaop>0 && $varfechafc>0){
   //inicio if
   //echo "en if 1";
        $query = "UPDATE llx_equipment_history set statut='1', fk_facture=$num_fac, fk_client=$cliente, 
        verify_code=$verify_code, control_port=$control_port, mac_address=$mac_address, fk_projet=$project, date_operation=$newDateop, 
        mark=$mark, modelo=$modelo, serial=$serial, web_port=$puerto_web, category_operation=$categoria_operacion, 
        warranty_time=$warranty_time, date_facture=$newDatefc WHERE rowid=$id ";

      // mysqli_query($conn, $query);//se actualiza en la base de datos
          if(mysqli_query($conn,$query)){

              
            echo ("<script LANGUAGE='JavaScript'>
            window.alert('Registro actualizado.');
            window.location.href='garantiasindex.php';
            </script>");

          } else {
              echo "ERROR: No se ejecuto $query. " . mysqli_error($conn);
          }
/* } else if($varfechaop>0 && $varfechafc==0){   // else if 1
      //echo "en else if 1";

      $query = "UPDATE llx_equipment_history set statut='1', fk_facture='$num_fac', fk_client='$cliente', 
      verify_code='$verify_code', control_port='$control_port', mac_address='$mac_address', fk_projet='$project', date_operation='$date',
      mark='$mark', modelo='$modelo', serial='$serial', web_port='$puerto_web', category_operation='$categoria_operacion', 
      warranty_time='$warranty_time' WHERE rowid='$id' ";

          // mysqli_query($conn, $query);//se actualiza en la base de datos
          if(mysqli_query($conn,$query)){

              
            echo ("<script LANGUAGE='JavaScript'>
            window.alert('Registro actualizado.');
            window.location.href='garantiasindex.php';
            </script>");

          } else {
              echo "ERROR: No se ejecuto $query. " . mysqli_error($conn);
          }

} else if($varfechaop==0 && $varfechafc>0){  // else if 2
  //echo "en else if 2";

            $query = "UPDATE llx_equipment_history set statut='1', fk_facture='$num_fac', fk_client='$cliente', 
            verify_code='$verify_code', control_port='$control_port', mac_address='$mac_address', fk_projet='$project', 
            mark='$mark', modelo='$modelo', serial='$serial', web_port='$puerto_web', category_operation='$categoria_operacion', 
            warranty_time='$warranty_time', date_facture='$date_fac' WHERE rowid='$id' ";

          // mysqli_query($conn, $query);//se actualiza en la base de datos
          if(mysqli_query($conn,$query)){

              echo ("<script LANGUAGE='JavaScript'>
              window.alert('Registro actualizado.');
              window.location.href='garantiasindex.php';
              </script>");  

          } else {
              echo "ERROR: No se ejecuto $query. " . mysqli_error($conn);
          }

}//fin else if 2


else if($varfechaop==0 && $varfechafc==0){  // else if 3
  //echo "en else if 3";

            $query = "UPDATE llx_equipment_history set statut='1', fk_facture='$num_fac', fk_client='$cliente', 
            verify_code='$verify_code', control_port='$control_port', mac_address='$mac_address', fk_projet='$project', 
            mark='$mark', modelo='$modelo', serial='$serial', web_port='$puerto_web', category_operation='$categoria_operacion', 
            warranty_time='$warranty_time' WHERE rowid='$id' ";

          // mysqli_query($conn, $query);//se actualiza en la base de datos
          if(mysqli_query($conn,$query)){

                echo ("<script LANGUAGE='JavaScript'>
                window.alert('Registro actualizado.');
                window.location.href='garantiasindex.php';
                </script>");

              } else {
                  echo "ERROR: No se ejecuto $query. " . mysqli_error($conn);
              }

}//fin else if 3 */



}

//al presionar el boton cancelar se carga la pagina principal
if (isset($_POST['cancelar'])) {
  echo "cancelar";
  header("Location: hojagarantia.php?id=$id");
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
           
      <link rel="stylesheet"  href="js/jquery-ui.css" >
      <script type="application/javascript" src="js/jquery-ui.js" ></script>
      <link rel="stylesheet" href="styles.css">
      
      </head>
  <body>
    <!-- Se imprimen todos los inputs editables y no editables-->
    <div>
      <!--Se recibe el ID de la hoja de vida del equipo -->
      <form action="edit.php?id=<?php echo $_GET['id']; ?>" method="POST">
        <!-- Se Imprime la referencia de la hoja de vida-->
        <h3><?php echo $row['ref']; ?></h3></br></br>
        <!-- Se imprime si ya fue guardada la referencia del equipo y el input para ser modificada-->
        <label>Referencia </label>
        <input style="text-transform:uppercase" id="reference" name="reference" type="text" class="form-control" value="<?php echo utf8_encode($row['reference']); ?>"  disabled></br></br>
        <!-- Se imprimen el tipo de equipo y el input para ser modificado-->
        <label>Tipo </label>
        <input style="text-transform:uppercase"  name="type" type="text" class="form-control" value="<?php echo utf8_encode($row['type']); ?>"  disabled></br></br>
        <!-- Se imprimen el numero de factura y el input para ser modificado-->
        <label >Numero de Factura </label>
        <input style="text-transform:uppercase" name="num_fac" type="text" class="form-control" value="<?php echo $row['fk_facture']; ?>" ></br></br>
        <!-- Se imprimen la orden de compra y el input para ser modificado-->
        <label>Orden de Compra </label>
        <input style="text-transform:uppercase;" name="orden" type="text" class="form-control" value="<?php echo $cf['ref']; ?>" disabled></br></br>
        <!-- Se imprimen el cliente y un menu desplegable para ser modificado-->
        <label>Cliente </label>
        <?php 
        $query="SELECT llx_societe.nom as nombre FROM llx_societe, llx_equipment_history WHERE llx_societe.client>0 AND llx_societe.rowid=llx_equipment_history.fk_client and llx_equipment_history.fk_client='$varcompcliente' "; 
        $result = mysqli_fetch_assoc(mysqli_query($conn, $query));
      
  
         $nomc = $result['nombre'];


          //Si el id de un cliente  es igual al que ya tenemos registrado en la hoja de vida, se marca seleccionado en el menu desplegable
          if($result){?>
        <input style="text-transform:uppercase;" type="text" id="cliente" name="cliente" value="<?php echo $nomc; ?>">
        <?php
          }
          else{
            ?>
            <input style="text-transform:uppercase;" type="text" id="cliente" name="cliente" required >
            <?php
          }

            ?>
            <br><br>
        <!-- Se imprimen el proveedor y el input para ser modificado-->
        <label>Proveedor </label>
        <input style="text-transform:uppercase;" name="proveedor" type="text" class="form-control" value="<?php echo utf8_encode($proveedor['nom']); ?>" disabled></br></br>
        <!-- Se imprimen la fecha de recepcion y el input para ser modificado-->
        <label>Fecha de Recepción </label>
        <input  name="date_reception" type="text" class="form-control" value="<?php echo $row['date_reception']; ?>" disabled></br></br>
        <!-- Se imprimen la fecha de operacion del equipo y el input para ser modificado-->
        <label>Fecha de Operación </label>
        <input  name="date_operation" type="date" class="form-control" value="<?php echo $row['date_operation']; ?>" placeholder="aaaa-mm-dd"></br></br>
        <!-- Se imprimen la fecha de Facturación del equipo y el input para ser modificado-->
        <label>Fecha de Facturación </label>
        <input  name="date_facture" type="date" class="form-control" value="<?php echo $row['date_facture']; ?>" placeholder="aaaa-mm-dd"></br></br>
        <!-- Se imprimen el codigo de verificacion y el input para ser modificado-->
        <label>Codigo de Verificación </label>
        <input style="text-transform:uppercase;" name="verify_code" type="text" class="form-control" value="<?php echo $row['verify_code']; ?>"></br></br>
        <!-- Se imprimen el puerto de control y el input para ser modificado-->
        <label>Puerto de Control </label>
        <input style="text-transform:uppercase;" name="control_port" type="text" class="form-control" value="<?php echo $row['control_port']; ?>"></br></br>
        <!-- Se imprimen la direccion MAC y el input para ser modificado-->
        <label>Mac Address </label>
        <input style="text-transform:uppercase;" name="mac_address" type="text" class="form-control" value="<?php echo $row['mac_address']; ?>"></br></br>
        <!-- Se imprimen la marca del equipo y el input para ser modificado-->
        <label>Marca </label>
        <input style="text-transform:uppercase;" name="mark" type="text" class="form-control" value="<?php echo utf8_encode($row['mark']); ?>"></br></br>
        <!-- Se imprimen el modelo del equipo y el input para ser modificado-->
        <label>Modelo</label>
        <input style="text-transform:uppercase;" name="modelo" type="text" class="form-control" value="<?php echo utf8_encode($row['modelo']); ?>"></br></br>
        <!-- Se imprimen el serial del equipo y el input para ser modificado-->
        <label>Serial </label>
        <input style="text-transform:uppercase;" name="serial" type="text" class="form-control" value="<?php echo $row['serial']; ?>"></br></br>

        <!-- Se imprimen el proyecto al que pertenece el equipo y unmenu desplegable para ser modificado-->
        <label>Proyecto</label>

        <?php 
        $query="SELECT llx_projet.title as titulo FROM llx_projet, llx_equipment_history WHERE llx_projet.rowid=llx_equipment_history.fk_projet and llx_equipment_history.fk_client='$varcompcliente' "; 
        $result = mysqli_fetch_assoc(mysqli_query($conn, $query));
      
  
         $titulop = $result['titulo'];


          //Si el id de un cliente  es igual al que ya tenemos registrado en la hoja de vida, se marca seleccionado en el menu desplegable
          if($result){?>
        <input style="text-transform:uppercase;" type="text" id="project" name="project" value="<?php echo $titulop; ?>">
        <?php
          }
          else{
            ?>
            <input style="text-transform:uppercase;" type="text" id="project" name="project">
            <?php
          }

            ?>
 
        <br><br>

        <!-- Se imprimen el tipo de nacionalidad y el input para ser modificado-->
        <label>Tipo de Nacionalidad </label>
        <input style="text-transform:uppercase;" name="national_type" type="text" class="form-control" value="<?php if($row['national_type']==0){echo "Nacional";}else{ echo"Internacional";} ?>" disabled></br></br>
        <!-- Se imprimen el puerto web y el input para ser modificado-->
        <label>Puerto Web </label>
        <input style="text-transform:uppercase;" name="web_port" type="text" class="form-control" value="<?php echo $row['web_port']; ?>"></br></br>
        <!-- Se imprimen el catalogo de manejo SI tiene o NO tiene y el menu desplegable para ser modificado-->
        <label>Catalogo de Manejo </label>
        <select name="category"><?php
          if($row['category_operation']=="SI"){ ?>
    				<option selected value="SI">SI</option>
    				<option value="NO">NO</option>
          <?php } else { ?>
    				<option value="SI">SI</option>
    				<option selected value="NO">NO</option>
          <?php } ?>
        </select></br></br>
        <!-- Se imprimen el tiempo de garantia de fabricante y el menu desplegable para ser modificado-->
        <label>Tiempo de Garantia </label>
        <select name="warranty_time"><?php
          for($i=1; $i<=10; $i++){
            //El menu desplegable de tiempo de garantia sera de 1 a 10 años
            if($row['warranty_time']==$i){ ?>
              <option selected value="<?php echo $i; ?>"><?php echo "$i"." AÑOS"; ?></option>
            <?php } else { ?>
              <option value="<?php echo $i; ?>"><?php echo "$i"." AÑOS" ?></option>
            <?php }
          } ?>
        </select></br></br></br></br>
        <!-- Boton para guardar lo modificado-->
        <button class="btn btn-secondary" name="update"> Grabar</button>
        <!-- Boton para volver a la hoja de vida del equipo-->
        <button class="btn btn-secondary" name="cancelar"> Anular</button>
      </form>
    </div>
  </body>
</html>
<!-- Footer Esqueleto Dolibarr IMPORTANTE-->
<?php   
llxFooter();
?>
<!-- funcion javascript para campos cliente y proyecto -->
<script type="application/javascript">
            //crea la funcion inicial, inicia con el primer input, que es el de cliente
            $(document).ready(function (){
              //crea la variable y le asigna el json_encode del arreglo creado en la funcion de consulta a clientes. linea 2. ojo, abre y cierra php
              var itemscl = <?=  json_encode($arraycl) ?>
              //a el campo de con id cliente, le genera la funcion autocomplete para que al momento de introducir por teclado texto, vaya filtrando y aparezcan el listado de clientes
              $("#cliente").autocomplete({
                //crea desde la variable itemscl los datos para el autocomplete
              source: itemscl,
              //crea la funcion para poder interconectar los proyectos que solo correspondan a el cliente seleccionado
              select: function (event, item){
                //asigna a params el valor del campo #cliente seleccionado anteriormente
                var params = {
                    cliente: item.item.value
                };
                //envia por el metodo get la variable params, para hacer la consulta de clientes y proyectos
                $.get("getProyecto.php", params, function(response){
                    //console para verificar como esta llegando el array
                    console.log(response);
                    //variable que asigna el arreglo que llega desde el archivo php. linea 244
                    var json = JSON.parse(response);
                    //for para recorrer todo el arreglo, verificar como llegan los datos
                    for(let i=0; i < json.length; i+=1){
                      //console para verificar el contenido de cada valor en la posicion
                      console.log(json[i]);

                    }
                    //a el campo #project del formulario, se le genera la funcion autocomplete para que al introducir valores por teclado vaya filtrando
                    $("#project").autocomplete({
                      source: json,
                    });

            
                });
              }
              });

            });

</script>

