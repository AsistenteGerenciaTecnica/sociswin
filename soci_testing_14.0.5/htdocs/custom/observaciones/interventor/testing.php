<?php

/* $dir = "uploads/";
$file = $dir . basename($_FILES["fileToUpload"]["name"]);

echo "file: ". $file . "<br>";
echo "tmp: ". $_FILES["fileToUpload"]["tmp_name"] . "<br>";

$uploadOk = 1;

$imageFileType = strtolower(pathinfo($file, PATHINFO_EXTENSION));

echo $imageFileType;

if (isset($_POST['upload'])){
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        echo "Es imagen - " . $check["mime"];
        $uploadOk = 1;
    } else {
        echo "No es imagen";
        $uploadOk = 0;
    }
}

if (isset($_POST["upload"]) ){
    if ($uploadOk == 0) {
        echo "<br>Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $file)) {
            echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
        } else {
            echo "<br>Sorry, there was an error uploading your file.";
        }
    }
} */

if (isset($_POST["canvas_content"])) {
    
    $upload_dir = "uploads/";

    $img = $_POST["canvas_content"];

    $img = str_replace('data:image/png;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);
    $file = $upload_dir."firma". date("Y-m-d H:i:s") .".png";
    $success = file_put_contents($file, $data);

    if ($success) {
        echo '<div>Se guardó la firma</div>';
    } else {
        echo '<div>XD</div>';
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!-- <form action="testing.php" method="POST" enctype="multipart/form-data">
        Imagen: 
        <input type="file" name="fileToUpload" id="fileToUpload"/>
        <input type="submit" value="UPLOAD" name="upload">
    </form> -->

    <div style="border-style:solid; width: 50%">
        <div>Firma</div>
        <div style="height: 500px; overflow: hidden">
            <canvas id="canvas">
                
            </canvas>
        </div>
        <div>
            <form action="" method="POST" name="canvas_form">
                <input type="hidden" id="canvas_content" name="canvas_content" value="">
            </form>
            <button id="clear">Limpiar</button>
            <button id="save">Guardar</button>
        </div>
        </div>
</body>
</html>

<script src="./js/firma.js"></script>


