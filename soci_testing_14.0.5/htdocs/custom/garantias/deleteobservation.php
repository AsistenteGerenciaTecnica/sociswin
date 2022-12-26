<?php
include("db.php");
//Se recibe el id de la observacion a ser eliminada
if(isset($_GET['id'])) {
  $id = $_GET['id'];
  //Se obtiene el id de la hoja de vida
  $idh = $_GET['idh'];
  //Se actualiza el status de la hoja de vida a 0 para marcarla como eliminada
  $status  = 0;
  $query = "UPDATE llx_observation set status = '$status' WHERE rowid=$id";
  mysqli_query($conn, $query);
  header("Location: intervenciones.php?id=$idh");
}
?>