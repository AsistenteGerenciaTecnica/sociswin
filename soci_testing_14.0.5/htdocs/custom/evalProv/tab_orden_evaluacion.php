<?php

/**
 *      \file       htdocs/custom/evalProv/tab_orden_evaluacion.php
 *      \ingroup    comm
 *      \brief      Pestaña para evaluar órdenes de compra
 */

require '../../main.inc.php';
	
// Terceros
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
// Proyectos
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';

// Órdenes de compra
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


if ($user->rights->evalProv->evaluacion->read)
{
    
    $back = DOL_URL_ROOT .'/custom/evalProv/tab_orden_evaluacion.php';

    echo '<link rel="stylesheet" href="./css/evaluaciones.css">';

    /**
     * MODAL
     */
    #region MODAL
    echo '<div class="modal" id="modal" style="display: none">';

    echo '<div class="modal_container">';

    echo '<div class="modal_title">Eliminar</div>';

    echo '<div class="modal_content">';

    echo '<div>¿Estás seguro de eliminar esta evaluación? Se eliminarán también los registros de calificación</div>';
    
    echo '<form method="POST" action="'. DOL_URL_ROOT .'/custom/evalProv/inc/orden_evaluacion.inc.php">';
    
    echo '<div class="modal_bottom">';
    
    echo '<input type="hidden" name="eval_ref" id="eval_ref" value="'. $eval_ref .'"/>';
    echo '<input type="hidden" name="order_id" value="'. $id .'"/>';
    echo '<input type="hidden" name="back" value="'. $back .'"/>';
    
    echo '<input type="submit" class="modal_button" name="delete" value="Si"></input>';
    echo '<input type="button" class="modal_button" value="No" onclick="hideModal()"></input>';

    echo '</div>';

    echo '</form>';

    echo '</div>';

    echo '</div>';

    echo '</div>';
    #endregion
    /**
     * FIN MODAL
     */
    

    $form = new Form($db);
    
    if ($id > 0 || !empty($ref))
    {
    
        $object = new CommandeFournisseur($db);
        $object->fetch($id, $ref);
    
        $head = ordersupplier_prepare_head($object);
    
        dol_fiche_head($head, 'TabOrdenEvaluacion', $langs->trans('SupplierOrder'), -1, 'order');
    
        // Dirección de regreso al listado
        $linkback = '<a 
            href="' . 
                DOL_URL_ROOT . 
                '/custom/evalProv/evalProv_index.php?restore_lastsearch_values=1' . 
                (! empty($socid) ? 
                '&socid=' . $socid : 
                '') . 
                '">' . 
                $langs->trans("BackToList") . 
            '</a>';
    
        $morehtmlref='<div class="refidno">';
    
        $morehtmlref .= $form->editfieldkey(
            "RefSupplier", 
            'ref_supplier', 
            $object->ref_supplier, 
            $object, 
            false, 
            'string', 
            '', 
            0, 
            1
        );
        $morehtmlref .= $form->editfieldval(
            "RefSupplier", 
            'ref_supplier', 
            $object->ref_supplier, 
            $object, 
            false, 
            'string', 
            '', 
            null, 
            null, 
            '', 
            1);
    
        $object->fetch_thirdparty();
        $morehtmlref .= '<br>'.$langs->trans('ThirdParty');
        $morehtmlref .= ' : '.$object->thirdparty->getNomUrl(1);
    
        $morehtmlref .= '<br>'.$langs->trans('Project').' : ';
    
        if (!empty($object->fk_project)) {
            $proj = new Project($db);
            $proj->fetch($object->fk_project);
            $morehtmlref .= '<a href="'.DOL_URL_ROOT.'/projet/card.php?id='.$object->fk_project.
            '" title="'.$langs->trans('ShowProject').'">';
            $morehtmlref .= $proj->ref;
            $morehtmlref .= '</a>';
        } else {
            $morehtmlref .= '';
        }
    
        $morehtmlref.='</div>';
    
        dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);
    
        // Título
        echo '<div style="display: flex; align-items: center; margin: 10px 10px 10px 0px">';
        echo '<span class="fas fa-building valignmiddle widthpictotitle pictotitle" style=" color: #6c6aa8;"></span>';
        echo '<div class="titre">Formato de Evaluación</div> ';
        echo '</div>';
    
        $eval = new Evaluacion($db);
    
        $eval->fetch(0, $eval_ref);
    
        $is_evaluated = false;
    
        if ($eval->id != "")
        {
            $is_evaluated = true;
        }
        
        /**
         * DETALLES DE LA EVALUACIÓN
         */
        #region DETALLES
    
        echo '<div class="eval_info">';
    
        echo '<div class="eval_score">';
        
        /**
         * Si ya se ha evaluado, muestra la calificación actual
         * con un color 
         */
        if ($is_evaluated) 
        {
            if ($eval->calificacion >= 0 && $eval->calificacion < 3)
            {
                echo '<div class="eval_score_score bad">';
            }
            else if ($eval->calificacion >= 3 && $eval->calificacion < 4)
            {
                echo '<div class="eval_score_score average">';
            }
            else 
            {
                echo '<div class="eval_score_score good">';            
            }
            echo number_format((float)$eval->calificacion, 1, '.', '');
            echo '</div>';
    
            echo '<div>';
            echo 'Calificación Actual';
            echo '</div>';
        } 
        else 
        {
            echo '<div class="eval_score_score">';
            echo '0.0';
            echo '</div>';
            
            echo '<div>';
            echo 'Sin calificar ';
            echo '</div>';
        }
    
        echo '</div>';
    
        // También muestra el nombre del evaluador, y la fecha en que se evaluó
        if ($is_evaluated)
        {
            echo '<div style="display: flex; flex-direction: column; height: 100%; align-content: space-between">';
        
                echo '<div style="display: flex; font-size: 14pt">';
            
                    echo '<div>';
                    echo 'Evaluado por:';
                    echo '</div>';
        
                    echo '<div style="margin-left: 5px">';
        
                    $evaluador = new User($db);
                    $evaluador->fetch($eval->fk_user_modif);
        
                    echo '<a href="'. DOL_URL_ROOT .'/user/card.php?id='. $evaluador->id .'">';
                    echo $evaluador->firstname .' '. $evaluador->lastname;
                    echo '</a>';
                    echo '</div>';
    
                echo '</div>';
    
                echo '<div>';
                echo $eval->fecha;
                echo '</div>';
        
            echo '</div>';
        }
    
        echo '</div>';
        #endregion
        /**
         * FIN DETALLES
         */
        
        /**
         * TABLA
         */
    
        $form_id = "eval_form";
    
        echo '<form method="POST" action="./inc/orden_evaluacion.inc.php" id="'. $form_id .'">';
    
        echo '<table class="eval_table">';
    
        /**
         * HEADERS
         */
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
        echo 'COMENTARIO';
        echo '</div>';
        echo '</th>';
    
        echo '</tr>';
    
        $cat = new Categoria($db);
    
        $listCategorias = $cat->getAll();
    
        /**
         * Si ya se evaluó, obtiene los resultados de todas
         * las preguntas asociadas a la evaluación
         */
        if ($is_evaluated)
        {
            $sql = "SELECT p.rowid, p.pregunta, p.tipo, p.fk_categoria, ep.comentario, ep.calificacion FROM llx_evalprov_pregunta as p";
            $sql .= " LEFT JOIN (SELECT ep.comentario, ep.calificacion, ep.fk_evaluacion, ep.fk_pregunta FROM `llx_evalprov_evaluacion_pregunta` as ep";
            $sql .= " WHERE ep.fk_evaluacion = '". $eval->id ."') as ep";
            $sql .= " ON ep.fk_pregunta = p.rowid";    
        
            $pregs = $db->getRows($sql);
        }
        
        foreach($listCategorias as $categoria) 
        {
            if ($is_evaluated)
            {
                $preguntas = array();
                
                foreach ($pregs as $preg)
                {
                    if ($preg->fk_categoria == $categoria->id)
                    {
                        $preguntas[] = $preg;
                    }
                }
            }
            // Si no se había evaluado, simplemente imprime las preguntas
            else 
            {
                $sql = "SELECT * FROM llx_evalprov_pregunta";
                $sql .= " WHERE fk_categoria = '". $categoria->id ."'";
        
                $preguntas = $db->getRows($sql);
            }
    
    
            /**
             * CATEGORÍA
             */
            echo '<tr class="eval_row">';
    
            /**
             * La cantidad de filas que ocupa la categoría
             * es la cantidad de preguntas asociadas a esta + 1
             */
            echo '<th rowspan="'. ($preguntas != 0 ? count($preguntas) + 1 : 1) .'">';
    
            echo '<div style="flex-direction: column">';
    
            echo '<div>'. strtoupper($categoria->nombre) .'</div>';
    
            echo '</div>';
    
            echo '</th>';
    
            echo '</tr>';
    
            if (count($preguntas) != 0){
    
            
                /**
                 * Cuando la evaluación ya ha sido realizada, se añade
                 * la propiedad disabled a las entradas de la evaluación,
                 * para que no sean editadas.
                 * Algunos botones también están desactivados si no se 
                 * cuenta con los permisos
                 */

                foreach($preguntas as $pregunta) 
                {
                    echo '<tr class="eval_row">';
                    echo '<td class="col_pregunta">';
                    echo '<div>'. $pregunta->pregunta .'</div>';
                    echo '</td>';
    
                    /**
                     * Para poder enviar los datos de cada una de las preguntas,
                     * se construyen las entradas con la propiedad name:
                     * "calificacion_id" o "comentario_id", además, una entrada
                     * oculta nombrada "pregunta_id", para manetener también
                     * la identificación de la pregunta
                     */

                    echo '<td class="col_calificacion">';
                    echo '<div>';
                    if ($pregunta->tipo == "selection")
                    {
                        echo '<select class="score evalInput" name="calificacion_'. $pregunta->rowid .'"'. (($is_evaluated && !isset($_GET["re_evaluating"])) || !$user->rights->evalProv->evaluacion->write ? 'disabled' : '') .'>';
    
                        for ($i = 1; $i <= 5; $i++){
                            echo '<option value="'.$i.'"'. ($pregunta->calificacion == $i ? 'selected' : '') .'>'.$i.'</option>';
                        }
    
                        echo '</select>';
                    }
                    elseif ($pregunta->tipo == "check")
                    {
                        echo '<input type="checkbox" class="score evalCheckBox" name="calificacion_'. $pregunta->rowid .'"'. ($pregunta->calificacion == '5' ? 'value="5" checked' : 'value="1"') .' '. (($is_evaluated && !isset($_GET["re_evaluating"])) || !$user->rights->evalProv->evaluacion->write ? 'disabled' : '') .'>';				
                    }
                    echo '</div>';
    
                    echo '<td class="col_comentario">';
    
                    echo '<div>';
                    
                    echo '<textarea name="comentario_'. $pregunta->rowid .'"'. (($is_evaluated && !isset($_GET["re_evaluating"])) || !$user->rights->evalProv->evaluacion->write ? 'disabled' : '') .'>'. $pregunta->comentario .'</textarea>';
    
                    echo '<input type="hidden" name="pregunta_'. $pregunta->rowid .'" value="'. $pregunta->rowid .'"/>';
    
                    echo '</div>';
    
                    echo '</td>';
    
                    echo '</tr>';
                
                }
            }
        }
        echo '<tr class="eval_row">';
    
        echo '<td class="col_categoria col_none">';
        echo '</td>';
    
        echo '<td class="col_pregunta col_none">';
        echo '</td>';
    
        echo '<th class="col_calificacion">';
    
        echo '<div id="avg_score" style="font-size: 20pt">';
        echo '0';
        echo '</div>';
    
        echo '</th>';
    
        echo '<td class="col_comentario ">';
    
        echo '<div>';
    
        echo '<input type="hidden" name="order_id" value="'. $id .'"/>';
        echo '<input type="hidden" name="user" value="'. $user->id .'"/>';
        echo '<input type="hidden" name="back" value="'. $back .'"/>';
        

        /**
         * El botón guardar se activa cuando no se ha evaluado
         * o cuando se está re-evaluando
         */

        if (!$is_evaluated || isset($_GET["re_evaluating"]))
        {
            echo '<button class="butAction table_btn" type="submit" name="save" '. (!$user->rights->evalProv->evaluacion->write ? 'disabled' : '' ).'>Guardar</button>';
            
            // En el caso de ya haberse evaluado, también se muestra el botón cancelar
            if ($is_evaluated)
            {
                echo '<input type="hidden" name="id" value="'. $id .'" form="form_cancel"/>';
                echo '<button class="butAction table_btn" name="cancel" form="form_cancel">Cancelar</button>';                    
            }
        }
        else
        /**
         * Se muesra el botón de reevaluar o de eliminar la aevaluación
         */
        {
            echo '<input type="hidden" name="id" value="'. $id .'" form="form_re_evaluate"/>';
            echo '<button class="butAction table_btn" name="re_evaluating" form="form_re_evaluate" '. (!$user->rights->evalProv->evaluacion->update ? 'disabled' : '' ).'>Re-Evaluar</button>';
    
            echo '<button class="butAction table_btn" type="button" onclick="showModal()" '. (!$user->rights->evalProv->evaluacion->delete ? 'disabled' : '' ).'>Eliminar</button>';
        }
        echo '</div>';
    
        echo '</td>';
        
        echo '</tr>';
        echo '</table>';
    
        echo '</form>';
        
        echo '<form method="GET" id="form_re_evaluate">';
        echo '</form>';
        echo '<form method="GET" id="form_cancel">';
        echo '</form>';
    
        
    
    
    }
}
else
{
    echo "Permisos insuficientes";
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

