<?php

    if ( isset($_POST['widthControl']) && (int)$_POST['widthControl'] === 1 ) {
        session_start();
        $_SESSION['assist']['containerFullWidth'] = 1;
        $arr['done'] = 1;

        $strname = "assist[containerFullWidth]";
        setcookie($strname, "", 1, '/', $_SERVER['HTTP_HOST'] );
        setcookie($strname, 1, time()+(3600*24*30), '/', $_SERVER['HTTP_HOST'] );

        echo json_encode($arr);
        exit;
    }
    if ( isset($_POST['widthControl']) && (int)$_POST['widthControl'] === 2 ) {

        session_start();
        $_SESSION['assist']['containerFullWidth'] = 0;
        $arr['done'] = 2;

        $strname = "assist[containerFullWidth]";
        setcookie($strname, "", 1, '/', $_SERVER['HTTP_HOST'] );
        setcookie($strname, 0, time()+(3600*24*30), '/', $_SERVER['HTTP_HOST'] );

        echo json_encode($arr);
        exit;
    }

	if ( isset($_POST['noticeActivate']) && (int)$_POST['noticeActivate'] === 1 ) {
		session_start();
		$_SESSION['assist']['PushNotice'] = 1;
		$arr['done'] = 1;
		
		$strname = "assist[PushNotice]";
		setcookie($strname, "", 1, '/', $_SERVER['HTTP_HOST'] );
		setcookie($strname, 1, time()+(3600*24*30), '/', $_SERVER['HTTP_HOST'] );
		
		echo json_encode($arr);
		exit;
	}
	if ( isset($_POST['noticeActivate']) && (int)$_POST['noticeActivate'] === 2 ) {
		
		session_start();
		$_SESSION['assist']['PushNotice'] = 0;
		$arr['done'] = 2;
		
		$strname = "assist[PushNotice]";
		setcookie($strname, "", 1, '/', $_SERVER['HTTP_HOST'] );
		setcookie($strname, 0, time()+(3600*24*30), '/', $_SERVER['HTTP_HOST'] );
		
		echo json_encode($arr);
		exit;
	}
	
	if ( isset($_POST['srcBgImg']) && !empty($_POST['srcBgImg']) ) {
		
		
		session_start();
		$_SESSION['assist']['bodyImg'] = $_POST['srcBgImg'];
		$arr['done'] = 1;
		$strname = "assist[bodyImg]";
		
		setcookie($strname, "", 1, '/', $_SERVER['HTTP_HOST'] );
		setcookie($strname, $_POST['srcBgImg'], time()+(3600*24*30), '/', $_SERVER['HTTP_HOST'] );

		echo json_encode($arr);
		exit;
	}