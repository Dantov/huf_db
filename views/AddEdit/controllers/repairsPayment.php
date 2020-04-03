<?php
$repairPaid = (int)$_POST['paid'];
$repairID = (int)$_POST['repairID'];

if ($repairPaid !== 1 || empty($repairID) || $repairID < 0 || $repairID > 983495 ) {
    echo json_encode(['error'=>'Wrong incoming data']);
    exit;
}

include_once( _rootDIR_ . 'Views/Glob_Controllers/classes/Handler.php');
$handler = new Handler($id, $_SERVER);
$handler->connectToDB();
$handler->setDate( date('Y-m-d') );

$result['done'] = $handler->setRepairPaid($repairID);

echo json_encode($result);
exit();
