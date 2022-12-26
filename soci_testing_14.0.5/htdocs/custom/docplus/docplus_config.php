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

llxHeader('', $title, $helpurl, '', '', '', $morejs);

$modulo_select = GETPOST("modulo");
if ($modulo_select == "")
{
    $modulo_select = "user";
}

$open_doc = GETPOST("open_doc");
$open_carp = GETPOST("open_carp");
$open_list = explode(",", GETPOST("open"));

$open_cat = array_pop($open_list);


$back = DOL_URL_ROOT .'/custom/docplus/docplus_config.php';

/**
 * MODALES
 */
#region Modales

#region Eliminar Objeto

echo '<div class="modal_delete" id="modalDelete">';

echo '<div class="modal_container">';

echo '<div class="delete_modal" id="deleteModal">';

echo '<div class="modal_title">';
echo '<div class="modal_name">';
echo 'Eliminar';
echo '</div>';
echo '<button class="modal_button" onclick="hideModal('. "'modalDelete'" . ", 'deleteModal'" .')">X</button>';;
echo '</div>';

echo '<div class="modal_content">';

echo '<div>';
echo 'Esta acción eliminará el elemento <b><span id="eliminar_obj_nombre"></span></b> y todo su contenido';
echo '</div>';

echo '<div class="delete_modal_bottom">';

echo '¿Seguro?';

echo '<form method="POST" action="./inc/docplus.inc.php">';

echo '<input type="hidden" name="eliminar_obj_id" id="eliminar_obj_id">';
echo '<input type="hidden" name="modulo" value="'. $modulo_select .'">';
echo '<input type="hidden" name="back" value="'. $back .'">';

echo '<button type="submit" class="modal_button" name="delete_obj" id="delete_obj">Si</button>';

echo '</form>';
echo '<button class="modal_button" onclick="hideModal('. "'modalDelete'" . ", 'deleteModal'" .')">No</button>';
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>';
echo '</div>';

#endregion Eliminar Objeto

#endregion Modales


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
        // Inicio Categoría
        echo '<div class="categoria">';

        /**
         * TÍTULO DE LA CATEGORÍA 
         */
        echo '<div class="categoria_title categoria_title_hover do_pointer" onclick="document.getElementById('. "'dropdown_". $categoria->id ."'" .').click()">';


        // Título Regular
        echo '<div class="categoria_title_left" id="regular_cat_'. $categoria->id .'" '. (GETPOST("editing_cat") == $categoria->id ? 'style="display: none"' : '') .'>';

        echo '<input type="hidden" id="categoria_id" value="'. $categoria->id .'">';

        echo '<div class="title" id="cat_nombre_'. $categoria->id.'">';
        echo $categoria->nombre;
        echo '</div>';
    
        $doc = new Documento($db);
        $docs = $doc->getAll($categoria->id, "categoria");
        $carp = new Carpeta($db);
        $carps = $carp->getAll($categoria->id, "categoria");

        echo '<a class="editfielda" id="edit_cat_'. $categoria->id .'" onclick="changeEditing('. $categoria->id .', '. "'cat'" .')">';
        echo '<span class="fas fa-pencil-alt do_pointer" style="color: gray !important;" title="Modificar"></span>';
        echo '</a>';

        if (count($docs) <= 0 || count($carps) <= 0)
        {
            echo '<a name="delete" class="marginrightonly do_pointer delete_obj" style="margin-left: 10px">';
            echo '<span class="fas fa-trash pictodelete" id="btn_eliminar_cat_'. $categoria->id .'" style="" title="Eliminar"></span>';
            echo '</a>';
        }

        echo '</div>';
        // Fin regular

        // Título Edición
        echo '<div class="categoria_title_left" id="editing_cat_'. $categoria->id .'" '. (GETPOST("editing_cat") == $categoria->id ? 'style="display: flex"' : 'style="display: none"') .'>';

        echo '<div id="editing_cat_'. $categoria->id .'">';
        echo '<form method="POST" action="./inc/docplus.inc.php">';
            
        echo '<input type="hidden" name="back" value="'. $back .'">';
        echo '<input type="hidden" name="user" value="'. $user->id .'">';
        echo '<input type="hidden" name="modulo" value="'. $modulo_select .'">';
        echo '<input type="hidden" name="edit_cat_id" value="'. $categoria->id .'">';
            
        echo '<input type="text" name="edit_cat_nombre" placeholder="Nombre" value="'. $categoria->nombre .'">';

        echo '<button type="submit" class="butAction" name="edit_cat">Guardar</button>';
        echo '<button type="button" class="butAction" onclick="changeEditing('. $categoria->id .', '. "'cat'" .')" name="cancel_edit_cat" id="cancel_edit_cat_'. $categoria->id .'" style="margin-left: 0px">Cancelar</button>';
             
        echo '</form>';
        echo '</div>';

        echo '</div>';
        // Fin edición

        // Dropdown
        echo '<div class="dropdown_button" id="dropdown_'. $categoria->id .'"></div>';

        echo '</div>';
        // Fin título

        /**
         * CONTENIDO DE LA CATEGORÍA (DOCUMENTOS Y CARPETAS)
         */
        #region Contenido de la Categoría
        echo '<div class="categoria_content" style="display:'. ($categoria->id == $open_cat ? 'block' : 'none') .'">';

        // Opciones para la categoría (Nueva Carpeta / Nuevo Documento)
        echo '<div class="content_options">';

        echo '<form method="POST" action="./inc/docplus.inc.php">';
        
        echo '<input type="hidden" name="parent" value="'. $categoria->id .'">';
        echo '<input type="hidden" name="tipo_parent" value="categoria">';
        echo '<input type="hidden" name="back" value="'. $back .'">';
        echo '<input type="hidden" name="user" value="'. $user->id .'">';
        echo '<input type="hidden" name="modulo" value="'. $modulo_select .'">';
        
        echo '<button type="submit" class="butAction" style="margin-left: 0px" name="new_carp">Nueva Carpeta</button>';
        echo '<button type="submit" class="butAction" style="margin-left: 0px" name="new_doc">Nuevo Documento</button>';

        echo '</form>';

        echo '</div>';
        // Fin Opciones

        loadContent($categoria, "categoria");
        
        echo '</div>';
        // Fin Contenido

        echo '</div>';
        // Fin Categoría
    }
    
    echo '</div>';
}

function loadContent($parent, $tipo_parent)
{
    /**
     * 
     * ELEMENTOS INDIVIDUALES
     * 
     */
    #region Elementos

    global $db, $user, $back, $open_list, $open_doc, $open_carp, $modulo_select;

    $doc = new Documento($db);
    $documentos = $doc->getAll($parent->id, $tipo_parent);
    
    $car = new Carpeta($db);
    $carpetas = $car->getAll($parent->id, $tipo_parent);    
    
    
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
            // Documento
            echo '<div class="elemento">';

            // Título del documento
            echo '<div class="elemento_title elemento_title_hover do_pointer" onclick="document.getElementById('. "'dropdown_doc_". $documento->id ."'" .').click()">';

            /**
             * REGULAR
             */
            echo '<div class="elemento_title_left" id="regular_doc_'. $documento->id .'">';

            // Nombre del documento
            echo '<div class="elemento_name">'; 

            echo '<div class="elemento_icon doc"></div>';
            echo '<div class="title" id="doc_nombre_'. $documento->id .'">'. $documento->nombre .'</div>';
            
            echo '</div>';
            // Fin nombre

            echo '<a class="editfielda" id="edit_doc_'. $documento->id .'" onclick="changeEditing('. $documento->id .', '. "'doc'" .')">';
            echo '<span class="fas fa-pencil-alt do_pointer" style="color: gray !important;" title="Modificar"></span>';
            echo '</a>';

            $sql = "SELECT * FROM llx_docplus_documento_objeto WHERE fk_documento = '". $documento->id ."'";

            $res = $db->getRow($sql);

            $cam = new Campo($db);
            $campos = $cam->getAll($documento->id);

            if (!$res && count($campos) <= 0)
            {
                echo '<a name="delete" class="marginrightonly do_pointer delete_obj" style="margin-left: 10px">';
                echo '<span class="fas fa-trash pictodelete" id="btn_eliminar_doc_'. $documento->id .'" style="" title="Eliminar"></span>';
                echo '</a>';
            }

            
            echo '</div>';
            // Fin regular

            /**
             * EDITANDO
             */                       
            echo '<div class="elemento_title_left" id="editing_doc_'. $documento->id .'" style="display: none">';

            echo '<form method="POST" action="./inc/docplus.inc.php">';
                
            echo '<input type="hidden" name="back" value="'. $back .'">';
            echo '<input type="hidden" name="user" value="'. $user->id .'">';
            echo '<input type="hidden" name="modulo" value="'. $modulo_select .'">';
            echo '<input type="hidden" name="edit_doc_id" value="'. $documento->id .'">';
            echo '<input type="text" name="edit_doc_nombre" placeholder="Nombre" value="'. $documento->nombre .'">';

            echo '<button type="submit" class="butAction" name="edit_doc">Guardar</button>';
            echo '<button type="button" class="butAction" onclick="changeEditing('. $documento->id .', '. "'doc'" .')" name="cancel_edit_doc" id="cancel_edit_doc_'. $documento->id .'" style="margin-left: 0px">Cancelar</button>';
                
            echo '</form>';

            echo '</div>';
            // Fin editando

            // Dropdown
            echo '<div class="dropdown_button" id="dropdown_doc_'. $documento->id .'"></div>';

            echo '</div>';
            // Fin título


            /**
             * CONTENIDO DEL ELEMENTO 
             */
            echo '<div class="elemento_content" style="display:'. ($documento->id == $open_doc ? 'block' : 'none') .'">';

            // Barra de edición
            echo '<div class="elemento_document">';

            // Archivo
            echo '<div class="filename">';
            
            echo '<a href="xd.com">';
            echo 'documento_nombre.pdf';
            echo '</a>';
            
            echo '</div>';
            // Fin archivo

            // Zona derecha del título
            echo '<div class="document_end">';

            // Fecha
            echo '<div class="title">';
            echo '<span>(DD/MM/YYY)</span>';
            echo '</div>';
            // Fin fecha

            // Botones
            echo '<div class="document_options">';
            
            echo '<div class="icon_btn history" disabled></div>';
            echo '<div class="icon_btn upload" disabled></div>';
            echo '<div class="icon_btn delete" disabled></div>';
            
            echo '</div>';
            // Fin botones

            echo '</div>';
            // Fin zona derecha

            echo '</div>';
            // Fin barra de edición
            
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


            $cam = new Campo($db);
            $campos = $cam->getAll($documento->id);

            if (count($campos) > 0)
            {
                foreach ($campos as $campo)
                {
                    
                    $sql = "SELECT * FROM llx_docplus_documento_objeto_campo WHERE fk_campo ='". $campo->id ."'";

                    $doc_obj_campo = $db->getRows($sql);

                    /**
                     * REGULAR
                     */
                    echo '<div class="campo" id="regular_campo_'. $campo->id .'">';
                    
                    echo '<div class="campo_name" id="campo_nombre_'. $campo->id .'">'. $campo->nombre .'</div>';

                    if ($campo->tipo == "selection")
                    {
                        $values = explode(";", $campo->valores);
                        
                        echo '<select class="main_input" disabled>';

                        foreach ($values as $value)
                        {
                            echo '<option value="'. trim($value) .'">'. trim($value) .'</option>';
                        }

                        echo '</select>';
                    }
                    else
                    {
                        echo '<input class="main_input" type="'. $campo->tipo .'" disabled>';
                    }

                    echo '<a class="editfielda" id="edit_campo_'. $campo->id .'" onclick="changeEditing('. $campo->id .', '. "'campo'" .')">';
                    echo '<span class="fas fa-pencil-alt do_pointer" style="color: gray !important;" title="Modificar"></span>';
                    echo '</a>';
                    
                    if (count($doc_obj_campo) <= 0)
                    {
                        echo '<a name="delete" class="marginrightonly do_pointer delete_obj" style="margin-left: 10px">';
                        echo '<span class="fas fa-trash pictodelete" id="btn_eliminar_campo_'. $campo->id .'" style="" title="Eliminar"></span>';
                        echo '</a>';
                    }

                    
                    echo '</div>';

                    /**
                     * EDITANDO
                     */
                    echo '<div class="campo" id="editing_campo_'. $campo->id .'" style="display: none">';
                    
                    echo '<table>';

                    // Nombre
                    echo '<tr>';

                    echo '<td>';

                    echo '<div>';
                    echo 'Nombre:';
                    echo '</div>';
                    
                    echo '</td>';

                    echo '<td>';

                    echo '<div>';
                    echo '<input type="text" name="campo_nombre" value="'. $campo->nombre .'" form="form_edit_campo_'. $campo->id .'">';
                    echo '</div>';
                    
                    echo '</td>';

                    echo '</tr>';

                    // Tipo
                    echo '<tr>';

                    echo '<td>';

                    echo '<div>';
                    echo 'Tipo:';
                    echo '</div>';
                    
                    echo '</td>';

                    echo '<td>';

                    echo '<div>';
                    
                    echo '<select class="campo_select" name="campo_tipo" form="form_edit_campo_'. $campo->id .'" id="select_tipo_'. $campo->id .'" '. (count($doc_obj_campo) > 0 ? 'disabled' : '') .'>';

                    echo '<option value="text" '. ($campo->tipo == "text" ? 'selected' : '') .'>Texto</option>';
                    echo '<option value="number" '. ($campo->tipo == "number" ? 'selected' : '') .'>Numérico</option>';
                    echo '<option value="selection" '. ($campo->tipo == "selection" ? 'selected' : '') .'>Listado</option>';

                    echo '</select>';

                    echo '</div>';
                    
                    echo '</td>';

                    echo '</tr>';

                    // Valores
                    echo '<tr>';

                    echo '<td>';

                    echo '<div>';
                    echo 'Valores:';
                    echo '</div>';
                    
                    echo '</td>';

                    echo '<td>';

                    echo '<div>';

                    echo '<div>Separar con <b>;</b></div>';
                    echo '<textarea id="valores_campo_'. $campo->id .'" name="campo_valores" form="form_edit_campo_'. $campo->id .'" disabled>'. $campo->valores .'</textarea>';

                    echo '</div>';
                    
                    echo '</td>';

                    echo '</tr>';

                    // Botones
                    echo '<tr>';

                    echo '<td>';

                    echo '<div>';

                    echo '<form method="POST" action="./inc/docplus.inc.php" id="form_edit_campo_'. $campo->id .'">';

                    echo '<input type="hidden" name="user" value="'. $user->id .'">';
                    echo '<input type="hidden" name="modulo" value="'. $modulo_select .'">';
                    echo '<input type="hidden" name="back" value="'. $back .'">';
                    echo '<input type="hidden" name="campo_id" value="'. $campo->id .'">';

                    echo '<button type="submit" class="butAction" name="edit_campo" style="margin-left: 0px">Guardar</button>';
                    
                    echo '</form>';
                    echo '</div>';

                    
                    echo '</td>';

                    echo '<td>';

                    echo '<div>';
                    echo '<button type="button" class="butAction" onclick="changeEditing('. $campo->id .', '. "'campo'" .')" name="cancel_edit_campo" style="margin-left: 0px">Cancelar</button>';
                    echo '</div>';
                    
                    echo '</td>';

                    echo '</tr>';

                    echo '</table>';
                    
                    echo '</div>';  
                    
                    

                }
            }
            else
            {
                echo 'Este documento aun no tiene campos';
            }

            echo '</div>';
            #endregion Campos a la izquierda
            
            /**
             * Campos a la derecha, sobre la renovación del documento
             */
            #region Campos a la derecha
            echo '<div class="campos_right">';
            echo '</div>';
            #endregion Campos a la derecha

            echo '</div>';
            #endregion Campos

            /**
             * Opciones en la inferior
             */
            echo '<div class="data_bottom">';

            echo '<form method="POST" action="./inc/docplus.inc.php">';

            echo '<input type="hidden" name="back" value="'. $back .'">';
            echo '<input type="hidden" name="user" value="'. $user->id .'">';
            echo '<input type="hidden" name="modulo" value="'. $modulo_select .'">';
            echo '<input type="hidden" name="documento" value="'. $documento->id .'">';
            
            echo '<button class="butAction" name="new_campo" style="margin-left: 0px">Nuevo Campo</button>';
            
            echo '</form>';

            echo '</div>';
            // Fin opciones inferiores

            echo '</div>';
            // Fin datos

            echo '</div>';
            // Fin contenido

            echo '</div>';
            // Fin Documento
        }
        #endregion documentos

        /**
         * CARPETAS
         */
        #region Carpetas

        foreach ($carpetas as $carpeta)
        {
            // Carpeta
            echo '<div class="elemento">';

            // Título
            echo '<div class="elemento_title elemento_title_hover do_pointer" onclick="document.getElementById('. "'dropdown_carpeta_". $carpeta->id ."'" .').click()"">';

            // Regular
            echo '<div class="elemento_title_left" id="regular_carp_'. $carpeta->id .'">';

            // Ícono y nombre
            echo '<div class="elemento_name">';

            echo '<div class="elemento_icon folder"></div>';
            echo '<div class="title">'. $carpeta->nombre .'</div>';

            echo '</div>';
            // Fin nombre

            echo '<a class="editfielda" id="edit_carp_'. $carpeta->id .'" onclick="changeEditing('. $carpeta->id .', '. "'carp'" .')">';
            echo '<span class="fas fa-pencil-alt do_pointer" style="color: gray !important;" title="Modificar"></span>';
            echo '</a>';

            $doc = new Documento($db);
            $docs = $doc->getAll($carpeta->id, "carpeta");

            if (count($docs) <= 0)
            {
                echo '<a name="delete" class="marginrightonly do_pointer delete_doc" style="margin-left: 10px">';
                echo '<span class="fas fa-trash pictodelete" id="btn_eliminar_carp_'. $carpeta->id .'" style="" title="Eliminar"></span>';
                echo '</a>';
            }
            
            echo '</div>';
            // Fin regular

            // Editando                        
            echo '<div class="elemento_title_left" id="editing_carp_'. $carpeta->id .'" style="display: none">';

            echo '<form method="POST" action="./inc/docplus.inc.php">';
                
            echo '<input type="hidden" name="back" value="'. $back .'">';
            echo '<input type="hidden" name="user" value="'. $user->id .'">';
            echo '<input type="hidden" name="modulo" value="'. $modulo_select .'">';
            echo '<input type="hidden" name="edit_carp_id" value="'. $carpeta->id .'">';
            echo '<input type="text" name="edit_carp_nombre" placeholder="Nombre" value="'. $carpeta->nombre .'">';

            echo '<button type="submit" class="butAction" name="edit_carp">Guardar</button>';
            echo '<button type="button" class="butAction" onclick="changeEditing('. $carpeta->id .', '. "'carp'" .')" name="cancel_edit_carp" id="cancel_edit_carp_'. $carpeta->id .'" style="margin-left: 0px">Cancelar</button>';
                
            echo '</form>';

            echo '</div>';

            // Dropdown
            echo '<div class="dropdown_button" id="dropdown_carpeta_'. $carpeta->id .'"></div>';

            echo '</div>';
            // Fin título

            /**
             * CONTENIDO
             */
            echo '<div class="elemento_content_folder" style="display:'. (in_array($carpeta->id, $open_list) || $carpeta->id == $open_carp ? 'block' : 'none') .'">';

            // Opciones
            echo '<div class="content_options">';

            echo '<form method="POST" action="./inc/docplus.inc.php">';
            
            echo '<input type="hidden" name="parent" value="'. $carpeta->id .'">';
            echo '<input type="hidden" name="tipo_parent" value="carpeta">';
            echo '<input type="hidden" name="back" value="'. $back .'">';
            echo '<input type="hidden" name="user" value="'. $user->id .'">';
            echo '<input type="hidden" name="modulo" value="'. $modulo_select .'">';
            
            echo '<button type="submit" class="butAction" style="margin-left: 0px" name="new_carp">Nueva Carpeta</button>';
            echo '<button type="submit" class="butAction" style="margin-left: 0px" name="new_doc">Nuevo Documento</button>';
            
            echo '</form>';

            echo '</div>';
            // Fin opciones

            loadContent($carpeta, "carpeta");

            echo '</div>';
            // Fin contenido
            echo '</div>';
            // Fin carpeta
        }
        #endregion carpetas
    }
    #endregion Elementos
}

print dol_get_fiche_end();

llxFooter();

$db->close();

echo '<script src="./lib/js/docplus.js"></script>';
