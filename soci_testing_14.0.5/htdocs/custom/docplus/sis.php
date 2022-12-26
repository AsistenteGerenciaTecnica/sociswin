<?php

require '../../main.inc.php';

require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/categoria.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/carpeta.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/documento.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/campo.class.php';

$langs->load("docplus@docplus");

$id=GETPOST('id','int');  // For backward compatibility
$ref=GETPOST('ref','alpha');
$socid=GETPOST('socid','int');
$action=GETPOST('action','alpha');

$title = $langs->trans('docplus');
$morejs = array();
$helpurl = "ES:DocumentosPlus";

$eval_ref = "EVAL-".$id;

// Security check
$socid=0;
if ($user->societe_id) $socid=$user->societe_id;

llxHeader('', $title, $helpurl, '', '', '', $morejs);

$modulo_select = GETPOST("modulo");
if ($modulo_select == "")
{
    $modulo_select = "user";
}

$back = DOL_URL_ROOT .'/custom/docplus/docplus_index.php';

/**
 * MODALES
 */
#region Modales

/**
 * MODAL ELIMINAR DOCUMENTO
 */
#region Modal Eliminar
echo '<div class="modal_delete" id="modalDelete">';

echo '<div class="modal_container">';

echo '<div class="delete_modal" id="deleteModal">';

echo '<div class="modal_title">';
echo '<div class="modal_name">';
echo 'Eliminar documento_nombre.pdf';
echo '</div>';
echo '<button class="modal_button" onclick="hideModal('. "'modalDelete'" . ", 'deleteModal'" .')">X</button>';;
echo '</div>';

echo '<div class="modal_content">';
echo '<div>';
echo 'Esta acción eliminará el documento del historial, si lo que desea es reemplazarlo, puede subir un nuevo archivo presionando en el botón correspondiente';
echo '</div>';
echo '<div class="delete_modal_bottom">';
echo '¿Seguro?';
echo '<button class="modal_button">Si</button>';
echo '<button class="modal_button" onclick="hideModal('. "'modalDelete'" . ", 'deleteModal'" .')">No</button>';
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>';
echo '</div>';
#endregion Modal Eliminar


/**
 * MODAL HISTORIAL DOCUMENTO
 */
#region Modal Historial

echo '<div class="modal_history" id="modalHistory">';

echo '<div class="modal_container">';

echo '<div class="history_modal" id="historyModal">';

echo '<div class="modal_title">';
echo '<div class="modal_name">';
echo 'Historial Documento 1';
echo '</div>';
echo '<button class="modal_button" onclick="hideModal('. "'modalHistory'" . ", 'historyModal'" .')">X</button>';
echo '</div>';

echo '<div class="modal_content">';
echo '<table>';
echo '<tr class="history_header">';
echo '<th>Archivo</th>';
echo '<th>Subido</th>';
echo '<th>Actualizado</th>';
echo '<th style="width: 15px"></th>';
echo '</tr>';
echo '<tr class="history_row">';
echo '<td><a href="xd.com">13documento.pdf</a></td>';
echo '<td>15/06/2021</td>';
echo '<td>15/06/2022</td>';
echo '<td>';
echo '<div class="icon_btn delete" style="height: 15px; width: 100%"></div>';
echo '</td>';
echo '</tr>';
echo '</table>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';
#endregion Modal Historial

/**
 * MODAL ELIMINAR CATEGORÍA
 */
#region Eliminar Categoría

echo '<div class="modal_delete" id="modalDeleteCat">';

echo '<div class="modal_container">';

echo '<div class="delete_modal" id="deleteModalCat">';

echo '<div class="modal_title">';
echo '<div class="modal_name">';
echo 'Eliminar';
echo '</div>';
echo '<button class="modal_button" onclick="hideModal('. "'modalDeleteCat'" . ", 'deleteModalCat'" .')">X</button>';;
echo '</div>';

echo '<div class="modal_content">';

echo '<div>';
echo 'Esta acción eliminará la categoría <span id="eliminar_cat_nombre"></span>';
echo '</div>';

echo '<div class="delete_modal_bottom">';

echo '¿Seguro?';

echo '<form method="POST" action="./inc/docplus.inc.php">';

echo '<input type="hidden" name="eliminar_cat_id" id="eliminar_cat_id">';
echo '<input type="hidden" name="modulo" value="'. $modulo_select .'">';
echo '<input type="hidden" name="back" value="'. $back .'">';

echo '<button type="submit" class="modal_button" name="delete_cat">Si</button>';

echo '</form>';
echo '<button class="modal_button" onclick="hideModal('. "'modalDeleteCat'" . ", 'deleteModalCat'" .')">No</button>';
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>';
echo '</div>';

#endregion


#endregion Modales


function loadContent($parent, $tipo_parent)
{
    /**
     * 
     * ELEMENTOS INDIVIDUALES
     * 
     */
    #region Elementos

    global $db;

    $doc = new Documento($db);
    $documentos = $doc->getAll($parent->id);
    
    $car = new Carpeta($db);
    $carpetas = $car->getAll($parent->id);    
    
    
    if (count($documentos) <= 0 && count($carpetas) <= 0)
    {
        echo '<div>';
        echo 'Aun no hay elementos en esta '. $tipo_parent .'';
        echo '</div>';
    } 
    else
    {
        /**
         * DOCUMENTOS
         */
        #region documentos
        foreach ($documentos as $documento)
        {
            echo '<div class="elemento">';
        
            /**
             * TÍTULO DEL ELEMENTO
             */
            echo '<div class="elemento_title">';
            echo '<div class="elemento_name">';
            echo '<div class="elemento_icon doc"></div>';
            echo '<div class="title">Documento 1</div>';
            echo '</div>';
            echo '<div class="dropdown_button"></div>';
            echo '</div>';
            
            /**
             * CONTENIDO DEL ELEMENTO 
             */
            echo '<div class="elemento_content">';
            
            /**
             * Barra de edición del documento
             */
            echo '<div class="elemento_document">';
            
            echo '<div class="filename">';
            
            echo '<a href="xd.com">';
            echo 'documento_nombre.pdf';
            echo '</a>';
            
            echo '</div>';
            
            echo '<div class="document_end">';
            
            echo '<div class="title">';
            echo '<span>(DD/MM/YYY)</span>';
            echo '</div>';
            
            echo '<div class="document_options">';
            
            echo '<div class="icon_btn history"></div>';
            echo '<div class="icon_btn upload"></div>';
            echo '<div class="icon_btn delete"></div>';
            
            echo '</div>';
            echo '</div>    ';
            echo '</div>';
            
            /**
             * Datos del documento
             */
            #region Datos del documento
            echo '<div class="elemento_data">';
            
            
            /**
             * Campos modificables
             */
            #region Campos
            echo '<div class="data_campos">';
            
            
            /**
             * Campos a la izquierda (Determinados por el usuario)
             */
            #region Campos a la izquierda
            echo '<div class="campos_left">';
            
            echo '<div class="campo">';
            
            echo '<div class="campo_name">Campo 1</div>';
            echo '<input class="main_input" type="text" disabled>';
            
            echo '</div>';
            
            echo '<div class="campo">';
            
            echo '<div class="campo_name">Campo 2</div>';
            echo '<select class="main_input" name="" id="" disabled>';
            echo '<option value="">A</option>';
            echo '<option value="">B</option>';
            echo '<option value="">C</option>';
            echo '</select>';
            
            echo '</div>';
            
            echo '<div class="campo">';
            
            echo '<div class="campo_name">Campo 3</div>';
            echo '<input class="main_input" type="text" disabled>';
            
            echo '</div>';
            
            echo '</div>';
            #endregion Campos a la izquierda
            
            /**
             * Campos a la derecha, sobre la renovación del documento
             */
            #region Campos a la derecha
            echo '<div class="campos_right">';
            
            // Renovación del documento
            #region Renovación
            echo '<div class="renovacion">';
            
            echo '<div class="renovacion_check">';
            
            echo '<input id="renovacion" type="checkbox" disabled>';
            echo 'Renovable';
            
            echo '</div>';
            
            // Opciones para la renovación del documento
            #region Opciones de Renovación
            echo '<div class="renovacion_options">';
            
            // Tipo de renovación
            #region Tipo de renovación
            echo '<div class="renovacion_type">';
            
            // Tipo Cada
            echo '<div class="reno_input">';
            
            echo '<input name="reno_type_1" id="cada" type="radio">';
            
            echo '<div class="title">';
            echo 'Cada';
            echo '</div>';
            
            echo '</div>';
            // Fin tipo cada
            
            // Tipo En la fecha
            echo '<div class="reno_input">';
            
            echo '<input name="reno_type_1" id="fecha" type="radio">';

            echo '<div class="title">';
            echo 'En la fecha';
            echo '</div>';

            echo '</div>';
            // Fin tipo en la fecha

            echo '</div>';
            // Fin Tipo de renovación
            #endregion Tipo de renovación


            // Fin tipos

            // Inputs de acuerdo con el tipo seleccionado
            #region Inputs tipo
            // Tipo cada
            echo '<div class="type_cada">';

            echo '<input class="main_input number_input" type="text">';

            echo '<select class="main_input" name="" id="">';
            echo '<option value="">Días</option>';
            echo '<option value="">Meses</option>';
            echo '<option value="">Años</option>';
            echo '</select>';

            echo '</div>';
            // Fin tipo cada

            // Tipo fecha
            echo '<div class="type_fecha">';

            echo '<input class="main_input" type="date">';
            
            echo '</div>';

            // Tiempo de aviso previo al vencimiento
            echo '<div class="alert">';

            echo '<div class="title">';
            echo 'Aviso Previo';
            echo '</div> ';

            // Input alerta
            echo '<div class="alert_input">';

            echo '<input class="main_input number_input" type="text">';

            echo '<select class="main_input" name="" id="">';
            echo '<option value="">Días</option>';
            echo '<option value="">Meses</option>';
            echo '<option value="">Años</option>';
            echo '</select>';

            echo '</div>';
            // Fin input alerta
            // maybe

            echo '</div>';
            // Fin tipo fecha

            echo '</div>';
            // Fin input tipos
            #endregion Input tipos

            echo '</div>';
            // Fin opciones de renovación
            #endregion Opciones de renovación

            echo '</div>';
            // Fin Renovación
            #endregion Renovación

            echo '</div>';
            #endregion Campos a la derecha

            echo '<div class="data_bottom">';
            echo '<button class="btn_elemento">GUARDAR</button>';
            echo '</div>';

            echo '</div>';
            #endregion Campos

            echo '</div>';
            #endregion Datos del documento

            echo '</div>';
            }
        #endregion documentos
            
    }
    #endregion Elemento
}

// Título de la página
echo '<div style="display: flex; align-items: center; margin: 10px 10px 10px 0px">';
echo '<span class="fas fa-folder-open valignmiddle widthpictotitle pictotitle" style=" color: #6c6aa8;"></span>';

echo '<div class="titre">Documentos Plus - Configuración</div> ';
echo '</div>';

echo '<div class="titre">Categorías, Carpetas y Documentos</div> ';

$cat = new Categoria($db);
$categorias = $cat->getAll($modulo_select);

echo '<div class="top_options">';

echo '<div>';
echo '<form method="GET" id="form_modulo">';

echo 'Módulo:';
echo '<select id="module_select" name="modulo" onchange="changeModule()">';
echo '<option value="user" '. ($modulo_select == 'user' ? 'selected' : '') .'>Usuarios</option>';
echo '<option value="thirdparty" '. ($modulo_select == 'thirdparty' ? 'selected' : '') .'>Terceros</option>';
echo '</select>';

echo (count($categorias) <= 0 ? '<br>No hay categorías aún' : '');

echo '</form>';
echo '</div>';



echo '<form method="POST" action="./inc/docplus.inc.php">';

echo '<input type="hidden" name="modulo" value="'. $modulo_select .'">';
echo '<input type="hidden" name="user" value="'. $user->id .'">';
echo '<input type="hidden" name="back" value="'. $back .'">';

echo '<button class="butAction" type="submit" name="new_cat" style="margin: 0px">Nueva Categoría</button>';

echo '</form>';

echo '</div>';


/**
 * 
 * CATEGORÍAS
 * 
 */

if (count($categorias) > 0)
{
    echo '<div>';

    /**
     * 
     * CATEGORÍA
     * 
     */
    #region Categoría

    foreach($categorias as $categoria)
    {
        echo '<div class="categoria">';
    
        /**
         * TÍTULO DE LA CATEGORÍA 
         */
        echo '<div class="categoria_title">';
        

        /**
        * REGULAR
        */
        echo '<div class="categoria_title_left" id="regular_cat_'. $categoria->id .'" '. (GETPOST("editing_cat") == $categoria->id ? 'style="display: none"' : '') .'>';

        echo '<input type="hidden" id="categoria_id" value="'. $categoria->id .'">';

        echo '<div class="title" id="categoria_nombre_'. $categoria->id.'">';
        echo $categoria->nombre;
        echo '</div>';
    
        echo '<a class="editfielda" id="edit_cat_'. $categoria->id .'" onclick="changeEditingCat('. $categoria->id .')">';
        echo '<span class="fas fa-pencil-alt do_pointer" style="color: gray !important;" title="Modificar"></span>';
        echo '</a>';
        echo '<a name="delete" class="marginrightonly do_pointer delete_cat" style="margin-left: 10px">';
        echo '<span class="fas fa-trash pictodelete" id="btn_eliminar_cat_'. $categoria->id .'" style="" title="Eliminar"></span>';
		echo '</a>';

        echo '</div>';
        // Fin regular
            
        /**
        * EDITANDO
        */
        echo '<div class="categoria_title_left" id="editing_cat_'. $categoria->id .'" '. (GETPOST("editing_cat") == $categoria->id ? 'style="display: flex"' : 'style="display: none"') .'>';

        echo '<div id="editing_cat_'. $categoria->id .'">';
        echo '<form method="POST" action="./inc/docplus.inc.php">';
            
        echo '<input type="hidden" name="back" value="'. $back .'">';
        echo '<input type="hidden" name="user" value="'. $user->id .'">';
        echo '<input type="hidden" name="modulo" value="'. $modulo_select .'">';
        echo '<input type="hidden" name="edit_cat_id" value="'. $categoria->id .'">';
            
        echo '<input type="text" name="edit_cat_nombre" placeholder="Nombre" value="'. $categoria->nombre .'">';

        echo '<button type="submit" class="butAction" name="edit_cat">Guardar</button>';
        echo '<button type="button" class="butAction" onclick="changeEditingCat('. $categoria->id .')" name="cancel_edit_cat" id="cancel_edit_cat_'. $categoria->id .'" style="margin-left: 0px">Cancelar</button>';
             
        echo '</form>';
        echo '</div>';

        echo '</div>';
        // Fin editando
    
        echo '<div class="dropdown_button"></div>';
        
        echo '</div>';
        
        /**
         * CONTENIDO DE LA CATEGORÍA (DOCUMENTOS Y CARPETAS)
         */
        #region Contenido de la Categoría
        echo '<div class="categoria_content">';
        
        echo '<div class="categoria_options">';

        echo '<form method="POST" action="./inc/docplus.inc.php">';

        echo '<input type="hidden" name="parent" value="'. $categoria->id .'">';
        echo '<input type="hidden" name="tipo_parent" value="categoria">';
        echo '<input type="hidden" name="back" value="'. $back .'">';
        echo '<input type="hidden" name="user" value="'. $user->id .'">';
        echo '<input type="hidden" name="modulo" value="'. $modulo_select .'">';

        echo '<button type="submit" class="butAction" name="new_carp">Nueva Carpeta</button>';
        echo '<button type="submit" class="butAction" name="new_doc">Nuevo Documento</button>';

        echo '</form>';
        echo '</div>';

        loadContent($categoria, "categoria");

        
        echo '</div>';
        #endregion Contenido de la categoría
        
        echo '</div>';
        #endregion Categoría
    }

   
    
    echo '</div>';
}



echo '<script src="./lib/js/docplus.js"></script>';
