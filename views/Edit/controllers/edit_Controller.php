<?php

require('Edit.php');

$edit = new Edit(false, $_SERVER);
$connection = $edit->connectToDB();
if ( !$connection ) exit;

$prevPage = $edit->setPrevPage();

$status = $edit->getStatus();

$header = "Проставить статус для моделей: ";

$strModels = $edit->createlinks();