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
$langs->load("users");

$id=GETPOST('id','int');  // For backward compatibility
$ref=GETPOST('ref','alpha');
$socid=GETPOST('socid','int');
$action=GETPOST('action','alpha');

$title = $langs->trans('docplus');
$morejs = array();
$helpurl = "ES:DocumentosPlus";

$eval_ref = "EVAL-".$id;

// Módulo de la página actual
$modulo = "user";

$back = DOL_URL_ROOT .'/custom/docplus/tab_usuario.php';

$object = new User($db);
if ($id > 0)
{
    $object->fetch($id, $ref, '', 1);
    $object->getrights();
}

llxHeader('', $langs->trans("UserCard").' - '.$langs->trans("docplus"), $helpurl, '', '', '', $morejs);

$head = user_prepare_head($object);

print dol_get_fiche_head($head, 'TabUsuario', $langs->trans("User"), -1, 'user');

$linkback = '';
	if ($user->rights->user->user->lire || $user->admin) {
		$linkback = '<a href="'.DOL_URL_ROOT.'/user/list.php?restore_lastsearch_values=1">'.$langs->trans("BackToList").'</a>';
	}

dol_banner_tab($object, 'id', $linkback, $user->rights->user->user->lire || $user->admin);

/**
 * MODALES
 */
#region Modales

#region Eliminar Documento

echo '<div class="modal_delete" id="modalDeleteDoc">';

echo '<div class="modal_container">';

echo '<div class="delete_modal" id="deleteModalDoc">';

echo '<div class="modal_title">';
echo '<div class="modal_name">';
echo 'Eliminar Documento';
echo '</div>';
echo '<button class="modal_button" onclick="hideModal('. "'modalDeleteDoc'" . ", 'deleteModalDoc'" .')">X</button>';;
echo '</div>';

echo '<div class="modal_content">';

echo '<div>';
echo 'Esta acción eliminará el documento <b><span id="eliminar_file_nombre"></span></b> del historial, para siempre, si lo que desea es reemplazarlo, puede subir un nuevo archivo presionando en el botón correspondiente';
echo '</div>';

echo '<div class="delete_modal_bottom">';

echo '¿Seguro?';

echo '<form method="POST" action="./inc/usuario.inc.php">';

echo '<input type="hidden" name="eliminar_file" id="eliminar_file">';
echo '<input type="hidden" name="eliminar_doc_id" id="eliminar_doc_id">';
echo '<input type="hidden" name="usuario_id" value="'. $object->id .'">';
echo '<input type="hidden" name="modulo" value="'. $modulo .'">';
echo '<input type="hidden" name="back" value="'. $back .'">';

echo '<button type="submit" class="modal_button" name="delete_file" id="delete_file">Si</button>';

echo '</form>';
echo '<button class="modal_button" onclick="hideModal('. "'modalDeleteDoc'" . ", 'deleteModalDoc'" .')">No</button>';
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>';
echo '</div>';

#endregion Eliminar Objeto

#region Subir Documento

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

echo '<form method="POST" action="./inc/usuario.inc.php">';

echo '<input type="hidden" name="eliminar_obj_id" id="eliminar_obj_id">';
echo '<input type="hidden" name="fk_usuario" value="'. $object->id .'">';
echo '<input type="hidden" name="modulo" value="'. $modulo .'">';
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

echo '<div class="modal_upload" id="modalUpload">';

echo '<div class="modal_container">';

echo '<div class="upload_modal" id="uploadModal">';

// Título
echo '<div class="modal_title">';

// Nombre
echo '<div class="modal_name">';
echo 'Subir Documento - ';
echo '<span id="upload_doc_nombre"></span>';
echo '</div>';

// Botón para cerrar
echo '<button class="modal_button" onclick="hideModal('. "'modalUpload'" . ", 'uploadModal'" .')">X</button>';;

echo '</div>';

// Contenido 
echo '<div class="modal_content">';

echo '<div style="display:flex; align-items: center; flex-direction: column">';

// Botón para subir archivo
echo '<form method="POST" action="./inc/usuario.inc.php" enctype="multipart/form-data">';

echo '<div>';
echo '<button type="button" class="butAction" onclick="document.getElementById('. "'upload_doc'" .').click()" style="margin: 0px">Seleccionar Archivo</button>';
echo '<input type="file" name="upload_doc" id="upload_doc" style="display: none" onchange="updateFileName()">';
echo '</div>';

echo '<div id="file_name">';
echo 'Ningún archivo seleccionado';
echo '</div>';

echo '</div>';

echo '<div class="upload_modal_bottom">';


echo '<input type="hidden" name="upload_doc_id" id="upload_doc_id">';
echo '<input type="hidden" name="fk_usuario" value="'. $object->id .'">';
echo '<input type="hidden" name="user" value="'. $user->id .'">';
echo '<input type="hidden" name="modulo" value="'. $modulo .'">';
echo '<input type="hidden" name="back" value="'. $back .'">';

echo '<button type="submit" class="modal_button" name="upload_doc" id="upload_doc">Guardar</button>';

echo '</form>';
echo '<button class="modal_button" onclick="hideModal('. "'modalUpload'" . ", 'uploadModal'" .')">Cancelar</button>';
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>';
echo '</div>';

#endregion Subir

#region Historial

if (isset($_GET['doc_history']))
{

    $doc_id = GETPOST("open_doc");

    $documento = new Documento($db);
    $documento->fetch($doc_id);

    echo '<div class="modal_history" id="modalHistory" style="display: block">';

    echo '<div class="modal_container">';
    
    echo '<div class="history_modal" id="historyModal">';
    
    echo '<div class="modal_title">';
    echo '<div class="modal_name">';
    echo 'Historial <b>'. $documento->nombre . '</b>';
    echo '</div>';
    echo '<button class="modal_button" onclick="hideModal(' . "'modalHistory', 'historyModal'" . ')">X</button>';
    echo '</div>';
    
    echo '<div class="modal_content">';

    $path = '/custom/docplus/upload/'. $modulo .'/'. $object->id .'/'. $documento->id;
    $files = get_doc_files($path);

    echo '</tr>';
    // Fin Encabezados

    if (count($files) > 0)
    {
        echo '<table>';

        // Encabezados
        echo '<tr class="history_header">';

        echo '<th>Archivo</th>';
        echo '<th>Subido</th>';
        echo '<th style="width: 15px"></th>';

        for ($i = 0; $i < count($files); $i++)
        {
            $file = $files[$i];

            echo '<tr class="history_row">';
    
            echo '<td>';
            echo '<a href="'. DOL_URL_ROOT . $path .'/'. $file['file'] .'">'. $file['file'] .'</a>';
            echo '</td>';
            
            echo '<td>';
            echo date("m/d/Y", $file['date']);
            echo '</td>';
            
            echo '<td>';

            echo '<input type="hidden" id="file_id_'. $i .'" value="'. $file['file'] .'">';
            echo '<input type="hidden" id="file_doc_id_'. $i .'" value="'. $documento->id .'">';

            echo '<div class="icon_btn delete" id="eliminar_file_'. $i .'" style="height: 15px; width: 100%"></div>';
            echo '</td>';
    
            echo '</tr>';
        }

        echo '</table>';
    }
    else
    {
        echo 'No se ha subido ningún archivo para este documento';
    }

    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}


#endregion Historial

#endregion Modales


echo '<div class="titre">Documentos del Usuario</div> ';

$cat = new Categoria($db);

$categorias = $cat->getAll($modulo);

$open_doc = GETPOST("open_doc");
$open_list = explode(",", GETPOST("open"));

$open_cat = array_pop($open_list);

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
    
        echo '</div>';
        // Fin regular

        // Dropdown
        echo '<div class="dropdown_button" id="dropdown_'. $categoria->id .'"></div>';

        echo '</div>';
        // Fin título

        /**
         * CONTENIDO DE LA CATEGORÍA (DOCUMENTOS Y CARPETAS)
         */
        #region Contenido de la Categoría
        echo '<div class="categoria_content" style="display:'. ($categoria->id == $open_cat ? 'block' : 'none') .'">';

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

    global $db, $user, $back, $object, $open_list, $open_doc, $modulo;

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
            $doc_usuario = $documento->fetch_doc_objeto($object->id);

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
            
            echo '</div>';
            // Fin regular

            // Dropdown
            echo '<div id="dropdown_doc_'. $documento->id .'" class="dropdown_button"></div>';

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
            
            $path = '/custom/docplus/upload/'. $modulo .'/'. $object->id .'/'. $documento->id;

            $files = get_doc_files($path);

            // Si habían archivos, se guardan los valores del más reciente para
            // mostrarlo en el documento
            if (count($files) > 0)
            {
                $filename = $files[0]['file'];
                $filedate = date("d/m/Y", $files[0]['date']);
            }
            // Si no, se dejan los placeholders
            else
            {
                $filename = "Ningún archivo subido";
                $filedate = "DD/MM/YYYY";
            }

            // Si había algun archivo, se genera un enlace al primero que se muestra
            echo '<a '. (count($files) > 0 ? 'href="'. DOL_URL_ROOT . $path .'/'. $filename .'"' : '') .' id="file_nombre_'. $documento->id .'">';
            echo $filename;
            echo '</a>';
            
            echo '</div>';
            // Fin archivo

            // Zona derecha del título
            echo '<div class="document_end">';

            // Fecha
            echo '<div class="title">';
            echo '<span>(Subido el '. $filedate .')</span>';
            echo '</div>';
            // Fin fecha

            // Botones
            echo '<div class="document_options">';
            
            echo '<form method="GET" id="history_form_'. $documento->id .'">';
            echo '<input type="hidden" name="doc_history">';
            echo '<input type="hidden" name="id" value="'. $object->id .'">';
            echo '<input type="hidden" name="open_doc" value="'. $documento->id .'">';
            
            $this_parents = get_parents($documento);
            echo '<input type="hidden" name="open" value="'. $this_parents .'">';           
            
            echo '</form>';
            echo '<div class="icon_btn history" onclick="document.getElementById('. "'history_form_". $documento->id ."'" .').submit()" disabled></div>';

            echo '<div id="upload_doc_'. $documento->id .'" class="icon_btn upload" disabled></div>';

            echo '<input type="hidden" id="file_id_0" value="'. $filename .'">';
            echo '<input type="hidden" id="file_doc_id_0" value="'. $documento->id .'">';
            echo '<div class="icon_btn delete" id="eliminar_file_0"></div>';
            
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
                /**
                 * CAMPOS
                 */
                
                echo '<table>';
                
                foreach ($campos as $campo)
                {
                    $doc_usuario_campo = $campo->fetch_doc_objeto_campo($object->id);

                    echo '<tr class="campo">';

                    echo '<td>';
                    echo '<div class="campo_name" id="campo_nombre_'. $campo->id .'">'. $campo->nombre .'</div>';
                    echo '</td>';                    
                    
                    echo '<td class="campo_input">';
                    echo '<div>';
                    if ($campo->tipo == "selection")
                    {
                        $values = explode(";", $campo->valores);
                        
                        echo '<select class="main_input campo_'. $documento->id .'" name="campo_d'. $documento->id .'_'. $campo->id .'" form="form_doc_'. $documento->id .'" disabled>';

                        foreach ($values as $value)
                        {
                            echo '<option value="'. trim($value) .'" '. ($doc_usuario_campo->valor == $value ? 'selected' : '') .'>'. trim($value) .'</option>';
                        }

                        echo '</select>';
                    }
                    else if ($campo->tipo == "text" || $campo->tipo == "number")
                    {
                        echo '<input class="main_input campo_'. $documento->id .'" type="'. $campo->tipo .'" name="campo_d'. $documento->id .'_'. $campo->id .'" value="'. ($doc_usuario_campo ? $doc_usuario_campo->valor : '') .'" form="form_doc_'. $documento->id .'" disabled>';
                    }
                    echo '</div>';
                    echo '</td>';                    

                    echo '</tr>';
                }

                echo '</table>';
                // Fin campos
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

            // Renovación del documento
            #region Renovación
            echo '<div class="renovacion">';

            echo '<div class="renovacion_check">';

            $is_renovable = $doc_usuario && $doc_usuario->renovable > 0;
            
            echo '<input class="campo_'. $documento->id .'" name="renovable" id="renovacion" type="checkbox" form="form_doc_'. $documento->id .'" '. ($is_renovable ? 'checked' : '') .' disabled>';
            echo 'Renovable';
            echo '</div>';

            // Opciones para la renovación del documento
            #region Opciones de Renovación
            echo '<div class="renovacion_options" style="display:'. ($is_renovable ? 'block' : 'none') .'">';

            // Tipo de renovación
            #region Tipo de renovación
            echo '<div class="renovacion_type">';

            // Tipo Cada
            echo '<div class="reno_input">';
            
            echo '<input class="campo_'. $documento->id .'" name="tipo_renovacion" value="cada" id="cada" type="radio" form="form_doc_'. $documento->id .'" '. (!($doc_usuario > 0) || $doc_usuario->tipo_renovacion == 'cada' ? 'checked' : '')  .' disabled>';
            
            echo '<div class="title">';
            echo 'Cada';
            echo '</div>';
            
            echo '</div>';
            // Fin tipo cada
            
            // Tipo En la fecha
            echo '<div class="reno_input">';
            
            echo '<input class="campo_'. $documento->id .'" name="tipo_renovacion" value="fecha" id="fecha" type="radio" form="form_doc_'. $documento->id .'" '. ($is_renovable && $doc_usuario->tipo_renovacion == 'fecha' ? 'checked' : '') .' disabled>';

            echo '<div class="title">';
            echo 'En la fecha';
            echo '</div>';

            echo '</div>';
            // Fin tipo en la fecha

            echo '</div>';
            // Fin tipo
            #endregion tipo de renovación

            // Inputs de acuerdo con el tipo seleccionado
            #region Inputs tipo
            // Inputs tipo cada
            echo '<div class="type_cada">';

            echo '<div>';
            echo '<input class="main_input number_input campo_'. $documento->id .'" type="text" name="tipo_cada" value="'. ($is_renovable ? $doc_usuario->valor_cada : '') .'" form="form_doc_'. $documento->id .'" disabled>';
            
            echo '<select class="main_input campo_'. $documento->id .'" name="tiempo_cada" id="" form="form_doc_'. $documento->id .'" disabled>';
            echo '<option value="days" '. ($doc_usuario->tiempo_cada == 'days' ? 'selected' : '') .'>Días</option>';
            echo '<option value="months" '. ($doc_usuario->tiempo_cada == 'months' ? 'selected' : '') .'>Meses</option>';
            echo '<option value="years" '. ($doc_usuario->tiempo_cada == 'years' ? 'selected' : '') .'>Años</option>';
            echo '</select>';
            
            echo '</div>';

            echo '<div>';

            $cada_inicio = date("Y-m-d");

            if ($is_renovable && $doc_usuario->tipo_renovacion == "cada")
            {
                $cada_inicio = date("Y-m-d", strtotime('- '. $doc_usuario->valor_cada . ' ' . $doc_usuario->tiempo_cada, strtotime($doc_usuario->fecha_renovacion)));
            }

            echo 'A partir de: ';
            echo '<input type="date" class="main_input campo_'. $documento->id .'" name="cada_inicio" value="'. $cada_inicio .'" form="form_doc_'. $documento->id .'" disabled>';
            echo '</div>';
            
            echo '</div>';
            // Fin inputs tipo cada

            // Inputs tipo fecha
            echo '<div class="type_fecha">';

            $fecha = ($is_renovable && $doc_usuario->fecha_renovacion != "" ? date("Y-m-d", strtotime($doc_usuario->fecha_renovacion)) : date("Y-m-d"));
            echo '<input class="main_input campo_'. $documento->id .'" name="tipo_fecha" type="date" value="'. $fecha .'" form="form_doc_'. $documento->id .'" disabled>';

            echo '</div>';
            // Fin inputs tipo fecha

            // Alertas
            echo '<div class="alert">';

            echo '<div class="title">';
            echo 'Aviso Previo';
            echo '</div> ';

            // Input alerta
            echo '<div class="alert_input">';

            echo '<input class="main_input number_input campo_'. $documento->id .'" name="value_aviso" type="text" value="'. ($is_renovable ? $doc_usuario->valor_aviso : '') .'" form="form_doc_'. $documento->id .'" disabled>';

            echo '<select class="main_input campo_'. $documento->id .'" name="tiempo_aviso" id="" form="form_doc_'. $documento->id .'" disabled>';
            echo '<option value="days" '. ($doc_usuario->tiempo_aviso == 'days' ? 'selected' : '') .'>Días</option>';
            echo '<option value="months" '. ($doc_usuario->tiempo_aviso == 'months' ? 'selected' : '') .'>Meses</option>';
            echo '<option value="years" '. ($doc_usuario->tiempo_aviso == 'years' ? 'selected' : '') .'>Años</option>';
            echo '</select>';

            echo '</div>';
            // Fin input alerta

            echo '</div>';
            // Fin alertas

            echo '</div>';
            // Fin opciones
            #endregion opciones de renovación

            echo '</div>';
            // Fin Renovación

            echo '</div>';
            #endregion Campos a la derecha

            echo '</div>';
            #endregion Campos

            /**
             * Opciones en la parte inferior
             */
            echo '<div class="data_bottom">';

            echo '<div id="editing_campos_'. $documento->id .'" style="display: none">';
            echo '<form method="POST" action="./inc/usuario.inc.php" id="form_doc_'. $documento->id .'">';
            
            echo '<input type="hidden" name="user" value="'. $user->id .'">';
            echo '<input type="hidden" name="back" value="'. $back .'">';
            echo '<input type="hidden" name="documento_id" value="'. $documento->id .'">';
            echo '<input type="hidden" name="fk_usuario" value="'. $object->id .'">';
            
            
            echo '<button type="submit" class="btn_elemento" name="save_doc" id="edit_campos_'. $documento->id .'">GUARDAR</button>';
            
            echo '</form>';

            echo '<button type="button" class="btn_elemento edit_campos" id="edit_campos_'. $documento->id .'" style="margin-left: 5px">CANCELAR</button>';
            
            echo '</div>';
            
            echo '<div id="regular_campos_'. $documento->id .'">';
            
            echo '<button type="button" class="btn_elemento edit_campos" id="edit_campos_'. $documento->id .'">EDITAR</button>';
            echo '<button type="button" class="btn_elemento delete_obj" id="delete_doc_'. $documento->id .'" style="margin-left: 6px">BORRAR</button>';

            echo '</div>';
            
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
            echo '<div class="elemento_title elemento_title_hover do_pointer" onclick="document.getElementById('. "'dropdown_carpeta_". $carpeta->id ."'" .').click()">';

            // Regular
            echo '<div class="elemento_title_left" id="regular_carp_'. $carpeta->id .'">';

            // Ícono y nombre
            echo '<div class="elemento_name">';

            echo '<div class="elemento_icon folder"></div>';
            echo '<div class="title">'. $carpeta->nombre .'</div>';

            echo '</div>';
            // Fin nombre
            
            echo '</div>';
            // Fin regular

            // Dropdown
            echo '<div id="dropdown_carpeta_'. $carpeta->id .'" class="dropdown_button"></div>';

            echo '</div>';
            // Fin título

            /**
             * CONTENIDO
             */
            echo '<div class="elemento_content_folder" style="display:'. (in_array($carpeta->id, $open_list) ? 'block' : 'none') .'">';

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

dol_fiche_end();


llxFooter();

$db->close();

echo '<script src="./lib/js/docplus.js"></script>';
