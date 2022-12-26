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
$year = '';
$anio='';
$mes='';
$nom='';
$i='';
$arreglo_serial = array();
#$arreglo_serial[]='';

//Funcion para calular Años Meses y dias que faltan para vencimiento de garantia de un equipo


if (isset($_POST['ingresar'])) {

    $i=0;

    $cliente = $_POST['cliente'];
    $dias_garantia = 5;
    //echo $cliente;
    //echo "en isset";
    $sql = "SELECT nom FROM llx_societe WHERE rowid='$cliente'";
    $result = mysqli_query($conn, $sql) or die("SQL Query Failed.");
    while($row = mysqli_fetch_assoc($result)){
    $nom=$row['nom'];
    }
   
    

    $sql = "SELECT * FROM llx_equipment_history WHERE fk_client='$cliente'";
    $result = mysqli_query($conn, $sql) or die("SQL Query Failed.");
    
       echo "Nombre: " . $nom;
        if(mysqli_num_rows($result) > 0){  
            while($row = mysqli_fetch_assoc($result)){

               $type = $row['type'];
               $mark = $row['mark'];
               $modelo = $row['modelo'];
               $serial = $row['serial'];
               $arreglo_serial[$i]=$serial;
               $date_fac = $row['date_facture'];
               $dop = $row['date_operation'];
               $fkp = $row['fk_projet'];
               $warranty_time = $row['warranty_time'];

               $sql2 = "SELECT ref FROM llx_projet WHERE rowid='$fkp'";
               $result2 = mysqli_query($conn, $sql2) or die("SQL Query Failed.");
               while($row2 = mysqli_fetch_assoc($result2)){
                $proj=$row2['ref'];
                

               echo $type . " " . $mark . " " . $modelo . " " . $serial . " " . $date_fac . " " . $dop . " " . $proj . " " . " <br>"; 
               $i++;
            }
        

            $timestamp = strtotime($date_fac); //1072915200
            $anio =  idate('Y', $timestamp);
            $mes=  idate('m', $timestamp);
            $dia =  idate('d', $timestamp);
            echo "<br>";
            $year = $anio + $warranty_time;
            
            echo "LA GARANTIA CADUCA EL: " . $year . " - "  . $mes .  " - " . $dia;

            
           
            
            }
      }else{  
          echo "NO existen Equipos en el cliente ingresado";
      } 

 /*   nombre cliente 
//    {
    
    type
    marca 
    modelo
    serial
    fecha de facturacion
    facha de operacion
    tiempo restante
    codigo de proyecto

 //   }
*/
echo "i " . $i;
}

// Se obtiene el ID del elemento donde estamos

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

?>
<html>
<head>
      <!-- Hoja de estilos CSS -->
      <link rel="stylesheet" href="styles.css">
      <!-- Hoja de estilos de CSS para mostrar lineas de las tablas-->
  

</head>
  <body>
    <!-- Se imprimen todos los inputs editables y no editables-->
    <div>
        <table>
            <tr>
      <!--Se recibe el ID de la hoja de vida del equipo -->
      <form action="listado_equipos_cliente.php" method="POST">
        <!-- Se Imprime la referencia de la hoja de vida-->

        <!-- Se imprimen el cliente y un menu desplegable para ser modificado-->
        <label>Cliente </label>
        <select name="cliente"><?php
          //se consulta en la base de datos todos los nombres de clientes (fournisseur=0)
          $query="SELECT * FROM llx_societe WHERE fournisseur=0 ORDER BY nom "; 
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
    
          
        <!-- Boton para guardar lo modificado-->
        <button class="btn btn-secondary" name="ingresar"> Grabar</button>
        <!-- Boton para volver a la hoja de vida del equipo-->
        <button class="btn btn-secondary" name="cancelar"> Anular</button>
      </form>
      </tr>
      <tr>
        <td>
            <?php
        if(isset($_POST['ingresar'])){
            ?>
            </br></br>
            <h4><b><?php echo "------------------------------------------------------------------------------------------"  ?></b></h4></br></br>
            </br></br>
            <h4><b><?php echo "Cliente:  " . $nom; ?></b></h4></br></br>
            <h4><b><?php echo "La garantia caduca el: "  . $year . " - "  . $mes .  " - " . $dia; ?></b></h4></br></br>

            <h4><b><?php 
            $t=0;            
            foreach ($arreglo_serial as $op){
               $t+=1;
             }
            echo $t;

            for($x=0;$x<$t;$x++){
                echo ": " . $arreglo_serial[$x] . "<br>" ;
            }
            ?> </b></h4></br></br>
            <?php
            
            }
            ?>
        </td>
        </tr>
    </table>
    </div>
  </body>
</html>

<?php
llxFooter();   
?>