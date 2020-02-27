<?php
	session_start();

	if ( isset($_POST['active']) )
	{
	
		$active = (int)$_POST['active'];
		$resp = 0;
		
		if ( $active === 1 ) {
			$_SESSION['selectionMode']['activeClass'] = "btnDefActive";
			$resp = 1;
		}
		if ( $active === 2 ) {
			$_SESSION['selectionMode']['activeClass'] = "";
			$resp = 2;
		}
		
		$_SESSION['selectionMode']['models'] = array();
		
		echo json_encode($resp);
		exit;
	}


	if ( isset($_POST['checkBox']) )
	{
		$checkBox = (int)$_POST['checkBox'];
		$id = (int)$_POST['modelId'];
		$name = isset($_POST['modelName']) ? $_POST['modelName'] : "";
		$type = isset($_POST['modelType']) ? $_POST['modelType'] : "";
		if ( $checkBox === 1 ) {
                    $_SESSION['selectionMode']['models'][$id] = array(
                        'id' => $id,
                        'name' => $name,
                        'type' => $type
                    );
		}
		if ( $checkBox === 2 ) {
			unset($_SESSION['selectionMode']['models'][$id]);
		}
		
		$resp = array();
		$resp['checkBox'] = $_POST['checkBox'];
		$resp['id'] = $_POST['modelId'];
		$resp['name'] = $_POST['modelName'];
		$resp['type'] = $type;
                
		echo json_encode($resp);
		exit;
	}


	if ( isset($_POST['checkSelectedModels']) ) {
		echo json_encode($_SESSION['selectionMode']['models']);
	}


	if ( isset($_GET['selectedModels']) && $_GET['selectedModels'] === 'show' )
	{
		if ( !$selectedModels = $_SESSION['selectionMode']['models']?:[]) return;
        unset($_SESSION['foundRow']);

        $orderBy = $_SESSION['assist']['reg'];
        $sortDirect = $_SESSION['assist']['sortDirect'];

		$statQuery = "";
		if ( isset($_SESSION['assist']['regStat']) && $_SESSION['assist']['regStat'] != "Нет" )
		{
			$regStat = $_SESSION['assist']['regStat'];
			$statQuery = "AND status='$regStat'";
		}

		require_once('../../Glob_Controllers/db.php');

        $modelIds = '(';
        foreach( $selectedModels as $model ) $modelIds .= $model['id'] .',';
        $modelIds = trim($modelIds,',') . ')';

        $selectRow = "SELECT * FROM stock WHERE id IN $modelIds $statQuery ORDER BY $orderBy $sortDirect";
        //debug($selectRow);

		$resultQuery = mysqli_query($connection, $selectRow);
		mysqli_close($connection);
		
		if ( !$resultQuery )
		{
			header("location: ../index.php");
			exit;
		}
		while( $foundRow = mysqli_fetch_assoc($resultQuery) ) $_SESSION['foundRow'][] = $foundRow;

		//debug($_SESSION['foundRow'],'foundRow=',1);

		$_SESSION['countAmount'] = count($_SESSION['foundRow']);
		$_SESSION['assist']['page'] = 0;
		$_SESSION['assist']['startfromPage'] = 0;
		//$_SESSION['assist']['collectionName'] = 'Выделенное';
		//$_SESSION['assist']['collection_id'] = -1;
		
		header("location: ../index.php");
	}