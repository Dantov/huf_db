<?php
	
	if ( !isset( $_POST['modelType_quer'] ) || empty($_POST['modelType_quer']) ) exit;
	$modelType_quer = $_POST['modelType_quer'];

	//$dir = _stockDIR_HTTP_; //"../../Stock/";
	include(_globDIR_.'classes/Handler.php');
	
	$handler = new Handler();
	
	if ( !$handler->connectToDB() ) exit;
	
	$resp = $handler -> getModelsByType($modelType_quer);
	
	$handler->closeDB();
	
	echo json_encode($resp);
	
	exit;
?>
