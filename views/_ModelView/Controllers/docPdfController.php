<?php
date_default_timezone_set('Europe/Kiev');
ini_set('max_execution_time', 180); // макс. время выполнения скрипта в секундах
ini_set('memory_limit','256M'); // -1 = может использовать всю память, устанавливается в байтах

require_once _viewsDIR_ . 'ModelView/classes/DocumentPDF.php';
$docPdf = new DocumentPDF($_GET['id'], $_GET['userName'], $_GET['tabID']);

if ( $_GET['document'] === 'passport' )
{
	$docPdf->printPassport();
	$fileName = $docPdf->exportToFile('passport');

	echo json_encode($fileName);
	exit;
}

if ( $_GET['document'] === 'runner' )
{
	$docPdf->printRunner();
	$fileName = $docPdf->exportToFile('runner');

	echo json_encode($fileName);
	exit;
}

if ( $_GET['document'] === 'both' )
{
	$docPdf->printPassport();
	$docPdf->printRunner();
	$fileName = $docPdf->exportToFile('passportRunner');

	echo json_encode($fileName);
	exit;
}