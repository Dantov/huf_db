<?php
	
	$zip_name = $_POST['zip_name'];
	$zip_path = $_POST['zip_path'];
	$file_folder = "../../../Stock/";
	
	/*
	echo "zip_name = ".$zip_name."<br>";
	echo "file_folder = ".$file_folder."<br>";
	*/
	
	$zip = new ZipArchive();
	$res = $zip->open($file_folder.$zip_name);
	
	$numFiles = $zip->numFiles;
	
	$filename = $zip->getNameIndex(0);
	
	/*echo "res = ".$res."<br>";*/
	
	if ( $res ) {
		$zip->extractTo($file_folder.$zip_path);
		echo"
		<script>
			var result = parent.window.document.getElementById(\"content\");	

			var formtodell = parent.window.document.getElementById(\"dellstlform\");
			var names = [];
		";
		
		
		for ($i = 0; $i < $zip->numFiles; $i++) {
			//$filename = ;
			echo"
				names[$i] = '".$zip->getNameIndex($i)."';
			";

		}
		
		
		echo"
		
			for ( var i=0; i<names.length; i++ ) {
				
				var input = document.createElement('input');
				input.setAttribute( 'type', 'hidden' );
				input.setAttribute( 'name', 'dell_name[]' );
				input.setAttribute('value','../../Stock/$zip_path' + names[i]);
				
				formtodell.appendChild(input);
			}
				var script = document.createElement('script');
				script.setAttribute('src','js/view3D.js?ver=013');
				result.appendChild(script);
				
				</script>
			";
	} else {
		echo "Error";
	}
	
?>