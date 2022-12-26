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
$refer = $_GET['reference'];


//genero consulta a la bd, que me indique los nombres de los proyectos, cuando coincidan las llaves y el nombre del cliente

$query="SELECT label FROM llx_product WHERE ref='$refer' ORDER BY ref"; 


$result = mysqli_query($conn, $query);

// creo un array vacio para ir agregando los diferentes elementos (proyectos al arreglo)
$arraylabel= array();
if($result){
    //while para recorre la consulta
  while($pr = mysqli_fetch_array($result)){
      //codificacion utf8 del campo title de la consulta
    $label = utf8_encode($pr['label']);
    //agrego elemento por elemento a el arreglo
    array_push($arraylabel, $label);
  } 
  
  //devuelvo el arreglo en tipo JSON a la funcion javascript
  echo json_encode($arraylabel);
}   




?>