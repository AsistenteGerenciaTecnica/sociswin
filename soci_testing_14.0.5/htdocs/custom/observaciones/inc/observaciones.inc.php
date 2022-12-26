<?php

/**
 * 
 * INCLUDES
 * 
 * Esta ṕagina contiene las funcionalidades necesarias
 * para el CRUD de las observaciones. 
 * 
 * Incluye: 
 * - Creación de observaciones
 * - Eliminación de observaciones
 * - Actualización de observaciones
 * - Listado completo de observaciones
 * - Creación y eliminación de archivos subidos
 * - Subida de firmas
 * 
 * Se trata de seguir todo lo posible los lineamientos establecidos
 * por Dolibarr, utilizando las funciones internas de la base de datos,
 * que además generan registros en el archivo dolibarr.log
 * 
 */

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/fichinter/class/fichinter.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/observaciones/class/observacion.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';

/**
 * 
 * SUBIR IMÁGENES
 * 
 * 
 * @param   $tmp_file   string  Cadena con la ubicación temporal del archivo subido
 * @param   $filename   string  Nombre original del archivo
 * @param   $obs_id     int     ID de la observación
 * 
 * @return  $location   string  Dirección en la que se guardó el archivo
 * @return  false       bool    Si no se guardó el archivo
 *                              
 * 
 */

function uploadImage($tmp_file, $filename, $obs_id) {

    // Dirección de la carpeta donde se va a subir el archivo
    $upload_dir = DOL_DOCUMENT_ROOT .'/custom/observaciones/upload/obs/' . $obs_id;

    // Dirección incluyendo el nombre del archivo
    $imageFileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $new_filename = 'obs-'. $obs_id .'.'. $imageFileType;
    $file = $upload_dir . "/". $new_filename;;

    if (!file_exists($upload_dir)) {
        $created = mkdir($upload_dir, 0777, true);
    }

    $result = move_uploaded_file($tmp_file, $file);

    $location = DOL_URL_ROOT .'/custom/observaciones/upload/obs/'. $obs_id .'/'. $new_filename;

    if ($result) {
        return $location;
    }
    return false;

}


/**
 * 
 * ELIMINAR IMÁGENES Y CARPETA
 * 
 * 
 * @param   $obs_id         int     ID de la observación
 * @param   $filename       string  Nombre original del archivo
 * 
 * @return  $res            int     Resultado positivo si salió bien, negativo si no
 * 
 * Crea la dirección de la carpeta donde se guarda la imagen
 * Genera la dirección del archivo
 * Elimina el archivo
 * Elimina la carpeta                              
 * 
 */

function deleteFiles($obs_id, $filename){

    $dir = DOL_DOCUMENT_ROOT .'/custom/observaciones/upload/obs/' . $obs_id;
    
    $imageFileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $remove_path = $dir .'/obs-'. $obs_id .'.'. $imageFileType;

    $res = 1;

    if (file_exists($remove_path)){
        if (unlink($remove_path)){
            $res = 1;
        }
    }
    
    if (file_exists($dir)){
        if(rmdir($dir)) {
            $res = 1;
        } else {
            $res = -2;
        }
    }
    return $res;

}

if (isset($_POST["update"])){

    $descripcion = GETPOST("descripcion");

    // Tomar la fecha y separarla en sus partes
    $fecha = GETPOST("fecha");
    
    ($fecha == "" ? $fecha = date("Y-m-d") : $fecha);

    $dia_fecha = date("d", strtotime($fecha));
    $mes_fecha = date("m", strtotime($fecha));
    $year_fecha = date("Y", strtotime($fecha));

    $horas_fecha = GETPOST("horas_fecha");
    ($horas_fecha == "" ? $horas_fecha = 0 : $horas_fecha);

    $mins_fecha = GETPOST("mins_fecha");
    ($mins_fecha == "" ? $mins_fecha = 0 : $mins_fecha);

    $fecha_full = date(
        "Y-m-d H:i:s", 
        mktime(
            $horas_fecha, 
            $mins_fecha, 
            0, 
            $mes_fecha, 
            $dia_fecha, 
            $year_fecha
        ));

    // Tomar la duración y convertirla en segundos
    //$horas_duracion = GETPOST("horas_duracion");
    //$mins_duracion = GETPOST("mins_duracion");

    //$duracion_secs = ((int)$horas_duracion * 3600) + ((int)$mins_duracion * 60);

    $tmp_file = ($_FILES['upload_image']['tmp_name']);
    $filename = ($_FILES['upload_image']['name']);

    $line_id = GETPOST("line_id");
    $inter_id = GETPOST("id");
    
    $user = GETPOST("user_id");
    ($user == "" ? $user = 1 : $user);    
    
    $from = GETPOST("from");

    if ($tmp_file && $filename) {
        $image = uploadImage($tmp_file, $filename, $line_id);
    }

    $obs = new Observacion($db);
    $obs->fetch($line_id);
    
    $obs->descripcion = $descripcion;
    $obs->fecha = $fecha_full;

    if ($image){
        $obs->filename = $image;
    }
    //$obs->duracion = $duracion_secs;
    
    $res = $obs->update($user);

    $back = GETPOST("back");

    header('Location: '. $back .'?id='. $inter_id . '&from='. $from .'&updated='. ($res >= 0 ? "1" : "0"));
    
    exit();    
}
else if (isset($_POST['create']))
{
    
    $new_descripcion = GETPOST("new_descripcion");

    $new_fecha = GETPOST("new_fecha");

    ($new_fecha == "" ? $new_fecha = date("Y-m-d") : $new_fecha);

    $dia_fecha = date("d", strtotime($new_fecha));
    $mes_fecha = date("m", strtotime($new_fecha));
    $year_fecha = date("Y", strtotime($new_fecha));

    $new_horas_fecha = GETPOST("new_fecha_hora", "int");
    ($new_horas_fecha == "" ? $new_horas_fecha = date("H") : $new_horas_fecha);

    $new_mins_fecha = GETPOST("new_fecha_min", "int");
    ($new_mins_fecha == "" ? $new_mins_fecha = date("i") : $new_mins_fecha);

    $new_fecha_full = date(
        "Y-m-d H:i:s", 
        mktime(
            $new_horas_fecha, 
            $new_mins_fecha, 
            0, 
            $mes_fecha, 
            $dia_fecha, 
            $year_fecha
        ));

    $tmp_file = ($_FILES['upload_image']['tmp_name']);
    $filename = ($_FILES['upload_image']['name']);

    $line_id = GETPOST("line_id");
    $inter_id = GETPOST("id");
    $user = GETPOST("user_id");

    ($user == "" ? $user = 1 : $user);

    $from = GETPOST("from");

    $obs = new Observacion($db);
    
    $obs->fk_intervention = $inter_id;
    $obs->descripcion = $new_descripcion;
    $obs->fecha = $new_fecha_full;
    //$obs->duracion = $new_duracion_secs;
    $obs->filename = (!empty($image) ? $image : "NULL");
    $obs->date_creation = date('Y-m-d H:i:s');
    $obs->tms = date('Y-m-d H:i:s');
    
    $res = $obs->create($user);

    $file_uploaded = uploadImage($tmp_file, $filename, $obs->id);

    if ($file_uploaded){
        $obs->filename = $file_uploaded;

        $obs->update($user);
    }

    $back = GETPOST("back");

    header('Location: '. $back .'?id='. $inter_id . '&from='. $from .'&created='. ($res >= 0 ? "1" : "0"));
    

}
else if (isset($_POST['delete']))
{

    $inter_id = GETPOST("id");
    $obs_id = GETPOST("line_id");

    $obs = new Observacion($db);
    $obs->fetch($obs_id);

    if ($obs->filename && $obs->filename != "NULL"){
        $res = deleteFiles($obs_id, $obs->filename);
    }

    $res = $obs->delete();

    $from = GETPOST("from");
    $back = GETPOST("back");

    header('Location: '. $back .'?id='. $inter_id . '&from='. $from .'&deleted='. ($res >= 0 ? "1" : $res));
       
}
else if (isset($_POST["canvas_content"])) {
    
    $img = GETPOST("canvas_content");
    $sign_type = GETPOST("sign_type");
    $inter_id = GETPOST("id");
    $upload_dir = DOL_DOCUMENT_ROOT .'/custom/observaciones/upload/signatures/'. $inter_id .'/';

    if (!file_exists($upload_dir)) {
        $created = mkdir($upload_dir, 0777, true);
    }

    
    $img = str_replace('data:image/png;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);
    
    $name = "sign-". $sign_type .'-'. $inter_id .'.png';
    
    $file = $upload_dir . $name;
    $success = file_put_contents($file, $data);

    $success ? $res = 1 : $res = 0;
    
    $back = GETPOST("back");
    $from = GETPOST("from");

    header('Location: '. $back .'?id='. $inter_id . '&from='. $from .'&signed='. ($res >= 0 ? "1" : $res));

}
else 
{
    header('Location: '. DOL_URL_ROOT .'/custom/observaciones/interventor/interventor_index.php');
}

