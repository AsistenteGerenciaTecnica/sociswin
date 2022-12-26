<?php

/**
 * LISTADO
 * 
 * Listado de todas las intervenciones
 * Incluye una barra de búsqueda
 */


include_once "./interventor_header.php";

?>

<div class="container">
    <div style="height: 10px; margin-bottom: 15px">
    <?php
        $from = (isset($_GET["from"]) ? $_GET["from"] : "interventor_index.php");
        echo '<a class="btn_small" href="'. $from .'"><</a>'
    ?>
    </div>
    <div class="title">
        INTERVENTOR
    </div>
    <div class="subtitle">
        Listado de Intervenciones
    </div>
    <form action="">
        <div class="search_bar">
            <?php
            echo '<input class="main_input" name="search_ref" type="text" value="'. $_GET["search_ref"] .'">';
            ?>
            <input type="submit" name="search">
        </div>
    </form>
    <div class="content">
        

<?php

require_once DOL_DOCUMENT_ROOT.'/fichinter/class/fichinter.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';

$sql = "SELECT rowid, fk_soc, fk_projet, fk_contrat, ref, fk_statut, description";
$sql .= " FROM llx_fichinter";
$sql .= " WHERE fk_statut < 2";

// En caso que se haya hecho una búsqueda, la añade a las condiciones de la consulta
if (GETPOST("search_ref")) {
    $sql .= " AND ref LIKE '%" . GETPOST("search_ref") . "%'"; 
}

$sql .= " ORDER BY rowid DESC";

// getRows recibe la sentencia SQL y retorna las filas en forma de array
$result = $db->getRows($sql);


// Se verifica que se haya obtenido un resultado al hacer la consulta

if ($result){

    if (count($result) > 1){
        echo '<div class="list_table">';
    
        echo '<table class="this_table">';

        // Headers
        echo '<tr class="table_header">';
    
        echo '<td><div>Estado</div></td>';
        echo '<td><div>ID</div></td>';
        echo '<td><div>Descripción</div></td>';
        echo '<td><div>Proyecto</div></td>';
        echo '<td><div>Contrato</div></td>';

        echo '</tr>';

        // Recorrer las filas del resultado
        for ($i = 0; $i < count($result); $i++){

            $id = $result[$i]->rowid;
            $ref = $result[$i]->ref;
            $description = $result[$i]->description;
            $project_id = $result[$i]->fk_projet;
            $contract_id = $result[$i]->fk_contrat;

            // Obtiene el proyecto
            if ($project_id > 0){

                $project = new Project($db);

                $project->fetch($project_id);

                $project_ref = $project->ref;

            } else {

                $project_ref = "Sin Proyecto";

            }

            // Obtiene el contrato
            if ($contract_id > 0){

                $contract = new Contrat($db);

                $contract->fetch($contract_id);

                $contract_ref = $contract->ref;

            } else {

                $contract_ref = "Sin Contrato";

            }

            echo '<tr class="table_row">';

            // Estado
            // Dependiendo del estado, un color de fondo diferente para la celda
            if ($result[$i]->fk_statut == "0"){

                echo '<td style="background-color: rgba(190, 190, 190, 1)">';
                echo '<div>'. Borrador .'</div>';
                echo '</td>';

            } else if ($result[$i]->fk_statut == "1"){

                echo '<td style="background-color: rgba(6, 178, 0, 0.5)">';
                echo '<div>'. Validado .'</div></td>';

            }

            // ID de la intervención
            echo '<td>';
            echo '<div>';
            echo '<a href="./interventor_card.php?id='. $id .'&from=interventor_list.php">'. $ref. '</a>';
            echo '</div>';
            echo '</td>';

            // Descripción
            echo '<td>';
            echo '<div style="font-size: 12pt">'. $description .'</div>';
            echo '</td>';

            // Proyecto 
            echo '<td>';
            echo '<div>';
            if ($project_id > 0) {

                echo '<a href="'. DOL_URL_ROOT .'/projet/card.php?id='. $project_id .'">'. $project_ref .'</a>';

            } else {

                echo $project_ref;

            }    
            echo '</div>';
            echo '</td>';

            // Contrato
            echo '<td>';
            echo '<div>';
            if ($contract_id > 0){

                echo '<a href="'. DOL_URL_ROOT .'/contrat/card.php?id='. $contract_id .'">'. $contract_ref .'</a>';

            } else {

                echo $contract_ref;

            }
            echo '</div>';
            echo '</td>';


            echo '</tr>';
        }

        echo '</table>';

        echo '</div>';
    }
    else
    {
        header("Location: ./interventor_card.php?id=". $result[0]->rowid);
    }

    
} else {
    echo '<div class="subtitle">No se encontraron intervenciones</div>';
}

?>           
    </div>
</div>

<?php

include_once "./interventor_footer.php";

?>