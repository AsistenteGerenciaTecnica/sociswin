<!-- Se incluye conexion a Base de datos -->
<?php include("db.php");
//-- Se definen variables
$idproduct='';
$idh='';
$ref='';
// S eobtiene el id del producto
if  (isset($_GET['id'])) {
  $idproduct = $_GET['id'];
 
}
// Se obtiene el ID de la hoja de vida
if  (isset($_GET['idh'])) {
  $idh = $_GET['idh'];
  //Se consulta la informacion del producto que tenga el ID registrado en la hoja de vida
  $query = "SELECT * FROM llx_equipment_history WHERE rowid=$idh";
  $result = mysqli_query($conn, $query);
  //Si es igual a 1 quiere decir que se encontro un resultado
  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    $ref = $row['ref'];
    $reference = $row['reference'];
  }

  $query = "SELECT rowid FROM llx_product WHERE ref=$reference";
  $result = mysqli_query($conn, $query);
  //Si es igual a 1 quiere decir que se encontro un resultado
  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    $idproduct = $row['rowid'];
  }

}

//al presionar el boton regresar se carga la pagina principal
if (isset($_POST['regresar'])) {
    header("Location: hojagarantia.php?id=$idh");
}
//Lineas creadas automaticamente por module builder para correcta visualizacion del modulo
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
?>
<html>
    <head><!-- CABECERA PARA LOS ESTILOS BOOTSTRAP -->
        <meta charset="UTF-8">
    	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <!-- BOOTSTRAP 4 -->
        <link rel="stylesheet" href="bootstrap.css">
        <!-- LIbrerias de Datatable para visualizacion de las tablas -->
        <link rel="stylesheet" href="lib/datatables-1.10.20/css/dataTables.bootstrap4.min.css"/>
        <link rel="stylesheet" href="lib/datatables-1.10.20/plugins/responsive-2.2.3/css/responsive.bootstrap4.min.css"/>
        <script src="lib/datatables-1.10.20/js/jquery.dataTables.js"></script>
        <script src="lib/datatables-1.10.20/js/dataTables.bootstrap4.min.js"></script>
        <script src="lib/datatables-1.10.20/plugins/responsive-2.2.3/js/dataTables.responsive.min.js"></script>
        <script src="lib/datatables-1.10.20/plugins/responsive-2.2.3/js/responsive.bootstrap4.min.js"></script>
    </head>
    <body>
        <!-- Se envian el id de la hoja de vida y del producto registrado en la hoja de vida del formulario-->
        <form action="doc.php?id=<?php echo $idproduct?>&idh=<?php echo $idh?>" method="POST">
            <!-- Tabla para mostrar los documentos relacionados a este producto/equipo-->
            <table class="table" style="text-align: left" id="data">
                
                </br>
                <!--Link para entrar a visualizar la hoja de vida-->
                <a href="hojagarantia.php?id=<?php echo $idh; ?>" style="color:#08298A; font-size:15px;" title="Hoja de vida">Hoja de Vida</a>&nbsp;&nbsp;&nbsp;&nbsp;
                <!--Link para visualizar los documentos relacionados al equipo-->
                <a href="doc.php?id=<?php echo $idproduct?>&idh=<?php echo $idh?>" style="color:#08298A; font-size:15px; padding: 10px; border-top:2px solid; border-right:1px #999 solid; border-left:1px #999 solid;" title="Documentos Vinculados al Equipo">Documentos</a>&nbsp;&nbsp;&nbsp;&nbsp;
                <!--Link para visualizar los procedimientos relacionados al equipo-->
                <a href="procedimientos.php?id=<?php echo $idh; ?>" style="color:#08298A; font-size:15px;" title="Procedimientos de Mantenimiento">Procedimientos</a>&nbsp;&nbsp;&nbsp;&nbsp;
                <!--Link para visualizar las intervenciones realizadas al equipo-->
                <a href="intervenciones.php?id=<?php echo $idh; ?>" style="color:#08298A; font-size:15px;" title="Intevenciones internas">Intervenciones</a>&nbsp;&nbsp;&nbsp;&nbsp;
                <!--Link para visualizar los envios a garantia del equipo-->
                <a href="enviogarantia.php?id=<?php echo $idh; ?>" style="color:#08298A; font-size:15px;" title="Envíos a Garantía">Envíos a Garantía</a>&nbsp;&nbsp;&nbsp;&nbsp;
                <br><hr></hr>
        
                <!--Se imprime la referencia de la hoja de vida-->
                <h4><b><?php echo $row['ref']; ?></b></h4></br></br>

                <h5><b>Documentos Relacionados al Equipo: </b></h5>
                </br>
                <h6><b>
                    <?php
                    //Se consultan la referencia y etiqueta del producto
                    $query = "SELECT ref, label FROM llx_product WHERE rowid=$idproduct";
                    if(mysqli_query($conn, $query)!= NULL){
                        $row = mysqli_fetch_assoc(mysqli_query($conn, $query));
                        echo "<b>Ref: </b>".$row['ref']."</br>"."<b>Etiqueta: </b>".$row['label']."</br>"."<b>Id producto: </b>".$idproduct;
                    } else {
                        echo "<b>Ref: </b>"."</br>"."<b>Etiqueta: </b>";
                    }
           
                    ?>
                </b></h6>
                </br></br>
                <thead><!-- Cabecera de la tabla-->
                    <tr><!-- Titulos de los campos de la tabla-->
                        <th>Documentos </th>
                    </tr>
                </thead>
                <tbody><!-- Cuerpo de la Tabla Impresion de datos-->
                    <?php
                    //Se buscan todos los documentos registrados en la tabla "llx_ecm_files" con el iD del producto
                    $query = "SELECT * FROM llx_ecm_files WHERE src_object_id = $idproduct AND src_object_type='product'";
                    $result = mysqli_query($conn, $query);
                    if ($result != NULL) {
                        //While para imprimir todos los documentos relacionados al producto
                        while($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                            <!-- imprime el ID con link para descargar el documento-->
                            <td><a href="descargar.php?filepath=../../../documents/<?php echo $row['filepath']; ?>&file=<?php echo $row['filename']; ?>" ><?php echo $row['filename']; ?></a></td>
                            </tr><!-- se cierra la fila-->
                        <?php } 
                    } else {
                        echo "No se encuentran documentos relacionados a este equipo";
                    } 
                    
                    $query = "SELECT * FROM llx_links WHERE objectid = '$idproduct' AND objecttype='product'";
                    $result = mysqli_query($conn, $query);
                    if ($result != NULL) {
                        //While para imprimir todos los documentos relacionados al producto
                        while($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                            <!-- imprime el ID con link para descargar el documento-->
                            <td><a href="<?php echo $row['url'];?>" ><?php echo $row['label']; ?></a></td>
                            </tr><!-- se cierra la fila-->
                        <?php } 
                    } else {
                        echo "No se encuentran documentos relacionados a este equipo";
                    } 
                    ?>
                </tbody><!-- fin del body de la tabla-->
            </table><!-- fin de la tabla-->
            </br></br>
        </form>
    </body>
</html>
<!-- Footer Esqueleto Dolibarr IMPORTANTE-->
<?php
llxFooter();
?>
<!-- Se crea una funcion de Javascript para cada tabla 
de esta forma se visualiza con un mejor diseño las tablas-->
<script type="application/javascript">
        $(function () {
            $('#data').DataTable({
                responsive: true,
                autoWidth: false,
            });
        });
</script>