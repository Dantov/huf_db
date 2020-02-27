<?php
ini_set('max_execution_time',600); // макс. время выполнения скрипта в секундах
ini_set('memory_limit','256M'); // -1 = может использовать всю память, устанавливается в байтах

if ( (int)$_GET['excel'] !== 1 ) exit;

require_once _viewsDIR_ . "Main/classes/ToExcel.php";
$excel = new ToExcel();

if ( (int)$_GET['getXlsx'] === 1 )
{
    $excel->getXlsx();
    exit;
}

if ( (int)$_GET['getFileName'] === 1 )
{
    if ( !isset($_SESSION['foundRow']) || empty($_SESSION['foundRow']) )
    {
        $collectionName = $_SESSION['assist']['collectionName'];
    } else {
        $collectionName = (int)$_SESSION['assist']['searchIn'] === 1 ? $_SESSION['searchFor'] : $_SESSION['assist']['collectionName'].'_-_'.$_SESSION['searchFor'];
    }

    $date = date('d.m.Y');
    $res['fileName'] = $excel->translit($collectionName) . '_'. $date;

    echo json_encode($res);
}

