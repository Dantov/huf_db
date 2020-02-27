<?php
	
	require('classes/PushNotice.php');
	
	$pn = new PushNotice();

	if ( isset($_POST['closeNotice']) ) {
		
		$arr['done'] = $pn->addIPtoNotice( (int)$_POST['closeNotice'] );
        //echo (int)$_POST['closeNotice'];
		echo json_encode($arr);
		exit;
	}
	if ( isset($_POST['closeAllPN']) ) {

		$arr['done'] = $pn->addIPtoALLNotices($_POST['closeById']);
		
		echo json_encode($arr);
		exit;
	}
	
	$arr = $pn->checkPushNotice();
	
	echo json_encode($arr);