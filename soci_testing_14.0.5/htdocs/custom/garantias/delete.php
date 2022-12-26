<?php
include("db.php");
//Se obtiene el id de la hoja de vida
if(isset($_GET['id'])) {
  $id = $_GET['id'];
  //Se actualiza el status de la hoja de vida a 0 para marcarla como eliminada, pero no se elimina de la base de datos.
  $status  = 0;
  $query = "UPDATE llx_equipment_history set status = '$status' WHERE rowid=$id";
  mysqli_query($conn, $query);
  header('Location: garantiasindex.php');
}
?>