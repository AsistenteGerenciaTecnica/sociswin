<!-- Se incluye conexion a Base de datos -->
<?php 
// dbX.php para el servidor ultimate
// db.php para el de pruebas
include("db.php"); 


 #include("C:\wamp\www\dolibarr\htdocs\filefunc.inc.php");
 #include("C:\wamp\www\dolibarr\htdocs\main.inc.php");


?>

<?php
//Lineas creadas automaticamente por module builder para correcta visualizacion del modulo
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])){
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; 
$tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;

while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) { 
	$i--; 
	$j--; 
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("falla - no se encuentra main.inc.php");
}

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
$fecha_ini = date("2021-05-10");//IMPORTANTE CAMBIAR LA FECHA A LA DE LANZAMIENTO DEL MÓDULO
/*$format = 'Y-m-d H:i:s';
$datetime = '2022-01-01 00:00:00';
$fecha_ini = chk_MySQL_Datetime($format, $datetime);*/


/*Se busca en la tabla de ordenes de compra todas las ordenes de
que ya se marcaron como recibidas y que aun no se han creado las Hojas
de vida de los equipos que contiene. */



/*$query = "SELECT t1.* FROM llx_commande_fournisseur t1
LEFT JOIN llx_equipment_history t2 ON t1.rowid = t2.fk_commande_fournisseur
WHERE (t2.fk_commande_fournisseur IS NULL) AND (t1.fk_statut=4 OR t1.fk_statut=5) AND (t1.tms>'$fecha_ini') ";*/
//Se hace la consulta

//
$query = "SELECT t1.* FROM llx_commande_fournisseur t1
LEFT JOIN llx_equipment_history t2 ON t1.rowid = t2.fk_commande_fournisseur
WHERE (t2.fk_commande_fournisseur IS NULL) AND (DATE(t1.tms) > '$fecha_ini') AND (t1.fk_statut=4 OR t1.fk_statut=5) LIMIT 5"; 

$result =  mysqli_query($conn, $query) /*/or die("query failed")*/;

//while si es Null no encontro hojas de vida por crear

while (($row = mysqli_fetch_assoc($result)) != NULL) {
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
	//Se extraen todos los productos que pertenencen a esa orden de compra
	$query2="SELECT * FROM llx_commande_fournisseurdet WHERE fk_commande=$fk_commande_fournisseur";
	$result2 = mysqli_query($conn, $query2);

	foreach($row as $data) {
		$projet="";
		$proveedor="";
		$fecha="";
		$fk_commande_fournisseur="";
		$proveedor=$data['fk_soc'];
		$fk_commande_fournisseur=$data['rowid'];
		$fecha=$data['tms'];
		//Si no tiene proyecto asignado se coloca fk_projet=0, proyecto de ventas
		if($data['fk_projet'] == NULL){
			$projet = 0;
			
			
			
		}
		else{
			$projet=$data['fk_projet'];
			
		}
	
	
		########################  select para validar si se debe o no realizar la consulta automatica
		$ga="SELECT fk_object FROM llx_societe, llx_societe_extrafields WHERE (llx_societe.rowid='$proveedor' AND llx_societe.rowid = llx_societe_extrafields.fk_object AND llx_societe_extrafields.gra=1)";
		
		$ga2 = mysqli_query($conn, $ga);
		
		if($ga2) {
			while ($ga3 = mysqli_fetch_assoc($ga2)) {
				//	$prueba=$ga3['fk_object'];
				//	echo $prueba;
				//echo "select 2";
				//Se extraen todos los productos que pertenencen a esa orden de compra
				$query2="SELECT * FROM llx_commande_fournisseurdet WHERE fk_commande='$fk_commande_fournisseur'";
				$result2 = mysqli_query($conn, $query2);
				
				if($result2) {
					while ($row2 = mysqli_fetch_assoc($result2)) {
						$producto=$row2['fk_product'];
						//echo "select 3";
						//se extra la nacionalidad del producto de la tabla de productos
						$query3="SELECT fk_country, rowid, ref, label FROM llx_product WHERE rowid='$producto'";
						$row3 = mysqli_fetch_assoc(mysqli_query($conn, $query3));
						//echo "select 4";
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
							//echo "select 5";
							$num=$result['COUNT(*)']+1;
							$ref="HV" . $dia . $mes . "-" . $num;
				
				
							//  $proveedor - variable que contiene el rowid (tabla llx_societe)
				
							//sentencia insertar los datos del productos en la Base de Datos
				
							// 
							/* impresiones para probar si se entra en el ciclo
							echo $proveedor;
							echo "<br>";
							echo $label;
							echo "<br>";
							*/

							$check = $data["fk_soc"];
							
							$query3 = "INSERT INTO llx_equipment_history 
							(ref, reference, type, fk_product, fk_fournisseur, fk_commande_fournisseur, date_reception, fk_projet, national_type, status, statut) 
							VALUES 
							('$ref', '$reference', '$label', '$idproduct', '$proveedor', '$fk_commande_fournisseur', '$fecha', '$projet', '$nacionalidad', 1, 0)";
							
							$result3 = mysqli_query($conn, $query3);
							if(!$result3) {
								//die("error al ingresar." . mysqli_error());
								die("check: $check" . $query3 . mysqli_error($conn));
							}
							
				
							//prueba de errores
							/*
							echo "numero " . $num;
							echo "<br>";
							echo "ref " . $ref;
							echo "<br>";
							echo "reference " . $reference;
							echo "<br>";
							echo "type " . $label;
							echo "<br>";
							echo "fk product  " . $idproduct;
							echo "<br>";
							echo "fk_fournisseur " . $proveedor;
							echo "<br>";
							echo "fk coomande fou " . $$fk_commande_fournisseur;
							echo "<br>";
							echo "date_reception " . $fecha;
							echo "<br>";
							echo "fk_projet " . $projet;
							echo "<br>";
							echo "national_type " . $nacionalidad;
							echo "<br>";
							*/
							// El operador !== también puede ser usado. Puesto que != no funcionará como se espera
							// porque la posición de 'a' es 0. La declaración (0 != false) se evalúa a 
							// false.
							
								
						}
					}
				}
			}
		}
		##########################   fin de consulta que valida si se debe o no generar la garantia automatica
	
	}
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
		<div>
		<?php
			// Gráfico Combinado

			$monthLimit = 12;

			/*
			SELECT date_format(`date_reception`, '%M %Y') as `date`, COUNT(*) AS `count`
			FROM `llx_equipment_history`
			WHERE `status` = 1
			GROUP BY date_format(`date_reception`, '%M %Y' )
			ORDER BY  date_format(`date_reception`, '%M %Y') DESC
			LIMIT 24
			*/

			$qCombCreados = "SELECT date_format(`date_reception`, '%m %Y') as `date`, COUNT(*) AS `count` ";
			$qCombCreados .= "FROM `llx_equipment_history` ";
			$qCombCreados .= "WHERE `status` = 1 ";
			$qCombCreados .= "GROUP BY date_format(`date_reception`, '%m %Y' ) ";
			$qCombCreados .= "ORDER BY  date_format(`date_reception`, '%m %Y') DESC ";
			$qCombCreados .= "LIMIT $monthLimit";

			$qCombMod = "SELECT date_format(`date_reception`, '%m %Y') as `date`, COUNT(*) AS `count` ";
			$qCombMod .= "FROM `llx_equipment_history` ";
			$qCombMod .= "WHERE `status` = 1 AND `statut` = 1 ";
			$qCombMod .= "GROUP BY date_format(`date_reception`, '%m %Y' ) ";
			$qCombMod .= "ORDER BY  date_format(`date_reception`, '%m %Y') DESC ";
			$qCombMod .= "LIMIT $monthLimit";

			$mes = idate("m");
			$year = idate("Y");

			$countMes = 1;

			$dataComb = array();

			for ($i = $mes; $countMes <= $monthLimit; $countMes++, $i--){
				if ($i <= 0){
					$i = 12;
					$year--;
				} 				
				if ($i < 10){
					$label = "0$i $year"; 
					$dataComb[] = array($label);
				} else {
					$label = "$i $year"; 
					$dataComb[] = array($label);						
				}
				
			}

			$resultCombCreados = mysqli_query($conn, $qCombCreados);
			$resultCombMod = mysqli_query($conn, $qCombMod);

			$rowsCreados = array();
			$rowsMod = array();

			while($row = mysqli_fetch_assoc($resultCombCreados)) {
				
				$rowsCreados[] = $row;

			}

			while($row = mysqli_fetch_assoc($resultCombMod)) {
				
				$rowsMod[] = $row;

			}

			$totalCreados;
			$totalModificados;
			$totalNoModificados;

			// Insertar Creados
			for($i = 0; $i < sizeof($dataComb); $i++){
				for ($j = 0; $j < sizeof($rowsCreados); $j++){
					if ($rowsCreados[$j]['date'] == $dataComb[$i][0]){
						array_push($dataComb[$i], $rowsCreados[$j]['count']);
						$totalCreados += $rowsCreados[$j]['count'];
					}
				}
			}

			for($i = 0; $i < sizeof($dataComb); $i++){
				if (!$dataComb[$i][1]){
					array_push($dataComb[$i], 0);
				}
			}

			// Insertar Modificados
			for($i = 0; $i < sizeof($dataComb); $i++){
				for ($j = 0; $j < sizeof($rowsMod); $j++){
					if ($rowsMod[$j]['date'] == $dataComb[$i][0]){
						array_push($dataComb[$i], $rowsMod[$j]['count']);
						$totalModificados += $rowsMod[$j]['count'];
					}
				}
			}

			for($i = 0; $i < sizeof($dataComb); $i++){
				if (!$dataComb[$i][2]){
					array_push($dataComb[$i], 0);
				}
			}

			$totalNoModificados = $totalCreados - $totalModificados;

			//print("creados ". $totalCreados);
			//print("mod ". $totalModificados);
			//print("no mod". $totalNoModificados);

			$dataPie = array(
				array("Validados", $totalModificados),
				array("No validados", $totalNoModificados)
			);			


			$typeGraphComb= "bars";

			$dataCombInv = array_reverse($dataComb, true);

			$legendComb = array("Creados", "Validados");
			$legendPie = array("Validados", "No Validados");

			$resultComb = '<div class="div-table-responsive-no-min">';
			$resultComb .= '<table class="noborder nohover" style="width: 100%">';
			$resultComb .= '<tr class="liste_titre"><th colspan="2">'.$langs->trans("Statistics").' - Hojas de Vida Creadas y Modificadas en los últimos '. $monthLimit .' meses - <b>Total: '. ($totalCreados?$totalCreados:0) .'</b></th></tr>'."\n";
			$resultComb .= '<tr class="impair"><td align="center" colspan="2">';
			$totalComb = 1; 

			$resultComb .= '<div style="display: flex; height: 85%">';

			include_once DOL_DOCUMENT_ROOT.'/core/class/dolgraph.class.php';
			$dolgraphComb= new DolGraph();
			$dolgraphComb->SetData($dataCombInv);
			$dolgraphComb->setShowLegend(1);
			$dolgraphComb->SetLegend($legendComb);
			//$dolgraph->setShowPercent(1);
			//$dolgraph->setShowPointValue(1);
			$dolgraphComb->SetType(array($typeGraphComb));
			$dolgraphComb->setHeight('100%');
			$dolgraphComb->setWidth('100%');
			$dolgraphComb->draw('graphComb');
			$resultComb.= $dolgraphComb->show($totalComb ? 0 : 1);

			$graphPie = new DolGraph();
			$graphPie->SetData($dataPie);
			$graphPie->setShowLegend(2);
			$graphPie->SetLegend($legendPie);
			$graphPie->setShowPercent(1);
			//$graphPie->setShowPointValue(1);
			$graphPie->SetType(array("pie"));
			$graphPie->setHeight('100%');
			$graphPie->setWidth('100%');
			$graphPie->draw('graphPie');
			$resultComb.= $graphPie->show($totalComb ? 0 : 1);

			//print("Total: $totalCreados");
			//print("Validados: $totalModificados");
			//print("No Validados: $totalNoModificados");

			$resultComb .= "</div>";

			$resultComb .= '</td></tr></div>';

			print($resultComb);			

		?>
		</div>
		<!-- Tabla de envios a REVISIÓN DE GARANTÍAS CONTANDO SOLO DÍAS HÁBILES-->
		<hr></hr><!-- Linea horizontal -->
		</br><!-- Salto de linea -->
		
		<table style="text-align: center" class="table" id="data3"><!-- Cabecera de la tabla -->
		</br></br></br>
		<hr></hr>
		</br>
			<h4><b>Envíos a Revisión Próximos a Expirar</b></h4></br><!-- Titulo de la tabla -->
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
						<?php
							//Link href para entrar a visualizar la hoja de vida del equipo
							echo '<td><a href="hojagarantia.php?id='. $row2['rowid'] .'">'. $row['ref'] .'</a></td>';
							
							// Marca del equipo 
							echo '<td>'. $row2['mark'] .'</td>';

							// Referencia 
							echo '<td>'. utf8_encode($row2['reference']) .'</td>';
							
							// Serial
							echo '<td>'. $row2['serial'] .'</td>';
							
							// Proveedor
							echo '<td><a href="../../societe/card.php?socid='. $row2['fk_fournisseur'] .'">'. utf8_encode($fourn['nom']) .'</td>';
							
							// Días en garantía
							echo '<td>'. $dias .' días</td>';
						
						?>
							
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
			<h4><b>Últimas 5 hojas de vida Creadas</b></h4><!-- Titulo de la tabla -->
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
				$query = "SELECT * FROM llx_equipment_history WHERE status=1 AND statut=$statut ORDER BY rowid DESC LIMIT 5";
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
					$query2="SELECT title, ref FROM llx_projet WHERE rowid=$p";
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
					<?php
						// imprime el ID con link para entrar a ver la informacion completa de Hoja de Vida
						echo '<td><a href="hojagarantia.php?id='. $row['rowid'] .'">'. $row['ref'] .'</a></td>';

						// se imprime la orden de compra
						echo '<td><a href="../../fourn/commande/card.php?id='. $row['fk_commande_fournisseur'] .'">'. $cf['ref'] .'</a></td>';
						
						echo '<td>'. utf8_encode($row['reference']) .'</td>'; //referencia
						
						// Proyecto
						echo '<td>';
						if ($row['fk_projet']==0) {
							echo "NO EXISTE PROYECTO ASIGNADO";
						} else {
							echo '<a href="../../projet/card.php?id='. $row['fk_projet'] .'">'. $p['ref'] . ' ' . utf8_encode($p['title']) .'</a>';//se imprime el titulo del proyecto
				   		}
						echo '</td>';

						//serial del equipo
						echo '<td>'. $row['serial'] .'</td>'; 

						// se imprime el nombre del cliente
						echo '<td><a href="../../societe/card.php?socid='. $row['fk_client'] .'">'. utf8_encode($cl['nom']) .'</a></td>';
						
						//fecha de recepcion
						echo '<td>'. $row['date_reception'] .'</td>'; 
						// se imprime el nombre del proveedor
						echo '<td><a href="../../societe/card.php?socid='. $row['fk_fournisseur'] .'">'. utf8_encode($fourn['nom']) .'</a></td>';
					?>
					</tr><!-- se cierra la fila-->
				<?php }
				
				}//cierra if if($result) 
				
				?>
		    </tbody><!-- fin del body de la tabla-->
		</table><!-- fin de la tabla -->

		<!-- Tabla de las ultimas hojas de vida modificadas/procesadas -->
		<hr></hr><!-- Linea horizontal -->
		</br>
		<table class="table" style="text-align: center" id="data2">
			<h4><b>Últimas 5 hojas de vida Validadas</b></h4>
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
				$query = "SELECT * FROM llx_equipment_history WHERE status=1 AND statut=$statut ORDER BY statut DESC LIMIT 5";
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
					$query2="SELECT title, ref FROM llx_projet WHERE rowid=$p";
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
					<?php
						// imprime el ID con link para entrar a ver la informacion completa de Hoja de Vida
						echo '<td><a href="hojagarantia.php?id='. $row['rowid'] .'">'. $row['ref'] .'</a></td>';

						echo '<td><a href="../../fourn/commande/card.php?id='. $row['fk_commande_fournisseur'] .'">'. $cf['ref'] .'</a></td>';// se imprime la orden de compra
						
						echo '<td>'. utf8_encode($row['reference']) .'</td>'; //referencia
						
						echo '<td>';
						if ($row['fk_projet']==0) {
							echo "NO EXISTE PROYECTO ASIGNADO";
						} else {
							echo '<a href="../../projet/card.php?id='. $row['fk_projet'] .'">'. $p['ref'] .' '. utf8_encode($p['title']) .'</a>';//se imprime el titulo del proyecto
				   		}
						echo '</td>';

						
						echo '<td>'. $row['serial'] .'</td>'; //serial del equipo
						echo '<td><a href="../../societe/card.php?socid='. $row['fk_client'] .'">'. utf8_encode($cl['nom']) .'</a></td>';// se imprime el nombre del proveedor
						echo '<td>'. $row['date_reception'] .'</td>'; //fecha de recepcion
						echo '<td><a href="../../societe/card.php?socid='. $row['fk_fournisseur'] .'">'. utf8_encode($fourn['nom']) .'</a></td>';// se imprime el nombre del proveedor
					?>
					</tr><!-- se cierra la fila-->
				<?php }
				} //cierra if ($result) linea 330
				?>
		    </tbody><!-- fin del body de la tabla-->
		</table><!-- fin de la tabla -->
		</div>
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