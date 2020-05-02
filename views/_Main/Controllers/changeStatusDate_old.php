<?php
	session_start();

	if ( !isset($_POST['id']) ) exit();

    include_once _globDIR_.'classes/General.php';
	$general = new General();
    $connection = $general->connectToDB();

    $id = (int)$_POST['id'];

    if ( !validateDate( $_POST['newDate'], 'Y-m-d' ) ) return;
    $newDate = trim( htmlentities($_POST['newDate'], ENT_QUOTES) );

    $addIPShowed = mysqli_query($connection," UPDATE statuses SET date='$newDate' WHERE id='$id' ");

    if ( $addIPShowed )
    {
        echo json_encode(['ok'=>1]);
    } else {
        echo json_encode(['ok'=>0]);
    }