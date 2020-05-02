<?php

	if ( !isset( $_POST['modelType_quer'] ) || empty($_POST['modelType_quer']) ) exit;
	$modelType_quer = $_POST['modelType_quer'];

	$handler = new \Views\_AddEdit\Models\Handler();
	
	if ( !$handler->connectToDB() ) exit;
	
	$resp = $handler -> getModelsByType($modelType_quer);
	
	$handler->closeDB();
	
	echo json_encode($resp);
	
	exit;