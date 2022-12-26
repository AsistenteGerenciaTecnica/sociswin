<?php 
include("db.php");
/*
$conn = mysqli_connect(
    'localhost',
    'ultimate',//usuario
    'Ultmplataforma12A',//contraseña
    'ultimate_dolibarr'//base de datos
  ) or die(mysqli_erro($mysqli));

*/
//capturo por metodo get, el valor del input cliente en la funcion javascritp inicial
$proyecto = $_GET['cliente'];


//genero consulta a la bd, que me indique los nombres de los proyectos, cuando coincidan las llaves y el nombre del cliente
$query="SELECT title FROM llx_projet, llx_societe WHERE llx_societe.rowid=llx_projet.fk_soc AND llx_societe.nom = '$proyecto' " ; 

$result = mysqli_query($conn, $query);

// creo un array vacio para ir agregando los diferentes elementos (proyectos al arreglo)
$arraypr= array();
if($result){
    //while para recorre la consulta
  while($pr = mysqli_fetch_array($result)){
      //codificacion utf8 del campo title de la consulta
    $proyecto = utf8_encode($pr['title']);
    //agrego elemento por elemento a el arreglo
    array_push($arraypr, $proyecto);
  } 
  array_push($arraypr, "Venta sin Proyecto");
  //devuelvo el arreglo en tipo JSON a la funcion javascript
  echo json_encode($arraypr);
}   




?>