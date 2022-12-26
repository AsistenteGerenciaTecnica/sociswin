<?php

/**
 * TARJETA DE INTERVENCIÓN
 * 
 * Vista simplificada de la tarjeta de intervención
 * Incluye:
 * - Vínculo a la intervención en la plataforma completa
 * - Vínculo al proyecto y contrato asociados a la intervención
 * - Listado con las observaciones relacionadas
 * - Creación de observaciones
 * - Modificación de observaciones
 * - Eliminación de observaciones
 * 
 * Todos los formularios que se dirigen a "interventor.inc.php" requieren
 * del parámetro "back", esto para que se pueda regresar a la página
 * desde la que se llamó al archivo, ya sea desde Interventor, o desde
 * la pestaña de observaciones en la plataforma
 */



include_once "./interventor_header.php";
// Clase Intervenciones
require_once DOL_DOCUMENT_ROOT.'/fichinter/class/fichinter.class.php';
// Clase Observaciones
require_once DOL_DOCUMENT_ROOT.'/custom/observaciones/class/observacion.class.php';
// Clase Proyectos
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
// Clase Contratos
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';


$id = GETPOST("id");
$from = (isset($_GET["from"]) ? $_GET["from"] : "interventor_list.php");

$inter = new Fichinter($db);
$inter->fetch($id);

// Se obtiene la información del proyecto
if ($inter->fk_project > 0){
    $project = new Project($db);
    $project->fetch($inter->fk_project);
}

// Se obtiene la información del contrato
if ($inter->fk_contrat > 0) {
    $contract = new Contrat($db);
    $contract->fetch($inter->fk_contrat);
}

$back = DOL_URL_ROOT .'/custom/observaciones/interventor/interventor_card.php';



?>

<div class="modal" id="modal">
    <div class="modal_container">
        <div class="modal_title">Eliminar</div>
        <div class="modal_content">
            <div>¿Estás seguro de eliminar esta línea?</div> 
            <div>Descripción: <span id="modal_descripcion">xd</span></div>
            <?php
            echo '<form method="POST" action="'. DOL_URL_ROOT .'/custom/observaciones/inc/observaciones.inc.php">';
            ?>
                
                <div class="modal_bottom">
                    <?php
                    echo '<input type="hidden" name="back" value="'. $back .'"/>';
                    echo '<input type="hidden" id="modal_id" name="id" value="'. $id .'">';
                    echo '<input type="hidden" id="modal_from" name="from" value="'. $from .'">';
                    ?>                    
                    <input type="hidden" id="modal_line_id" name="line_id" value="">
                    
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

<div class="modal" id="sign_modal" style="display:none">
    <div class="modal_container">
        <div class="modal_title">Firmar</div>
        <div class="modal_content">
            <div class="canvas_container">
                <canvas id="canvas">
                
                </canvas>
            </div>
            </div>
            <div class="modal_bottom">
                <?php
                echo '<form method="POST" name="canvas_form" action="'. DOL_URL_ROOT .'/custom/observaciones/inc/observaciones.inc.php">';
                ?>
                    <input type="hidden" id="canvas_content" name="canvas_content" value="">
                    <input type="hidden" id="sign_type" name="sign_type" value="">
                    <?php
                    echo '<input type="hidden" name="back" value="'. $back .'"/>';
                    echo '<input type="hidden" name="id" value="'. $id .'">';
                    echo '<input type="hidden" id="modal_from" name="from" value="'. $from .'">';
                    ?>
                </form>

                <input type="submit" class="modal_button" id="save" name="saveSign" value="Guardar"></input>
                <input type="button" class="modal_button" id="clear" value="Limpiar"></input>
                <input type="button" class="modal_button" value="Cancelar" onclick="hideSignModal()"></input>
            </div>
        </div>
    </div>
</div>



<div class="container">
    <div style="height: 10px; margin-bottom: 15px">

    <?php
    
    echo '<a class="btn_small" href="'. $from .'"><</a>';

    ?>

    </div>
    <div class="title">
        INTERVENTOR
    </div>

    <div class="content">

<?php

// Inicio Card
echo '<div class="inter_card">';

// Título
echo '<div class="inter_top">';
echo '<div class="inter_title">'. $inter->ref .'</div>';
echo '<a class="btn_inter_plus" href="'. DOL_URL_ROOT .'/custom/observaciones/tab_observaciones.php?id='. $id .'">+</a>';
echo '</div>';

#region DETALLES

/**
 * DETALLES
 */

echo '<div class="inter_details">';

// Descripción
echo '<div>Descripción: '. $inter->description .'</div>';

// Creación
echo '<div>Creación: '. date("Y-m-d", $inter->datec) .'</div>';

// Proyecto
echo '<div>Proyecto: ';
if ($inter->fk_project > 0){
    
    echo '<a href="'. DOL_URL_ROOT .'/projet/card.php?id='. $project->id .'">'. $project->ref .'</a>';

} else {
    echo "Sin Proyecto";
}
echo '</div>';

// Contrato
echo '<div>Contrato: ';
if ($inter->fk_contrat > 0){
    
    echo '<a href="'. DOL_URL_ROOT .'/contrat/card.php?id='. $contract->id .'">'. $contract->ref .'</a>';

} else {
    echo "Sin Contrato";
}
echo '</div>';

echo '</div>';

/** 
 * FIN DETALLES
 * */ 

#endregion

/**
 * FIRMAS
 */

echo '<div>';

echo '<table class="sign_table">';

echo '<tr class="sign_row">';

echo '<td>';
echo '<div>';
echo '<button class="btn_list btn_sign" onclick="showSignModal('. "'inter'" .')">FIRMA INTERVENTOR</button>';
echo '</div>';
echo '</td>';

echo '<td>';
echo '<div>';
echo '<button class="btn_list btn_sign" onclick="showSignModal('. "'third'" .')">FIRMA TERCERO</button>';
echo '</div>';
echo '</td>';

echo '</tr>';

echo '<tr class="sign_row">';

echo '<td>';
$inter_sign = '/custom/observaciones/upload/signatures/'. $id .'/sign-inter-'. $id .'.png';
if (file_exists(DOL_DOCUMENT_ROOT . $inter_sign)){
    echo '<img src='. DOL_URL_ROOT . $inter_sign .' style="width: 150px"></img>';
} else {
    echo "Sin firma aun";
}
echo '</td>';

echo '<td>';
$third_sign = '/custom/observaciones/upload/signatures/'. $id .'/sign-third-'. $id .'.png';
if (file_exists(DOL_DOCUMENT_ROOT . $third_sign)){
    echo '<img src='. DOL_URL_ROOT . $third_sign .' style="width: 150px"></img>';
} else {
    echo "Sin firma aun";
}
echo '</td>';

echo '</tr>';

echo '</table>';

echo '</div>';

/**
 * FIN FIRMAS
 */

#region OBSERVACIONES
/**
 * 
 * INICIO OBSERVACIONES
 * 
 */
echo '<div class="inter_act">';

echo '<div class="table_title">';
echo '<div class="inter_title">Observaciones</div>';

// Botón nueva observación
echo '<form method="GET">';
echo '<input type="hidden" name="from" value="'. $from . '"/>';
        echo '<input type="hidden" name="row" value="'. $i .'"/>';
        echo '<input type="hidden" name="id" value="'. $id .'"/>';
        echo '<input type="hidden" name="line_id" value="'. $line->id .'"/>';
echo '<button class="btn_inter_plus" name="new">+</button>';

echo '</form>';
echo '</div>';

// Tabla
echo '<div class="list_table">';

echo '<table class="this_table">';

// Headers
echo "<tr class='table_header'>";
    
echo "<td>Descripción</td>";
echo "<td style='min-width: 100px'>Fecha</td>";
/* echo "<td>Duración</td>"; */
echo "<td>Imagen</td>";
echo "<td></td>";

echo "</tr>";

/**
 * FILAS
 */
$obs = new Observacion($db);
$lines = $obs->getAll($id);

for ($i = 0; $i < count($lines); $i++){

    $line = $lines[$i];

    // Se descompone la duración en horas y minutos
    // para mostrarlas de una mejor manera
    $init_duracion = $line->duracion;
    $hours = floor($init_duracion / 3600);
    $minutes = floor(($init_duracion / 60) % 60);
    //$seconds = $init_duracion % 60;

    $duracion = sprintf("%02d:%02d" ,$hours, $minutes);

    $editing = GETPOST("editing", "int");

    /** 
     * 
     * SI NO SE ESTÁ EDITANDO LA LÍNEA
     * 
     * */ 
    if ($editing != $line->id || !isset($_GET["editing"])){
        echo '<tr class="table_row">';
    
        // Descripción
        echo '<td><div>'. $line->descripcion .'</div></td>';

        // Fecha y Hora
        echo '<td><div>'. $line->fecha .'</div></td>';

        // Duración
        // echo '<td><div>'. $duracion .'</div></td>';

        // Imagen
        echo '<td><div>';
        if ($line->filename && $line->filename != "NULL"){
            echo '<img src="'. $line->filename .'" style="max-height: 100px"></img>';
        } else {
            echo 'Sin imagen';
        }
        echo '</div></td>'; 

        echo '<td>';
        echo '<div>';

        // Botones
        echo '<form>';

        // Inputs ocultos que contienen información sobre la línea  
        echo '<input type="hidden" name="from" value="'. $from . '">';
        echo '<input type="hidden" name="editing" value="'. $line->id .'">';
        echo '<input type="hidden" name="id" value="'. $id .'">';
        echo '<input type="hidden" name="line_id" value="'. $line->id .'">';

        echo '<div class="row_options">';
        echo '<input type="submit" name="edit" class="edit_line" style="">';
        echo '<input type="button" name="delete" class="delete_line" style="margin-left: 10px" onclick="showModal('. "'". $id ."'," ."'". $line->id ."'," ."'". $line->descripcion ."'," ."'". $from ."'". ')">';
        echo '</div>';
        
        echo '</form>';

        echo '</div>';
        echo '</td>';

        echo '</tr>';
    /**
     * 
     * SI SE ESTÁ EDITANDO LA LÍNEA
     * 
     */
    } else {

        echo '<tr class="table_row">';
        echo '<form method="POST" action="'. DOL_URL_ROOT .'/custom/observaciones/inc/observaciones.inc.php" enctype="multipart/form-data">';
    
        /**
         * DESCRIPCIÓN
         */
        echo '';
        echo '<td><div><textarea name="descripcion" class="main_input" required oninvalid="this.setCustomValidity('. "'Por favor complete este campo'" .')" oninput="this.setCustomValidity('. "''" .')">'. $line->descripcion .'</textarea></div></td>';

        /**
         * FECHA Y HORA
         * 
         * Se decide dividir la fecha y hora en varios inputs para 
         * evitar complicaciones con el formato predefinido con el input type="datetime-local"
         */
        echo '<td><div>';
        echo '<input class="main_input" type="date" name="fecha" value="'. date("Y-m-d", strtotime($line->fecha)) .'">';
        echo '<input class="main_input" type="number" name="horas_fecha" min="0" max="23" placeholder="Hrs" style="width: 50px" value="'. date("H", strtotime($line->fecha)) .'">';
        echo '<input class="main_input" type="number" name="mins_fecha" min="0" max="59" placeholder="Min" style="width: 50px" value="'. date("i", strtotime($line->fecha)) .'">';
        echo '</div></td>';

        /**
         * DURACIÓN
         * 
         * Está comentada en caso que se deba llegar a utilizar, se consideró
         * innecesaria para las observaciones
         */
        /* echo '<td><div>';
        echo '<input class="main_input" type="number" name="horas_duracion" min="0" placeholder="Hrs" style="width: 50px" value="'. $hours .'">';
        echo '<input class="main_input" type="number" name="mins_duracion" min="0" max="59" placeholder="Min" style="width: 50px" value="'. $minutes     .'">';
        echo '</div></td>'; */

        /**
         * IMAGEN
         * 
         * Para subir imágenes se oculta el botón predeterminado del input type="file"
         * y se llama su funcionalidad desde otro botón
         */
        echo '<td><div class="image_cell">';
        echo '<button class="btn_list" type="button" onclick="' . "document.getElementById('uploadImage').click()" . '">Seleccionar Imagen</button>';
        echo '<input type="file" id="uploadImage" name="upload_image" style="display: none" onchange="updateFileName()" accept="image/*">';
        
        if ($line->filename && $line->filename != "NULL") {
            echo '<div class="file_name" id="file_name">Imagen Actual</div>';
            echo '<img id="image_preview" class="image_prev" src="'. $line->filename .'" alt="preview" style="display:block"></img>';        
        } else {
            echo '<div class="file_name" id="file_name">Ninguna imagen seleccionada</div>';
            echo '<img id="image_preview" class="image_prev" src="" alt="preview"></img>';        
        }
        echo '</div></td>'; 
        
        /**
         * BOTONES
         */
        
        echo '<td>';
        echo '<div>';

        echo '<input type="hidden" name="back" value="'. $back .'"/>';
        echo '<input type="hidden" name="id" value="'. $id .'">';
        echo '<input type="hidden" name="from" value="'. $from . '">';
        echo '<input type="hidden" name="line_id" value="'. $line->id .'">';
        
        /**
         * REQUIERE
         * 
         * back             Página a la que regresar
         * id               ID de la intervención
         * from             Página de la que viene el sitio actual
         * line_id          ID de la observación
         * 
         * upload_image     Imagen a subir
         * fecha            Fecha de la observación
         * horas_fecha      Hora
         * mins_fecha       Minutos
         * descripcion      Descripción
         */
        echo '<input class="btn_list" type="submit" name="update" value="Guardar">';
        
        echo '</form>';
        
        echo '<form method="GET">';
        echo '<input type="hidden" name="id" value="'. $id .'">';
        echo '<input type="hidden" name="from" value="'. $from . '"/>';
        echo '<input class="btn_list gray" type="submit" name="" value="Cancelar">';
        echo '</form>';
        
        echo '</div>';
        echo '</td>';

        echo '</tr>';
    }
}


echo '</table>';


echo '</div>';
// Fin Tabla

/**
 * 
 * NUEVA LÍNEA
 * 
 * La información requerida para líneas nuevas es prácticamente la misma
 * que para actualizarlas, y se recibe de la misma manera
 * 
 */
if (isset($_GET["new"])){
    echo '<div class="new_line">';

    echo '<div class="inter_title">Nueva Observación</div>';

    echo '<div class="new_line_form">';
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
    echo '<textarea name="new_descripcion" class="main_input" required oninvalid="this.setCustomValidity('. "'Por favor complete este campo'" .')" oninput="this.setCustomValidity('. "''" .')"></textarea>';
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
    echo '<input class="main_input" type="date" name="new_fecha" value="'. date("Y-m-d") .'">';
    // Hora y Minuto
    echo '<div style="margin-top: 5px">';
    echo '<input class="main_input" type="number" placeholder="Hrs" name="new_fecha_hora" min="0" max="23" value="'. date("H") .'">';
    echo '<input class="main_input" type="number" placeholder="Min" name="new_fecha_min" min="0" max="59" value="'. date("i") .'">';
    echo '</div>';
    echo '</div>';
    echo '</td>';

    echo '</tr>';

    /**
     * DURACIÓN
     */
    /* echo '<tr class="form_row">';

    echo '<td>';
    echo '<div>Duración</div>';
    echo '</td>';

    echo '<td>';
    // Horas
    echo '<input class="main_input" type="number" name="new_duracion_horas" min="0" style="width: 20%" value="0">';
    // Minutos
    echo '<input class="main_input" type="number" name="new_duracion_mins" min="0" max="59" style="width: 20%" value="0">';
    echo '</td>';

    echo '</tr>'; */

    /**
     * IMAGEN
     */
    echo '<tr class="form_row">';
    
    echo '<td>';
    echo '<div>Imagen</div>';
    echo '</td>';

    echo '<td>';
    // Botón Imagen
    echo '<button class="btn_list gray" type="button" onclick="' . "document.getElementById('uploadImage').click()" . '">Seleccionar Imagen</button>';
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
     * 
     * upload_image     Imagen a subir
     * fecha            Fecha de la observación
     * horas_fecha      Hora
     * mins_fecha       Minutos
     * descripcion      Descripción
    */

    echo '<input type="hidden" name="back" value="'. $back .'"/>';
    echo '<input type="hidden" name="row" value="'. $i .'">';
    echo '<input type="hidden" name="id" value="'. $id .'">';
    echo '<input type="hidden" name="from" value="'. $from . '">';
    echo '<input type="hidden" name="line_id" value="'. $line->id .'">';

    echo '<button class="btn_list" type="submit" name="create" style="padding: 10px; width: 100px">GUARDAR</button>';
    
    echo '</form>';

    /**
     * BOTÓN CANCELAR
     */
    echo '<form>';

    echo '<input type="hidden" name="id" value="'. $id .'">';
    echo '<button class="btn_list gray" type="submit" style="padding: 10px; width: 100px">CANCELAR</button>';
    
    echo '</form>';

    echo '</div>';

    echo '</div>';

    echo '</div>';
}

echo '</div>';

/**
 * FIN OBSERVACIONES
 */

#endregion 

echo '</div>';
// Fin Card

?>
            
    </div>
</div>

<?php

include_once "./interventor_footer.php";

?>

<!-- MODALES -->
<script>

    /**
     * 
     * PREVISUALIZAR IMAGEN
     * 
     * Actualiza la previsualización de la imagen
     * cuando detecta que hubo un cambio en el input
     * correspondiente
     * 
     */
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
     * 
     * MOSTRAR Y OCULTAR MODAL DE ELIMINACIÓN
     * 
     * Para mostrar el modal, se recibe la información de la línea actual,
     * y se actualiza dentro del formulario al inicio de la página, esto
     * para que no haya que recargar la página cada vez que se presiona
     * el botón de eliminar línea
     * 
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

    /**
     * 
     * MOSTRAR Y OCULTAR EL MODAL DE FIRMA
     * 
     */
    function showSignModal(type) {
        let modal = document.getElementById("sign_modal");
        let sign_type = document.getElementById("sign_type");

        modal.style.display = "flex";
        sign_type.value = type;

        
        thiscanvas = document.getElementById("canvas");
        thiscanvas.width = thiscanvas.parentNode.offsetWidth;
        thiscanvas.height = thiscanvas.parentNode.offsetHeight;
    }

    function hideSignModal() {
        let modal = document.getElementById("sign_modal");

        modal.style.display = "none";
        document.getElementById("clear").click();
    }

</script>

<!-- FIRMA -->
<script src="./js/firma.js"></script>