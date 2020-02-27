<?php

include($_SERVER['DOCUMENT_ROOT'].'/HUF_DB/Views/Glob_Controllers/classes/General.php');

$stat = new General($_SERVER);
	
if ( !$connection = $stat->connectToDB() ) exit;


$query_stock = mysqli_query($connection, " SELECT * FROM stock ");
$query_coll = mysqli_query($connection, " SELECT name FROM collections ");
while( $row_coll[] = mysqli_fetch_assoc($query_coll) ){}
array_pop($row_coll);
$c = 0;
$result = array();
while( $row = mysqli_fetch_assoc($query_stock) ){
	
	for( $i = 0; $i < count($row_coll); $i++ ){
		if ( $row['collections'] === $row_coll[$i]['name'] ) continue(2);
	}
	$result[$c] = $row;
	$c++;
}
echo '<pre>';
print_r($result);
echo '</pre>';
?>