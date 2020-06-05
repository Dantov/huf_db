<?php

	require_once('db.php');
	
    session_start();
	$id_progr = $_SESSION['id_progr'];
    session_write_close();
	
	if ( (int)$_POST['killProgressBar'] ) 
	{
		$kill = mysqli_query($connection, " DELETE FROM progress WHERE idd='$id_progr' ");
		
		if (!$kill) exit("Boroda");
 		$arr['killed'] = "Killed";
		echo json_encode($arr);
		exit;
	}
    
	$queri = mysqli_query($connection, " SELECT status,overalProgress,filename FROM progress WHERE idd='$id_progr' " );
	mysqli_close($connection);
	$row = mysqli_fetch_assoc($queri);
	
	$arr['status'] = $row['status'];
	$arr['overalProgress'] = $row['overalProgress'];
	$arr['filename'] = $row['filename'];
	
	echo json_encode($arr);

?>