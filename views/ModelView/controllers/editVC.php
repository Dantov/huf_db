<?php
	
	if ( !isset($_POST['id']) || empty($_POST['id']) ) exit;
	echo "hello";
	
	session_start();
	include('../../Glob_Controllers/classes/Handler.php');
	date_default_timezone_set('Europe/Kiev');
	$id = (int)$_POST['id'];
	$date = date('Y-m-d');
	
	$vendor_code = isset($_POST['vendor_code']) ? strip_tags(trim($_POST['vendor_code'])) : trim($_POST['mountVC']);
	$number_3d = strip_tags(trim($_POST['n3d']));
	$model_type = strip_tags(trim($_POST['modType']));
	
	$handler = new Handler($id, $_SERVER);
	$connection = $handler->connectToDB();
	if ( !$connection ) exit;
	$handler -> setVendor_code($vendor_code);
	$handler -> setNumber_3d($number_3d);
	$handler -> setModel_type($model_type);
	$handler -> setIsEdit(true);
	$handler -> setDate($date);
	
	
    if ( isset($_POST['vendor_code']) ) {
		
		$quertext = mysqli_query($connection, " UPDATE stock SET vendor_code='$vendor_code' WHERE id='$id' ");
		if ( $quertext ) $handler->addPushNotice();
		
		mysqli_close($connection);
		
		echo"
			<script>
				var result = parent.window.document.getElementById(\"articl\");
				var text = \"$vendor_code\";
				result.appendChild(document.createTextNode(text));
				if ( result ) alert(\"Артикул изменен на - $vendor_code.\");
			</script>
		";
		$_SESSION['re_search'] = true; // флаг для репоиска
		exit;
	}
	
	if ( isset($_POST['status']) ) {
       
		$status = $_POST['status'];
		$mounting_descr = strip_tags(trim($_POST['mounting_descr']));
		
		$quertext = mysqli_query($connection, " UPDATE stock SET status='$status',
																 mounting_descr='$mounting_descr',
																 status_date='$date'
																 WHERE id='$id' ");
		if ($quertext) {
			$handler->addPushNotice();
			echo "
				<script>
					var result = parent.window.document.getElementById(\"status_window\");
					var text = 'Изменения внесены.';
					
					var center = document.createElement('center');
					var input = document.createElement('input');
					input.setAttribute('class','btn btn-success');
					input.setAttribute('type','button');
					input.setAttribute('value','OK');
					input.setAttribute('onclick','close_status_window(1);');
					center.appendChild(input);
					
					result.appendChild(document.createTextNode(text));
					result.appendChild(center);
				</script>
			";
		}
		mysqli_close($connection);
		$_SESSION['re_search'] = true; // флаг для репоиска
		exit;
	}
	
	
	
?>