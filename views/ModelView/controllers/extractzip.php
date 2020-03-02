<?php
	$zip_name = $_POST['zip_name'];
	$zip_path = $_POST['zip_path'];
	$file_folder = _stockDIR_;
	
	$zip = new ZipArchive();
	$res = $zip->open($file_folder.$zip_name);

    $resp_arr = [];
    $resp_arr['Error'] = false;

	if ( $res )
	{
        $numFiles = $zip->numFiles;
		$zip->extractTo($file_folder.$zip_path);

        $names = [];
        for ($i = 0; $i < $zip->numFiles; $i++) $names[$i] = $zip->getNameIndex($i);

        $resp_arr['names'] = $names;
        $resp_arr['zip_path'] = $zip_path;

	} else {
        $resp_arr['Error'] = 'Can\'t open Zip archive';
	}

    echo json_encode($resp_arr);
    exit;