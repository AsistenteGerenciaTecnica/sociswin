<?php
//Se obtiene el nombre del archivo
if(!empty($_GET['file'])){
    $fileName = basename($_GET['file']);
    //Se obtiene la ruta del archivo
    $filePath = $_GET['filepath']."/".$fileName;
    if(!empty($fileName) && file_exists($filePath)){
        // Se defienen las cabeceras para ser leido
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/pdf");
        header("Content-Transfer-Encoding: binary");
        //Se lee el archivo
        readfile($filePath);
        exit;
    } else {
        echo 'El Archivo no existe.';
    }
}