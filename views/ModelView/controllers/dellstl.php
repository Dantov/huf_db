<?php
	
	for ($i = 0; $i < count($_POST['dell_name']); $i++) {
		$dell_name = $_POST['dell_name'][$i];
		
		if ( file_exists('../'.$dell_name) ) unlink('../'.$dell_name);
	}