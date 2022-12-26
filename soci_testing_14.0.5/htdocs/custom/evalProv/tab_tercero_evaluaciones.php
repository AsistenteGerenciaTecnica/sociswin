<?php

/**
 *      \file       htdocs/custom/observaciones/tab_observacion.php
 *      \ingroup    comm
 *      \brief      Visualización de las observaciones asociadas a una intervención
 */

require '../../main.inc.php';
	
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';

require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

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

$title = $langs->trans('Evaluacion');
$morejs = array();
$helpurl = "ES:EvaluacionesDeProveedor";

$eval_ref = "EVAL-".$id;

// Security check
$socid=0;
if ($user->societe_id) $socid=$user->societe_id;

llxHeader('', $title, $helpurl, '', '', '', $morejs);

$back = DOL_URL_ROOT .'/custom/evalProv/tab_orden_evaluacion.php';

$form = new Form($db);

if ($id > 0 || !empty($ref))
{

    $object = new Societe($db);
    $object->fetch($id, $ref);

    $head = societe_prepare_head($object);

    dol_fiche_head($head, 'TabTerceroEvaluacion', $langs->trans('Thirdparty'), -1, 'companies');

    $linkback = '<a 
		href="' . 
			DOL_URL_ROOT . 
			'/societe/list.php?restore_lastsearch_values=1' . 
			(! empty($socid) ? 
			'&socid=' . $socid : 
			'') . 
			'">' . 
			$langs->trans("BackToList") . 
		'</a>';

    // Para los terceros sobra todo lo de $morehtmlref
    dol_banner_tab($object, 'socid', $linkback, ($user->socid ? 0 : 1), 'rowid', 'nom');

    echo '<link rel="stylesheet" href="./css/evaluaciones.css">';

    // Obtener las evaluaciones asocaidas al tercero
    $eval = new Evaluacion($db);
    $evaluaciones = $eval->getAllFromThird($id);

    // Obtener todas las categorías
    $cat = new Categoria($db);
    $categorias = $cat->getAll();

    /**
     * 
     * GRÁFICO
     * 
     */

    echo '<div class="fichehalfleft">';
    
    echo '<div class="eval_score">';

    /**
     * Obtener la calificación promedio de todas las evaluaciones
     * asociadas al tercero
     */

    $sum = 0;
    $avg = 0;

    foreach($evaluaciones as $evaluacion)
    {
        $sum += $evaluacion->calificacion;
    }

    if (count($evaluaciones) > 0)
    {
        $avg = $sum / count($evaluaciones);
    }


    if ($avg <= 0)
    {
        echo '<div class="eval_score_score">';
    }
    else if ($avg >= 0 && $avg < 3)
    {
        echo '<div class="eval_score_score bad">';
    }
    else if ($avg >= 3 && $avg < 4)
    {
        echo '<div class="eval_score_score average">';
    }
    else 
    {
        echo '<div class="eval_score_score good">';            
    }
    echo number_format((float)$avg, 1, '.', '');
    echo '</div>';
    
    echo '<div>';
    echo 'Calificación Promedio ';
    echo '</div>';

    echo '</div>';

    echo '<div style="height: 100%">';

    $avg_categorias = array();

    /**
     * Encontrar, dentro de las evaluaciones, la calificacion de 
     * preguntas asociadas con cada categoría, que además, 
     * estén también vinculadas al tercero
     */
    foreach($categorias as $categoria)
    {
        $sql = "SELECT ep.rowid";
        $sql .= ", ep.fk_evaluacion";
        $sql .= ", ep.fk_pregunta";
        $sql .= ", p.fk_categoria";
        $sql .= ", ep.calificacion";
        $sql .= ", e.fk_orden";
        $sql .= ", cf.fk_soc";
        $sql .= " FROM llx_evalprov_evaluacion_pregunta as ep";
        $sql .= " INNER JOIN llx_evalprov_evaluacion as e";
        $sql .= " ON e.rowid = ep.fk_evaluacion";
        $sql .= " INNER JOIN llx_commande_fournisseur as cf ";
        $sql .= " ON cf.rowid = e.fk_orden";
        $sql .= " INNER JOIN llx_evalprov_pregunta as p";
        $sql .= " ON p.rowid = ep.fk_pregunta";
        $sql .= " WHERE p.fk_categoria = '". $categoria->id ."' AND cf.fk_soc = '". $id ."'";

        $rows = $db->getRows($sql);

        $sum_cat = 0;
        $avg_cat = 0;
        if (count($rows) > 0)
        {
            foreach($rows as $row)
            {
                $sum_cat += $row->calificacion;
            }
        }

        if (count($rows) > 0)
        {
            $avg_cat = $sum_cat / count($rows);
        }

        $avg_categorias[] = array(strtoupper($categoria->nombre), $avg_cat);
    }


    /**
     * Para construir el gráfico se requiere un arreglo 
     * estructurado así:
     * 
     * SetData(
     *      array(
     *          array(
     *              [0] => Etiqueta
     *              [1] => Cantidad 1
     *              [2] => Cantidad 2
     *              [3] => Cantidad 3
     *          )
     *          array(
     *              [0] => Etiqueta
     *              [1] => Cantidad 1
     *          )
     *      )
     * )
     */

    include_once DOL_DOCUMENT_ROOT.'/core/class/dolgraph.class.php';
	$graph= new DolGraph();

    $graph->SetData($avg_categorias);

	$graph->setShowLegend(0);
	$graph->setShowPointValue(1);
	$graph->SetType(array('bars'));
	$graph->setHeight('100%');
	$graph->setWidth('100%');
	$graph->draw('graphic');
	$graphResult = $graph->show(0);

    echo $graphResult;

    echo '</div>';

    echo '</div>';

    /**
     * FIN GRÁFICO
     */


    /**
     * 
     * TABLA
     * 
     */

    echo '<div class="fichehalfright">';
    
    echo '<div style="display:flex; justify-content: space-between; margin-bottom: 15px">';
	echo '<div class="titre inline-block">Evaluaciones</div>';
	echo '</div>';
    
    echo '<table class="noborder centpercent">';

    /**
     * HEADERS
     */
    #region HEADERS

    echo '<tr class="liste_titre">';

    // Referencia de la evaluación
	echo '<th class="center">';
	echo 'Evaluación';
	echo '</th>';

    // Orden de compra
	echo '<th class="center">';
	echo 'Orden de Compra';
	echo '</th>';

    // Usuario que evaluó la orden de compra
	echo '<th class="center">';
	echo 'Evaluada por';
	echo '</th>';

    // Fecha en que se guardó por última vez la evaluación
	echo '<th class="center">';
	echo 'Fecha';
	echo '</th>';
 
    // Calificación promedio de la evaluación
	echo '<th class="center">';
	echo 'Calificación';
	echo '</th>';

    // Una columna para cada categoría existente
    foreach($categorias as $categoria)
    {

        echo '<th class="center">';
        echo ucfirst($categoria->nombre);
        echo '</th>';

    }

    echo '</tr>';

    #endregion

    /**
     * FILAS
     */
    #region FILAS

    foreach($evaluaciones as $evaluacion)
    {
        $order = new CommandeFournisseur($db);
        $order->fetch($evaluacion->fk_orden);

        $user = new User($db);
        $user->fetch($evaluacion->fk_user_modif);

        echo '<tr>';

        // Evaluación
        echo '<td class="center">';
        echo '<a href="'. DOL_URL_ROOT .'/custom/evalProv/tab_orden_evaluacion.php?id='. $order->id .'">';
        echo $evaluacion->ref;
        echo '</a>';
        echo '</td>';
        
        // Orden de compra
        echo '<td class="center">';
        echo '<a href="'. DOL_URL_ROOT .'/fourn/commande/card.php?id='. $order->id .'">';
        echo $order->ref;
        echo '</a>';
        echo '</td>';

        // Usuario
        echo '<td class="center">';
        echo '<a href="'. DOL_URL_ROOT .'/user/card.php?id='. $user->id .'">';
        echo $user->firstname . ' ' . $user->lastname;
        echo '</a>';
        echo '</td>';

        // Fecha
        echo '<td class="center">';
        echo date("d-m Y", strtotime($evaluacion->fecha));
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
        
        // Calificación promedio de la evaluación
        echo '<td class="center">';
        echo '<div class="'. $color .'" style="border-radius: 5px; padding: 5px">';
        echo number_format((float)$evaluacion->calificacion, 1, '.', '');
        echo '</div>';  
        echo '</td>';

        /**
         * Obtener el valor promedio de cada categoría dentro de 
         * la evaluación
         */
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

        echo '</tr>';
    }

    #endregion 

    echo '</table>';

    echo '</div>';
        


}


?>

<script>

    let checkboxes = document.querySelectorAll('input[type=checkbox]');

    for (let i = 0; i < checkboxes.length; i++){

        let checkbox = checkboxes[i];

        checkbox.addEventListener('change', function(e){
            if (checkbox.checked) {
                checkbox.value = "5";
            } else {
                checkbox.value = "1";
            }
        });
    }

    const scores = document.getElementsByClassName('score');

    for (let i = 0; i < scores.length; i++) {

        let score = scores[i];

        score.addEventListener('change', function(e){
            updateScore();
        });

    }

    function updateScore(){

        let avg_score = document.getElementById('avg_score');

        let sum = 0;

        for (let i = 0; i < scores.length; i++) { 

            sum += parseFloat(scores[i].value);
            console.log("value: " + scores[i].value)            

        }

        let avg = (sum / scores.length).toFixed(1);

        avg_score.innerHTML = avg;
    }

    updateScore();

    function showModal() {

        let modal = document.getElementById("modal");

        modal.style.display = "flex";   	 
    }

    function hideModal() {

        let modal = document.getElementById("modal");

        modal.style.display = "none";
    }
    
    
	
</script>


<?php

	dol_fiche_end();


llxFooter();

$db->close();

