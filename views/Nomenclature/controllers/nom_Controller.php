<?php 
	
	include(_globDIR_ . 'classes/General.php');
	$general = new General($_SERVER);
	if ( !$connection = $general->connectToDB() ) exit;
	$general->unsetSessions();
    
	$coll = mysqli_query($connection, "SELECT * FROM collections ORDER BY name ASC");
	$gems_names_quer =  mysqli_query($connection, "SELECT * FROM gems_names ORDER BY name ASC");
	$gems_cut_quer = mysqli_query($connection, "SELECT * FROM gems_cut ORDER BY name ASC");
	$gems_color_quer = mysqli_query($connection, "SELECT * FROM gems_color");
	$gems_size_quer = mysqli_query($connection, "SELECT * FROM gems_sizes ORDER BY name ASC");
	$gems_author_quer = mysqli_query($connection, "SELECT * FROM author ORDER BY name");
	$gems_modeller3D_quer = mysqli_query($connection, "SELECT * FROM modeller3D ORDER BY name");
    $jeweler_quer = mysqli_query($connection, "SELECT * FROM jeweler_names ORDER BY name");
	$gems_model_type_quer = mysqli_query($connection, "SELECT * FROM model_type ORDER BY name");
	$gems_vc_names_quer = mysqli_query($connection, "SELECT * FROM vc_names ORDER BY name");