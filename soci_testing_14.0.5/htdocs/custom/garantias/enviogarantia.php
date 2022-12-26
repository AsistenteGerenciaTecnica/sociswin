<?php include("db.php");

// Esto le dice a PHP que usaremos cadenas UTF-8 hasta el final
mb_internal_encoding('UTF-8');
 
// Esto le dice a PHP que generaremos cadenas UTF-8
mb_http_output('UTF-8');

// Se obtiene el ID del elemento donde estamos
if  (isset($_GET['id'])) {
  $idh = $_GET['id'];
  // Se extrae la hoja de vida con este ID en la base de datos
  $query = "SELECT * FROM llx_equipment_history WHERE rowid=$idh";
  $result = mysqli_query($conn, $query);
  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    //se definen variables con los datos de la hoja de vida extraidos de la base de datos

    $idproduct=$row['fk_product'];
    $warranty_time=$row['warranty_time'];
    $date_fac=strtotime($row['date_facture']);
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

//Funcion para calular Años Meses y dias que faltan para vencimineto de garantia de un equipo
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
//header('Content-Type: text/html; charset=UTF-8');
?>
<html>
    <head><!--CABECERA-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
        <!--hojasde estilos de bootstrap4-->
        <link rel="stylesheet" href="styles.css">
        <link rel="stylesheet" href="bootstrap.css">
        <!--Estilos y plugins para los datables y generación de tablas con cuadros de busqueda-->
        <link rel="stylesheet" href="lib/datatables-1.10.20/css/dataTables.bootstrap4.min.css"/>
        <link rel="stylesheet" href="lib/datatables-1.10.20/plugins/responsive-2.2.3/css/responsive.bootstrap4.min.css"/>
        <script src="lib/datatables-1.10.20/js/jquery.dataTables.js"></script>
        <script src="lib/datatables-1.10.20/js/dataTables.bootstrap4.min.js"></script>
        <scrip src="lib/datatables-1.10.20/plugins/responsive-2.2.3/js/dataTables.responsive.min.js"></script>
        <scrip src="lib/datatables-1.10.20/plugins/responsive-2.2.3/js/responsive.bootstrap4.min.js"></scrip>
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
    </script>
    <body>
        <!-- Se envian el id de la hoja de vida y del producto registrado en la hoja de vida del formulario-->
        <form action="enviogarantia.php?id=<?php echo $idh; ?>" method="POST">

            </br>
            <!--Link para entrar a visualizar la hoja de vida-->
            <a href="hojagarantia.php?id=<?php echo $idh; ?>" style="color:#08298A; font-size:15px;" title="Hoja de vida">Hoja de Vida</a>&nbsp;&nbsp;&nbsp;&nbsp;
            <!--Link para visualizar los documentos relacionados al equipo-->
            <a href="doc.php?id=<?php echo $idproduct?>&idh=<?php echo $idh?>" style="color:#08298A; font-size:15px;" title="Documentos Vinculados al Equipo">Documentos</a>&nbsp;&nbsp;&nbsp;&nbsp;
            <!--Link para visualizar los procedimientos relacionados al equipo-->
            <a href="procedimientos.php?id=<?php echo $idh; ?>" style="color:#08298A; font-size:15px;" title="Procedimientos de Mantenimiento">Procedimientos</a>&nbsp;&nbsp;&nbsp;&nbsp;
            <!--Link para visualizar las intervenciones realizadas al equipo-->
            <a href="intervenciones.php?id=<?php echo $idh; ?>" style="color:#08298A; font-size:15px;" title="Intevenciones internas">Intervenciones</a>&nbsp;&nbsp;&nbsp;&nbsp;
            <!--Link para visualizar los envios a garantia del equipo-->
            <a href="enviogarantia.php?id=<?php echo $idh; ?>" style="color:#08298A; font-size:15px; padding: 10px; border-top:2px solid; border-right:1px #999 solid; border-left:1px #999 solid;" title="Envíos a Garantía">Envíos a Garantía</a>&nbsp;&nbsp;&nbsp;&nbsp;
            <br><hr></hr>
            
            <!--Se imprime la referencia de la hoja de vida-->
            <h4><b><?php echo $row['ref']; ?></b></h4><br></br>
            
            <!-- TABLA DE ENVIO A GARANTIAS -->
            <b> Revisión de Garantia</b>
            </br></br>
            <div class="btn btn-danger"><a href="generargarantiapdf.php?id=<?php echo $idh?>" style="color:#FFF; text-decoration:none; " target="_blank"><b>Generar PDF</b></a></div>
            
            <table class="table" style="text-align: center" id="data"><!-- Tabla de garantias -->
                <thead><!-- Cabecera de la tabla -->
                    <tr>
                        <th>Consecutivo/Referencia</th>
                        <th>Fecha de Envío</th>
                        <th>Fecha de Recepción</th>
                        <th>Tiempo en Garantía</th>
                        <th>Tiempo Restante</th>
                        <th>Intervenciones Internas</th>
                        <th>Observaciones del Proveedor</th>
                        <th>Agregar/Eliminar</th>
                        <th>Recepción</th>
                    </tr>
                </thead>
                <tbody><!-- Cuerpo de la Tabla Impresion de datos-->
                    <?php
                    //SE GENERA LA FECHA ACTUAL PARA LA IMPRESION DE LOS DÍAS DE REVISION EN GARANTÍA
                    $fecha2 = strtotime(date("Y-m-d H:i:s"));
                    //Se cuentan los días que han pasado desde la fecha de facturacion del equipo
                    $dias_garantia=5;
                    for($date_fac;$date_fac<=$fecha2;$date_fac=strtotime('+1 day ' . date('Y-m-d',$date_fac))){ 
                        $dias_garantia=$dias_garantia+1;
                    }
                    //Se consulta el envio a garantia de el equipo y que este activa (status=1)
                    $query = "SELECT * FROM llx_garantia WHERE fk_equipment_history = $idh AND status=1 ORDER BY date_send";
                    $result = mysqli_query($conn, $query);
                    $fecharecepcion='0000-00-00 00:00:00';
                    //while para imprimir la garantia
                    while($row = mysqli_fetch_assoc($result)) {
                        $idg=$row['rowid'];

                        if($row['date_reception'] == NULL){
                            $fecha1=strtotime($row['date_send']);
                            //Se cuentan los días que han pasado desde la fecha de envío de revision de garantía sin contar los domingos
                            $dias=-1;
                            for($fecha1;$fecha1<=$fecha2;$fecha1=strtotime('+1 day ' . date('Y-m-d',$fecha1))){ 
                                if((strcmp(date('D',$fecha1),'Sun')!=0) && (strcmp(date('D',$fecha1),'Sat')!=0)){
                                $dias=$dias+1;
                                }
                            }
                        }
                        ?>
                        <tr>
                            <!--Se imprimen las fecha de envío y Recepción si ya se recibió-->
                            <td><?php echo $row['ref']; //fecha de envio?>
                                <br></br>
                                <!--Boton para generar pdf a cada garantia por separado-->
                                <div class="btn btn-danger"><a href="generargarantiaunicapdf.php?id=<?php echo $row['rowid']?>&idh=<?php echo $idh?>" style="color:#FFF; text-decoration:none; " target="_blank"><b>Generar PDF</b></a></div>  
                            </td>
                            <td><?php echo $fechaenvio=$row['date_send']; //fecha de envio?></td>
                            <td><?php echo $row['date_reception']; //fecha de recepcion?></td>
                            <td><?php if($row['date_reception'] == NULL){echo $dias; echo " días";} else {echo "Recibido";} //numero de dias en revision de garantias, si ya se recibio se imprime recibido?></td>              
                            <td><?php if($warranty_time!=NULL && $date_fac!=NULL){ $lista=calcular(($warranty_time*365)-$dias_garantia); if($lista[0]!=0){echo $lista[0]." años</br>";} if($lista[1]!=0){echo $lista[1]." meses</br>";} if($lista[2]>=0){ echo $lista[2]." dias";} if($lista[2]<0){echo "Garantia Vencida";}} //Se calculan el tiempo que falta para que termine la garantia de fabricante?></td> 
                            <td><!-- celda de la subtabla-->
                                <table style="text-align: center" class="table"><!-- inicio de la subtabla de intervenciones-->
                                    <thead><!-- cabecera de la tabla-->
                                        <tr>
                                            <th>Fecha de Intervención</th>
                                            <th>Orden de Servicio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        //se consulta las intervenciones internas del equipo antes del envio a garantia de fabricante
                                        $query = "SELECT * FROM llx_revision WHERE fk_equipment_history = $idh AND status=1 AND date_send<'$fechaenvio' AND date_send>'$fecharecepcion'";
                                        $result2 = mysqli_query($conn, $query);
                                        //while para imprimir todas las intervenciones internas al equipo
                                        while($row2 = mysqli_fetch_assoc($result2)) { ?>
                                            <tr>
                                                <td><?php echo $row2['date_send']; //fecha de intervencion?></td>
                                                <?php
                                                $idrev=$row2['rowid'];
                                                //se consulta la orden de servicio a la que pertenece esta intervencion
                                                $query="SELECT llx_fichinter.ref as ref, llx_fichinter.rowid as rowid FROM llx_fichinter, llx_revision WHERE llx_fichinter.rowid=llx_revision.orden AND llx_revision.rowid=$idrev";
                                                $result3 = mysqli_fetch_assoc(mysqli_query($conn, $query));?>
                                                <!-- Se imprime el codigo de la orden de servicio como un link que redirecciona a la orden de servicio en el modulo de comercio-->
                                                <td><a href="../../../fichinter/card.php?id=<?php echo $result3['rowid']?>" id="<?php echo $result3['rowid']?>" name="busqueda" class="busqueda"><?php echo $result3['ref']; ?></a></td>
                                            </tr>           
                                        <?php } ?>
                                    </tbody>
                                </table><!-- fin de la subtabla-->
                            </td><!-- fin de la celda de la subtabla-->
                            <td><!-- celda de la subtabla observaciones proveedor-->
                                <table style="text-align: center" class="table"><!-- inicio de la subtabla de observaciones del proveedor-->
                                    <thead>
                                        <tr>
                                            <th>Fecha de Observación</th>
                                            <th>Observación</th>
                                            <th>Imagen</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        //impresion de todas las observaciones del proveedor mientras el equipo esta en revision de garantia
                                        $query2 = "SELECT llx_observation_fourn.imagen as imagen, llx_observation_fourn.date as date, llx_observation_fourn.rowid as rowid, llx_observation_fourn.observation as ob FROM llx_observation_fourn, llx_garantia WHERE llx_observation_fourn.fk_garantia = llx_garantia.rowid AND llx_garantia.fk_equipment_history = $idh AND llx_garantia.status=1 AND llx_garantia.rowid=$idg AND llx_observation_fourn.status=1";
                                        $result2 = mysqli_query($conn, $query2);
                                        //while para imprime todas las observaciones del proveedor
                                        while($row2 = mysqli_fetch_assoc($result2)) { ?>
                                            <tr>
                                                <td><?php echo $row2['date']; //fecha de observacion?></td>
                                                <td><textarea cols="10" readonly><?php echo utf8_encode($row2['ob']); //observacion?></textarea></td>
                                                <td><?php echo '<img src="'.$row2['imagen'].'" width="100" height="100">'; //imagen se se desea adjuntar alguna?></td>
                                                <td>
                                                    <?php
                                                    //si statut = 1 la garantia aun esta abierta y se muestra el boton de elimianr alguna observacion
                                                    if($row['statut']==1){?>
                                                        <!-- Boton para eliminar-->
                                                        <a href="deleteobservationfourn.php?id=<?php echo $row2['rowid']?>&idh=<?php echo $idh?>"><button type='button' 
                                                        class="btn btn-danger" onclick="return ConfirmDelete()">
                                                        <i class="far fa-trash-alt"></i></button><!-- icono eliminar-->
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table><!-- fin de la subtabla-->
                            </td><!-- fin de la celda de la subtabla-->
                            <td>
                                <?php
                                //si statut =1 la garantia esta abierta y se muestra botones de crear obervacion y eliminar
                                if($row['statut']==1){?>
                                    <!-- Boton agregar observacion-->
                                    <a href="crearobservationfourn.php?id=<?php echo $row['rowid']?>&idh=<?php echo $idh?>" class="btn btn-secondary">
                                    <i class="fas fa-plus"></i><!-- icono marcador del boton-->
                                    </a>
                                    <!-- Boton para eliminar-->
                                    <a href="deletegarantia.php?id=<?php echo $row['rowid']?>&idh=<?php echo $idh?>"><button type='button' 
                                    class="btn btn-danger" onclick="return ConfirmDelete()">
                                    <i class="far fa-trash-alt"></i></button><!-- icono eliminar--> 
                                    </a>
                                <?php } ?>
                            </td>
                            <td>
                                <?php
                                //Si status= 1 l garantia esta abierta y si date_reception=NULL el equipo no se ha recibido de vuelta y se muestra el boton de recepcion
                                if($row['statut']==1 && $row['date_reception']==NULL){?>
                                    <!-- Boton para recepcion-->
                                    <a href="receptiongarantia.php?id=<?php echo $row['rowid']?>&idh=<?php echo $idh?>" class="btn btn-secondary">
                                    <i class="fas"></i><!-- icono marcador del boton-->Crear Recepción
                                    </a>
                                <?php } else {
                                    echo "Recibido";
                                }?>
                            </td>
                        </tr><!-- se cierra la fila-->
                    <?php $fecharecepcion=$row['date_reception'];
                    } ?>
                </tbody><!-- fin del body de la tabla-->
            </table><!-- fin de la tabla GARANTIAS-->
            <!-- boton para cerrar el envio a garantia-->
            <a href="cerrargarantia.php?id=<?php echo $idg ?>&idh=<?php echo $idh?>" class="btn btn-secondary">
            <i class="fas"></i><b> Cerrar Garantia</b></a>
            </br></br></br></br>

        </form>
        <!-- Hoja de estilos CSS y script para mostrar informacion de la orden de servicio al momento de poner el Mouse encima del link de la orden de servicio-->
        <link rel="StyleSheet" href="estilos.css" type="text/css">
        <script src="script.js"></script>
    </body>
</html>
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