<?php

	if( !isset($_POST['save']) ) {
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		exit();
	}
    if (!class_exists('PushNotice', false)) include( _globDIR_ . 'classes/PushNotice.php' );
	session_start();

    date_default_timezone_set('Europe/Kiev');
        
	include(_globDIR_.'classes/Handler.php');
	
	$manualProcesses = 3;
	$imagesProcesses = count( $_FILES['upload_images']['name']?:[] );
	$stlProcesses = count( $_FILES['fileSTL']['name']?:[] );
	$stonesProcesses = (int)$_POST['gemsName'];
	$dopVCProcesses = count( $_POST['dop_vc_name_']?:[] );
	
	$overalProcesses = $manualProcesses + $imagesProcesses + $stlProcesses + $stonesProcesses + $dopVCProcesses;
	$overalProgress = 0;
	$progressCounter = 0;
	
	if ( isset($_POST['edit']) && (int)$_POST['edit'] === 2 ) {
		$isEdit = true;
		$id = (int)$_POST['id'];
	} else {
		$isEdit = false; // новая модель!
		unset($_SESSION['general_data']);
	}
	
	chdir(_stockDIR_);

	$date        = trim($_POST['date']);
	$number_3d   = strip_tags(trim($_POST['number_3d']));
	$vendor_code = strip_tags(trim($_POST['vendor_code']));
	$model_type  = strip_tags(trim($_POST['model_type']));
	
	$handler = new Handler($id, $_SERVER);
	
	if ( !$handler -> connectToDB() ) exit;


	$number_3d = $handler -> setNumber_3d($number_3d);
	$handler -> setVendor_code($vendor_code);
	$handler -> setModel_typeEn($model_type);
	$handler -> setModel_type($model_type);
	$handler -> setIsEdit($isEdit);
	$handler -> setDate($date);
	
	// проверяем поменялся ли номер 3Д
	if ( $isEdit === true ) $handler -> checkModel();
	
	$progressCounter++; // добавляем элемент когда задача выполнена 
	$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );
	
	// добавляем во все коиплекты артикул, если он есть
	$handler -> addVCtoComplects($vendor_code, $number_3d);
	
	// формируем строку model_material
	$model_material = $handler -> makeModelMaterial($_POST['model_material'],$_POST['samplegold'],$_POST['whitegold'],$_POST['redgold'],$_POST['eurogold']);
	
	// формируем строку model_covering
	$model_covering = $handler -> makeModelCovering($_POST['rhodium'],$_POST['golding'],$_POST['blacking'],$_POST['rhodium_fill'],$_POST['onProngs'],$_POST['onParts'],$_POST['rhodium_PrivParts']);

	$str_labels =  $handler->makeLabels($_POST['labels']);

	// берем все остальное
	$collection   = $handler->setCollections($_POST['collection']);
	$author       = strip_tags(trim($_POST['author']));
	$modeller3d   = strip_tags(trim($_POST['modeller3d']));
	$jewelerName   = strip_tags(trim($_POST['jewelerName']));
	$model_weight = strip_tags(trim($_POST['model_weight']));
    $description  = trim($_POST['description']);
	$size_range   = strip_tags(trim($_POST['size_range']));
	$print_cost   = strip_tags(trim($_POST['print_cost']));
	$model_cost   = strip_tags(trim($_POST['model_cost']));
	$creator_name    = $_SESSION['user']['fio'];
    $status = $_POST['status']; // число
	
	$datas = "UPDATE stock SET ";

	if ( !empty($number_3d) ) $datas .= "number_3d='$number_3d',";
	if ( !empty($vendor_code) ) $datas .= "vendor_code='$vendor_code',";
	if ( !empty($collection) ) $datas .= "collections='$collection',";
	if ( !empty($author) ) $datas .= "author='$author',";
	if ( !empty($modeller3d) ) $datas .= "modeller3D='$modeller3d',";
	if ( !empty($jewelerName) ) $datas .= "jewelerName='$jewelerName',";
	if ( !empty($model_type) ) $datas .= "model_type='$model_type',";
	if ( !empty($size_range) ) $datas .= "size_range='$size_range',";
	if ( !empty($print_cost) ) $datas .= "print_cost='$print_cost',";
	if ( !empty($model_cost) ) $datas .= "model_cost='$model_cost',";
	if ( !empty($model_covering) ) $datas .= "model_covering='$model_covering',";
	if ( !empty($model_material) ) $datas .= "model_material='$model_material',";
	if ( !empty($model_weight) ) $datas .= "model_weight='$model_weight',";
	if ( !empty($description) ) $datas .= "description='$description',";
	if ( !empty($str_labels) ) $datas .= "labels='$str_labels',";

    $datas = trim($datas,',');

    //т.е добавляем новую модель
	if ( $isEdit === false )
	{
		$id = $handler -> addNewModel($number_3d, $model_type); // возвращает id новой модели при успехе
		if ( !$id ) exit();

		$datas .= ",status='$status',
                    status_date='$date',
                    creator_name='$creator_name',
                    date='$date'
		";

        //04,07,19 - вносим статус в таблицу statuses
        $statusT = [
            'pos_id'      => $id,
            'status'      => $status,
            'creator_name'=> $creator_name,
            'UPdate'      => $date
        ];

        $handler -> addStatusesTable($statusT);
        // end
                
		$updateModelData = $handler -> updateDataModel($datas, $id);
	} else { // редактирование старой
		//if ( isset($mounting_descr) && !empty($mounting_descr) ) $datas .= ",mounting_descr='$mounting_descr'";

		$updateModelData = $handler -> updateDataModel($datas);
		$handler -> updateCreater($creator_name);    // добавим создателя, если его не было
		$handler -> updateStatus($status, $creator_name); // обновляем статус
	}
	
    if ( $updateModelData )
    {
		$progressCounter++; // добавляем элемент когда задача выполнена 
		$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );

    } else {
		exit();
    }
	
	
	//--------- добавляем картинки---------//
	if ( $imgCount = count($_FILES['upload_images']['name']?:[]) )
	{
		if( !file_exists($number_3d) ) mkdir($number_3d, 0777, true);
		if( !file_exists($number_3d.'/'.$id) ) mkdir($number_3d.'/'.$id, 0777, true);
		if( !file_exists($number_3d.'/'.$id.'/images') ) mkdir($number_3d.'/'.$id.'/images', 0777, true);
		
		for ( $i = 0; $i < $imgCount; $i++ )
		{
			$quer_addImg = $handler -> addImage($_FILES['upload_images'], $_POST['upload_images_word'], $i);
			
			if ( $quer_addImg ) {	
				$progressCounter++; // добавляем элемент когда задача выполнена 
				$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );

			} else {
				exit();
			}
		}
	}
	// в этом массиве индексы картинок на которых установлены флажки
	//$imgFlags = array();
	/*
	$imgFlags['mainImg']   = isset($_POST['mainImg'])   ? (int) $_POST['mainImg']   : "false";
	$imgFlags['onBodyImg'] = isset($_POST['onBodyImg']) ? (int) $_POST['onBodyImg'] : "false";
	$imgFlags['sketchImg'] = isset($_POST['sketchImg']) ? (int) $_POST['sketchImg'] : "false";
	$imgFlags['detailImg'] = isset($_POST['detailImg']) ? (int) $_POST['detailImg'] : "false";
	*/
	$quer_updFlags = $handler->updateImageFlags($_POST['imgFor']);
	if ( !$quer_updFlags ) exit();
	// ----- конец добавляем картинки ----- //
	
	
	// ----- Добавляем STL FILE ----- //
	if ( !empty($_FILES['fileSTL']['name'][0]) ) {

		
		if( !file_exists($number_3d) ) mkdir($number_3d, 0777, true);
		if( !file_exists($number_3d.'/'.$id.'/stl') ) mkdir($number_3d.'/'.$id.'/stl', 0777, true);
		
		$querSTL = $handler -> addSTL($_FILES['fileSTL']);

		if ( $querSTL ) {
		
			$progressCounter++;
			$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );

		} else {
			exit();
		}
		
	}
	//END Добавляем STL FILE



	// ----- Добавляем Ai FILE ----- //
	if ( !empty($_FILES['fileAi']['name'][0]) ) {
		
		if( !file_exists($number_3d) ) mkdir($number_3d, 0777, true);
		if( !file_exists($number_3d.'/'.$id.'/ai') ) mkdir($number_3d.'/'.$id.'/ai', 0777, true);
		
		$querSTL = $handler->addAi($_FILES['fileAi']);
		
		if ( $querSTL ) {
			
		} else {
			exit;
		}
		
	}
	//END Добавляем Ai FILE
	
	//---------- добавляем камни ----------//
	$gem_rows_count = count($_POST['gemsName']?:[]);

    //если камни есть то добавляем их
	if ( !empty($gem_rows_count) )
	{

		$gems = array();
		$gems['name']  = &$_POST['gemsName'];
		$gems['cut']   = &$_POST['gemsCut'];
		$gems['val']   = &$_POST['gemsVal'];
		$gems['diam']  = &$_POST['gemsDiam'];
		$gems['color'] = &$_POST['gemsColor'];
		
		$quer_gem = $handler -> addGems( $gems );

		if ( $quer_gem ) {	
			$progressCounter++;
			$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );
		} else {
			exit();
		}
    } // конец добавляем камни
	
	
	
	// добавляем доп. артикулы
	$dopVCcount = count($_POST['dop_vc_name_']?:[]);
	if ( !empty($dopVCcount) ) { //если доп. артикулы есть то добавляем их

		$vc = array();
		$vc['dop_vc_name'] =  &$_POST['dop_vc_name_'];
		$vc['num3d_vc']	   =  &$_POST['num3d_vc_'];
		$vc['descr_dopvc'] =  &$_POST['descr_dopvc_'];
		
		$quer_dop_vc = $handler -> addDopVC( $vc );

		if ( $quer_dop_vc ) {
			$progressCounter++;
			$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );

		} else {
			exit();
		}
    } //конец добавляем доп. артикулы
	
	
	
	/// --------- Добавляем ремонты ----------///
	if ( $rep_count = count($_POST['repairs_descr']?:[]) ) {
		
		$repairs = array();
		$repairs['repairs_num']   = &$_POST['repairs_num'];
		$repairs['repairs_descr'] = &$_POST['repairs_descr'];
		$quer_rep = $handler -> addRepairs( $repairs );
		
		if ( $quer_rep ) {
			$progressCounter++;
			$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );
                        
		} else {
			exit();
		}
	}
	/// --------- END ремонты ----------///

    $handler->closeDB();
    $_SESSION['re_search'] = true; // флаг для репоиска
    $handler->unsetSessions();

    $lastMess = "Модель добавлена";
    if ( $isEdit === true ) $lastMess = "Данные изменены";
    $resp_arr['number_3d'] = $number_3d;
    $resp_arr['model_type'] = $model_type;
    $resp_arr['lastMess'] = $lastMess;
    $resp_arr['id'] = $id;

    $pn = new PushNotice();
    $addPushNoticeResp = $pn->addPushNotice($id, $isEdit?2:1, $number_3d, $vendor_code, $model_type, $date, $status, $creator_name);
    if ( !$addPushNoticeResp ) $resp_arr['errors']['pushNotice'] = 'Error adding push notice';

    echo json_encode($resp_arr);