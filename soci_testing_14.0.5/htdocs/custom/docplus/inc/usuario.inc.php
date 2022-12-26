<?php

require '../../../main.inc.php';

require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/categoria.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/carpeta.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/documento.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/campo.class.php';

require_once DOL_DOCUMENT_ROOT.'/custom/docplus/lib/docplus.lib.php';

/**
 * GUARDAR INFORMACIÓN DEL DOCUMENTO
 */
if (isset($_POST["save_doc"]))
{
    $user = GETPOST("user");
    
    $fk_usuario = GETPOST("fk_usuario");
    $documento_id = GETPOST("documento_id");

    $renovable = (GETPOST("renovable") != "" ? "1" : "0");    
    
    // Si es cada, se suma al tiempo actual para determinar la fecha en que sería
    // la siguiente renovación
    if ($renovable > 0)
    {
        $tipo_renovacion = GETPOST("tipo_renovacion");
        if ($tipo_renovacion == "cada")
        {
            $fecha_inicio = GETPOST("cada_inicio");
            $cada = GETPOST("tipo_cada");
            $tiempo_cada = GETPOST("tiempo_cada");
    
            $fecha_renovacion = date("Y-m-d H:i:s", strtotime('+ '. $cada . $tiempo_cada, strtotime($fecha_inicio)));
        }
        // Si es una fecha, simplemente se pone ella misma
        else if ($tipo_renovacion == "fecha")
        {
            echo GETPOST("tipo_fecha");
            $fecha_renovacion = date("Y-m-d H:i:s", strtotime(GETPOST("tipo_fecha")));
        }
        
        $valor_aviso = GETPOST("value_aviso");
        $tiempo_aviso = GETPOST("tiempo_aviso");
    
        // El aviso es en la fecha de renovación calculada, menos el tiempo de aviso
        $aviso_renovacion = date("Y-m-d H:i:s", strtotime('- '. $valor_aviso . $tiempo_aviso, strtotime($fecha_renovacion)));
    }

    $documento = new Documento($db);
    $documento->fetch($documento_id);
    
    $doc_usuario = $documento->fetch_doc_objeto($fk_usuario);

    // Si no existe, crearlo
    if ($doc_usuario <= 0)
    {
        $res = $documento->create_doc_objeto(
            $user, 
            $fk_usuario, 
            $renovable, 
            $tipo_renovacion, 
            $cada,
            $tiempo_cada,
            $valor_aviso,
            $tiempo_aviso,
            $fecha_renovacion, 
            $aviso_renovacion
        );   
    }
    // Si existe, actualizarlo
    else
    {
        $res = $documento->update_doc_objeto(
            $user, 
		    $fk_usuario, 
		    $renovable, 
		    $cada, 
		    $tiempo_cada, 
		    $tipo_renovacion, 
		    $valor_aviso,
		    $tiempo_aviso,
		    $fecha_renovacion, 
		    $aviso_renovacion
        );
    }

    // Refrescar los valores de la relación
    $doc_usuario = $documento->fetch_doc_objeto($fk_usuario);

    // Si se registró la relación, guardar los valores de los campos
    if ($res > 0)
    {
        $cam = new Campo($db);

        $doc_campos = $cam->getAll($documento->id);

        foreach($doc_campos as $campo)
        {
            /**
             * Los inputs de los campos están guardados con la estructura
             * campo_d{id del documento}_{id del campo}
             */
            $campo_valor = GETPOST("campo_d". $documento->id ."_". $campo->id);

            // Se obtiene el registro para verificar si existe
            $doc_usuario_campo = $campo->fetch_doc_objeto_campo($fk_usuario);
            
            // Si no existe, se crea
            if ($doc_usuario_campo <= 0)
            {
                $res = $campo->create_doc_objeto_campo($user, $doc_usuario->rowid, $campo_valor);
            }
            // Si existe, se actualiza
            else
            {
                $res = $campo->update_doc_objeto_campo($user, $doc_usuario->rowid, $campo_valor);
            }
        }
    }

    /**
     * Se obtiene el listado de elementos padres del documento,
     * para que al momento de regresar a la vista, estén desplegados
     * todos los elementos necesarios para visualizar los cambios
     * realizados
     */
    $parents = get_parents($documento);
    

    echo '<br>user: '. $user;
    echo '<br>fk_usuario: '. $fk_usuario;
    echo '<br>documento_id: '. $documento_id;
    echo '<br>renovable: '. $renovable;
    echo '<br>tipo_renovacion: '. $tipo_renovacion;
    echo '<br>fecha_renovacion: '. $fecha_renovacion;
    echo '<br>aviso_renovacion: '. $aviso_renovacion;
    echo '<br>parents: '. $parents;
    
    $back = GETPOST("back");

    header('Location: '. $back . '?id='. $fk_usuario .'&open='. $parents .'&open_doc='. $documento->id .'&saved_doc_user='. $res);
    
}
else if (isset($_POST["upload_doc"]))
{
    $modulo = GETPOST("modulo");
    $fk_usuario = GETPOST("fk_usuario");
    $user = GETPOST("user");
    
    $doc_id = GETPOST("upload_doc_id");

    $tmp_file = ($_FILES['upload_doc']['tmp_name']);
    $filename = ($_FILES['upload_doc']['name']);

    $res = uploadFile($tmp_file, $filename, $modulo, $fk_usuario, $doc_id) ? "1" : "-1";

    $documento = new Documento($db);
    $documento->fetch($doc_id);

    $doc_usuario = $documento->fetch_doc_objeto($fk_usuario);

    // Si no existe, crearlo
    if ($doc_usuario <= 0)
    {
        $res = $documento->create_doc_objeto(
            $user, 
            $fk_usuario, 
            "0", 
            "", 
            "",
            "",
            "",
            "",
            "", 
            ""
        );   
    }

    $parents = get_parents($documento);

    $back = GETPOST("back");

    header('Location: '. $back . '?id='. $fk_usuario .'&open='. $parents .'&open_doc='. $documento->id .'&file_uploaded='. $res);
}
else if (isset($_POST["delete_doc"]))
{
    $modulo = GETPOST("modulo");
    $fk_usuario = GETPOST("fk_usuario");
    
    $doc_id = GETPOST("eliminar_doc_id");

    $documento = new Documento($db);
    $documento->fetch($doc_id);

    $res = $documento->delete_doc_objeto($fk_usuario);

    $parents = get_parents($documento);

    $back = GETPOST("back");

    header('Location: '. $back . '?id='. $fk_usuario .'&open='. $parents .'&open_doc='. $documento->id .'&doc_deleted='. $res);
}
else if (isset($_POST["delete_file"]))
{
    $modulo = GETPOST("modulo");
    $usuario_id = GETPOST("usuario_id");

    $doc_id = GETPOST("eliminar_doc_id");
    $doc_nombre = GETPOST("eliminar_file");

    $res = deleteFiles($usuario_id, $doc_id, $doc_nombre, $modulo);

    $documento = new Documento($db);
    $documento->fetch($doc_id);
    $parents = get_parents($documento);

    $back = GETPOST("back");

    header('Location: '. $back . '?id='. $usuario_id .'&open='. $parents .'&open_doc='. $documento->id .'&file_deleted='. $res);
}
else
{
    if (isset($_POST['back'])){
        header('Location: '. $_POST['back'] .'?nomethod');
    } else {
        header('Location: '. DOL_URL_ROOT .'/custom/docplus/docplus_index.php');
    }
}