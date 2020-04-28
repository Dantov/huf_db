<?php
session_start();

if ( isset( $_COOKIE['meme_sessA'] ) ) {
	
	if ( !isset($_SESSION['access']) || empty($_SESSION['access']) ) {
		
		$_SESSION['access'] = $_COOKIE['meme_sessA'];
		
		
		foreach ($_COOKIE['assist'] as $key => $value) {
			$_SESSION['assist'][$key] = $value;
		}
		
	}
	if ( !isset($_SESSION['user']) || empty($_SESSION['user']) ) {
		
		foreach ($_COOKIE['user'] as $key => $value) {
			$_SESSION['user'][$key] = $value;
		}
		
	}
}

if( !isset( $_SESSION['access'] ) 
	|| $_SESSION['access'] != true
	|| $_SESSION['assist']['update'] !== 7 ) header("location:". _glob_HTTP_ ."exit.php");