<!-- Se incluye conexion a Base de datos -->
<?php 
//include("db.php"); 

 #include("C:\wamp\www\dolibarr\htdocs\filefunc.inc.php");
 #include("C:\wamp\www\dolibarr\htdocs\main.inc.php");
 require("logica/Garantia.php");
 include("db2.php"); 

?>

<?php
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
if (!$res) die("falla - no se encuentra main.inc.php");



require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

// Load translation files required by the page
$langs->loadLangs(array("garantias@garantias"));

$action = GETPOST('action', 'aZ09');

#############
// Securite acces client

$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0) {
	$action = '';
	$socid = $user->socid;
}

$max = 5;
$now = dol_now();

//USUARIO LOGEADO
$usuariol= $_SESSION["dol_login"];  #sesion de usuario. nombre



// HEADER MODULO
llxHeader("", $langs->trans("Area Garantias"));

//TITULO MODULO
print load_fiche_titre($langs->trans("Area Garantias"), '', 'garantias.png@garantias');

//CREACION AUTOMATICA DE HOJAS DE VIDA ----------------------
$fecha_ini = date("2021-01-12 00:00:00");//IMPORTANTE CAMBIAR LA FECHA A LA DE LANZAMIENTO DEL MÓDULO

/*Se busca en la tabla de ordenes de compra todas las ordenes de
que ya se marcaron como recibidas y que aun no se han creado las Hojas
de vida de los equipos que contiene. */


$garantia = new Garantia();
$objeto = new Garantia();
$orden = new Garantia();
$nacionalidad = new Garantia();

$garantias = $garantia -> consultarTodo($fecha_ini); // objeto para extraer proveedor y estado de recepcion de mercancia


$arrObjetos = array();
$arrLabel = array();
$arrQty = array();
$arrFecha = array();
$arrIdpedido = array();
$arrProveedor = array();
$arrProjet = array();
$arrNac = array();
$arrIdproducto = array();
$arrReference = array();


$vueltas = 0;


$counts = $garantia -> consultarCount(); //CONOCER LA CANTIDAD DE REGISTROS
foreach ($counts as $co) {
	$num = $co -> getCounts();
	$num +=1;
	}


foreach ($garantias as $p) {
	//echo "<tr>";
	//echo " Proveedor: ". "<td>" . $p -> getProveedor() . "</td>" . "<br>";
	//echo " Fecha: " . "<td>" . $p -> getFecha() . "</td>" .  "<br>";
	//echo " pedido: " . "<td>" . $p -> getNumPedido() . "</td>" . "<br>";
	
	$proyNumber = $p -> getNumProyecto();
	$IdProveedor = $p -> getProveedor();
	$arrObjetos[$i] =$p -> getProveedor();
	
	$Idpedido = $p ->  getNumPedido();
	
	$fecha =  $p -> getFecha();
	

	if($proyNumber == NULL){
		$projet = 0;
		
	}
	else{
		$projet=$proyNumber;
		
	}
	//echo " Num Proyecto: " . "<td>" . $projet;
	//echo  "<br>";
	


	
	$objetos = $objeto -> consultarObjeto($IdProveedor); // objeto para validar si el proveedor cuenta con garantia automatica o no
	foreach ($objetos as $pr) {
		//echo "<tr>";
		
		//echo  "<br>";
		//echo " FK_Object: ". "<td>" . $pr -> getfkObject() . "</td>" . "<br>";


	//////// //Se extraen todos los productos que pertenencen a esa orden de compra
	
	$ordenes = $orden -> consultarOrdenes($Idpedido); // objeto para validar si el proveedor cuenta con garantia automatica o no
	
	foreach ($ordenes as $or) {
		//echo "<tr>";
		
		//echo  "<br>";
		//echo "LA CANTIDAD PEDIDA DEL PRODUCTO (FK_PRODUCT): ". "<td>" . $or -> getFkProducto() . "</td>" . " ES ".  $or -> getQty() ."<br>";

		$producto = $or -> getFkProducto();
		$cantidad = $or -> getQty();
		$arrQty[$vueltas] = $cantidad;
		$total = $total+$cantidad;
		
		//$total = $total + $cantidad;

		$nacionalidades = $nacionalidad -> consultarNacionalidad($producto);

		foreach ($nacionalidades as $na) {
			//echo "<tr>";
			
			$pais=$na -> getFkcountry();

			if ($pais==70) {

				$nac = 0;
			}
			else{
				$nac = 1;

			}
		
			//echo  "<br>";
			//echo " FK_Country: ". "<td>" . $nac . "</td>" . "<br>";
			//echo " cantidad: ". "<td>" . $cantidad . "</td>" . "<br>";
			//echo " id_product: ". "<td>" . $na -> getid_producto() . "</td>" . "<br>";
			$idproduct = $na -> getid_producto();
			$arrNac[$vueltas]= $nac;
			$arrIdproducto[$vueltas]=$idproduct;

			//echo " Refencia: ". "<td>" . $na -> getReference() . "</td>" . "<br>";
			$reference = $na -> getReference() ;
			$arrReference[$vueltas]=$na -> getReference() ;
			
			//echo " Label: ". "<td>" . $na -> getLabel() . "</td>" . "<br>";
			$label2 = $na -> getLabel();
			$arrLabel[$vueltas]=$na -> getLabel();
			$arrProjet[$vueltas] = $projet;
			$arrFecha[$vueltas]= $p -> getFecha();
			$arrIdpedido[$vueltas]= $p ->  getNumPedido();
			$arrProveedor[$vueltas] =$p -> getProveedor();
			$vueltas +=1;
		//////////////////////////////////////////////////////////////////////////////	
		
		

				}//// cierra foreach nacionalidades

			}//// cierra foreach ordenes de compra

		} //// cierra foreach objetos
		
	}//// cierra foreach general


			//	'$ref', '$reference', '$label', '$idproduct', '$proveedor', '$fk_commande_fournisseur', '$fecha', '$projet', '$nacionalidad', 1, 0
/*		


	*/		
////////FOR FINAL
	//echo $total;
	//echo $num;
	





	

	//////////////////////////////////////////////////////////////////////////////	
	for($i=0; $i<=$vueltas; $i++){
		//creacion de la referencia de las hojas de vida
		$j= $arrQty[$i];

		for($t=0; $t<$j; $t++){
			$mes=date("m");
			$dia=date("d");
			//echo  "<br>";
		//echo "cantidad" . $cantidad . "<br>";
		//echo "mes" . $mes . "<br>";
		//echo "dia" . $dia . "<br>";
			$label2= $arrLabel[$i];
			$fecha= $arrFecha[$i];
			$Idpedido= $arrIdpedido[$i];
			$IdProveedor= $arrProveedor[$i];
			$projet= $arrProjet[$i];
			$nac= $arrNac[$i];
			$idproduct= $arrIdproducto[$i];
			$reference= $arrReference[$i] ;	
			
			$ref="HV" . $dia . $mes . "-" . $num;
			//echo "referencia hoja de vida: " . $ref . "<br>";	
			
			$nuevoProducto = new Garantia ("", $ref, $reference, $label2, $idproduct, $IdProveedor, $Idpedido, $fecha, $projet, $nac);

			$nuevoProducto -> insertar1($ref, $reference, $label2, $idproduct, $IdProveedor, $Idpedido, $fecha, $projet, $nac);

			//echo "<br>";
			//echo "Ingresado";
		
			$num +=1;


		}
		
		

	}
	
///////CIERRA FOR FINAL
	
	
	
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
		<!-- Tabla de envios a REVISIÓN DE GARANTÍAS CONTANDO SOLO DÍAS HÁBILES-->
		<hr></hr><!-- Linea horizontal -->
		</br><!-- Salto de linea -->
		<table style="text-align: center" class="table" id="data3"><!-- Cabecera de la tabla -->
			<h5><b>Envíos a Revisión Próximos a Expirar</b></h5></br><!-- Titulo de la tabla -->
			<thead><!-- cabecera de la tabla -->
				<tr><!-- columnas de la tabla -->
					<th>Visualizar</th>
					<th>Marca</th>
					<th>Referencia</th>
					<th>Serial</th>
					<th>Proveedor</th>
					<th>Días en Revisión</th>
				</tr>
			</thead>
			<tbody><!-- cuerpo de la tabla -->
				<?php
				//Se crea la fecha de hoy
				$fecha2 = strtotime(date("Y-m-d H:i:s"));
				//se busca las garantias que aun no han sido recibidas y que estan activas (no eliminadas)
				$query = "SELECT * FROM llx_garantia WHERE statut=1 AND status=1";
				$result = mysqli_query($conn, $query);
				
				if($result){  //valida que la consulta no este vacia

				  while($row = mysqli_fetch_assoc($result)) { 
					$fecha1=strtotime($row['date_send']);
					/*Se cuentan los días que han pasado desde la fecha de envío de revision de garantía
					sin contar los domingos.*/
					$dias=-1;//De esta manera se cuenta tambien el dia en que se envio
					//For para contar dias habiles sin sabados ni domingos
					for($fecha1;$fecha1<=$fecha2;$fecha1=strtotime('+1 day ' . date('Y-m-d',$fecha1))){
						if((strcmp(date('D',$fecha1),'Sun')!=0) && (strcmp(date('D',$fecha1),'Sat')!=0)){
							$dias=$dias+1;
						}
					}
					//Se seleccionan los equipos que llevan entre 10 y 15 de revision en garantia
					if($dias >=10 && $dias <=15 && ($row['date_reception']==NULL)){
						$idg=$row['fk_equipment_history'];
						//se busca la hoja de vida con el rowid=fk_equipment_history de la tabla de llx_garantias
						$query="SELECT * FROM llx_equipment_history WHERE rowid=$idg AND status=1";
						$row2 = mysqli_fetch_assoc(mysqli_query($conn, $query));
						//se busca el proveedor que tenga el mismo id registrado en la hoja de vida
						$fourn=$row2['fk_fournisseur'];
						$query2="SELECT nom FROM llx_societe WHERE rowid=$fourn";
						$fourn = mysqli_fetch_assoc(mysqli_query($conn, $query2));
						?>
						<tr><!-- se imprime la informacion de la hoja de vida en la fila de la tabla-->
							<!-- Link href para entrar a visualizar la hoja de vida del equipo-->
							<td><a href="hojagarantia.php?id=<?php echo $row2['rowid']?>"><?php echo $row['ref']?></a></td>
							<td><?php echo $row2['mark']; //marca del equipo ?></td>
							<td><?php echo utf8_encode($row2['reference']); // referencia del equipo?></td>
							<td><?php echo $row2['serial']; //serial del equipo ?></td>
							<td><?php echo utf8_encode($fourn['nom']); // se imprime el nombre del proveedor?></td>
							<td><?php echo $dias; echo" días"; //se imprime el numero de dias en garantia?></td>
						</tr>
					<?php }
				  }
			    } ?>
			</tbody>
		</table>

		<!-- Tabla de la ultimas hojas de vida creadas -->
		<hr></hr><!-- Linea horizontal -->
		</br>
		<table class="table" style="text-align: center" id="data">
			<h5><b>Últimas hojas de vida Creadas</b></h5><!-- Titulo de la tabla -->
			</br>
		    <thead><!-- Cabecera de la tabla-->
		        <tr><!-- Columnas de los campos de la tabla-->
					<th>Visualizar</th>
					<th>Orden de Compra</th>
					<th>Referencia</th>
					<th>Proyecto</th>
					<th>Serial</th>
					<th>Cliente</th>
					<th>Fecha de Recepción</th>
					<th>Proveedor</th>
		        </tr>
		    </thead>
			<tbody><!-- Cuerpo de la Tabla Impresion de datos-->
				<?php

				echo "</br></br>";

				//<!-- Statut = 1 (Modificadas) o Statut= 0 (No modificadas)-->
				//<!-- Status = 0 (Eliminadas) o Status = 1 (No eliminadas)-->
				$statut=0;
				
				// Selecciona datos la tabla llx_equipmet_history que no esten eliminados-->
				//$query = "SELECT * FROM llx_equipment_history WHERE status=1 AND statut=$statut ORDER BY rowid DESC LIMIT 5";
				$query = "SELECT * FROM llx_equipment_history WHERE status=1 AND statut=$statut ORDER BY rowid DESC";
				$result = mysqli_query($conn, $query);
					
				//while para imprimir todas las hojas de vida

				if($result){  //valida que la consulta no este vacia

				   while($row = mysqli_fetch_assoc($result)) {
					//se busca el proveedor que tenga el mismo id registrado en la hoja de vida
					$fourn=$row['fk_fournisseur'];
					$query2="SELECT nom FROM llx_societe WHERE rowid=$fourn";
					$fourn = mysqli_fetch_assoc(mysqli_query($conn, $query2));
					//se busca el titulo del proyecto que tenga el mismo id registrado en la hoja de vida
					$p=$row['fk_projet'];
					$query2="SELECT title FROM llx_projet WHERE rowid=$p";
					$p = mysqli_fetch_assoc(mysqli_query($conn, $query2));
					//Se busca el nombre del cliente que tenga el mismo id registrado en la hoja de vida
					$cl=$row['fk_client'];
					$query2="SELECT nom FROM llx_societe WHERE rowid=$cl";
					//if para saber si ya se le asigno un cliente y no nos de un error en la busqueda
					if(mysqli_query($conn, $query2)!=NULL){
						$cl = mysqli_fetch_assoc(mysqli_query($conn, $query2));		
					}
					//se busca la referencia de la orden de compra del producto
					$cf=$row['fk_commande_fournisseur'];
					$query2="SELECT ref FROM llx_commande_fournisseur WHERE rowid=$cf";
					$cf = mysqli_fetch_assoc(mysqli_query($conn, $query2));
					?>
					<tr>
						<!-- imprime el ID con link para entrar a ver la informacion completa de Hoja de Vida-->
						<td><a href="hojagarantia.php?id=<?php echo $row['rowid']?>"><?php echo $row['ref']?></a></td>
						<td><?php echo $cf['ref']; //Orden de compra?></td>
						<td><?php echo utf8_encode($row['reference']); //referencia del equipo?></td>
						<td><?php if($row['fk_projet']==0){echo "ventas";}else{echo utf8_encode($p['title']);}//se imprime el titulo del proyecto?></td>
						<td><?php echo $row['serial']; //serial del equipo?></td>
						<td><?php echo utf8_encode($cl['nom']); //nombre del cliente?></td>
						<td><?php echo $row['date_reception']; //fecha de recepcion?></td>
						<td><?php echo utf8_encode($fourn['nom']); // se imprime el nombre del proveedor?></td>
					</tr><!-- se cierra la fila-->
				<?php }
				
				}//cierra if if($result) linea 255
				
				?>
		    </tbody><!-- fin del body de la tabla-->
		</table><!-- fin de la tabla -->

		<!-- Tabla de las ultimas hojas de vida modificadas/procesadas -->
		<hr></hr><!-- Linea horizontal -->
		</br>
		<table class="table" style="text-align: center" id="data2">
			<h5><b>Últimas hojas de vida Modificadas</b></h5>
			</br>
		    <thead><!-- Cabecera de la tabla-->
		        <tr><!-- Titulos de los campos de la tabla-->
					<th>Visualizar</th>
					<th>Orden de Compra</th>
					<th>Referencia</th>
					<th>Proyecto</th>
					<th>Serial</th>
					<th>Cliente</th>
					<th>Fecha de Recepción</th>
					<th>Proveedor</th>
		        </tr>
		    </thead>
			<tbody><!-- Cuerpo de la Tabla Impresion de datos-->
				<?php
				echo "</br></br>";
				
				//En la pagina principal se carga simpre statut=1 para mostrar las hojas de vida modificadas
				//<!-- Statut = Modificadas(1) o no modificadas (0)-->
				//<!-- Status = Eliminadas(0) No eliminadas(1)-->
				$statut=1;

				// Selecciona datos la tabla llx_equipmet_history que no esten eliminados-->
				$query = "SELECT * FROM llx_equipment_history WHERE status=1 AND statut=$statut ORDER BY statut DESC";
				$result = mysqli_query($conn, $query);
					
				//while para imprimir todas las hojas de vida

				if($result){

				while($row = mysqli_fetch_assoc($result)) {
					//se busca el proveedor que tenga el mismo id registrado en la hoja de vida
					$fourn=$row['fk_fournisseur'];
					$query2="SELECT nom FROM llx_societe WHERE rowid=$fourn";
					$fourn = mysqli_fetch_assoc(mysqli_query($conn, $query2));
					//se busca el titulo del proyecto que tenga el mismo id registrado en la hoja de vida
					$p=$row['fk_projet'];
					$query2="SELECT title FROM llx_projet WHERE rowid=$p";
					$p = mysqli_fetch_assoc(mysqli_query($conn, $query2));
					//Se busca el nombre del cliente que tenga el mismo id registrado en la hoja de vida
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
					?>
					<tr>
						<!-- imprime el ID con link para entrar a ver la informacion completa de Hoja de Vida-->
						<td><a href="hojagarantia.php?id=<?php echo $row['rowid']?>" title="<?php echo $row['ref']?>"><?php echo $row['ref']?></a></td>
						<td><?php echo $cf['ref']; //Orden de compra?></td>
						<td><?php echo utf8_encode($row['reference']); //referencia del equipo?></td>
						<td><?php if($row['fk_projet']==0){echo "ventas";}else{echo utf8_encode($p['title']);}//se imprime el titulo del proyecto?></td>
						<td><?php echo $row['serial']; //serial del equipo?></td>
						<td><?php echo utf8_encode($cl['nom']); //nombre del cliente?></td>
						<td><?php echo $row['date_reception']; //fecha de recepcion?></td>
						<td><?php echo utf8_encode($fourn['nom']); // se imprime el nombre del proveedor?></td>
					</tr><!-- se cierra la fila-->
				<?php }
				} //cierra if ($result) linea 330
				?>
		    </tbody><!-- fin del body de la tabla-->
		</table><!-- fin de la tabla -->
	</body>
</html>
<?php
//--------------------------------------------------------------------
//<!-- Footer Esqueleto Dolibarr IMPORTANTE-->
llxFooter();
?>
<!-- Se crea una funcion de Javascript para cada tabla 
de esta forma se visualiza con un mejor diseño las tablas-->
<script type="application/javascript">
        $(function () {
        	//se pasa el id de la tabla "#data"
            $('#data').DataTable({
            	//Para ajustar el tamaño de la tabla a la pagina
                responsive: true,
                autoWidth: false,
            });
        });
</script>

<script type="application/javascript">
        $(function () {
            $('#data2').DataTable({
                responsive: true,
                autoWidth: false,
            });
        });
</script>

<script type="application/javascript">
        $(function () {
            $('#data3').DataTable({
                responsive: true,
                autoWidth: false,
            });
        });
</script>