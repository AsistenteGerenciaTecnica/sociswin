<?php include("db.php"); ?>
<!-- Se definen variables-->
<?php
$id='';
// Se obtiene el ID del elemento donde estamos
if  (isset($_GET['id'])) {
  $id = $_GET['id'];
  //Se obtiene el id de la hoja de vida
  $idh = $_GET['idh'];
  $fecha_actual=date("Y-m-d H:i:s");
  //Se cargan los datos en la variable $query para ser enviada a la base de datos
  //STATUS =1 NO ELIMINADA STATUS=0 ELIMINADA
  //STATUT =1 ABIERTA STATUT=0 CERRADA
  $query = "UPDATE llx_revision SET statut = 0 WHERE rowid=$id";
  mysqli_query($conn, $query);//se actualizan en la base de datos
  //Se consulta la orden de servicio con el id registrado en la tabla de intervencion interna "llx_revision"
  $query="SELECT llx_fichinter.rowid as rowid FROM llx_revision, llx_fichinter WHERE llx_fichinter.rowid = llx_revision.orden AND llx_revision.rowid =$id";
  $result = mysqli_fetch_assoc(mysqli_query($conn, $query));
  $result=$result['rowid'];
  //Se actualiza el statut de la orden de servicio a 3, que la marcara como realizada "done"
  $query = "UPDATE llx_fichinter SET fk_statut = 3 WHERE rowid=$result";
  mysqli_query($conn, $query);//se actualizan en la base de datos

  header("Location: intervenciones.php?id=$idh");
}