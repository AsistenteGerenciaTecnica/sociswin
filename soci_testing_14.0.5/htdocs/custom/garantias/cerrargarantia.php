<?php include("db.php"); ?>
<!-- Se definen variables-->
<?php
$id='';
$fecha_actual='';
// Se obtiene el ID del elemento donde estamos
if  (isset($_GET['id'])) {
  $id = $_GET['id'];
  //Se obtiene el id de la hoja de vida
  $idh = $_GET['idh'];
  //Se crea la fecha de hoy, fecha actual
  $fecha_actual=date("Y-m-d H:i:s");
  //Se cargan los datos en la variable $query para ser enviada a la base de datos
  $query = "UPDATE llx_garantia set statut = 0 WHERE rowid = '$id'";
  mysqli_query($conn, $query);//se actualizan en la base de datos
  header("Location: enviogarantia.php?id=$idh");
}
?>