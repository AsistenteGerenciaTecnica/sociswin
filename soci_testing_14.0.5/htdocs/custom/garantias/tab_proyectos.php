<?php 
// dbX.php para el servidor ultimate
// db.php para el de pruebas
include("db.php"); 




 #include("C:\wamp\www\dolibarr\htdocs\filefunc.inc.php");
 #include("C:\wamp\www\dolibarr\htdocs\main.inc.php");


?>

<?php

/**
 *      \file       htdocs/aiu/tab_presupuesto.php
 *      \ingroup    comm
 *      \brief      Configuracion de los valores de la cotización para el presupuesto
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';			// Clase ordenes a proveedores
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';								// Librería funciones de los proveedores


$langs->load("companies");
$langs->load("bills");
$langs->load("garantias@garantias");


$id=GETPOST('id','int');  // For backward compatibility
$ref=GETPOST('ref','alpha');
$socid=GETPOST('socid','int');
$action=GETPOST('action','alpha');

// Security check
$socid=0;
if ($user->societe_id) $socid=$user->societe_id;
$result=restrictedArea($user,'facture',$id,'');

/*
* Actions
*/

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

/*
* View
 */

llxHeader("", $langs->trans("Area Garantias"));

if ($id > 0 || ! empty($ref))
{	

    
    
    $object = new Project($db);	// Crear el objeto de la orden a proveedor
	$object->fetch($id,$ref);	// Traer la información según la ID o la referencia
    
	
	$object->fetch_thirdparty();	// Obtener el tercero relacionado con la orden
	
	$head = project_prepare_head($object); // Prepara la información del encabezado
	
	/* 
	Genera las pestañas del encabezado
	'TabPresupuestoCotizacion' - Nombre de la pestaña creada en la clase del módulo
	'Proposal' - Propuesta
	*/	
    dol_fiche_head($head, 'TabGarantias', "Proyecto", -1, 'project');

    // Invoice content
 	
	// Dirección dentro de la carpeta de dolibarr a la que va a regresar desde el botón "Volver al Listado"
    $linkback = '<a href="' . DOL_URL_ROOT . '/fourn/commande/list.php?restore_lastsearch_values=1' . (! empty($socid) ? '&socid=' . $socid : '') . '">' . $langs->trans("BackToList") . '</a>';


	/* $morehtmlref='<div class="refidno">';
	// Ref Supplier
	$morehtmlref.=$form->editfieldkey("RefSupplier", 'ref_supplier', $object->ref_supplier, $object, 0, 'string', '', 0, 1);
	$morehtmlref.=$form->editfieldval("RefSupplier", 'ref_supplier', $object->ref_supplier, $object, 0, 'string', '', null, null, '', 1);
	// Thirdparty
	$morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . $object->thirdparty->getNomUrl(1);
	
	$morehtmlref.='</div>'; */

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
	<body>
		<!-- Tabla de la ultimas hojas de vida creadas -->
		</br>
		<table class="table" style="text-align: center" id="data">
			<h5><b>Hojas de Vida Relacionadas con el Proyecto</b></h5><!-- Titulo de la tabla -->
			</br>
		    <thead><!-- Cabecera de la tabla-->
		        <tr><!-- Columnas de los campos de la tabla-->
					<th>Visualizar</th>
					<th>Tiempo en Garantía</th>
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
				$query = "SELECT * FROM llx_equipment_history WHERE status=1 AND fk_projet=$id ORDER BY rowid DESC LIMIT 5";
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
					<tr>
					<?php
						// imprime el ID con link para entrar a ver la informacion completa de Hoja de Vida
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

						echo '<td><a href="../../fourn/commande/card.php?id='. $row['fk_commande_fournisseur'] .'">'. $cf['ref'] .'</a></td>';// se imprime la orden de compra
						
						echo '<td>'. utf8_encode($row['reference']) .'</td>'; //referencia
						
						echo '<td>';
						if ($row['fk_projet']==0) {
							echo "NO EXISTE PROYECTO ASIGNADO";
						} else {
							echo '<a href="../../projet/card.php?id='. $row['fk_projet'] .'">'. $p['ref'] . ' ' . utf8_encode($p['title']) .'</a>';//se imprime el titulo del proyecto
				   		}
						echo '</td>';

						
						echo '<td>'. $row['serial'] .'</td>'; //serial del equipo
						echo '<td><a href="../../societe/card.php?socid='. $row['fk_client'] .'">'. utf8_encode($cl['nom']) .'</a></td>';// se imprime el nombre del cliente
						echo '<td>'. $row['date_reception'] .'</td>'; //fecha de recepcion
						echo '<td><a href="../../societe/card.php?socid='. $row['fk_fournisseur'] .'">'. utf8_encode($fourn['nom']) .'</a></td>';// se imprime el nombre del proveedor
					?>
					</tr><!-- se cierra la fila-->
				<?php }
				
				}//cierra if if($result) 
				
				?>
		    </tbody><!-- fin del body de la tabla-->
		</table><!-- fin de la tabla -->
	</body>
</html>

<?php
//--------------------------------------------------------------------
//<!-- Footer Esqueleto Dolibarr IMPORTANTE-->
dol_fiche_end();
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
    <?php

}
