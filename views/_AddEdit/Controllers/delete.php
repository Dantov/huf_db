<?php
session_start();

$id = (int) $_POST['id'];
$imgname = $_POST['imgname'];
$isSTL = (int) $_POST['isSTL'];
$isPDF = (int) $_POST['isPDF'];
$dellpos = (int) $_POST['dellpos'];

$uploaddir = "../../../Stock/";
chdir($uploaddir);

include_once( _rootDIR_ . 'Views/Glob_Controllers/classes/Handler.php');
if (!class_exists('PushNotice', false)) include( _globDIR_ . 'classes/PushNotice.php' );

$handler = new Handler($id, $_SERVER);
$handler->connectToDB();

$handler->setDate( date('Y-m-d') );

if ( isset($id) && !empty($id) && isset($dellpos) && $dellpos === 1 )
{
	
	$resultDell = $handler->deleteModel();

    $pn = new PushNotice();
    $pn->addPushNotice($id, 3, $resultDell['number_3d'], $resultDell['vendor_code'], $resultDell['model_type'], $handler->date, false, $handler->user['fio']);

    $arr['dell'] = $resultDell['dell'];
}

if ( isset($id) && !empty($id) && isset($imgname) && !empty($imgname) ) {
	
	if ( isset($isSTL) && $isSTL===1 ) {
		
		$handler->deleteStl($imgname);
		$kartinka = 'STL файлы 3D модели ';
		
	} else if ( isset($isSTL) && $isSTL === 2 ) {
		
		$handler->deleteAi($imgname);
		$kartinka = 'Файлы накладки ';
		
	} else {
		$handler->deleteImage($imgname);
		$kartinka = 'Картинка ';
	}
    
	$arr['id'] = $id;
	$arr['imgname'] = $imgname;
	$arr['kartinka'] = $kartinka;

}

if ( isset($isPDF) && $isPDF === 1 ) {
	
	$pdfname = $_POST['pdfname'];
	if ( isset($pdfname) ) $arr['success'] = $handler->deletePDF($pdfname);
	echo json_encode($arr);
	exit;
}

	
	
	$_SESSION['re_search'] = true;
    
	$handler->closeDB();
	
	echo json_encode($arr);
	exit();