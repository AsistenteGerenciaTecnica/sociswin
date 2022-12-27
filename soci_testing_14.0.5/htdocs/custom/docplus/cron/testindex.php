<?php
require '/usr/share/php/libphp-phpmailer/class.phpmailer.php';
require '/usr/share/php/libphp-phpmailer/class.smtp.php';

$mail = new PHPMailer;
$mail->setFrom('nicolas.valencia@ultimate.com.co');
$servername = "localhost";
$username = "ultimate";
$password = "Ultmplataforma12A";
$dbname = "ultimate_dolibarr";


$conexion= mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conexion) {
    die("Connection failed: " . mysqli_connect_error());
}
//echo "Connected successfully";

$fecha_actual=date("Y-m-d");

$hora_actual=date("H-i-s"); //hora en formato 24 h, i= minuto

//echo $fecha_actual;

//echo "la hora actual es: $hora_actual ";

//include("database.php");


//$proveedores ="SELECT * FROM llx_societe";
$comp = 0;
$proyectos_atraso = "SELECT * FROM llx_projet WHERE datee<='$fecha_actual' AND fk_statut=1";

//$resultado = mysqli_query($conexion, $proveedores);

$resultado = mysqli_query($conexion, $proyectos_atraso);

$mail->Body .= 'PROYECTO(S) EN RETRASO:';
//You would want $_POST["one"]."\n".$_POST["two"]; as you need to have the \n surrounded by double quotes. Or in my second example $mail->Body .= "\n".$_POST["two"];

$mail->Body .= "\n";


//$mensaje   .= " PROYECTO(S) EN RETRASO: ";

while($rec=mysqli_fetch_assoc($resultado)){

//echo $rec["code_fournisseur"];
//echo $rec["ref"];
$proy= $rec["ref"];

$mail->Body   .= " \n ";
$mail->Body   .= " \n ";
$mail->Body   .= $proy;
$mail->Body   .= " \n ";

$comp=1;
}
//echo $mail->Body;
if($comp==1){
$mail->addAddress('nicolas.valencia@ultimate.com.co', 'Nicolas Valencia');  
$mail->addAddress('practicantegt21@gmail.com', 'Mateo Hernandez');
//$para      = "nicolas.valencia@ultimate.com.co";
//$mail->addAddress('nicolas.valencia@ultimate.com.co', 'nicolas');
//$titulo    = "Retraso de proyecto  $proy";
$mail->Subject   = "!!! PRECAUCION - SE EVIDENCIAN RETRASOS DE PROYECTOS";
$mail->IsSMTP();
$mail->SMTPSecure = 'ssl';
$mail->Host = 'ssl://smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Port = 465;

//Set your existing gmail address as user name
$mail->Username = 'nicolas.valencia@ultimate.com.co';

//Set the password of your gmail address here
$mail->Password = 'fdasfsfbpufjrynl';
$mail->send();
//if(!$mail->send()) {
//  echo 'Email is not sent.';
//  echo 'Email error: ' . $mail->ErrorInfo;
//} else {
//  echo 'Email has been sent.';
//}
}

$file = fopen("/opt/test.txt","w");
fwrite($file, "prueba");
fwrite($file, "test");

fclose($file);

mysqli_close($conexion);
?>


