<?php

/**
 *      \file       htdocs/custom/evalProv/evalProv_format.php
 *      \ingroup    comm
 *      \brief      Formato para las evaluaciones
 */

require '../../main.inc.php';
	
require_once DOL_DOCUMENT_ROOT.'/custom/evalProv/class/pregunta.class.php';	
require_once DOL_DOCUMENT_ROOT.'/custom/evalProv/class/categoria.class.php';	
require_once DOL_DOCUMENT_ROOT.'/custom/evalProv/class/evaluacion.class.php';	
	

$langs->load("companies");
$langs->load("thirdparty");
$langs->load("evalProv@evalProv");

$id=GETPOST('id','int');  // For backward compatibility
$ref=GETPOST('ref','alpha');
$socid=GETPOST('socid','int');
$action=GETPOST('action','alpha');


// Security check
$socid=0;
if ($user->societe_id) $socid=$user->societe_id;


$back = DOL_URL_ROOT .'/custom/evalProv/evalProv_format.php';

?>

<div class="modal" id="modal" style="display: none">
    <div class="modal_container">
        <div class="modal_title">Eliminar</div>
        <div class="modal_content">
            <div>¿Estás seguro de eliminar esta pregunta?</div> 
            <div>Descripción: <span id="modal_pregunta"></span></div>
			<?php
            echo '<form method="POST" action="'. DOL_URL_ROOT .'/custom/evalProv/inc/evaluaciones.inc.php">';
			?>
                
            <div class="modal_bottom">
				<?php
				
				echo '<input type="hidden" name="preg_id" id="preg_id" value=""/>';
				echo '<input type="hidden" name="back" value="'. $back .'"/>';

				?>

                <input type="submit" class="modal_button" name="delete_preg" value="Si"></input>
				<input type="button" class="modal_button" value="No" onclick="hideModal()"></input>
            </div>
            </form>
        </div>
    </div>	
</div>
<div class="modal" id="modal_cat" style="display: none">
    <div class="modal_container">
        <div class="modal_title">Eliminar</div>
        <div class="modal_content">
            <div>¿Estás seguro de eliminar esta categoría?</div> 
            <div>Descripción: <span id="modal_categoria"></span></div>
			<?php
            echo '<form method="POST" action="'. DOL_URL_ROOT .'/custom/evalProv/inc/evaluaciones.inc.php">';
			?>
                
            <div class="modal_bottom">
				<?php
				
				echo '<input type="hidden" name="cat_id" id="cat_id" value=""/>';
				echo '<input type="hidden" name="back" value="'. $back .'"/>';

				?>

                <input type="submit" class="modal_button" name="delete_cat" value="Si"></input>
				<input type="button" class="modal_button" value="No" onclick="hideModalCat()"></input>
            </div>
            </form>
        </div>
    </div>
</div>

<?php

/*
* View
*/

$title = $langs->trans('evalProv');
$morejs=array();
$helpurl = "ES:EvalProv";

// Header del sitio
llxHeader('', $title,$helpurl,'','','',$morejs);

if ($user->rights->evalProv->formato->read)
{

	echo '<link rel="stylesheet" href="./css/evaluaciones.css">';

	// Título 
	echo '<div style="display: flex; align-items: center; margin: 10px 10px 10px 0px">';
	echo '<span class="fas fa-building valignmiddle widthpictotitle pictotitle" style=" color: #6c6aa8;"></span>';
	echo '<div class="titre">Formato de Evaluación</div>';
	echo '</div>';

	echo '<table class="eval_table">';

	echo '<tr class="eval_row">';

	echo '<th>';

	echo '<div>';
	echo 'CATEGORÍA';
	echo '</div>';

	echo '</th>';

	echo '<th class="col_pregunta">';

	echo '<div>';
	echo 'PREGUNTA';
	echo '</div>';

	echo '</th>';

	echo '<th>';

	echo '<div>';
	echo 'CALIFICACIÓN';
	echo '</div>';

	echo '</th>';

	echo '<th class="col_edit">';

	echo '<div>';
	echo '</div>';

	echo '</th>';

	echo '</tr>';

	$cat = new Categoria($db);

	$listCategorias = $cat->getAll();

	foreach($listCategorias as $categoria) 
	{
		
		$sql = "SELECT * FROM llx_evalprov_pregunta";
		$sql .= " WHERE fk_categoria = '". $categoria->id ."'";

		$preguntas = $db->getRows($sql);

		$editing_cat = GETPOST("editing_cat");

		/**
		 * SI NO SE ESTÁ EDITANDO LA CATEGORÍA
		 */
		if (!isset($_GET["editing_cat"]) || $editing_cat != $categoria->id)
		{
			echo '<tr class="eval_row">';

			echo '<th rowspan="'. ($preguntas != 0 ? count($preguntas) + 1 : 1) .'">';

			echo '<div style="flex-direction: column">';

			echo '<div>'. strtoupper($categoria->nombre) .'</div>';

			echo '<div style="margin-top: 10px">';
			echo '<a class="editfielda " href="./evalProv_format.php?editing_cat='. $categoria->id .'">';
			echo img_edit();
			echo '</a>';
			if (count($preguntas) <= 0)
			{
				echo '<a name="delete" class="" style="margin-left: 10px" onclick="showModalCat('. "'". $categoria->id ."'," ."'". $categoria->nombre ."'" . ')">';
				echo img_delete();
				echo '</a>';
			}
			echo '</div>';

			echo '</div>';

			echo '</th>';

			echo '</tr>';
		} 
		/**
		 * SI SE ESTÁ EDITANDO LA CATEGORÍA
		 */
		else 
		{
			echo '<tr class="eval_row">';

			echo '<th rowspan="'. ($preguntas != 0 ? count($preguntas) + 1 : 1) .'">';

			echo '<form method="GET" id="form_cancel">';
			echo '</form>';

			echo '<form method="POST" action="./inc/evaluaciones.inc.php">';
			
			echo '<div class="table_form">';
			echo '<input type="text" placeholder="Nombre" name="cat_nombre_ed" value="'. $categoria->nombre .'"required>';
			echo '</div>';

			echo '<div>';
			
			if (isset($_GET["error"])){
				$error = $_GET["error"];
				if ($error == "editRepeatedCategoryName")
				{
					echo '<div style="color: red">Ya hay una categoría creada con este nombre</div>';
				}
			}

			echo '<input type="hidden" name="cat_id" value="'. $categoria->id .'"';
			echo '<input type="hidden" name="user" value="'. $user->id .'">';
			echo '<input type="hidden" name="back" value="'. $back .'">';

			echo '<button type="submit" name="update_cat" class="butAction" style="margin: 3px">Guardar</button>';
			echo '<button type="submit" class="butAction" form="form_cancel" style="margin: 3px">Cancelar</button>';
			echo '</form>';
			
			echo '</div>';

			echo '</th>';

			echo '</tr>';
		}

		

		$editing_preg = GETPOST("editing_preg");

		if (count($preguntas) != 0){

			
			foreach($preguntas as $pregunta) 
			{
				$sql = "SELECT * FROM llx_evalprov_evaluacion_pregunta WHERE fk_pregunta = '". $pregunta->rowid ."'";

				$exists = $db->getRow($sql);

				/**
				 * NO EDITANDO
				 */
				if (!isset($_GET["editing_preg"]) || $editing_preg != $pregunta->rowid)
				{
					echo '<tr class="eval_row">';

					// Pregunta
					echo '<td class="col_pregunta">';
					echo '<div>'. $pregunta->pregunta .'</div>';
					echo '</td>';
		
					// Calificación
					echo '<td class="col_calificacion">';
					if ($pregunta->tipo == "selection")
					{
						echo '<div>1 a 5</div>';
					}
					elseif ($pregunta->tipo == "check")
					{
						echo '<div>Chequeo</div>';				
					}

					echo '</td>';
					
					// Opciones
					echo '<td class="col_edit">';
					echo '<div>';
					echo '<a class="editfielda marginrightonly" href="./evalProv_format.php?editing_preg='. $pregunta->rowid .'">';
					echo img_edit();

					if ($exists == 0)
					{
						echo '</a>';
						echo '<a name="delete" class="marginrightonly" style="margin-left: 10px" onclick="showModal('. "'". $pregunta->rowid ."'," ."'". $pregunta->pregunta ."'" . ')">';
						echo img_delete();
						echo '</a>';
					}

					echo '</div>';
					echo '</td>';
					echo '</tr>';
				}
				/**
				 * EDITANDO
				 */
				else 
				{
					echo '<tr class="eval_row">';
					echo '<form method="POST" action="./inc/evaluaciones.inc.php">';

					// CATEGORIA + PREGUNTA
					echo '<td class="col_pregunta">';

					echo '<div>';

					// CATEGORIA
					echo '<select name="categoria_ed" style="margin-right: 10px">';				
					foreach ($listCategorias as $categoria) 
					{
						echo '<option value="'. $categoria->id .'" '. ($categoria->id == $pregunta->fk_categoria ? 'selected' : '') .'>'. strtoupper($categoria->nombre) .'</option>';
					}
					echo '</select>';

					// PREGUNTA
					echo '<textarea name="pregunta_ed" required>'. $pregunta->pregunta .'</textarea>';

					echo '</div>';

					echo '</td>';
					
					// TIPO DE CALIFICACIÓN
					echo '<td class="col_calificacion">';

					echo '<select name="tipo_ed">';
					echo '<option value="selection" '. ($pregunta->tipo == "selection" ? 'selected' : '') .'>1 a 5</option>';
					echo '<option value="check" '. ($pregunta->tipo == "check" ? 'selected' : '') .'>Chequeo</option>';
					echo '</select>';

					echo '</td>';
					
					// BOTONES
					echo '<td class="col_edit">';
					echo '<div>';
					echo '<div>';
					echo '<input type="hidden" name="back_ed" value="'. $back .'">';
					echo '<input type="hidden" name="preg_id_ed" value="'. $pregunta->rowid .'">';
					echo '<input type="hidden" name="user_ed" value="'. $user->id .'">';
					echo '<input type="submit" class="button buttongen marginbottomonly button-save" name="update_preg" value="'.$langs->trans("Save").'">';
					echo '</div>';
					echo '</form>';
					
					echo '<form method="GET">';
					echo '<div>';
					echo '<input type="submit" class="button buttongen marginbottomonly button-cancel" name="cancel" value="'.$langs->trans("Cancel").'">';
					echo '</div>';
					echo '</div>';
					echo '</form>';
					echo '</td>';

					echo '</tr>';
				}

				
			}

		}
	}

	
	echo '<tr class="eval_row">';

	// Ingresar nueva categoría
	echo '<td class="col_categoria">';

	echo '<div>';

	echo '<form method="POST" action="./inc/evaluaciones.inc.php" style="width: 100%">';

	echo '<div class="table_form">';

	echo '<input type="text" placeholder="Nombre" name="cat_nombre" required>';

	echo '</div>';

	echo '<div>';

	echo '<input type="hidden" name="user" value="'. $user->id .'">';
	echo '<input type="hidden" name="back" value="'. $back .'">';

	// La categoría no puede tener el mismo nombre que otra
	if (isset($_GET["error"])){

		$error = $_GET["error"];

		if ($error == "repeatedCategoryName"){

			echo '<div style="color: red">Ya hay una categoría creada con este nombre</div>';

		}
	}

	echo '<button type="submit" name="nueva_cat" class="butAction" style="margin: 0px">Nueva Categoría</button>';

	echo '</div>';

	echo '</form>';

	echo '</div>';

	echo '</td>';

	// Ingresar nueva pregunta
	echo '<td class="col_pregunta">';

	echo '<div>';

	echo '<form method="POST" action="./inc/evaluaciones.inc.php">';
	
	echo '<table class="table_form">';

	// Categoría
	echo '<tr class="table_form_row">';

	echo '<td>';
	echo '<div>Categoría: </div>';
	echo '</td>';

	echo '<td>';
	echo '<select name="categoria" id="">';
	foreach ($listCategorias as $categoria) {

		echo '<option value="'. $categoria->id .'">'. strtoupper($categoria->nombre) .'</option>';

	}
	echo '</select>';
	echo '</td>';

	echo '</tr>';

	// Descripción
	echo '<tr class="table_form_row">';

	echo '<td>';
	echo '<div>Descripción: </div>';
	echo '</td>';

	echo '<td>';
	echo '<textarea name="descripcion" id="" cols="30" rows="5" style="resize: none" required></textarea>';
	echo '</td>';

	echo '</tr>';

	// Tipo de calificación
	echo '<tr class="table_form_row">';

	echo '<td>';
	echo '<div>Calificación: </div>';
	echo '</td>';

	echo '<td>';

	echo '<select name="tipo" id="">';
	echo '<option value="selection">1 a 5</option>';
	echo '<option value="check">Chequeo</option>';
	echo '</select>';

	echo '</td>';

	echo '</tr>';

	echo '</table>';

	echo '<div>';

	echo '<input type="hidden" name="back" value="'. $back .'">';
	echo '<input type="hidden" name="user" value="'. $user->id .'">';
	echo '<button type="submit" class="butAction" name="nueva_preg" style="margin: 0px">Nueva Pregunta</button>';

	echo '</div>';

	echo '</form>';

	echo '</div>';

	echo '</td>';

	echo '</tr>';
	
	echo '</table>';
	
}
?>




<script>
	/**
   	 * MOSTRAR Y OCULTAR MODAL
   	 */
	function showModal(id, pregunta) {

		let modal = document.getElementById("modal");
		
		let modal_pregunta = document.getElementById("modal_pregunta");
		let preg_id = document.getElementById("preg_id");
		
		modal_pregunta.innerHTML = pregunta;
		preg_id.value = id;
		
		modal.style.display = "flex";   	 
   	}

	function hideModal() {
		
  		let modal = document.getElementById("modal");
	
   		modal.style.display = "none";
   	}

	function showModalCat(id, categoria) {

		let modal = document.getElementById("modal_cat");
		
		let modal_categoria = document.getElementById("modal_categoria");
		let cat_id = document.getElementById("cat_id");
		
		modal_categoria.innerHTML = categoria;
		cat_id.value = id;
		
		modal.style.display = "flex";   	 
   	}

	function hideModalCat() {
  		let modal = document.getElementById("modal_cat");
	
   		modal.style.display = "none";
   	}
</script>


<?php

	dol_fiche_end();


llxFooter();

$db->close();

