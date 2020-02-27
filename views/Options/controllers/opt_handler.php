<?php
	
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
		
		/*
		$url = $_POST['srcBgImg'];
		$class="
		.bodyimg {
			background: url('$url') no-repeat fixed center center rgba(0, 0, 0, 0) !important;
			background-size: 100% 100% !important;
		}";
		$cssfile = $_SERVER['DOCUMENT_ROOT'].'/HUF_DB/css/bodyImg.css';
		$handle = fopen($cssfile, "w"); // w - Открывает файл только для записи; помещает указатель в начало файла и обрезает файл до нулевой длины. Если файл не существует - пробует его создать.
		
		$bytes = fwrite($handle, $class);
		
		fclose($handle);
		
		if ( $bytes ) {
			$arr['done'] = 1;
		} else {
			$arr['done'] = 0;
		}
		*/
		echo json_encode($arr);
		exit;
	}
	
?>