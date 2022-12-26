<!-- Se incluye conexion a Base de datos -->
<?php include("db.php");?>

<?php
//al presionar el boton regresar se carga la pagina principal
if (isset($_POST['regresar'])) {
    header('Location: garantiasindex.php');
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

// Security check Ingreso Usuario
$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0)
{
	$action = '';
	$socid = $user->socid;
    $pass = $user->int;
}

// HEADER MODULO
llxHeader("", $langs->trans("Area Garantias"));

//TITULO MODULO
print load_fiche_titre($langs->trans("Area Garantias"), '', 'garantias.png@garantias');

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
?>
<html>
    <!-- CABECERA PARA LOS ESTILOS BOOTSTRAP -->
    <head>
        <meta charset="UTF-8">
    	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <!-- Hoja de estilos BOOTSTRAP 4 -->
        <link rel="stylesheet" href="bootstrap.css">
        <!--Estilos y plugins para los datables y generación de tablas con cuadros de busqueda-->
        <link rel="stylesheet" href="lib/datatables-1.10.20/css/dataTables.bootstrap4.min.css"/>
        <link rel="stylesheet" href="lib/datatables-1.10.20/plugins/responsive-2.2.3/css/responsive.bootstrap4.min.css"/>
        <script src="lib/datatables-1.10.20/js/jquery.dataTables.js"></script>
        <script src="lib/datatables-1.10.20/js/dataTables.bootstrap4.min.js"></script>
        <script src="lib/datatables-1.10.20/plugins/responsive-2.2.3/js/dataTables.responsive.min.js"></script>
        <script src="lib/datatables-1.10.20/plugins/responsive-2.2.3/js/responsive.bootstrap4.min.js"></script>
    </head>
    <body>
        <form action="equiposgarantias.php" method="POST">
            <!-- Tabla de equipos que se han enviado a revision de garantias-->
            <table class="table" style="text-align: center" id="data">
            	<h5><b>Listado de Equipos en Revisión</b></h5>
                </br>
                <!-- Cuadro de color verde-->
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 120 100">
                    <rect x="10" y="10" width="80" height="80" fill="#58FAAC" />
                </svg>
                <label> Menos de 5 días</label></br>
                <!-- cuadro de color amarillo-->
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 120 100">
                    <rect x="10" y="10" width="80" height="80" fill="#F4FA58" />
                </svg>
                <label>  5 a 10 días</label></br>
                <!-- cuadro de color rojo-->
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 120 100">
                    <rect x="10" y="10" width="80" height="80" fill="#FA5858" />
                </svg>
                <label>  Mas de 10 días</label></br>
                <!-- cuadro de color azul-->
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 120 100">
                    <rect x="10" y="10" width="80" height="80" fill="#58D3F7" />
                </svg>
                <label>  Recibido En Bodega</label></br>
                </br>
                <thead><!-- Cabecera de la tabla-->
                    <tr><!-- Titulos de los campos de la tabla-->
                        <th>Visualizar</th>
                        <th>Consecutivo</th>
                        <th>Tiempo Restante</th>
            			<th>Orden de Compra</th>
                        <th>Referencia</th>
            			<th>Proyecto</th>
                        <th>Serial</th>
                        <th>Cliente</th>
            			<th>Fecha de Recepción</th>
            			<th>Proveedor</th>
                        <th>Tiempo en Garantia</th>
            			
                    </tr>
                </thead>
            	<tbody><!-- Cuerpo de la Tabla Impresion de datos-->
            		<?php
                    //se crea la fecha actual
                    $fecha2 = strtotime(date("Y-m-d H:i:s"));
                    //se busca las garantias que aun no han sido recibidas y que estan activas (no eliminadas)
                    $query3 = "SELECT * FROM llx_garantia WHERE statut=1 AND status=1";
                    $result2 = mysqli_query($conn, $query3);
                    // si el resultado es NULL no hay garantias para mostrar
                    if ($result2 != NULL) {
                        //while para imprimir todas las hojas de vida de los equipos que estan en garantia
                        while($row2 = mysqli_fetch_assoc($result2)) {
                            $idg = $row2['fk_equipment_history'];
                            $date=strtotime($row2['date_reception']);
                            //se busca la hoja de vida con el id extraido y que aun este activa (No eliminada)
                            $query = "SELECT * FROM llx_equipment_history WHERE status=1 AND rowid = $idg";
                            $result = mysqli_query($conn, $query);
                            while($row = mysqli_fetch_assoc($result)){
                                //se crea la fecha de envío a revision de garantia
                                $fecha1=strtotime($row2['date_send']);
                                $date_op=strtotime($row['date_operation']);
                                $warranty_time=$row['warranty_time'];
                                //Se cuentan los días que han pasado desde la fecha de envío de revision de garantía sin contar los domingos
                                $dias=-1;
                                for($fecha1;$fecha1<=$fecha2;$fecha1=strtotime('+1 day ' . date('Y-m-d',$fecha1))){ 
                                    if((strcmp(date('D',$fecha1),'Sun')!=0) && (strcmp(date('D',$fecha1),'Sat')!=0)){
                                        $dias=$dias+1;
                                    }
                                }
                                //Se cuentan los días que han pasado desde la fecha de operacion del equipo
                                $dias_garantia=5;
                                for($date_op;$date_op<=$fecha2;$date_op=strtotime('+1 day ' . date('Y-m-d',$date_op))){ 
                                    $dias_garantia=$dias_garantia+1;
                                }
                                //se busca el proveedor que tenga el id registrado en la hoja de vida 
                                $fourn=$row['fk_fournisseur'];
                                $query2="SELECT nom FROM llx_societe WHERE rowid=$fourn";
                                $fourn = mysqli_fetch_assoc(mysqli_query($conn, $query2));
                                //se busca el titulo del proyecto que tenga el id registrado en la hoja de vida
                                $p=$row['fk_projet'];
                                $query2="SELECT title, ref FROM llx_projet WHERE rowid=$p";
                                $p = mysqli_fetch_assoc(mysqli_query($conn, $query2));
                                //Se busca el nombre cliente que tenga el id registrado en la hoja de vida
                                $cl=$row['fk_client'];
                                $query2="SELECT nom FROM llx_societe WHERE rowid=$cl";
                                //if para saber si ya se le asigno un cliente y no nos de un error en la busqueda
                                if(mysqli_query($conn, $query2)!=NULL){
                                    $cl = mysqli_fetch_assoc(mysqli_query($conn, $query2));     
                                }
                                //Se busca la referencia de la orden de compra del equipo
                                $cf=$row['fk_commande_fournisseur'];
                                $query2="SELECT ref FROM llx_commande_fournisseur WHERE rowid=$cf";
                                $cf = mysqli_fetch_assoc(mysqli_query($conn, $query2));
                                
                                if($row['warranty_time']!=NULL && $row['date_facture']!=NULL){
                                    $warranty_time=$row['warranty_time'];
                                    $date_fac=strtotime($row['date_facture']);
                                    
                                    //Se cuentan los días que han pasado desde la fecha de facturacion del equipo
                                    $dias_garantia=5;
                                    $fecha2 = strtotime(date("Y-m-d H:i:s"));
                                    for($date_fac;$date_fac<=$fecha2;$date_fac=strtotime('+1 day ' . date('Y-m-d',$date_fac))){ 
                                        $dias_garantia=$dias_garantia+1;
                                    }
                                }

                                //empieza la FILA para imprimir el color dependiendo el tiempo que lleva en revision
                                if($dias < 5 && $date==NULL){ echo '<tr style = "background-color:#58FAAC;">';}//verde
                                if($dias >= 5 && $dias <= 10 && $date==NULL){echo '<tr style = "background-color:#F4FA58;">';}//Amarillo
                                if($dias > 10 && $date==NULL){echo '<tr style = "background-color:#FA5858;">';}//Rojo
                                if($date!= NULL){echo '<tr style = "background-color:#58D3F7;">';}//Azul
                                ?>
                                    <?php
                                    //Link href para entrar a visualizar la hoja de vida del equipo
                                    echo '<td><a href="hojagarantia.php?id='. $row['rowid'] .'">'. $row['ref'] .'</a></td>';
                                    
                                    // Consecutivo de Garantia
                                    echo '<td>'. $row2['ref'] .'</td>';
                                    //Se calculan el tiempo que falta para que termine la garantia de fabricante
                                    echo '<td>';
                                    if($row['warranty_time']!=NULL && $row['date_facture']!=NULL) { 
                                        $lista=calcular(($warranty_time*365)-$dias_garantia); 
                                        if($lista[0]!=0) {
                                            echo $lista[0]." años</br>";
                                        } 
                                        if($lista[1]!=0) {
                                            echo $lista[1]." meses</br>";
                                        } 
                                        if ($lista[2]>=0) { 
                                            echo $lista[2]." dias";
                                        } 
                                        if($lista[2]<0) {
                                            echo "Garantia Vencida";
                                        }
                                    } 
                                    echo '</td>';
                                    // Orden de Compra
                                    echo '<td><a href="../../fourn/commande/card.php?id='. $row['fk_commande_fournisseur'] .'">'. $cf['ref'] .'</a></td>';
                                    // Referencia 
                                    echo '<td>'. utf8_encode($row['reference']) .'</td>';
                                    
                                    // Proyecto
                                    echo '<td>';
                                    if ($row['fk_projet']==0) {
                                        echo "NO EXISTE PROYECTO ASIGNADO";
                                    } else {
                                        echo '<a href="../../projet/card.php?id='. $row['fk_projet'] .'">'. $p['ref'] . ' ' . utf8_encode($p['title']) .'</a>';//se imprime el titulo del proyecto
                                    }
                                    echo '</td>';
                                    // Serial
                                    echo '<td>'. $row['serial'] .'</td>';
                                    // se imprime el nombre del cliente
                                    echo '<td><a href="../../societe/card.php?socid='. $row['fk_client'] .'">'. utf8_encode($cl['nom']) .'</a></td>';
                                    
                                    // Fecha de recepcion
                                    echo '<td>'. $row['date_reception'] .'</td>';
                                    
                                    // Proveedor
                                    echo '<td><a href="../../societe/card.php?socid='. $row['fk_fournisseur'] .'">'. utf8_encode($fourn['nom']) .'</td>';
                                    
                                    // Tiempo en garantía
                                    echo '<td>';
                                    if($date== NULL) {
                                        echo $dias . " días";
                                    } else { 
                                        echo "Recibido";
                                    } 
                                    echo '</td>';
                                    ?>
                                </tr><!-- se cierra la fila-->
                            <?php }
                        } 
                    } ?>
                </tbody><!-- fin del body de la tabla-->
            </table><!-- fin de la tabla-->
            </br></br>
            <button class="btn btn-secondary" name="regresar"><b> Regresar</b></button><!-- Boton para regresar a la pagina principal del modulo-->
        </form>
    </body>
</html>
<!-- Footer Esqueleto Dolibarr IMPORTANTE-->
<?php
llxFooter();
?>
<!-- Se crea una funcion de Javascript para visualizar con un mejor diseño las tablas-->
<script type="application/javascript">
        $(function () {
            $('#data').DataTable({
                responsive: true,
                autoWidth: false,
            });
        });
</script>