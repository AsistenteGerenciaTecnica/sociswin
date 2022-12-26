<?php

/**
 *      \file       htdocs/custom/observaciones/tab_observacion.php
 *      \ingroup    comm
 *      \brief      Visualización de las observaciones asociadas a una intervención
 */

require '../../main.inc.php';

// Clase Proyectos
require_once DOL_DOCUMENT_ROOT .'/projet/class/project.class.php';

// Clase Intervenciones
require_once DOL_DOCUMENT_ROOT .'/fichinter/class/fichinter.class.php';			
require_once DOL_DOCUMENT_ROOT .'/core/lib/fichinter.lib.php';
	
require_once DOL_DOCUMENT_ROOT.'/custom/observaciones/lib/observaciones.lib.php';			
require_once DOL_DOCUMENT_ROOT.'/custom/observaciones/class/observacion.class.php';	
	

$langs->load("companies");
$langs->load("interventions");
$langs->load("observaciones@observaciones");

$id=GETPOST('id','int');  // For backward compatibility
$ref=GETPOST('ref','alpha');
$socid=GETPOST('socid','int');
$action=GETPOST('action','alpha');


// Security check
$socid=0;
if ($user->societe_id) $socid=$user->societe_id;

// Intervención
$object = new Fichinter($db);
$object->fetch($id);

?>

<link rel="stylesheet" href="./interventor/styles/styles.css"/>
<div class="modal" id="modal">
    <div class="modal_container">
        <div class="modal_title">Eliminar</div>
        <div class="modal_content">
            <div>¿Estás seguro de eliminar esta línea?</div> 
            <div>Descripción: <span id="modal_descripcion"></span></div>
			<?php
            echo '<form method="POST" action="'. DOL_URL_ROOT .'/custom/observaciones/inc/observaciones.inc.php">';
			?>
                
                <div class="modal_bottom">
                    <?php
                    echo '<input type="hidden" name="back" value="'. DOL_URL_ROOT .'/custom/observaciones/tab_observaciones.php"/>';
                    echo '<input type="hidden" id="modal_id" name="id" value="'. $id .'">';
                    ?>                    
                    <input type="hidden" id="modal_line_id" name="line_id" value="">
                    <?php
                    echo '<input type="hidden" id="modal_from" name="from" value="'. $from .'">';
                    ?>

                    <!-- 
                        REQUIERE
                        back        Página a la que regresar
                        id          ID de la intervención
                        line_id     ID de la observación
                        from        Página de la que viene el sitio actual
                     -->
                    <input type="submit" class="modal_button" name="delete" value="Si"></input>
                    <input type="button" class="modal_button" value="No" onclick="hideModal()"></input>
                </div>
            </form>
        </div>
    </div>
</div>

<?php

/*
* View
*/

$title = $langs->trans('Intervention');
$morejs=array();
$helpurl = "ES:Observaciones";

// Header del sitio
llxHeader('', $title,$helpurl,'','','',$morejs);

$form = new Form($db);

if ($id > 0 || ! empty($ref))
{	
	$object = new Fichinter($db);
	$object->fetch($id, $ref);

	$object->fetch_thirdparty();
		
		
	$head = fichinter_prepare_head($object); // Prepara la información del encabezado
		
	/* 
	Genera las pestañas del encabezado
	'TabObservacion' - Nombre de la pestaña creada en la clase del módulo
	'Fichinter' - Intervencion
	'intervention' - Nombre para el ícono al inicio de la barra
	*/	
	dol_fiche_head($head, 'TabObservacion', $langs->trans('Fichinter'), -1, 'intervention');

	// Dirección dentro de la carpeta de dolibarr a la que va a regresar desde el botón "Volver al Listado"
	$linkback = '<a 
		href="' . 
			DOL_URL_ROOT . 
			'/fichinter/list.php?restore_lastsearch_values=1' . 
			(! empty($socid) ? 
			'&socid=' . $socid : 
			'') . 
			'">' . 
			$langs->trans("BackToList") . 
		'</a>';


	$morehtmlref='<div class="refidno">';

	// Thirdparty
	$morehtmlref.= $langs->trans('ThirdParty') . ' : ' . $object->thirdparty->getNomUrl(1);

	$morehtmlref.= '<br>' . $langs->trans('Project') . ' : ';
	if(!empty($object->fk_project)){

		$project = new Project($db);
		$project->fetch($object->fk_project);

		$morehtmlref.= '<a href="../../projet/card.php?id='. $project->id .'">'. $project->ref .'</a>';
	}
		
	$morehtmlref.='</div>';

	dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);

	/**
	 * TABLA
	 */
		
	echo '</div>';

	echo '<div class="fichecenter">';

	echo '<div style="display:flex; justify-content: space-between; margin-bottom: 15px">';
	echo '<div class="titre inline-block">Observaciones</div>';
	echo '<a class="btnTitle btnTitlePlus" href="./tab_observaciones.php?id='. $id .'&action=new"><span class="fa fa-plus-circle valignmiddle btnTitle-icon"></span></a>';
	echo '</div>';

	echo '<table class="noborder centpercent">';

	// CABECERA

	echo '<tr class="liste_titre">';

	echo '<th>';
	echo 'Descripción';
	echo '</th>';

	echo '<th>';
	echo 'Fecha';
	echo '</th>';

	echo '<th>';
	echo 'Imagen';
	echo '</th>';

	echo '<th>';
	echo '</th>';

	echo '</tr>';

	/**
	 * LÍNEAS
	 */

	$obs = new Observacion($db);
	$rows = $obs->getAll($id);

	$editing = GETPOST("editing");

	for ($i = 0; $i < count($rows); $i++){
		$row = $rows[$i];

		echo '<tr class="oddeven">';

		// Si NO se está editando la línea
		if ($row->id != $editing || !isset($_GET["editing"])) {

			// Descripción
			echo '<td class="wrap" style="max-width: 200px">';
			echo $row->descripcion;
			echo '</td>';
	
			// Fecha
			echo '<td class="nowrap">';
			echo $row->fecha;
			echo '</td>';
	
			// Imagen
			echo '<td class="nowrap">';
			if ($row->filename && $row->filename != "NULL"){
				echo '<img src="'. $row->filename .'" style="max-height: 100px"></img>';
			} else {
				echo 'Sin imagen';
			}
			echo '</td>';
	
			// Botones
			echo '<td class="center">';
			echo '<a class="editfielda marginrightonly" href="./tab_observaciones.php?id='. $id .'&editing='. $row->id .'">';
			echo img_edit();
			echo '</a>';
			echo '<a name="delete" class="marginrightonly" style="margin-left: 10px" onclick="showModal('. "'". $id ."'," ."'". $row->id ."'," ."'". $row->descripcion ."'," ."'". $from ."'". ')">';
			echo img_delete();
			echo '</a></td>';

		} else {

			echo '<form method="POST" action="'. DOL_URL_ROOT .'/custom/observaciones/inc/observaciones.inc.php" enctype="multipart/form-data">';

			// Descripción
			echo '<td class="nowrap" style="max-width: 200px">';
			echo '<textarea name="descripcion" required oninvalid="this.setCustomValidity('. "'Por favor complete este campo'" .')" oninput="this.setCustomValidity('. "''" .')">'. $row->descripcion .'</textarea>';
			echo '</td>';
	
			// Fecha
			echo '<td class="nowrap">';
			echo '<div style="display: flex; flex-direction: row">';
			echo '<input type="date" name="fecha" value="'. date("Y-m-d",strtotime($row->fecha)) .'">';
			echo '<div style="margin-left: 5px">';
			echo '<input type="number" name="horas_fecha" min="0" max="23" value="'. date("H",strtotime($row->fecha)) .'">';
    		echo '<input type="number" name="mins_fecha" min="0" max="59" value="'. date("i",strtotime($row->fecha)) .'">';
			echo '</div>';
			echo '</div>';
			echo '</td>';
	
			// Imagen
			echo '<td class="nowrap">';
			echo '<button class="button" type="button" onclick="' . "document.getElementById('uploadImage').click()" . '">Seleccionar Imagen</button>';
       		echo '<input type="file" id="uploadImage" name="upload_image" style="display: none" onchange="updateFileName()" accept="image/*">';

			if ($row->filename && $row->filename != "NULL") {
				echo '<div class="file_name" id="file_name">Imagen Actual</div>';
				echo '<img id="image_preview" class="image_prev" src="'. $row->filename .'" alt="preview" style="display:block"></img>';        
			} else {
				echo '<div class="file_name" id="file_name">Ninguna imagen seleccionada</div>';
				echo '<img id="image_preview" class="image_prev" src="" alt="preview"></img>';        
			}
			echo '</td>';
	
			// Botones
			echo '<td class="center" valign="center">';
			echo '<input type="hidden" name="id" value="'. $id .'">';
			echo '<input type="hidden" name="line_id" value="'. $row->id .'">';
			echo '<input type="hidden" name="back" value="'. DOL_URL_ROOT .'/custom/observaciones/tab_observaciones.php">';
			// El objeto $user viene incluida en la página
			echo '<input type="hidden" name="user_id" value="'. $user->id .'">';
			echo '<input type="submit" class="button buttongen marginbottomonly button-save" name="update" value="'.$langs->trans("Save").'">';
			echo '</form>';
				
			echo '<form method="GET">';
			echo '<input type="hidden" name="id" value="'. $id .'">';
			echo '<input type="submit" class="button buttongen marginbottomonly button-cancel" name="cancel" value="'.$langs->trans("Cancel").'"></td>';
			echo '</form>';
			echo '</a></td>';

		}



		echo '</tr>';

	}

	echo '</table>';

	if (GETPOST("action") == "new") {

		echo '<div style="margin-top: 15px">';

   		echo '<div class="titre" style="margin-bottom: 15px">Nueva Observación</div>';

   		echo '<div class="new_line_form" style="border-radius: 0px">';
   		echo '<form method="POST" action="'. DOL_URL_ROOT .'/custom/observaciones/inc/observaciones.inc.php" enctype="multipart/form-data">';

   		echo '<table class="form_table">';

   		/**
   		 * DESCRIPCIÓN
   		 */
   		echo '<tr class="form_row">';

   		echo '<td>';
   		echo '<div>Descripción</div>';
   		echo '</td>';

   		echo '<td>';
   		echo '<textarea name="new_descripcion" required oninvalid="this.setCustomValidity('. "'Por favor complete este campo'" .')" oninput="this.setCustomValidity('. "''" .')"></textarea>';
   		echo '</td>';

		echo '</tr>';

		/**
		 * FECHA
		 */
		echo '<tr class="form_row">';

		echo '<td>';
		echo '<div>Fecha</div>';
		echo '</td>';

		echo '<td>';
		echo '<div>';
		// Fecha
		echo '<input type="date" name="new_fecha" value="'. date("Y-m-d") .'">';
		// Hora y Minuto
		echo '<div style="margin-top: 5px">';
		echo '<input type="number" name="new_fecha_hora" min="0" max="23" value="'. date("H") .'">';
		echo '<input type="number" name="new_fecha_min" min="0" max="59" value="'. date("i") .'">';
		echo '</div>';
		echo '</div>';
		echo '</td>';

		echo '</tr>';

		/**
		 * IMAGEN
		 */
		echo '<tr class="form_row">';

		echo '<td>';
		echo '<div>Imagen</div>';
		echo '</td>';

		echo '<td>';
		// Botón Imagen
		echo '<button class="button" type="button" onclick="' . "document.getElementById('uploadImage').click()" . '">Seleccionar Imagen</button>';
		echo '<input type="file" id="uploadImage" name="upload_image" style="display: none" onchange="updateFileName()" accept="image/*">';

		// Nombre y previsualización
		echo '<div class="file_name" id="file_name">Ninguna imagen seleccionada</div>';
		echo '<img id="image_preview" class="image_prev" src="" alt="preview"></img>';

		echo '</td>';

		echo '</tr>';

		echo '</table>';

		/**
		 * BOTÓN ACEPTAR
		 */

		/**
		 * REQUIERE
		 * 
		 * back             Página a la que regresar
		 * row              Fila que se modifica
		 * id               ID de la intervención
		 * from             Página de la que viene el sitio actual
		 * line_id          ID de la observación
		 * user_id			ID del usuario
		 * 
		 * upload_image     Imagen a subir
		 * fecha            Fecha de la observación
		 * horas_fecha      Hora
		 * mins_fecha       Minutos
		 * descripcion      Descripción
		*/

		echo '<input type="hidden" name="back" value="'. DOL_URL_ROOT .'/custom/observaciones/tab_observaciones.php"/>';
		echo '<input type="hidden" name="row" value="'. $i .'">';
		echo '<input type="hidden" name="id" value="'. $id .'">';
		echo '<input type="hidden" name="from" value="'. $from . '">';
		// La variable $user viene incluida en la página
		echo '<input type="hidden" name="user_id" value="'. $user->id .'">';
		//echo '<input type="hidden" name="line_id" value="'. $line->id .'">';

		echo '<button class="butAction" type="submit" name="create" style="margin-bottom: 10px; margin-left: 0px">GUARDAR</button>';

		echo '</form>';

		/**
		 * BOTÓN CANCELAR
		 */
		echo '<form>';

		echo '<input type="hidden" name="id" value="'. $id .'">';
		echo '<button class="butAction" type="submit" style="margin-left: 0px">CANCELAR</button>';

		echo '</form>';

		echo '</div>';

		echo '</div>';

		echo '</div>';

	}
		
echo '</div></div>';



?>

<script>		
		
	function updateFileName(){
       	//console.log("file changed");
       	let name = document.getElementById("file_name");
       	let preview = document.getElementById("image_preview");
       	console.log("name: " + name);

       	//name.innerHTML = "";
			
       	let input = document.getElementById("uploadImage");
       	let file = input.files[0];
       	//console.log(file.name);
		
     	let filename;
       	try {
       	    filename = file.name;
       	    preview.src = URL.createObjectURL(file);
       	    preview.style.display = "block";
       	} catch (e) {
       	    filename = "Ninguna imagen seleccionada"
       	    preview.style.display = "none";
       	}
		
       	name.innerHTML = filename;
	}

	/**
   	 * MOSTRAR Y OCULTAR MODAL
   	 */
   	function showModal(id, line_id, description, from) {
   	    let modal = document.getElementById("modal");

   	    modal.style.display = "flex";

   	    let modal_description = document.getElementById("modal_descripcion");
   	    let modal_line_id = document.getElementById("modal_line_id");

   	    modal_description.innerHTML = description;
   	    modal_line_id.value = line_id;
   	}

	function hideModal() {
  	    let modal = document.getElementById("modal");
	
   	    modal.style.display = "none";
   	}
</script>
<?php

	dol_fiche_end();
}

llxFooter();

$db->close();