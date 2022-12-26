<?php include("db.php"); ?>
<!-- Se definen variables-->
<?php
$id='';
$fecha_actual='';

// Se obtiene el ID de la hoja de vida
if  (isset($_GET['id'])) {
  $id = $_GET['id'];
  //Se crea una variable con la fecha de hoy
  $fecha_actual=date("Y-m-d H:i:s");
  $mes=date("m");
  $dia=date("d");
  $query = "SELECT COUNT(*) FROM llx_garantia";
  $result = mysqli_fetch_assoc(mysqli_query($conn, $query));
  $num=$result['COUNT(*)']+1;
  $ref="GR" . $dia . $mes . "-" . $num;
  //Se cargan los datos en la variable $query para ser enviada a la base de datos
  //STATUS =1 NO ELIMINADA STATUS=0 ELIMINADA
  //STATUT =1 ABIERTA STATUT=0 CERRADA
  $query = "INSERT INTO llx_garantia (date_send, ref, fk_equipment_history, status, statut) 
  VALUES ('$fecha_actual', '$ref', '$id', 1, 1)";
  mysqli_query($conn, $query);//se actualizan en la base de datos
  header("Location: enviogarantia.php?id=$id");
}
?>