<?php include("db.php"); ?>

<!-- Se definen variables-->
<?php
$actividad='';
$frecuencia='';
$responsable='';
$destino='';
$idh=$_GET['idh'];

// Se obtiene el ID del elemento donde estamos
if  (isset($_GET['id'])) {
  $id = $_GET['id'];
  // Se extrae este ID en la base de datos
  $query = "SELECT * FROM llx_maintenance WHERE rowid=$id";
  $result = mysqli_query($conn, $query);
  //Se en la variable $result hay 1 elemento quiere decir que si lo encontro
  //Se procede guardar en la variables creadas los datos de la base de datos
  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
  }
}

//Se carga los datos al momento que se preciona el boton Grabar
if (isset($_POST['update'])) {
    $id = $_GET['id'];
    
    $actividad = $_POST['actividad'];
    $frecuencia = $_POST['frecuencia'];
    $responsable = $_POST['responsable'];

    $foto=$_FILES['image']['name'];
    $ruta=$_FILES['image']['tmp_name'];
    $destino="images/".$foto;
    copy($ruta, $destino);
  
    //Se cargan los datos en la variable $query para ser enviada a la base de datos
    $query = "UPDATE llx_maintenance set activities = '$actividad', frecuency = '$frecuencia', responsable = '$responsable', imagen = '$destino' WHERE rowid=$id";
    mysqli_query($conn, $query);//se actualizan en la base de datos
    $_SESSION['message'] = 'Modificada';
    $_SESSION['message_type'] = 'warning';
    header("Location: hojagarantia.php?id=$idh");
}

//al presionar el boton cancelar se carga la pagina principal
if (isset($_POST['cancelar'])) {
  header("Location: hojagarantia.php?id=$idh");
}

// Load Dolibarr environment
$res = 0;
// ESQUELETO MODULO IMPORTANTE
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";

// Security check
$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0)
{
	$action = '';
	$socid = $user->socid;
}

//HEADER MODULO
llxHeader("", $langs->trans("Area Garantias"));
//TITULO MODULO
print load_fiche_titre($langs->trans("Area Garantias"), '', 'garantias.png@garantias');

?>

<head>
    <link rel="stylesheet" href="styles.css">
</head>

<div>
    <form action="editmaintenance.php?id=<?php echo $_GET['id']; ?>&idh=<?php echo $idh ?>" method="POST">
    <h4>PROCEDIMIENTO DE MANTENIMIENTO</h4></br></br>
      <label>Actividad</label>
      <input name="actividad" type="text" class="form-control" value="<?php echo $row['activities']; ?>"></br></br>
      <label>Frecuencia </label>
      <input name="frecuencia" type="text" class="form-control" value="<?php echo $row['frecuency']; ?>"></br></br>
      
      <label>Responsable </label>
      <select name="responsable"><?php
      $query="SELECT llx_user.lastname as apellido, llx_user.rowid as id, llx_user.firstname as nombre FROM llx_usergroup, llx_usergroup_user, llx_user WHERE llx_usergroup.nom LIKE '%Tecnicos%' AND llx_usergroup.rowid=llx_usergroup_user.fk_usergroup AND llx_usergroup_user.fk_user=llx_user.rowid";
      $group = mysqli_query($conn, $query);
      while($row2 = mysqli_fetch_assoc($group)) {?>
          <option value="<?php echo $row2['nombre']; echo" "; echo $row2['apellido'];?>"><?php echo $row2['nombre']; echo" "; echo $row2['apellido'];?></option>
      <?php } ?>
      </select></br></br>
      <label>Imagen</label>
      <input type="file" name="image" id="image" class="form-control" value="<?php echo $row['imagen']; ?>"></br></br>

    </br></br>
      <!-- Boton para guardar lo modificado-->
      <button class="btn btn-secondary" name="update">Grabar</button>
      <button class="btn btn-secondary" name="cancelar">Anular</button>
    </form>
</div>

<!-- Footer Esqueleto Dolibarr IMPORTANTE-->
<?php
llxFooter();
?>