<?php
	session_start();
	if ( isset($_SESSION['assist']['access']) && $_SESSION['assist']['access'] === 2 ) {
		
		unset($_SESSION['assist']['access']);
		
		$_SESSION['access'] = true;
		
		$_SESSION['assist']['maxPos']		   = 24; 		// кол-во выводимых позиций по дефолту
		$_SESSION['assist']['regStat']         = "Нет"; 	// выбор статуса по умоляанию
		$_SESSION['assist']['wcSort']         = []; 	// выбор рабочего участка по умоляанию
		$_SESSION['assist']['searchIn']        = 1;
		$_SESSION['assist']['reg']             = "number_3d"; // сорттровка по дефолту
		$_SESSION['assist']['startfromPage']   = (int)0; 		// начальная страница пагинации
		$_SESSION['assist']['page']            = (int)0; 		// устанавливаем первую страницу
		$_SESSION['assist']['drawBy_']         = 1; 		// 2 полоски, 1 квадратики
		$_SESSION['assist']['sortDirect']      = "DESC"; 	// по умолчанию
	    $_SESSION['assist']['collectionName']  = "Все Коллекции";
		$_SESSION['assist']['collection_id']   = -1;		// все коллекции
		$_SESSION['assist']['PushNotice']      = 1;		// показываем уведомления
		$_SESSION['assist']['update']          = 8;
		$_SESSION['assist']['bodyImg']         = 'bodyimg0'; // название класса
		$_SESSION['selectionMode']['activeClass'] = "";
		$_SESSION['selectionMode']['models'] = array();
		$_SESSION['lastTime'] = 0;
		
		// если установлен флажок на "запомнить меня" пишем все в печеньки
		if ( isset($_SESSION['assist']['memeMe']) && $_SESSION['assist']['memeMe'] === 1 ) { 
		
			unset($_SESSION['assist']['memeMe']);
			
			setcookie("meme_sessA", $_SESSION['access'],  time()+(3600*24*30), '/', $_SERVER['HTTP_HOST'] );
			
			foreach( $_SESSION['user'] as $key => $value ){
				
				$strname = "user[$key]";
				setcookie($strname, $value, time()+(3600*24*30), '/', $_SERVER['HTTP_HOST'] );
			}
			foreach( $_SESSION['assist'] as $key => $value )
			{
                if ( $key == 'wcSort' ) continue;
				$strname = "assist[$key]";
				setcookie($strname, $value, time()+(3600*24*30), '/', $_SERVER['HTTP_HOST'] );
			}
			
		}
		
		header("location:" ._rootDIR_HTTP_ . "Views/Main/index.php?".session_name().'='.session_id() );
	}