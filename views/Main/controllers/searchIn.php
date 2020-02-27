<?php
	session_start();
	if ( !isset($_POST['searchInNum']) ) exit();
	$searchInNum = (int)$_POST['searchInNum'];
	$resp = "";
	if ( $searchInNum === 1 ) {
		$_SESSION['searchIn'] = 1;
		$resp = "В Базе ";
	}
	if ( $searchInNum === 2 ) {
		$_SESSION['searchIn'] = 2;
		$resp = "В Коллекции ";
	}
	echo $resp;