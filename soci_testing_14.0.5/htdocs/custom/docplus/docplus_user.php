<?php

require '../../main.inc.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/usergroup.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/usergroups.lib.php';

require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/categoria.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/carpeta.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/documento.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/campo.class.php';

require_once DOL_DOCUMENT_ROOT.'/custom/docplus/lib/docplus.lib.php';

$langs->load("docplus@docplus");

$id=GETPOST('id','int');  // For backward compatibility
$ref=GETPOST('ref','alpha');
$socid=GETPOST('socid','int');
$action=GETPOST('action','alpha');

$title = $langs->trans('docplus');
$morejs = array();
$helpurl = "ES:DocumentosPlus";

$eval_ref = "EVAL-".$id;

llxHeader('', $title, $helpurl, '', '', '', $morejs);

$modulo = "user";

$back = DOL_URL_ROOT .'/custom/docplus/docplus_config.php';

// Título de la página
echo '<div style="display: flex; align-items: center; margin: 10px 10px 10px 0px">';
echo '<span class="fas fa-folder-open valignmiddle widthpictotitle pictotitle" style=" color: #6c6aa8;"></span>';

echo '<div class="titre">Documentos Plus - Usuarios</div> ';
echo '</div>';

$conditions = array();

$search_params = array();

/**
 * Para filtrar los valores de búsqueda, se utiliza un arreglo
 * que contiene todas las condiciones que el usuario haya
 * establecido. Así mismo, para mantener los datos de la búsqueda
 * en el momento en que se cambie de página,
 * se guardan los parámetros enviados en otro arreglo
 */

if (isset($_GET["search"]))
{
	// Buscar Usuario
	if (GETPOST("search_usuario") != "")
	{
		$search_usuario = GETPOST("search_usuario");

		$q = "(u.firstname LIKE '%". $search_usuario ."%' OR u.lastname LIKE '%". $search_usuario ."%')";

		$conditions["search_usuario"] = $q;
		$search_params[] = array("param" => "search_usuario", "value" => $search_usuario);
	}

	// Buscar documento
	if (GETPOST("search_documento") != "")
	{
		$nombre = GETPOST("search_documento");

		$search_params[] = array("param" => "search_documento", "value" => $nombre);
	}

	// Buscar categoria
	if (GETPOST("search_categoria") != "")
	{
		$categoria = GETPOST("search_categoria");

		$search_params[] = array("param" => "search_categoria", "value" => $categoria);
	}

	// Buscar estado
	if (GETPOST("search_estado") != "")
	{
		$estado = GETPOST("search_estado");

		$search_params[] = array("param" => "search_estado", "value" => $estado);
	}

	// Buscar renovable
	if (GETPOST("search_renovable") != "")
	{
		$renovable = GETPOST("search_renovable");

		$search_params[] = array("param" => "search_renovable", "value" => $renovable);
	}

	$date_start = "";

	// Buscar fecha de inicio
	if (GETPOST("search_start_date") != "")
	{

		$date_start = strtotime(GETPOST("search_start_date"));

		$q = "(e.fecha >= '". date("Y-m-d H:i:s", $date_start) ."')";

		$conditions["search_start_date"] = $q;
		$search_params[] = array("param" => "search_start_date", "value" => GETPOST("search_start_date"));
	}

	// Buscar fecha de finalización
	$date_end = "";
	if (GETPOST("search_end_date") != "")
	{
		$date_end = strtotime(GETPOST("search_end_date"));

		if (!($date_start != "" && $date_start > $date_end))
		{
			$q = "(e.fecha <= '". date("Y-m-d H:i:s", strtotime("+1 day", $date_end))  ."')";
	
			$conditions["search_end_date"] = $q;
			$search_params[] = array("param" => "search_end_date", "value" => GETPOST("search_end_date"));
		}
	}

}

$sql = "SELECT u.rowid, u.firstname, u.lastname FROM llx_user as u";

if (isset($_GET["search"]))
{
	if (GETPOST("search_usuario") != "")
	{
		$sql .= " WHERE ". $conditions["search_usuario"];
	}	
}

$resql = $db->query($sql);

$all_usuarios = array();

if ($resql)
{
	$num = $db->num_rows($resql);
	$i = 0;
	while ($i < $num) {
		$obj = $db->fetch_object($resql);
				
		$line = new User($db);
		$line->fetch($obj->rowid);
				
		$all_usuarios[$i] = $line;
				
		$i++;
	}
}

// Número máximo de filas para la tabla
$max_rows = 50;

if (GETPOST("max_rows") != "")
{
	$max_rows = GETPOST("max_rows");
}

// Página actual
$current_page = 1;

if (GETPOST("page"))
{
	
	$current_page = GETPOST("page");
}

// Número máximo de páginas para los datos
$max_pages = (count($all_usuarios) > 0 ? ceil(count($all_usuarios) / $max_rows) : 1);

/**
 * Si el número de página actual sobrepasa al máximo, 
 * se reemplaza por el máximo; si es inferior al mínimo,
 * se reemplaza por el mínimo
 */
$current_page = $current_page <= 0 ? 1 : $current_page;
$current_page = $current_page > $max_pages ? $max_pages : $current_page;

// Rango según el número máximo de filas seleccionado por el usuario
$start_index = (($max_rows * $current_page) - $max_rows);
$max_index = $start_index + $max_rows;

$usuarios = array();

for ($i = $start_index; $i < count($all_usuarios); $i++)
{		
	if ($i < $max_index)
	{
		$usuarios[] = $all_usuarios[$i];
	}
}

$doc = new Documento($db);
$documentos = $doc->getAll("", "", $modulo);

/**
 * Formulario para los fitlros de búsqueda
 * de la tabla
 */
$form_id = "form_filtros";

echo '<form method="GET" id="'. $form_id .'">';
echo '<input type="hidden" name="max_rows" value="'. $max_rows .'">';
echo '</form>';

/**
 * CONTROL DE PÁGINAS
 */
#region Control de páginas
echo '<form method="GET" id="pages_form">';

echo '<div style="display: flex; align-items: center; justify-content: flex-end; font-size: 16pt; margin-bottom: 20px">';

$prev_page = $current_page - 1;
$next_page = $current_page + 1;

echo '<select name="max_rows" id="max_rows" style="font-size: 16pt; margin-right: 20px" form="pages_form">';

echo '<option value="1" '. ($max_rows == 1 ? 'selected' : '') .'>1</option>';
echo '<option value="10" '. ($max_rows == 10 ? 'selected' : '') .'>10</option>';
echo '<option value="15" '. ($max_rows == 15 ? 'selected' : '') .'>15</option>';
echo '<option value="20" '. ($max_rows == 20 ? 'selected' : '') .'>20</option>';
echo '<option value="30" '. ($max_rows == 30 ? 'selected' : '') .'>30</option>';
echo '<option value="40" '. ($max_rows == 40 ? 'selected' : '') .'>40</option>';
echo '<option value="50" '. ($max_rows == 50 ? 'selected' : '') .'>50</option>';

echo '</select>';

if ($prev_page > 0)
{
	
	echo '<a href="./docplus_user.php?page='. $prev_page .'&max_rows='. $max_rows .'&search= ';
	foreach ($search_params as $param)
	{
		echo '&'. $param["param"] .'='. $param["value"];
	}
	echo '">';
	echo '<span class="fa fa-chevron-left"></span>';
	echo '</a>';
}

echo '<input type="number" name="page" id="page_number" max="'. $max_pages .'" min="1" value="'. $current_page .'" style="max-width: 40px  form="pages_form"">';

echo '<span style="margin-left: 5px; margin-right: 5px"> / '. $max_pages .'</span>';

if ($next_page <= $max_pages)
{
	echo '<a href="./docplus_user.php?page='. $next_page .'&max_rows='. $max_rows .'&search= ';
	foreach ($search_params as $param)
	{
		echo '&'. $param["param"] .'='. $param["value"];
	}
	echo '">';
	echo '<span class="fa fa-chevron-right"></span>';
	echo '</a>';
}

if (!empty($search_params[0]))
{
	echo '<input type="hidden" name="search" value="" form="pages_form"/>';

	foreach($search_params as $s_param)
	{
		echo '<input type="hidden" name="'. $s_param["param"] .'" value="'. $s_param["value"] .'" form="pages_form"/>';
	}
}

echo '</div>';

echo '</form>';
#endregion Control de páginas
// FIN CONTROL DE PÁGINAS

/**
 * INICIO TABLA USUARIOS
 */
#region Tabla
echo '<table class="centpercent">';

#region Headers
echo '<tr class="doc_table_row_h">';

echo '<th>';
echo '<div>';

// USUARIO
echo '<div>';
echo 'Usuario';
echo '</div>';

echo '<div>';
echo '<input class="doc_table_input" name="search_usuario" type="text" value="'. GETPOST("search_usuario") .'" form="'. $form_id .'">';
echo '</div>';

echo '</div>';
echo '</th>';

echo '<th>';
echo '<div>';

// DOCUMENTO
echo '<div>';
echo 'Documento';
echo '</div>';

echo '<div>';
echo '<input class="doc_table_input" name="search_documento" type="text" value="'. GETPOST("search_documento") .'" form="'. $form_id .'">';
echo '</div>';

echo '</div>';

echo '</th>';

echo '<th>';
echo '<div>';

// CATEGORIA
echo '<div>';
echo 'Categoría';
echo '</div>';

echo '<div>';
echo '<input class="doc_table_input" name="search_categoria" type="text" value="'. GETPOST("search_categoria") .'" form="'. $form_id .'">';
echo '</div>';

echo '</div>';

echo '</th>';

echo '<th>';
echo '<div>';

// ESTADO
echo '<div>';
echo 'Estado';
echo '</div>';

echo '<div>';
echo '<select class="doc_table_input" name="search_estado" form="'. $form_id .'">';

echo '<option>';
echo '';
echo '</option>';

echo '<option value="actualizado" '. (GETPOST("search_estado") == "actualizado" ? 'selected' : '') .'>';
echo 'Actualizado';
echo '</option>';

echo '<option value="sin_actualizar" '. (GETPOST("search_estado") == "sin_actualizar" ? 'selected' : '') .'>';
echo 'Sin Actualizar';
echo '</option>';

echo '<option value="nunca_guardado" '. (GETPOST("search_estado") == "nunca_guardado" ? 'selected' : '') .'>';
echo 'Nunca Guardado';
echo '</option>';

echo '</select>';
echo '</div>';

echo '</div>';
echo '</th>';

echo '<th>';
echo '<div>';

// RENOVABLE
echo '<div>';
echo 'Renovable';
echo '</div>';

echo '<div>';
echo '<select class="doc_table_input" name="search_renovable" form="'. $form_id .'">';

echo '<option>';
echo '';
echo '</option>';

echo '<option value="1" '. (GETPOST("search_renovable") ? 'selected' : '') .'>';
echo 'Si';
echo '</option>';

echo '<option value="0" '. (GETPOST("search_renovable") == "0" ? 'selected' : '') .'>';
echo 'No';
echo '</option>';

echo '</select>';
echo '</div>';

echo '</div>';

echo '</th>';

// FECHA DE RENOVACIÓN
echo '<th>';
echo '<div>';

echo '<div>';
echo 'Fecha de Renovación';
echo '</div>';

echo '<div style="display: flex; flex-direction: column; align-items: center">';

echo '<input type="date" class="doc_table_input" name="search_start_date" value="'. GETPOST("search_start_date") .'" form="'. $form_id .'">';
echo '<input type="date" class="doc_table_input" name="search_end_date" value="'. GETPOST("search_end_date") .'" form="'. $form_id .'">';
echo '</div>';

echo '</div>';
echo '</th>';

// ÚLTIMO ARCHIVO
echo '<th>';
echo '<div>';
echo 'Último Archivo';
echo '</div>';
echo '</th>';

// SUBIDO EN
echo '<th>';
echo '<div>';
echo 'Subido en';
echo '</div>';
echo '</th>';

// CAMPOS
echo '<th>';
echo '<div>';
echo 'Campos';
echo '</div>';
echo '</th>';

// BOTONES
echo '<th class="center">';
echo '<div>';
echo '<button type="submit" name="search" class="button_search" form="'.$form_id.'"><span class="fa fa-search"></span></button>';
echo '<a href="./docplus_user.php?page='. $current_page .'&max_rows='. $max_rows .'"><span class="fa fa-remove"></span></a>';

echo '</div>';
echo '</th>';

echo '</tr>';

#endregion Headers

if (count($usuarios) > 0)
{	
	
	for ($i = 0; $i < count($usuarios); $i++)
	{
		$usuario = $usuarios[$i];

		$documentos_usuario = array();

		// REVISIÓN DE FILTROS
		foreach ($documentos as $documento)
		{
			$user_doc = $documento->fetch_doc_objeto($usuario->id);
			$has_doc = !empty($user_doc);

			$pass = true;

			if (isset($_GET["search"]))
			{

				// Nombre del documento
				if ($pass && GETPOST("search_documento") != "")
				{
					$nombre = GETPOST("search_documento");
					
					$pass = (strpos(strtolower($documento->nombre), strtolower($nombre)) !== false ? true : false);
				}

				// Categoría del documento
				if ($pass && GETPOST("search_categoria") != "")
				{
					$cat = GETPOST("search_categoria");

					$parents = get_parents($documento);
					$cat_id = array_pop(explode(",", $parents));

					$categoria = new Categoria($db);
					$categoria->fetch($cat_id);

					$pass = (strpos(strtolower($categoria->nombre), strtolower($cat)) !== false ? true : false);
				}

				// Estado del documento
				if ($pass && GETPOST("search_estado") != "")
				{
					$estado = GETPOST("search_estado");

					switch($estado)
					{
						case "actualizado":
							$pass = ($has_doc && (strtotime($user_doc->fecha_renovacion) > time() || !$user_doc->renovable)); 
							break;

						case "sin_actualizar":
							$pass = ($has_doc && $user_doc->renovable && strtotime($user_doc->fecha_renovacion) <= time());
							break;
						
						case "nunca_guardado":
							$pass = !$has_doc;
							break;
					}
				}

				// Renovable
				if ($pass && GETPOST("search_renovable") != "")
				{
					$renovable = GETPOST("search_renovable");

					$pass = $user_doc->renovable == $renovable;
				}

				// Fecha de Inicio
				$date_start = "";
				if ($pass && GETPOST("search_start_date") != "")
				{
			
					$date_start = strtotime(GETPOST("search_start_date"));
			
					$pass = strtotime($user_doc->fecha_renovacion) >= $date_start;
				}
			
				// Fecha de finalización
				$date_end = "";
				if ($pass && GETPOST("search_end_date") != "")
				{
					$date_end = strtotime(GETPOST("search_end_date"));
			
					if (!($date_start != "" && $date_start > $date_end))
					{
						$pass = strtotime($user_doc->fecha_renovacion) <= $date_end;
					}
				}
			}

			if ($pass)
			{
				$documentos_usuario[] = $documento;
			}
		}
		// Fin filtros

		// Color de fondo de la fila
		$bg = $i % 2 == 0 ? 'a' : 'b';

		echo '<tr class="doc_table_row '. $bg .'">';

		$rowspan = count($documentos_usuario) > 0 ? count($documentos_usuario) + 1 : 2;

		// NOMBRE USUARIO
		echo '<th rowspan="'. $rowspan .'">';
		
		echo '<div>';

		echo '<a href="'. DOL_URL_ROOT. '/user/card.php?id='. $usuario->id .'">';
		echo $usuario->firstname . ' ' . $usuario->lastname;
		echo '</a>';
			
		echo '</div>';

		echo '</th>';
			
		echo '</tr>';

		/**
		 * DOCUMENTOS DEL USUARIO FILTRADOS
		 */
		#region Documentos del Usuario
		if (count($documentos_usuario) > 0)
		{
			foreach ($documentos_usuario as $documento)
			{
				$user_doc = $documento->fetch_doc_objeto($usuario->id);
				$has_doc = !empty($user_doc);

				echo '<tr class="doc_table_row '. $bg .'">';
				
				echo '<td>';

				// NOMBRE DOCUMENTO
				echo '<div>';

				$parents = get_parents($documento);

				echo '<a href="'. DOL_URL_ROOT .'/custom/docplus/tab_usuario.php?id='. $usuario->id .'&open_doc='. $documento->id .'&open='. $parents .'">';
				echo $documento->nombre;
				echo '</a>';
				echo '</div>';

				echo '</td>';

				echo '<td>';

				// CATEGORÍA DOCUMENTO
				echo '<div>';

				$cat_id = array_pop(explode(",", $parents));

				$categoria = new Categoria($db);
				$categoria->fetch($cat_id);


				echo $categoria->nombre;
				echo '</div>';
				
				echo '</td>';

				// ESTADO
				echo '<td class="td_';
				if (!$has_doc)
				{
					echo 'red';
					$status = "Nunca Guardado";
				}
				else
				{
					if (!$user_doc->renovable || strtotime($user_doc->fecha_renovacion) > time())
					{
						echo 'green';
						$status = "Actualizado";
					}
					else
					{
						echo 'yellow';
						$status = "Sin Actualizar";
					}
				}
				echo '">';
				echo '<div>';
				echo $status;
				echo '</div>';

				echo '</td>';

				// RENOVABLE
				echo '<td>';
				echo '<div>';
				if ($has_doc)
				{
					echo ($user_doc->renovable ? 'Si' : 'No');
				}
				else
				{
					echo 'No asignado';
				}
				echo '</div>';
				echo '</td>';

				// FECHA DE RENOVACIÓN
				echo '<td>';

				echo '<div>';
				if ($has_doc)
				{
					if ($user_doc->renovable)
					{
						echo date("d/m/Y", strtotime($user_doc->fecha_renovacion));
					}
					else
					{
						echo 'N/A';
					}
				}
				else
				{
					echo 'No asignado';
				}
				echo '</div>';

				echo '</td>';

				// Obtener archivos
				$path = '/custom/docplus/upload/'. $modulo .'/'. $usuario->id .'/'. $documento->id;
				
				$files = get_doc_files($path);

				// ÚLTIMO ARCHIVO
				echo '<td>';
				echo '<div>';
				

				if (!empty($files))
				{
					echo '<a href="'. DOL_URL_ROOT . $path .'/'. $files[0]['file'] .'">';
					echo $files[0]['file'];
					echo '</a>';
				}
				else
				{
					echo 'Sin Archivo';
				}

				echo '</div>';
				echo '</td>';

				// SUBIDA DEL ARCHIVO
				echo '<td>';
				echo '<div>';
				

				if (!empty($files))
				{
					echo date("d/m/Y" ,$files[0]['date']);
				}
				else
				{
					echo 'N/A';
				}

				echo '</div>';
				echo '</td>';
				
				// CAMPOS

				echo '<td>';

				echo '<div>';

				$cam = new Campo($db);
				$campos = $cam->getAll($documento->id);

				if (count($campos) > 0)
				{

					echo '<table style="width: 100%; height: 100%">';
					
					foreach($campos as $campo)
					{
						$obj_campo = $campo->fetch_doc_objeto_campo($usuario->id);

						echo '<tr class="doc_campos_row">';

						
						// Nombre del campo
						echo '<td style="width: 50%">';

						echo '<div>';
						echo '<b>';
						echo $campo->nombre;
						echo '</b>';
						echo '</div>';

						echo '</td>';

						// Valor
						echo '<td style="width: 50%">';

						echo '<div>';
						echo ($obj_campo->valor ? $obj_campo->valor : "Sin valor");
						echo '</div>';

						echo '</td>';


						echo '<tr>';
					}

					echo '</table>';

				}
				else
				{
					//echo '<div>';
					echo 'No hay campos';
					//echo '</div>';
				}

				echo '</div>';

				echo '</td>';

				echo '<td>';
				echo '</td>';

				echo '</tr>';

			}
		}
		else
		{
			echo '<tr class="doc_table_row '. $bg .'">';

			echo '<td colspan="9">';
			echo '<div>';
			echo 'Sin coincidencias para este usuario';
			echo '</div>';
			echo '</td>';

			echo '</tr>';
		}
		// Fin Documentos del usuario
		#endregion Documentos del Usuario
		



	}
	
}

echo '</table>';
#endregion Tabla

dol_fiche_end();


llxFooter();

$db->close();

echo '<script src="./lib/js/docplus.js"></script>';
