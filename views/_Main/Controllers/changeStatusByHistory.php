<?php
	session_start();

    //include_once _globDIR_.'classes/General.php';
	//$general = new General();

    if ( isset($_POST['statHistoryON']) )
    {
        $checked = (int)$_POST['byStatHistory'];
        if ( $checked )
        {
            $_SESSION['assist']['byStatHistory'] = 1;
            echo json_encode(['ok'=>1]);
        } else {
            $_SESSION['assist']['byStatHistory'] = 0;

            $_SESSION['assist']['byStatHistoryFrom'] = '';
            $_SESSION['assist']['byStatHistoryTo'] = '';
            echo json_encode(['ok'=>0]);
        }
        exit;
    }

    if ( isset($_POST['changeDates']) )
    {
        if ( isset($_POST['byStatHistoryFrom']) )
        {
            $from = $_POST['byStatHistoryFrom'];
            $_SESSION['assist']['byStatHistoryFrom'] = $from;
            echo json_encode(['ok'=>$from]);
        }
        if ( isset($_POST['byStatHistoryTo']) )
        {
            $to = $_POST['byStatHistoryTo'];
            $_SESSION['assist']['byStatHistoryTo'] = $to;
            echo json_encode(['ok'=>$to]);
        }
        exit;
    }