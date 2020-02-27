<?php
//========= statistic Controller =========//
require('classes/Statistic.php');

$stat = new Statistic($_SERVER);
	
if ( !$connection = $stat->connectToDB() ) exit;

$thisPage = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if ( $thisPage !== $_SERVER["HTTP_REFERER"] ) {
	$_SESSION['prevPage'] = $_SERVER["HTTP_REFERER"];
}

$users = $stat->getUsers();
$models = $stat->getModels();
$likes = $stat->getLikedModels();
$modelsBy3Dmodellers = $stat->getModelsBy3Dmodellers();
$modelsByAuthors = $stat->getModelsByAuthors();
$fileSizes = []; //$stat->scanBaseFileSizes();

?>