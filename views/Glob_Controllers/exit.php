<?php
	session_start();
	
	if (isset($_SERVER['HTTP_COOKIE'])) {
		$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
		
		// Удалим куки
		foreach($cookies as $cookie) {
			$parts = explode('=', $cookie);
			$name = trim($parts[0]);
			setcookie($name, '', 1);
			setcookie($name, '', 1, '/', $_SERVER['HTTP_HOST']);
		}
	}
	session_destroy();
	
	header("location:" . _rootDIR_HTTP_ );