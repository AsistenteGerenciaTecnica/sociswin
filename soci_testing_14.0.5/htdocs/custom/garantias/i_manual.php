<?php 
//se incluye la base de datos
include("db.php");



############################################################################################
//consulta a todos los nombres de productos de la base de datos
$query="SELECT ref FROM llx_product ORDER BY ref"; 

$resultref = mysqli_query($conn, $query);
// creo un array vacio para ir agregando los diferentes elementos (clientes al arreglo)
$arrayref= array();
if($resultref){
   //while para recorre la consulta
  while($ref = mysqli_fetch_array($resultref)){
    //codificacion utf8 del campo nombre de la consulta
    $referencia = utf8_encode($ref['ref']);
    //agrego elemento por elemento a el arreglo
    array_push($arrayref, $referencia);
  } 

}         


############################################################################################
//consulta a todos los nombres de clientes de la base de datos
$query="SELECT nom FROM llx_societe WHERE client>0 ORDER BY nom"; 

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
############################################################################################

//consulta todos los nombres de ordenes de cmompra de la base de datos
$queryoc="SELECT ref FROM llx_commande_fournisseur ORDER BY ref"; 

$resultoc = mysqli_query($conn, $queryoc);
// creo un array vacio para ir agregando los diferentes elementos (clientes al arreglo)
$arrayod= array();
if($resultoc){
   //while para recorre la consulta
  while($oc = mysqli_fetch_array($resultoc)){
    //codificacion utf8 del campo nombre de la consulta
    $ordencompra = utf8_encode($oc['ref']);
    //agrego elemento por elemento a el arreglo
    array_push($arrayod, $ordencompra);
  } 

}         
############################################################################################

//consulta a todos los nombres de proveedores de la base de datos
$query="SELECT nom FROM llx_societe WHERE fournisseur=1 ORDER BY nom"; 

$result = mysqli_query($conn, $query);
// creo un array vacio para ir agregando los diferentes elementos (clientes al arreglo)
$arraypr= array();
if($result){
   //while para recorre la consulta
  while($pr = mysqli_fetch_array($result)){
    //codificacion utf8 del campo nombre de la consulta
    $proveedorcons = utf8_encode($pr['nom']);
    //agrego elemento por elemento a el arreglo
    array_push($arraypr, $proveedorcons);
  } 

}         
############################################################################################

//consulta a todos los nombres de proveedores de la base de datos
$query="SELECT nom FROM llx_societe WHERE fournisseur=1 ORDER BY nom"; 

$result = mysqli_query($conn, $query);
// creo un array vacio para ir agregando los diferentes elementos (clientes al arreglo)
$arraypr= array();
if($result){
   //while para recorre la consulta
  while($pr = mysqli_fetch_array($result)){
    //codificacion utf8 del campo nombre de la consulta
    $proveedorcons = utf8_encode($pr['nom']);
    //agrego elemento por elemento a el arreglo
    array_push($arraypr, $proveedorcons);
  } 

}         
############################################################################################


//Se carga los datos al momento que se preciona el boton Grabar
if (isset($_POST['ingresar'])) {

  //referencia del equipo
  $referencia = ("" != trim($_POST['reference']) ? "'".$_POST['reference']."'" : "NULL");
  //tipo - label del equipo
  $label = ("" != trim($_POST['type']) ? "'".$_POST['type']."'" : "NULL");
  //numero de factura
  $num_fac = ("" != trim($_POST['num_fac']) ? "'".$_POST['num_fac']."'" : "NULL");
  // numero de orden de compra
  $order = ("" != trim($_POST['orden']) ? "'".$_POST['orden']."'" : "NULL");
  //nombre de cliente
  $n_cliente = ("" != trim($_POST['cliente']) ? "'".$_POST['cliente']."'" : "NULL");
  //nombre de proveedor
  $supplier = ("" != trim($_POST['proveedor']) ? "'".$_POST['proveedor']."'" : "NULL");
  //fecha de recepcion
  $fecha_recepcion = ("" != trim($_POST['date_reception']) ? "'".$_POST['date_reception']."'" : "NULL");
  //fecha de operacion
  $newDateop = ("" != trim($_POST['date_operation']) ? "'".$_POST['date_operation']."'" : "NULL");
  // fecha de facturacion
  $newDatefc = ("" != trim($_POST['date_facture']) ? "'".$_POST['date_facture']."'" : "NULL");

  $verify_code=("" != trim($_POST['verify_code']) ? "'".$_POST['verify_code']."'" : "NULL");

  $control_port= ("" != trim($_POST['control_port']) ? "'".$_POST['control_port']."'" : "NULL");

  $mac_address=("" != trim($_POST['mac_address']) ? "'".$_POST['mac_address']."'" : "NULL");

  $mark=("" != trim($_POST['mark']) ? "'".$_POST['mark']."'" : "NULL");

  $modelo=("" != trim($_POST['modelo']) ? "'".$_POST['modelo']."'" : "NULL");

  $serial=("" != trim($_POST['serial']) ? "'".$_POST['serial']."'" : "NULL");

// nombre de proyecto
  $n_project=("" != trim($_POST['proyecto']) ? "'".$_POST['proyecto']."'" : "NULL");

  $nacionalidad=("" != trim($_POST['nacionalidad']) ? "'".$_POST['nacionalidad']."'" : "NULL");
  
  $puerto_web=("" != trim($_POST['web_port']) ? "'".$_POST['web_port']."'" : "NULL");
//catalogo de manejo
  $categoria_operacion=("" != trim($_POST['category']) ? "'".$_POST['category']."'" : "NULL");

  $warranty_time=("" != trim($_POST['warranty_time']) ? "'".$_POST['warranty_time']."'" : "NULL");


 /* echo gettype($newDateop);
  echo "<br>";
  echo gettype($newDatefc);
  echo "<br>";
*/
$date = $newDateop;
$date_fac = $newDatefc;
$varfechaop = strlen($date);
$varfechafc = strlen($date_fac);
/*
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


################################################################################################################################################################
  //consulta id de ORDEN DE COMPRA
  $query="SELECT rowid FROM llx_commande_fournisseur WHERE  ref=$order "; 
//$querync="SELECT rowid FROM llx_societe,  WHERE nom='$n_cliente'";
  $consulta = mysqli_fetch_assoc(mysqli_query($conn, $query));
  $fk_commande_fournisseur = "'".$consulta['rowid']."'";
  

################################################################################################################################################################
  //consulta id de productp
  $query="SELECT rowid FROM llx_product WHERE ref=$referencia "; 
//$querync="SELECT rowid FROM llx_societe,  WHERE nom='$n_cliente'";
  $consulta = mysqli_fetch_assoc(mysqli_query($conn, $query));
  $idproduct = "'".$consulta['rowid']."'";

################################################################################################################################################################
//consulta id de cliente
  $querync="SELECT llx_societe.rowid as idc FROM llx_commande, llx_societe WHERE llx_societe.rowid=llx_commande.fk_soc AND llx_societe.nom = $n_cliente" ; 
//$querync="SELECT rowid FROM llx_societe,  WHERE nom='$n_cliente'";
  $consulta = mysqli_fetch_assoc(mysqli_query($conn, $querync));
  $idcliente = "'".$consulta['idc']."'";



################################################################################################################################################################
//consulta id de proveedor
  $querync="SELECT llx_societe.rowid as idproveedor FROM llx_commande_fournisseur, llx_societe WHERE llx_societe.rowid=llx_commande_fournisseur.fk_soc AND llx_societe.nom = $supplier" ; 
//$querync="SELECT rowid FROM llx_societe,  WHERE nom='$n_cliente'";
  $consulta = mysqli_fetch_assoc(mysqli_query($conn, $querync));
  $idproveedor = "'".$consulta['idproveedor']."'";
  
  //$project = $consulta['idp'];
  
################################################################################################################################################################
//consulta id de proyecto

  $querynp="SELECT llx_projet.rowid as idp FROM llx_projet, llx_societe WHERE llx_societe.rowid=llx_projet.fk_soc AND llx_societe.nom = $n_cliente AND llx_projet.title=$n_project" ; 
  $consulta2 = mysqli_fetch_assoc(mysqli_query($conn, $querynp));
  if($consulta2>0){
  $idproject = "'".$consulta2['idp']."'";
  }else{
    $idproject ="'0'";
  }


################################################################################################################################################################
//consulta cantidad de hojas de vida + para la creacion de la nueva

  $mes=date("m");
			$dia=date("d");
			$query = "SELECT COUNT(*) FROM llx_equipment_history";
			$result = mysqli_fetch_assoc(mysqli_query($conn, $query));
			//echo "select 5";
			$num=$result['COUNT(*)']+1;
			$ref="'HV" . $dia . $mes . "-" . $num . "'";

################################################################################################################################################################
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
  $check = $newDateop;

  $query3 = "INSERT INTO llx_equipment_history (type, reference, ref, fk_facture, fk_commande_fournisseur, fk_client,fk_fournisseur, date_reception, date_operation, date_facture,
  verify_code, control_port, mac_address, mark, modelo,  serial,  fk_projet, national_type, web_port, category_operation, fk_product, warranty_time, status, statut)
  VALUES($label, $referencia, $ref, $num_fac, $fk_commande_fournisseur, $idcliente, $idproveedor, $fecha_recepcion, $newDateop, $newDatefc, $verify_code,
    $control_port, $mac_address, $mark, $modelo, $serial, $idproject, $nacionalidad, $puerto_web, $categoria_operacion, $idproduct, $warranty_time, 1, 0)";

			
   $result3 = mysqli_query($conn, $query3);
   if(!$result3) {
   //die("error al ingresar." . mysqli_error());
   die("check1: $check" . $query3 . mysqli_error($conn));
     }
     echo ("<script LANGUAGE='JavaScript'>
            window.alert('Registro Exitoso.');
            window.location.href='garantiasindex.php';
            </script>");


  //Se cargan los datos en la variable $query para ser enviada a la base de datos
/* if($varfechaop>0 && $varfechafc>0){
   //inicio if
   //echo "en if 1";


   $query3 = "INSERT INTO llx_equipment_history (type, reference, ref, fk_facture, fk_commande_fournisseur, fk_client,fk_fournisseur, date_reception, date_operation, date_facture,
   verify_code, control_port, mac_address, mark, modelo,  serial,  fk_projet, national_type, web_port, category_operation, fk_product, warranty_time, status, statut)
  VALUES($label, $referencia, $ref, $num_fac, $fk_commande_fournisseur, $idcliente, $idproveedor, $fecha_recepcion, $date, $date_fac, $verify_code,
    $control_port, $mac_address, $mark, $modelo, $serial, $idproject, $nacionalidad, $puerto_web, $categoria_operacion, $idproduct, $warranty_time, 1, 0)";

			
   $result3 = mysqli_query($conn, $query3);
   if(!$result3) {
   //die("error al ingresar." . mysqli_error());
   die("check1: $check" . mysqli_error($conn));
     }
     echo ("<script LANGUAGE='JavaScript'>
            window.alert('Registro Exitoso.');
            window.location.href='garantiasindex.php';
            </script>");

} else if($varfechaop>0 && $varfechafc==0){   // else if 1
      //echo "en else if 1";

     
      $query3 = "INSERT INTO llx_equipment_history (type, reference, ref, fk_facture, fk_commande_fournisseur, fk_client,fk_fournisseur, date_reception, date_operation,
      verify_code, control_port, mac_address, mark, modelo,  serial,  fk_projet, national_type, web_port, category_operation, fk_product, warranty_time, status, statut)
     VALUES($label, $referencia, $ref, $num_fac, $fk_commande_fournisseur, $idcliente, $idproveedor, $fecha_recepcion, $date, $verify_code,
       $control_port, $mac_address, $mark, $modelo, $serial, $idproject, $nacionalidad, $puerto_web, $categoria_operacion, $idproduct, $warranty_time, 1, 0)";
   
         
      $result3 = mysqli_query($conn, $query3);
      if(!$result3) {
      //die("error al ingresar." . mysqli_error());
      die("check2: $check" . mysqli_error($conn));
        }
        echo ("<script LANGUAGE='JavaScript'>
        window.alert('Registro Exitoso.');
        window.location.href='garantiasindex.php';
        </script>");


} else if($varfechaop==0 && $varfechafc>0){  // else if 2
  //echo "en else if 2";

           
  $query3 = "INSERT INTO llx_equipment_history (type, reference, ref, fk_facture, fk_commande_fournisseur, fk_client,fk_fournisseur, date_reception,  date_facture,
  verify_code, control_port, mac_address, mark, modelo,  serial,  fk_projet, national_type, web_port, category_operation, fk_product, warranty_time, status, statut)
 VALUES($label, $referencia, $ref, $num_fac, $fk_commande_fournisseur, $idcliente, $idproveedor, $fecha_recepcion, $date_fac, $verify_code,
   $control_port, $mac_address, $mark, $modelo, $serial, $idproject, $nacionalidad, $puerto_web, $categoria_operacion, $idproduct, $warranty_time, 1, 0)";

     
        $result3 = mysqli_query($conn, $query3);
        if(!$result3) {
        //die("error al ingresar." . mysqli_error());
        die(mysqli_error($conn));
          }

    echo ("<script LANGUAGE='JavaScript'>
            window.alert('Registro Exitoso.');
            window.location.href='garantiasindex.php';
            </script>");




}//fin else if 2


else if($varfechaop==0 && $varfechafc==0){  // else if 3
  //echo "en else if 3";

  $query3 = "INSERT INTO llx_equipment_history (type, reference, ref, fk_facture, fk_commande_fournisseur, fk_client,fk_fournisseur, date_reception,
  verify_code, control_port, mac_address, mark, modelo,  serial,  fk_projet, national_type, web_port, category_operation, fk_product, warranty_time, status, statut)
 VALUES($label, $referencia, $ref, $num_fac, $fk_commande_fournisseur, $idcliente, $idproveedor, $fecha_recepcion, $verify_code,
   $control_port, $mac_address, $mark, $modelo, $serial, $idproject, $nacionalidad, $puerto_web, $categoria_operacion, $idproduct, $warranty_time, 1, 0)";

     
  $result3 = mysqli_query($conn, $query3);
  if(!$result3) {
  //die("error al ingresar." . mysqli_error());
  die("check4: $check" .mysqli_error($conn));
    }

    echo ("<script LANGUAGE='JavaScript'>
            window.alert('Registro Exitoso.');
            window.location.href='garantiasindex.php';
            </script>");



}//fin else if 3*/



}

//al presionar el boton cancelar se carga la pagina principal
if (isset($_POST['cancelar'])) {
  //echo "cancelar";
  header("Location: garantiasindex.php");
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
      <form action="i_manual.php" method="POST">
        <!-- Se Imprime la referencia de la hoja de vida-->
      
        <!-- Se imprime si ya fue guardada la referencia del equipo y el input para ser modificada-->
        <label>Referencia </label>
        <input style="text-transform:uppercase" id="reference" name="reference" type="text" class="form-control" value=""  required></br></br>
        <!-- Se imprimen el tipo de equipo y el input para ser modificado-->
        <label>Tipo </label>
        <input style="text-transform:uppercase"  id="type" name="type" type="text" class="form-control" value=""  required></br></br>
        <!-- Se imprimen el numero de factura y el input para ser modificado-->
        <label >Numero de Factura </label>
        <input style="text-transform:uppercase" name="num_fac" type="text" class="form-control" value="" ></br></br>
        <!-- Se imprimen la orden de compra y el input para ser modificado-->
        <label>Orden de Compra </label>
        <input style="text-transform:uppercase;" id="orden" name="orden" type="text" class="form-control" value="" required></br></br>
        <!-- Se imprimen el cliente y un menu desplegable para ser modificado-->
        <label>Cliente </label>
        
            <input style="text-transform:uppercase;" type="text" id="cliente" name="cliente" required >
            <br><br>
        <!-- Se imprimen el proveedor y el input para ser modificado-->
        <label>Proveedor </label>
        <input style="text-transform:uppercase;" id="proveedor" name="proveedor" type="text" class="form-control" value="" required></br></br>
        <!-- Se imprimen la fecha de recepcion y el input para ser modificado-->
        <label>Fecha de Recepción </label>
        <input  name="date_reception" type="date" class="form-control" value="" required></br></br>
        <!-- Se imprimen la fecha de operacion del equipo y el input para ser modificado-->
        <label>Fecha de Operación </label>
        <input  name="date_operation" type="date" class="form-control" value="" placeholder="aaaa-mm-dd"></br></br>
        <!-- Se imprimen la fecha de Facturación del equipo y el input para ser modificado-->
        <label>Fecha de Facturación </label>
        <input  name="date_facture" type="date" class="form-control" value="" placeholder="aaaa-mm-dd"></br></br>
        <!-- Se imprimen el codigo de verificacion y el input para ser modificado-->
        <label>Codigo de Verificación </label>
        <input style="text-transform:uppercase;" name="verify_code" type="text" class="form-control" value=""></br></br>
        <!-- Se imprimen el puerto de control y el input para ser modificado-->
        <label>Puerto de Control </label>
        <input style="text-transform:uppercase;" name="control_port" type="text" class="form-control" value=""></br></br>
        <!-- Se imprimen la direccion MAC y el input para ser modificado-->
        <label>Mac Address </label>
        <input style="text-transform:uppercase;" name="mac_address" type="text" class="form-control" value=""></br></br>
        <!-- Se imprimen la marca del equipo y el input para ser modificado-->
        <label>Marca </label>
        <input style="text-transform:uppercase;" name="mark" type="text" class="form-control" value=""></br></br>
        <!-- Se imprimen el modelo del equipo y el input para ser modificado-->
        <label>Modelo</label>
        <input style="text-transform:uppercase;" name="modelo" type="text" class="form-control" value=""></br></br>
        <!-- Se imprimen el serial del equipo y el input para ser modificado-->
        <label>Serial </label>
        <input style="text-transform:uppercase;" name="serial" type="text" class="form-control" value=""></br></br>

        <!-- Se imprimen el proyecto al que pertenece el equipo y unmenu desplegable para ser modificado-->
        <label>Proyecto</label>    
            <input style="text-transform:uppercase;" type="text" id="project" name="project" required>
        <br><br>

        <!-- Se imprimen el tipo de nacionalidad y el input para ser modificado-->
        <label>Tipo de Nacionalidad </label>
        <select name="nacionalidad">
        <option selected value="0">NACIONAL</option>
    				<option value="1">INTERNACIONAL</option>
          </select>
            </br></br>
        <!-- Se imprimen el puerto web y el input para ser modificado-->
        <label>Puerto Web </label>
        <input style="text-transform:uppercase;" name="web_port" type="text" class="form-control" value=""></br></br>
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
        <button class="btn btn-secondary" name="ingresar"> Grabar</button>
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

<script type="application/javascript">
            //crea la funcion inicial, inicia con el primer input, que es el de cliente
            $(document).ready(function (){
              //crea la variable y le asigna el json_encode del arreglo creado en la funcion de consulta a clientes. linea 2. ojo, abre y cierra php
              var itemsref = <?=  json_encode($arrayref) ?>
              //a el campo de con id cliente, le genera la funcion autocomplete para que al momento de introducir por teclado texto, vaya filtrando y aparezcan el listado de clientes
              $("#reference").autocomplete({
                //crea desde la variable itemscl los datos para el autocomplete
                source: itemsref,
              //crea la funcion para poder interconectar los proyectos que solo correspondan a el cliente seleccionado
              select: function (event, item){
                //asigna a params el valor del campo #cliente seleccionado anteriormente
                var params = {
                  reference: item.item.value
                };
                //envia por el metodo get la variable params, para hacer la consulta de clientes y proyectos
                $.get("getTipo.php", params, function(response){
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
                    $("#type").autocomplete({
                     source: json
                    });
                    
            
                });
              }
              });

            });

</script>

<script type="application/javascript">
            //crea la funcion inicial, inicia con el primer input, que es el de cliente
            $(document).ready(function (){
              //crea la variable y le asigna el json_encode del arreglo creado en la funcion de consulta a clientes. linea 2. ojo, abre y cierra php
              var itemsoc = <?=  json_encode($arrayod) ?>
              //a el campo de con id cliente, le genera la funcion autocomplete para que al momento de introducir por teclado texto, vaya filtrando y aparezcan el listado de clientes
              $("#orden").autocomplete({
                //crea desde la variable itemscl los datos para el autocomplete
                source: itemsoc
              //crea la funcion para poder interconectar los proyectos que solo correspondan a el cliente seleccionado
              
              });

            });

</script>


<script type="application/javascript">
            //crea la funcion inicial, inicia con el primer input, que es el de cliente
            $(document).ready(function (){
              //crea la variable y le asigna el json_encode del arreglo creado en la funcion de consulta a clientes. linea 2. ojo, abre y cierra php
              var itemspr = <?=  json_encode($arraypr) ?>
              //a el campo de con id cliente, le genera la funcion autocomplete para que al momento de introducir por teclado texto, vaya filtrando y aparezcan el listado de clientes
              $("#proveedor").autocomplete({
                //crea desde la variable itemscl los datos para el autocomplete
                source: itemspr
              //crea la funcion para poder interconectar los proyectos que solo correspondan a el cliente seleccionado
              
              });

            });

</script>



