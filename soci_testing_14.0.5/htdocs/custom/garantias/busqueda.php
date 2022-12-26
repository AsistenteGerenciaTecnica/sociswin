<!-- Se incluye conexion a Base de datos -->
<?php include("db.php");
header('Content-Type: text/html; charset=utf-8');
//Se obtiene el id de la orden de servicio
if (isset($_GET['cod'])) {
    $idorden=$_GET['cod'];
    //Se consulta la informacion de la orden de servicio con el id recibido
    $query="SELECT llx_fichinter.ref as ref, llx_fichinter.description as des, llx_societe.nom as nom FROM llx_fichinter, llx_societe WHERE llx_fichinter.rowid=$idorden AND llx_societe.rowid=llx_fichinter.fk_soc";
    $row = mysqli_fetch_assoc(mysqli_query($conn, $query));
    //Se imprime la informacion de la orden de servicio
    echo utf8_encode("<b>Ref: </b>".$row['ref']."</br>"."<b>Descripcion: </b>".$row['des']."</br>"."<b>Cliente: </b>".$row['nom']);
}
?>