<style type="text/css">
	.item:hover {
		cursor: pointer;
	}
</style>

<?php

include("db.php");

$search_term = $_POST['city2'];
$search_term = strtoupper($search_term);

$sql = "SELECT ref FROM llx_commande_fournisseur WHERE ref LIKE '%{$search_term}%'";
$result = mysqli_query($conn, $sql) or die("SQL Query Failed.");

$output = "<ul>";

	if(mysqli_num_rows($result) > 0){  
		while($row = mysqli_fetch_assoc($result)){
			$output .= "<li class='item'>{$row['ref']}</li>";
		}
  }else{  
  	$output .= "<li>NO existe orden </li>";
  } 
$output .= "</ul>";

echo $output;

?>
