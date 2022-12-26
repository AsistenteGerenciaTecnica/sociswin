<?php

require '../../../main.inc.php';

require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/categoria.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/carpeta.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/documento.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/campo.class.php';

require_once DOL_DOCUMENT_ROOT.'/custom/docplus/lib/docplus.lib.php';

/**
 * NUEVA CATEGORÍA
 */
if (isset($_POST["new_cat"]))
{
    
    $user = GETPOST("user");
    $modulo = GETPOST("modulo");

    $categoria = new Categoria($db);

    $categoria->nombre = "Nueva Categoría";
    $categoria->modulo = $modulo;
    $categoria->tms = date("Y-m-d H:i:s");

    $res = $categoria->create($user);
    
    $back = GETPOST("back");

    header('Location: '. $back . '?modulo='. $modulo .'&editing_cat='. $categoria->id .'&created_cat='. $res);

}
/**
 * EDITAR CATEGORÍA
 */
else if (isset($_POST["edit_cat"]))
{

    $user = GETPOST("user");
    $modulo = GETPOST("modulo");

    $cat_id = GETPOST("edit_cat_id");
    $nombre = GETPOST("edit_cat_nombre");

    $categoria = new Categoria($db);
    $categoria->fetch($cat_id);

    $categoria->nombre = $nombre;
    $categoria->tms = date("Y-m-d H:i:s");

    $res = $categoria->update($user);

    $back = GETPOST("back");

    header('Location: '. $back . '?modulo='. $modulo .'&updated_cat='. $res);
}
/**
 * ELIMINAR CATEGORÍA
 */
else if (isset($_POST["delete_cat"]))
{

    $cat_id = GETPOST("eliminar_cat_id");
    $modulo = GETPOST("modulo");

    $categoria = new Categoria($db);
    $categoria->fetch($cat_id);

    $res = $categoria->delete();
    
    $back = GETPOST("back");

    header('Location: '. $back . '?modulo='. $modulo .'&deleted_cat='. $res);

}
/**
 * NUEVA CARPETA
 */
else if (isset($_POST["new_carp"]))
{

    $user = GETPOST("user");
    $parent_id = GETPOST("parent");
    $tipo_parent = GETPOST("tipo_parent");
    $modulo = GETPOST("modulo");

    $carpeta = new Carpeta($db);
    $carpeta->fk_parent = $parent_id;
    $carpeta->tipo_parent = $tipo_parent;
    $carpeta->nombre = "Nueva Carpeta";
    $carpeta->tms = date("Y-m-d H:i:s");

    $res = $carpeta->create($user);

    $parents = get_parents($carpeta);

    $back = GETPOST("back");
    header('Location: '. $back . '?modulo='. $modulo .'&open='. $parents .'&open_carp='. $carpeta->id .'&created_carp='. $res);

}
/**
 * EDITAR CARPETA
 */
else if (isset($_POST["edit_carp"]))
{

    $user = GETPOST("user");
    $modulo = GETPOST("modulo");
    
    $carp_id = GETPOST("edit_carp_id");
    $nombre = GETPOST("edit_carp_nombre");

    $carpeta = new Carpeta($db);
    $carpeta->fetch($carp_id);

    $carpeta->nombre = $nombre;
    $carpeta->tms = date("Y-m-d H:i:s");
    
    $res = $carpeta->update($user);

    $parents = get_parents($carpeta);

    $back = GETPOST("back");

    header('Location: '. $back . '?modulo='. $modulo .'&open='. $parents .'&open_carp='. $carp_id .'&updated_carp='. $res);

}
/**
 * NUEVO DOCUMENTO
 */
else if (isset($_POST["new_doc"]))
{
    $user = GETPOST("user");
    $parent_id = GETPOST("parent");
    $tipo_parent = GETPOST("tipo_parent");
    $modulo = GETPOST("modulo");

    $documento= new Documento($db);
    $documento->modulo = $modulo;
    $documento->fk_parent = $parent_id;
    $documento->tipo_parent = $tipo_parent;
    $documento->nombre = "Nuevo Documento";
    $documento->tms = date("Y-m-d H:i:s");
    $documento->date_creation = date("Y-m-d H:i:s");

    $res = $documento->create($user);

    $parents = get_parents($documento);

    $back = GETPOST("back");
    header('Location: '. $back . '?modulo='. $modulo .'&open='. $parents .'&open_doc='. $doc_id .'&created_doc='. $res);
}
/**
 * EDITAR DOCUMENTO
 */
else if (isset($_POST["edit_doc"]))
{
    $user = GETPOST("user");
    $modulo = GETPOST("modulo");
    
    $doc_id = GETPOST("edit_doc_id");
    $nombre = GETPOST("edit_doc_nombre");

    $documento = new Documento($db);
    $documento->fetch($doc_id);

    $documento->nombre = $nombre;
    $documento->tms = date("Y-m-d H:i:s");
    
    $res = $documento->update($user);

    $parents = get_parents($documento);

    $back = GETPOST("back");

    header('Location: '. $back . '?modulo='. $modulo .'&open='. $parents .'&open_doc='. $doc_id .'&updated_doc='. $res);
}
/**
 * ELIMINAR DOCUMENTO
 */
else if (isset($_POST["delete_doc"]))
{
    $modulo = GETPOST("modulo");
    
    $doc_id = GETPOST("eliminar_doc_id");

    $documento = new Documento($db);
    $documento->fetch($doc_id);

    $res = $documento->delete();

    $parents = get_parents($documento);

    $back = GETPOST("back");

    header('Location: '. $back . '?modulo='. $modulo .'&open='. $parents .'&open_doc='. $doc_id .'&deleted_doc='. $res);
}
/**
 * NUEVO CAMPO
 */
else if (isset($_POST["new_campo"]))
{
    $user = GETPOST("user");
    $modulo = GETPOST("modulo");
    
    $doc_id = GETPOST("documento");

    $campo = new Campo($db);

    $campo->fk_documento = $doc_id;
    $campo->valores = '';
    $campo->nombre = 'Nuevo Campo';
    $campo->tipo = 'text';
    $campo->tms = date('Y-m-d H:i:s');
    $campo->date_creation = date('Y-m-d H:i:s');

    $res = $campo->create($user);

    $documento = new Documento($db);
    $documento->fetch($campo->fk_documento);

    $parents = get_parents($documento);

    $back = GETPOST("back");

    header('Location: '. $back . '?modulo='. $modulo .'&open='. $parents .'&open_doc='. $doc_id .'&created_campo='. $res);
}
/**
 * EDITAR CAMPO
 */
else if (isset($_POST["edit_campo"]))
{
    $user = GETPOST("user");
    $modulo = GETPOST("modulo");
    
    $campo_id = GETPOST("campo_id");

    $nombre = GETPOST("campo_nombre");
    $tipo = GETPOST("campo_tipo");
    $valores = GETPOST("campo_valores");

    $campo = new Campo($db);
    $campo->fetch($campo_id);

    $campo->nombre = $nombre;
    $campo->tipo = ($tipo ? $tipo : $campo->tipo);
    $campo->valores = $valores;
    $campo->tms = date('Y-m-d H:i:s');

    echo $campo->tipo;

    $res = $campo->update($user);

    $documento = new Documento($db);
    $documento->fetch($campo->fk_documento);

    $parents = get_parents($documento);

    $back = GETPOST("back");

    header('Location: '. $back . '?modulo='. $modulo .'&open='. $parents .'&open_doc='. $campo->fk_documento .'&updated_campo='. $res);
}
/**
 * ELIMINAR CAMPO
 */
else if (isset($_POST["delete_campo"]))
{
    $modulo = GETPOST("modulo");
    
    $campo_id = GETPOST("eliminar_campo_id");

    $campo = new Campo($db);
    $campo->fetch($campo_id);

    $res = $campo->delete();

    $documento = new Documento($db);
    $documento->fetch($campo->fk_documento);

    $parents = get_parents($documento);

    $back = GETPOST("back");

    header('Location: '. $back . '?modulo='. $modulo .'&open='. $parents .'&open_doc='. $campo->fk_documento .'&deleted_campo='. $res);
}
else
{
    if (isset($_POST['back'])){
        header('Location: '. $_POST['back'] .'?nomethod');
    } else {
        header('Location: '. DOL_URL_ROOT .'/custom/docplus/docplus_index.php');
    }
}