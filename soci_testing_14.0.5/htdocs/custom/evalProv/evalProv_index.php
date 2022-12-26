<?php

/**
 *      \file       htdocs/custom/evalProv/evalProv_index.php
 *      \ingroup    comm
 *      \brief      Listado de evaluaciones realizadas en órdenes de compra
 */

require '../../main.inc.php';
	
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';

require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';

require_once DOL_DOCUMENT_ROOT.'/custom/evalProv/class/pregunta.class.php';	
require_once DOL_DOCUMENT_ROOT.'/custom/evalProv/class/categoria.class.php';	
require_once DOL_DOCUMENT_ROOT.'/custom/evalProv/class/evaluacion.class.php';	
	

$langs->load("companies");
$langs->load("thirdparty");
$langs->load("orders");
$langs->load("evalProv@evalProv");

$id=GETPOST('id','int');  // For backward compatibility
$ref=GETPOST('ref','alpha');
$socid=GETPOST('socid','int');
$action=GETPOST('action','alpha');

$title = $langs->trans('Evaluacion');
$morejs = array();
$helpurl = "ES:EvaluacionesDeProveedor";

$eval_ref = "EVAL-".$id;

// Security check
$socid=0;
if ($user->societe_id) $socid=$user->societe_id;

llxHeader('', $title, $helpurl, '', '', '', $morejs);

/**
 * Para ver el listado de evaluaciones, el usuario debe tener
 * permiso de lectura
 */
if ($user->rights->evalProv->evaluacion->read)
{
	$back = DOL_URL_ROOT .'/custom/evalProv/tab_orden_evaluacion.php';

	
	$form = new Form($db);echo '<link rel="stylesheet" href="./css/evaluaciones.css">';

	$ev = new Evaluacion($db);

	// Título de la página
	echo '<div style="display: flex; align-items: center; margin: 10px 10px 10px 0px">';
	echo '<span class="fas fa-building valignmiddle widthpictotitle pictotitle" style=" color: #6c6aa8;"></span>';

	echo '<div class="titre">Listado de Evaluaciones ('. count($ev->getAll()	) .')</div> ';
	echo '</div>';

	/**
	* TABLA

	*/
	$cat = new Categoria($db);

	$categorias = $cat->getAll();

	echo '<div>';

	$form_id = "table_form";

	/**
	 * El form para la tabla se crea por fuera de esta, para que pueda
	 * ser referenciado desde cualquier punto de la página, haciendo uso
	 * de la id que se le asigna al formulario
	 */
	echo '<form id="'. $form_id .'" method="GET" action="./evalProv_index.php">';
	echo '</form>';

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
		// Buscando por referencia de la evaluación		
		if (GETPOST("search_eval") != "")
		{
		
			$q = "(e.ref LIKE '%".GETPOST("search_eval")."%')";
		
			$conditions[] = $q;
			$search_params[] = array("param" => "search_eval", "value" => GETPOST("search_eval"));

		}
		
		/**
		 * 
		 * Para buscar por referencia, primero se buscan todos los objetos similares
		 * en su tabla correspondiente, y siendo que pueden haber múltiples 
		 * resultados que coinciden, se construye la condición de la siguiente manera:
		 * (e.fk_objeto = id1 OR e.fk_objeto = id2 OR e.fk_objeto = id3 OR ...)
		 * 
		 */

		// Buscando por referencia del tercero
		if (GETPOST("search_third") != "")
		{
		
			$sql = "SELECT cf.rowid FROM llx_commande_fournisseur as cf";
			$sql .= " INNER JOIN llx_societe as s";
			$sql .= " ON s.rowid = cf.fk_soc";
			$sql .= " WHERE s.nom LIKE '%". GETPOST("search_third") ."%'";
		
			$rows = $db->getRows($sql);
		
			$q = "";
		
			if (count($rows) > 0)
			{
		
				$q .= "(";
				for ($i = 0; $i < count($rows); $i++)
				{
					$q .= "e.fk_orden = '". $rows[$i]->rowid ."'";
		
					if (($i + 1) < count($rows))
					{
						$q .= " OR ";
					}
				}
				$q .= ")";
			}
		
			if ($q != "")
			{
				$conditions[] = $q;
				$search_params[] = array("param" => "search_third", "value" => GETPOST("search_third"));
			}
		
		}
		
		// Buscando por referencia de la orden de compra
		if (GETPOST("search_order") != "")
		{
			$sql = "SELECT rowid FROM llx_commande_fournisseur";
			$sql .= " WHERE ref LIKE '%". GETPOST("search_order") ."%'";
		
			$rows = $db->getRows($sql);
		
			$q = "";
		


			if (count($rows) > 0)
			{
				$q .= "(";
				for ($i = 0; $i < count($rows); $i++)
				{
					$q .= "e.fk_orden = '". $rows[$i]->rowid ."'";
		
					if (($i + 1) < count($rows))
					{
						$q .= " OR ";
					}
				}
				$q .= ")";
			}
		
			if ($q != "")
			{
				$conditions[] = $q;
				$search_params[] = array("param" => "search_order", "value" => GETPOST("search_order"));
			}
		}
		
		$date_start = "";
		if (GETPOST("date_start") != "")
		{

			$date_start = strtotime(GETPOST("date_start"));

			$q = "(e.fecha >= '". date("Y-m-d H:i:s", $date_start) ."')";

			$conditions[] = $q;
			$search_params[] = array("param" => "date_start", "value" => GETPOST("date_start"));
		}

		$date_end = "";
		if (GETPOST("date_end") != "")
		{
			$date_end = strtotime(GETPOST("date_end"));

			if ($date_start != "" && $date_start > $date_end)
			{

			}
			else
			{
				$q = "(e.fecha <= '". date("Y-m-d H:i:s", strtotime("+1 day", $date_end))  ."')";

				$conditions[] = $q;
				$search_params[] = array("param" => "date_end", "value" => GETPOST("date_end"));
			}
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

	$eval = new Evaluacion($db);

	$allEvaluaciones = $eval->getAll($conditions);

	// Número máximo de páginas para los datos
	$max_pages = ceil(count($allEvaluaciones) / $max_rows);

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

	$evaluaciones = array();

	for ($i = $start_index; $i < $max_index && $i < count($allEvaluaciones); $i++)
	{		
		$evaluaciones[] = $allEvaluaciones[$i];
	}

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
	echo '<option value="100" '. ($max_rows == 100 ? 'selected' : '') .'>100</option>';
	echo '<option value="250" '. ($max_rows == 250 ? 'selected' : '') .'>250</option>';
	echo '<option value="500" '. ($max_rows == 500 ? 'selected' : '') .'>500</option>';
	echo '<option value="1000" '. ($max_rows == 1000 ? 'selected' : '') .'>1000</option>';

	echo '</select>';

	if ($prev_page > 0)
	{
		
		echo '<a href="./evalProv_index.php?page='. $prev_page .'&max_rows='. $max_rows .'&search= ';
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
		echo '<a href="./evalProv_index.php?page='. $next_page .'&max_rows='. $max_rows .'&search= ';
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

	echo '<table class="noborder centpercent">';

	echo '<input type="hidden" name="max_rows" value="'. $max_rows .'" form="'. $form_id .'"/>';

	echo '<tr class="liste_titre">';

	echo '<th class="center">';
	echo 'Evaluación';
	echo '<div>';
	echo '<input name="search_eval" form="'.$form_id.'" value="'. GETPOST("search_eval") .'">';
	echo '</div>';
	echo '</th>';

	echo '<th class="center">';
	echo 'Tercero';
	echo '<div>';
	echo '<input name="search_third" form="'.$form_id.'" value="'. GETPOST("search_third") .'">';
	echo '</div>';
	echo '</th>';

	echo '<th class="center">';
	echo 'Orden de Compra';
	echo '<div>';
	echo '<input name="search_order" form="'.$form_id.'" value="'. GETPOST("search_order") .'">';
	echo '</div>';
	echo '</th>';

	echo '<th class="center">';
	echo 'Evaluada por';
	echo '</th>';

	echo '<th class="center">';
	echo 'Fecha';
	echo '<div>';
	echo '<input type="date" style="margin-right: 5px" name="date_start" form="'.$form_id.'" value="'. GETPOST("date_start") .'">';
	echo '<input type="date" name="date_end" form="'.$form_id.'" value="'. GETPOST("date_end") .'">';
	echo '</div>';
	echo '</th>';

	echo '<th class="center">';
	echo 'Calificación';
	echo '</th>';


	foreach($categorias as $categoria)
	{
		echo '<th class="center">';
		echo ucfirst($categoria->nombre);
		echo '</th>';
	}


	echo '<th class="center">';
	echo '<button type="submit" name="search" class="button_search" form="'.$form_id.'"><span class="fa fa-search"></span></button>';
	echo '<a href="./evalProv_index.php"><span class="fa fa-remove"></span></a>';
	echo '</th>';


	echo '</tr>';

	/**
	 * FILAS
	 */

	foreach($evaluaciones as $evaluacion)
	{
		$order = new CommandeFournisseur($db);
		$order->fetch($evaluacion->fk_orden);

		$third = new Societe($db);
		$third->fetch($order->socid);

		$evaluador = new User($db);
		$evaluador->fetch($evaluacion->fk_user_modif);

		echo '<tr>';

		// Referencia de la evaluación
		echo '<td class="center">';
		echo '<a href="'. DOL_URL_ROOT .'/custom/evalProv/tab_orden_evaluacion.php?id='. $order->id .'">';
		echo $evaluacion->ref;
		echo '</a>';
		echo '</td>';

		// Tercero
		echo '<td class="center">';
		echo '<a href="'. DOL_URL_ROOT .'/societe/card.php?id='. $third->id .'">';
		echo $third->name;
		echo '</a>';
		echo '</td>';

		// Orden de compra
		echo '<td class="center">';
		echo '<a href="'. DOL_URL_ROOT .'/fourn/commande/card.php?id='. $order->id .'">';
		echo $order->ref;
		echo '</a>';
		echo '</td>';

		// Usuario evaluador
		echo '<td class="center">';
		echo '<a href="'. DOL_URL_ROOT .'/user/card.php?id='. $evaluador->id .'">';
		echo $evaluador->firstname . ' ' . $evaluador->lastname;
		echo '</a>';
		echo '</td>';

		// Fecha
		echo '<td class="center">';
		echo $evaluacion->fecha;
		echo '</td>';

		$color;

		if ($evaluacion->calificacion <= 0)
		{
			$color = "";
		}
		else if ($evaluacion->calificacion >= 0 && $evaluacion->calificacion < 3)
		{
			$color = "badtr";
		}
		else if ($evaluacion->calificacion >= 3 && $evaluacion->calificacion < 4)
		{
			$color = "averagetr";
		}
		else 
		{
			$color = "goodtr";
		}

		echo '<td class="center">';
		echo '<div class="'. $color .'" style="border-radius: 5px; padding: 5px">';
		echo number_format((float)$evaluacion->calificacion, 1, '.', '');
		echo '</div>';
		echo '</td>';

		// Obtener los datos para cada categoría
		foreach($categorias as $categoria)
		{
			$sql = "SELECT ep.*, p.fk_categoria FROM `llx_evalprov_evaluacion_pregunta` as ep";
			$sql .= " INNER JOIN llx_evalprov_pregunta as p ON p.rowid = ep.fk_pregunta";
			$sql .= " WHERE p.fk_categoria = '". $categoria->id ."' AND ep.fk_evaluacion = '". $evaluacion->id ."'";
		
			$preguntas = $db->getRows($sql);

			$sum = 0;
			$avg = 0;

			if (count($preguntas) > 0)
			{
				foreach ($preguntas as $pregunta)
				{
					$sum += $pregunta->calificacion;
				}
			}
			
			if (count($preguntas) > 0)
			{
				$avg = $sum / count($preguntas);
			}

			$color;

			if ($avg <= 0)
			{
				$color = "";
			}
			else if ($avg >= 0 && $avg < 3)
			{
				$color = "badtr";
			}
			else if ($avg >= 3 && $avg < 4)
			{
				$color = "averagetr";
			}
			else 
			{
				$color = "goodtr";
			}

			echo '<td class="center">';
			echo '<div class="'. $color .'" style="border-radius: 5px; padding: 5px">';
			echo number_format((float)$avg, 1, '.', '');
			echo '</div>';
			echo '</td>';
		}

		echo '<td class="center">';
		echo '</td>';


		echo '</tr>';
	}

	echo '</table>';

	echo '</div>';
}
else
{
	echo "no tenes permisos che";
}


?>



<script>

    document.getElementById("max_rows").addEventListener("change", (e) => {

		let form = document.getElementById("pages_form");

		form.submit();

	})
    
    
	
</script>


<?php

	dol_fiche_end();


llxFooter();

$db->close();

