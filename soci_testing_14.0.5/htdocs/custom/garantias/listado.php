<!-- Se incluye conexion a Base de datos -->
<?php include("db.php");?>

<?php
//al presionar el boton regresar se carga la pagina principal
if (isset($_POST['regresar'])) {
    header('Location: garantiasindex.php');
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

//CREACION AUTOMATICA DE HOJAS DE VIDA ----------------------
$fecha_ini = date("2022-01-01");

/*Se busca en la tabla de ordenes de compra todas las ordenes de
que ya se marcaron como recibidas y que aun no se han creado las Hojas
de vida de los equipos que contiene. */
$query = "SELECT t1.* FROM llx_commande_fournisseur t1
LEFT JOIN llx_equipment_history t2 ON t1.rowid = t2.fk_commande_fournisseur
WHERE (t2.fk_commande_fournisseur IS NULL) AND (DATE(t1.tms) > '$fecha_ini') AND (t1.fk_statut=4 OR t1.fk_statut=5)"; 
//se hace la consulta en la base de datos
$result = mysqli_query($conn, $query);
//while si es Null no encontro hojas de vida por crear

if($result){

while ($row = mysqli_fetch_assoc($result)) {
	$projet="";
	$proveedor="";
	$fecha="";
	$fk_commande_fournisseur="";
	$proveedor=$row['fk_soc'];
	$fk_commande_fournisseur=$row['rowid'];
	$fecha=$row['tms'];
	//Si no tiene proyecto asignado se coloca fk_projet=0, proyecto de ventas
	if($row['fk_projet'] == NULL){
		$projet = 0;
	}
	else{
		$projet=$row['fk_projet'];
	}


	$ga="SELECT fk_object FROM llx_societe, llx_societe_extrafields WHERE (llx_societe.rowid=$proveedor AND llx_societe.rowid = llx_societe_extrafields.fk_object AND llx_societe_extrafields.gra=1)";
	
	$ga2 = mysqli_query($conn, $ga);
    
	if($ga2) {
	while ($ga3 = mysqli_fetch_assoc($ga2)) {


	//Se extraen todos los productos que pertenencen a esa orden de compra
	$query2="SELECT * FROM llx_commande_fournisseurdet WHERE fk_commande=$fk_commande_fournisseur";
	$result2 = mysqli_query($conn, $query2);

	while (($row2 = mysqli_fetch_assoc($result2)) != NULL) {
		$producto=$row2['fk_product'];
		//se extra la nacionalidad del producto de la tabla de productos
		$query3="SELECT fk_country, rowid, ref, label FROM llx_product WHERE rowid=$producto";
		$row3 = mysqli_fetch_assoc(mysqli_query($conn, $query3));
		if($row3['fk_country']==70){//nacionalidad=0 NACIONAL nacionalidad=1 INTERNACIONAL
			$nacionalidad=0;
		}
		else{
			$nacionalidad=1;
		}
		//se extrae la referencia y el rowid para luego mostrar los documentos relacionados a ese equipo
		$idproduct=$row3['rowid'];
		$reference=$row3['ref'];
		$label=$row3['label'];
		//FOR para recorrer la cantidad de equipos de la misma referencia e insertarlos en base de datos
		for($i=0; $i<$row2['qty']; $i++){
			//creacion de la referencia de las hojas de vida
			$mes=date("m");
			$dia=date("d");
			$query = "SELECT COUNT(*) FROM llx_equipment_history";
			$result = mysqli_fetch_assoc(mysqli_query($conn, $query));
			$num=$result['COUNT(*)']+1;
			$ref="HV" . $dia . $mes . "-" . $num;
			//sentencia para insertar datos del productos en la BD
			$query3 = "INSERT INTO llx_equipment_history(ref, reference, type, fk_product, fk_fournisseur, fk_commande_fournisseur, date_reception, fk_projet, national_type, status, statut) 
			VALUES ('$ref', '$reference', '$label', '$idproduct', '$proveedor', '$fk_commande_fournisseur', '$fecha', '$projet', $nacionalidad, 1, 0)";
			//se insertan los datos en la base de datos, imprime "query failed" si tuvo un error al insertar los datos
			$result3 = mysqli_query($conn, $query3);
			if(!$result3) {
				die("Query Failed.");
			}
		}
	}
   }
  }
 }
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

?>
<html>
	<!-- CABECERA PARA LOS ESTILOS BOOTSTRAP -->
	<head>
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
	<script style="text/javascript">
		//Funcion con Javascript de confirmacion de Eliminacion
		function ConfirmDelete(){
			//se usa la funcion confirm de javascript para mostrar la aletra de eliminar
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
		<!-- Se envian datos del statut del formulario -->
		<form action="listado.php?statut=<?php $statut ?>" method="POST">
			<table class="table" style="text-align: center" id="data">
				<h5><b>Listado Hojas de Vida de Equipos</b></h5>
				</br>
				<thead><!-- Cabecera de la tabla-->
					<tr><!-- Titulos de los campos de la tabla-->
						<th>Visualizar</th>
						<th>Tiempo Restante Garantia</th>
						<th>Orden de Compra</th>
						<th>Referencia</th>
						<th>Proyecto</th>
						<th>Serial</th>
						<th>Cliente</th>
						<th>Fecha de Recepción</th>
						<th>Proveedor</th>
						<th>Opciones</th>
					</tr>
				</thead>
				<tbody><!-- Cuerpo de la Tabla Impresion de datos-->
					<?php
					echo "</br></br>";
					//<!-- Statut = 1 (Modificadas) o Statut= 0 (No modificadas)-->
					//<!-- Status = 0 (Eliminadas) o Status = 1 (No eliminadas)-->
					if  (isset($_GET['statut'])) {
						$statut = $_GET['statut'];
					}
					else {
						$statut=1;
					}
					// Selecciona datos la tabla llx_equipmet_history que no esten eliminados-->
					$query = "SELECT * FROM llx_equipment_history WHERE (status=1 AND statut=$statut) ORDER BY rowid DESC";
					$result = mysqli_query($conn, $query);	
					//while para imprimir todas las hojas de vida

					if($result){

					while($row = mysqli_fetch_assoc($result)) {
						//se busca el proveedor que tenga el id registrado en la hoja de vida
						$fourn=$row['fk_fournisseur'];
						$query2="SELECT nom FROM llx_societe WHERE rowid=$fourn";
						$fourn = mysqli_fetch_assoc(mysqli_query($conn, $query2));
						//se busca el titulo del proyecto que tenga el id registrado en la hoja de vida
						$p=$row['fk_projet'];
						$query2="SELECT title, ref FROM llx_projet WHERE rowid=$p";
						$p = mysqli_fetch_assoc(mysqli_query($conn, $query2));
						//Se busca el nombre del cliente que tenga el id registrado en la hoja de vida
						$cl=$row['fk_client'];
						$query2="SELECT nom FROM llx_societe WHERE rowid=$cl";
						//if para saber si ya se le asigno un cliente y no nos de un error en la busqueda
						if(mysqli_query($conn, $query2)!=NULL){
							$cl = mysqli_fetch_assoc(mysqli_query($conn, $query2));		
						}
						//Se busca la referencia de la oren de compra del equipo
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
						
						?>
						<!-- FILAS -->
						<tr>
							<?php
							// Imprime el ID con link para entrar a ver la informacion completa de Hoja de Vida
							echo '<td><a href="hojagarantia.php?id='. $row['rowid'] .'">'. $row['ref'] .'</a></td>';

							// Se calculan el tiempo que falta para que termine la garantia de fabricante
							echo '<td>';
							if($row['warranty_time']!=NULL && $row['date_facture']!=NULL) { 
								$lista = calcular(($warranty_time*365) - $dias_garantia); 
								if ($lista[0]!=0) {
									echo $lista[0]." años</br>";
								} 
								if($lista[1]!=0) { 
									echo $lista[1]." meses</br>";
								} 
								if($lista[2]>=0) { 
									echo $lista[2]." dias";
								} 
								if($lista[2]<0) {
									echo "Garantia Vencida";
								}
							}
							echo '</td>';

							// Orden de compra + Link
							echo '<td><a href="../../fourn/commande/card.php?id='. $row['fk_commande_fournisseur'] .'">'. $cf['ref'] .'</a></td>';
							
							// Referencia
							echo '<td>'. utf8_encode($row['reference']) .'</td>'; 
							
							// Proyecto + Link
							echo '<td>';
							if ($row['fk_projet']==0) {
								echo "NO EXISTE PROYECTO ASIGNADO";
							} else {
								echo '<a href="../../projet/card.php?id='. $row['fk_projet'] .'">'. $p['ref'] . ' ' . utf8_encode($p['title']) .'</a>';
							}
							echo '</td>';

							// Serial del equipo
							echo '<td>'. $row['serial'] .'</td>';

							// Nombre del proveedor + Link
							echo '<td><a href="../../societe/card.php?socid='. $row['fk_client'] .'">'. utf8_encode($cl['nom']) .'</a></td>';
							
							// Fecha de recepcion
							echo '<td>'. $row['date_reception'] .'</td>'; 

							// Nombre del proveedor + Link
							echo '<td><a href="../../societe/card.php?socid='. $row['fk_fournisseur'] .'">'. utf8_encode($fourn['nom']) .'</a></td>';
							?>

							<td>
								<!-- Boton para modificar-->
								<a href="edit.php?id=<?php echo $row['rowid']?>" class="btn btn-secondary">
								<i class="fas fa-edit"></i><!-- icono marcador del boton-->
								</a>
								<!-- Boton para eliminar-->
								<a href="delete.php?id=<?php echo $row['rowid']?>">
								<button type='button' class="btn btn-danger" onclick="return ConfirmDelete()"><!-- Se envia e la funcion de javascript para confirmar -->
								<i class="far fa-trash-alt"></i></button><!-- icono eliminar-->
								</a>
							</td><!-- se cierra la columna con botones de eliminar y modificar-->
						</tr><!-- se cierra la fila-->
					<?php }} ?>
				</tbody><!-- fin del body de la tabla-->
			</table><!-- fin de la tabla -->
			</br></br>
			<button class="btn btn-secondary" name="regresar"><b> Regresar</b></button><!-- Boton para regresar a la pagina principal -->
		</form>
	</body>
</html>

<?php
//<!-- Footer Esqueleto Dolibarr IMPORTANTE-->
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