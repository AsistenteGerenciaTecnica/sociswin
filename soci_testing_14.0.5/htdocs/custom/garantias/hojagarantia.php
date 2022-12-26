<?php include("db.php"); ?>

<?php
//se definen como variables globales
$proveedor='';
$project='';
$cf='';
$cliente='';
$idproduct='';
$idg='';
$warranty_time='';

// Se obtiene el ID del elemento donde estamos
if  (isset($_GET['id'])) {
  $id = $_GET['id'];
  // Se extrae la hoja de vida con este ID en la base de datos
  $query = "SELECT * FROM llx_equipment_history WHERE rowid=$id";
  $result = mysqli_query($conn, $query);
  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    //se definen variables con los datos de la hoja de vida extraidos de la base de datos
    $proveedor=$row['fk_fournisseur'];
    $project=$row['fk_projet'];
    $cf=$row['fk_commande_fournisseur'];
    $cliente=$row['fk_client'];
    $idproduct=$row['fk_product'];
    $date_fac=strtotime($row['date_facture']);
    $warranty_time=$row['warranty_time'];

    //Se extrae las referencias de los campos conociendo el ID
    $query2="SELECT nom FROM llx_societe WHERE rowid=$proveedor";
    $proveedor = mysqli_fetch_assoc(mysqli_query($conn, $query2));
    //se extrae el titulo del proyecto que tenga el id guardado en la hoja de vida en la base de datos
    $query2="SELECT title FROM llx_projet WHERE rowid=$project";
    $project = mysqli_fetch_assoc(mysqli_query($conn, $query2));
    //se extrae la referencia de
    $query2="SELECT ref FROM llx_commande_fournisseur WHERE rowid=$cf";
    $cf = mysqli_fetch_assoc(mysqli_query($conn, $query2));
    //se extrae el nombre del cliente asignado al equipo
    $query2="SELECT nom FROM llx_societe WHERE rowid=$cliente";
    // si no se tiene cliente no se asigna
    if(mysqli_query($conn, $query2)!=NULL){
      $cliente = mysqli_fetch_assoc(mysqli_query($conn, $query2));   
    }
  }
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

// Security check Ingreso Usuario
$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0)
{
	$action = '';
	$socid = $user->socid;
}

// HEADER MODULO
llxHeader("", $langs->trans("Area Garantias"));

//TITULO MODULO
print load_fiche_titre($langs->trans("Area Garantias"), '', 'garantias.png@garantias');

//al presionar el boton regresar se carga la pagina principal
if (isset($_POST['regresar'])) {
  header('Location: garantiasindex.php');
}

//Funcion para calular Años Meses y dias que faltan para vencimiento de garantia de un equipo
function calcular($dias){
  $lista=[0,0,0];
  while($dias>=365){
    $dias=$dias-365;
    $lista[0]+=1;
  }
  while($dias<365 && $dias>=30){
    $dias=$dias-30;
    $lista[1]+=1;
  }
  $lista[2]=$dias;
  return $lista;
}
?>
<html>
  <head><!--CABECERA-->
      <!--hojasde estilos de bootstrap4-->
      <link rel="stylesheet" href="styles.css">
      <link rel="stylesheet" href="bootstrap.css">
  </head>
  <script style="text/javascript">
    //Lanza una alerta antes de eliminar un registro
  	function ConfirmDelete(){
  		var respuesta = confirm("¿Está seguro de Eliminar este Registro de forma Permanente?\n\nDespués no podrá recuperarlo");
      if(respuesta == true){
  			return true;
  		}
  		else{
  			return false;
  		}
  	}
    //Lanza una alerta antes de enviar a garantia un equipo
    function ConfirmEnvio(){
  		var respuesta = confirm("¿Está Seguro de Enviar este Equipo a Revision de Garantia?");
      if(respuesta == true){
  			return true;
  		}
  		else{
  			return false;
  		}
  	}
  </script>
  <body><!--CUERPO DE LA PAGINA-->
      <form action="hojagarantia.php?id=<?php echo $_GET['id']; ?>" method="POST"><!--Se recibe el id de la hoja de vida-->
        </br>
        <!--Link para entrar a visualizar la hoja de vida-->
        <a href="hojagarantia.php?id=<?php echo $id; ?>" style="color:#08298A; font-size:15px; padding: 10px; border-top:2px solid; border-right:1px #999 solid; border-left:1px #999 solid;" title="Hoja de vida">Hoja de Vida</a>&nbsp;&nbsp;&nbsp;&nbsp;
        <!--Link para visualizar los documentos relacionados al equipo-->
        <a href="doc.php?id=<?php echo $idproduct?>&idh=<?php echo $id?>" style="color:#08298A; font-size:15px;" title="Documentos Vinculados al Equipo">Documentos</a>&nbsp;&nbsp;&nbsp;&nbsp;
        <!--Link para visualizar los procedimientos relacionados al equipo-->
        <a href="procedimientos.php?id=<?php echo $id; ?>" style="color:#08298A; font-size:15px;" title="Procedimientos de Mantenimiento">Procedimientos</a>&nbsp;&nbsp;&nbsp;&nbsp;
        <!--Link para visualizar las intervenciones realizadas al equipo-->
        <a href="intervenciones.php?id=<?php echo $id; ?>" style="color:#08298A; font-size:15px;" title="Intevenciones internas">Intervenciones</a>&nbsp;&nbsp;&nbsp;&nbsp;
        <!--Link para visualizar los envios a garantia del equipo-->
        <a href="enviogarantia.php?id=<?php echo $id; ?>" style="color:#08298A; font-size:15px;" title="Envíos a Garantía">Envíos a Garantía</a>&nbsp;&nbsp;&nbsp;&nbsp;
        <br><hr></hr>
        
        <!--Se imprime la referencia de la hoja de vida-->
        <h4><b><?php echo $row['ref']; ?></b></h4></br></br>

        <?php
        //Se cuentan los días que han pasado desde la fecha de facturacion del equipo
        $dias_garantia=5;
        $fecha2 = strtotime(date("Y-m-d H:i:s"));
        if($date_fac!=NULL){
          for($date_fac;$date_fac<=$fecha2;$date_fac=strtotime('+1 day ' . date('Y-m-d',$date_fac))){ 
            $dias_garantia=$dias_garantia+1;
          }
        }
        
        ?>
        <!--Tiempo de garantia de fabricante y tiempo que falta para vencer-->
        <label>Tiempo de garantia </label>
        <input name="warranty_time" type="text" value="<?php if($row['warranty_time']!=NULL){echo $warranty_time=$row['warranty_time']; echo " años";} ?>" style = "background-color:#FA5858; color:white; font-weight: bolder;" disabled></br></br>
        
        <label>Tiempo Restante </label>
        <input name="time" value="<?php if($row['warranty_time']!=NULL && $row['date_facture']!=NULL){ $lista=calcular(($warranty_time*365)-$dias_garantia); if($lista[0]!=0){echo $lista[0]." años ";} if($lista[1]!=0){echo $lista[1]." meses ";} if($lista[2]>=0){ echo $lista[2]." dias";} if($lista[2]<0){echo "Garantia Vencida";}} //Se calculan el tiempo que falta para que termine la garantia de fabricante?>" style = "background-color:#FA5858; color:white; font-weight: bolder;" disabled></br></br>
        
        <!--labels e inputs para mostrar la informacion del equipo-->
        <label>Referencia </label>
        <input name="reference" type="text" value="<?php echo utf8_encode($row['reference']); ?>" disabled></br></br>
        <label>Tipo </label>
        <input name="type" type="text" value="<?php echo utf8_encode($row['type']); ?>" disabled></br></br>
        <label>Numero de Factura </label>
        <input name="num_fac" type="text" value="<?php echo $row['fk_facture']; ?>" disabled></br></br>
        <label>Orden de Compra </label>
        <input name="orden" type="text" value="<?php echo $cf['ref']; ?>" disabled></br></br>
        <label>Cliente </label>
        <input name="cliente" type="text" value="<?php echo utf8_encode($cliente['nom']); ?>" disabled></br></br>
        <label>Proveedor </label>
        <input name="proveedor" type="text" value="<?php echo utf8_encode($proveedor['nom']); ?>" disabled></br></br>
        <label>Fecha de Recepción </label>
        <input name="date_reception" type="text" value="<?php echo $row['date_reception']; ?>" disabled></br></br>
        <label>Fecha de Operación </label>
        <input name="date_operation" type="text" value="<?php echo $row['date_operation']; ?>" disabled></br></br>
        <label>Fecha de Facturación </label>
        <input name="date_facture" type="text" value="<?php echo $row['date_facture']; ?>" disabled></br></br>
        
        <label>Codigo de Verificación </label>
        <input name="verify_code" type="text" value="<?php echo $row['verify_code']; ?>" disabled></br></br>
        <label>Puerto de Control </label>
        <input name="control_port" type="text" value="<?php echo $row['control_port']; ?>" disabled></br></br>
        <label>Mac Address </label>
        <input name="mac_address" type="text" value="<?php echo $row['mac_address']; ?>" disabled></br></br>
        <label>Marca </label>
        <input name="mark" type="text" value="<?php echo utf8_encode($row['mark']); ?>" disabled></br></br>
        <label>Modelo</label>
        <input name="modelo" type="text" value="<?php echo utf8_encode($row['modelo']); ?>" disabled></br></br>
        <label>Serial </label>
        <input name="serial" type="text" value="<?php echo $row['serial']; ?>" disabled></br></br>
        <label>Proyecto</label>
        <input name="projet" type="text" value="<?php if($row['fk_projet']==0){echo "ventas";}else{echo utf8_encode($project['title']);} //si el fk_projet es 0, no tiene proyecto asignado y se marca como ventas?>" disabled></br></br>
        <label>Tipo de Nacionalidad</label>
        <input name="national_type" type="text" value="<?php if($row['national_type']==0){echo "Nacional";}else{ echo"Internacional";} ?>" disabled></br></br>
        <label>Puerto Web </label>
        <input name="web_port" type="text" value="<?php echo $row['web_port']; ?>" disabled></br></br>
        <label>Catalogo de Manejo </label>
        <input name="catalogo" type="text" value="<?php echo $row['category_operation']; ?>" disabled></br></br>
        </br></br>

        <!--BOTONES DE EDITAR Y ELIMINAR LA HOJA DE VIDA-->
        <!-- boton para editar lo modificado-->
        <a href="edit.php?id=<?php echo $row['rowid']?>" class="btn btn-secondary">
        <i class="fas fa-edit"></i><b> Modificar</b></a>&nbsp;&nbsp;&nbsp;&nbsp;
        <!-- boton para eliminar-->
        <a href="delete.php?id=<?php echo $row['rowid']?>"><button type='button' 
  			class="btn btn-danger" onclick="return ConfirmDelete()">
  			<i class="far fa-trash-alt"></i><b> Eliminar</b></button></a>&nbsp;&nbsp;&nbsp;&nbsp;

        <!-- boton para Regresar a la pagina principal-->
        <button class="btn btn-secondary" name="regresar"><b> Regresar</b></button>
        </br></br><hr></hr>
        </br></br></br>
      
      </form>
  </body>
</html>

<?php
llxFooter();   
?>