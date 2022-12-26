<?php

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/evalProv/class/evaluacion.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/evalProv/class/pregunta.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/evalProv/class/categoria.class.php';

// Guardar evaluación
if (isset($_POST['save']))
{

    $order_id = GETPOST("order_id");
    $user = GETPOST('user');
    $back = GETPOST('back');

    $preg = new Pregunta($db);
    $allPregs = $preg->getAll();

    $listPregs = array();

    /**
     * Para actualizar la calificación de la evaluación,
     * es necesario conocer qué calificación se obtuvo
     * en cada una de las preguntas, por lo que se obtienen
     * todas las respuestas y se guardan en varios arreglos,
     * siguiendo la construcción del formulario en HTML.
     * 
     * Se toman todas las preguntas, y se verifica dentro del
     * POST si existen los parámetros necesarios, a partir
     * de la id de la pregunta 
     */

    foreach ($allPregs as $pregunta)
    {
        $id = GETPOST('pregunta_'.$pregunta->id);
        $calificacion = GETPOST('calificacion_'.$pregunta->id);
        $comentario = GETPOST('comentario_'.$pregunta->id);

        if ($calificacion == '')
        {
            $calificacion = '1';
        }

        $listPregs[] = array(
            "id" => $id,
            "calificacion" => $calificacion,
            "comentario" => $comentario
        );
    }

    $evaluacion = new Evaluacion($db);

    // La codificación actual de la evaluación es EVAL-[id de la orden]

    $ref = 'EVAL-'. $order_id;

    $exists = $evaluacion->fetch(0, $ref);

    $eval_res = 0;

    /**
     * Si ya existe una evaluación con esa referencia, se va a actualizar,
     * si no, se crea una nueva
     */

    if ($evaluacion->id > 0)
    {
        $evaluacion->tms = date('Y-m-d H:i:s');
        $evaluacion->fecha = date('Y-m-d H:i:s');
        
        $eval_res = $evaluacion->update($user);
    }
    else
    {
        $evaluacion->fk_orden = $order_id;
        $evaluacion->ref = $ref;
        $evaluacion->calificacion = 0;
        $evaluacion->fecha = date('Y-m-d H:i:s');
        $evaluacion->date_creation = date('Y-m-d H:i:s');
        $evaluacion->tms = date('Y-m-d H:i:s');

        $eval_res = $evaluacion->create($user);
        
    }

    $ok = 0;
    $ko = 0;

    if ($eval_res > 0)
    {
        foreach($listPregs as $pregunta)
        {
            $id_pregunta = $pregunta["id"];
            $calificacion = $pregunta["calificacion"];
            $comentario = $pregunta["comentario"];
    
            $res = $evaluacion->evaluar($user, $id_pregunta, $calificacion, $comentario);

            if ($res > 0)
            {
                $ok++;
            }
            else
            {
                $ko++;
            }
        }
    }

    echo "<br> ok:". $ok;
    echo "<br> ko:". $ko;

    header('Location: '. $back . '?id='. $order_id .'&ok='. $ok . '&ko=' . $ko);

}
// Eliminar evaluación
else if (isset($_POST['delete']))
{

    $eval_ref = GETPOST("eval_ref");
    $order_id = GETPOST("order_id");
    $back = GETPOST("back");

    $eval = new Evaluacion($db);
    $eval->fetch(0, $eval_ref);

    $res = $eval->delete();

    header('Location: '. $back . '?id='. $order_id .'&deleted='. $res);

}
else
{
    if (isset($_POST['back'])){
        header('Location: '. $back);
    } else {
        header('Location: '. DOL_URL_ROOT .'/custom/evalProv/evalProv_index.php');
    }
}