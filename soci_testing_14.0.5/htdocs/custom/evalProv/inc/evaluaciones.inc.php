<?php

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/fichinter/class/fichinter.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/evalProv/class/evaluacion.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/evalProv/class/pregunta.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/evalProv/class/categoria.class.php';

// Crear nueva categoría
if (isset($_POST['nueva_cat']))
{

    $nombre = GETPOST("cat_nombre");
    $user = GETPOST("user");
    $back = GETPOST("back");

    $nombre = strtolower($nombre);

    $categoria = new Categoria($db);

    $list = $categoria->getAll();

    for ($i = 0; $i < count($list); $i++) {
        $cat = $list[$i];

        if ($cat->nombre == $nombre){
            
            header('Location: '. $back .'?created=0&error=repeatedCategoryName');    
            exit();
        }
    }

    $categoria->nombre = $nombre;
    $categoria->tms = date("Y-m-d H:i:s");

    $res = $categoria->create($user);  

    header('Location: '. $back .'?createdCat='. ($res >= 0 ? "1" : $res));    

} 
// Actualizar categoría
else if (isset($_POST["update_cat"]))
{
    $cat_id = GETPOST("cat_id");
    $nombre = GETPOST("cat_nombre_ed");
    $back = GETPOST("back");
    $user = GETPOST("user");

    $nombre = strtolower($nombre);

    $categoria = new Categoria($db);

    $list = $categoria->getAll();

    for ($i = 0; $i < count($list); $i++) {
        $cat = $list[$i];

        if ($cat->nombre == $nombre){
            
            header('Location: '. $back . '?editing_cat='. $cat_id .'&updated=0&error=editRepeatedCategoryName');    
            exit();
        }
    }

    $categoria->fetch($cat_id);

    $categoria->nombre = $nombre;
    $categoria->tms = date("Y-m-d H:i:s");

    $res = $categoria->update($user);

    header('Location: '. $back .'?updatedCat='. ($res >= 0 ? "1" : $res));

}
// Eliminar categoría
else if (isset($_POST["delete_cat"]))
{

    $cat_id = GETPOST("cat_id");
    $back = GETPOST("back");

    $categoria = new Categoria($db);
    $categoria->fetch($cat_id);

    $res = $categoria->delete();

    header('Location: '. $back .'?deletedCat='. ($res >= 0 ? "1" : $res));

}
// Nueva pregunta
else if (isset($_POST['nueva_preg']))
{

    $categoria_id = GETPOST("categoria");
    $descripcion = GETPOST("descripcion");
    $tipo = GETPOST("tipo");

    $back = GETPOST("back");
    $user = GETPOST("user");

    
    $pregunta = new Pregunta($db);
    
    $pregunta->ref = 'PREG-'. $categoria_id .'-'. date("H:i:s");
    $pregunta->pregunta = $descripcion;
    $pregunta->tipo = $tipo;
    $pregunta->fk_categoria = $categoria_id;
    $pregunta->tms = date("Y-m-d H:i:s");
    $pregunta->date_creation = date("Y-m-d H:i:s");
    
    echo $pregunta->ref;
    $res = $pregunta->create($user);
    
    
    header('Location: '. $back .'?createdPreg='. ($res >= 0 ? "1" : $res) . '&id=' . $pregunta->id); 


}
// Actualizar pregunta
else if(isset($_POST['update_preg']))
{

    $categoria_id = GETPOST("categoria_ed");
    $preg = GETPOST("pregunta_ed");
    $tipo = GETPOST("tipo_ed");
    $preg_id = GETPOST("preg_id_ed");

    $back = GETPOST("back_ed");
    $user = GETPOST("user_ed");

    $pregunta = new Pregunta($db);

    $pregunta->fetch($preg_id);

    $pregunta->pregunta = $preg;
    $pregunta->tipo = $tipo;
    $pregunta->fk_categoria = $categoria_id;
    $pregunta->tms = date("Y-m-d H:i:s");

    $res = $pregunta->update($user);

    header('Location: '. $back .'?updatedPreg='. ($res >= 0 ? "1" : $res) . '&id=' . $preg_id); 

}
// Eliminar pregunta
else if (isset($_POST['delete_preg']))
{

    $preg_id = GETPOST("preg_id");
    $back = GETPOST("back");

    $pregunta = new Pregunta($db);
    $pregunta->fetch($preg_id);

    $res = $pregunta->delete();

    header('Location: '. $back .'?deletedPreg='. ($res >= 0 ? "1" : $res) . '&id=' . $preg_id); 

}
else 
{
    if (isset($_POST['back'])){
        header('Location: '. $back);
    } else {
        header('Location: '. DOL_URL_ROOT .'/custom/evalProv/evalProv_index.php');
    }
}