<?php

include('../../Glob_Controllers/classes/Handler.php');
$handler = new Handler(false, $_SERVER);
if ( !$connection = $handler->connectToDB() ) exit;

if ( isset($_POST['likeDisl']) ) {
	$likeDisl = $_POST['likeDisl'];
	$id = $_POST['id'];
	if ( $likeDisl == 1 ) {
		$arr['done'] = $handler->likePos($id);
	}
	if ( $likeDisl == 2 ) {
		$arr['done'] = $handler->dislikePos($id);
	}
	
	echo json_encode($arr);
	exit;
}

?>