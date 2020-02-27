<?php
//========= Options Controller =========//
require('classes/Options.php');

$options = new Options($_SERVER);
	
if ( !$connection = $options->connectToDB() ) exit;

$thisPage = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if ( $thisPage !== $_SERVER["HTTP_REFERER"] ) {
	$_SESSION['prevPage'] = $_SERVER["HTTP_REFERER"];
}

$PushNoticeCheck = '';
if ( $_SESSION['assist']['PushNotice'] == 1 ) {
	$PushNoticeCheck = 'checked';
}
$bgsImg = $options->scanBGfolder();
$bgsImgCheck = '';
for( $i = 0; $i < count($bgsImg); $i++ ) {
	
	if ( $bgsImg[$i]['body'] == $_SESSION['assist']['bodyImg'] ) $bgsImg[$i]['checked'] = 'checked';
	
}