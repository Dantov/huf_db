<?php
include(_globDIR_.'sessions.php');

if( !isset( $_SESSION['access'] ) || $_SESSION['access'] != true ){
header("location: ../index.php");
} else {
	
	if( isset($_POST['form_data']) ) {
		
		date_default_timezone_set('Europe/Kiev');
		
		parse_str($_POST['form_data'], $form_data); // разбираем строку запроса
		
		if ( (int)$_SESSION['user_access'] === 3 ) { // ВЛАД
			include_once('../db.php');
			
			$id = $form_data['id'];
			$status = $form_data['status'];
			
			if ( isset($status) && !empty($status) ) {
				// обновляем статус
				$date = $form_data['date'];
				$quer_status =  mysqli_query($connection, " SELECT status FROM stock WHERE id='$id' " );
				$_status_old = mysqli_fetch_assoc($quer_status);
				if ( $_status_old['status'] != $status ) {
					$quertext = mysqli_query($connection, " UPDATE stock SET
															status='$status',
															status_date='$date'
															WHERE id='$id' 
					");
				}
			}
			
			$size_range = $form_data['size_range'];
			$print_cost = $form_data['print_cost'];
			$description = $form_data['description'];
			
			$datas = "UPDATE stock SET default_img='default.jpg'";
			
			if ( isset($size_range) && !empty($size_range) ) $datas.=",size_range='$size_range'";
			if ( isset($print_cost) && !empty($print_cost) ) $datas.=",print_cost='$print_cost'";
			if ( isset($description) && !empty($description) ) $datas.=",description='$description'";
			
			$where = " WHERE id='$id'";
			$queryStr = $datas.$where;
			$addEdit = mysqli_query($connection,$queryStr);
			
			if ( $addEdit ) {
				
				$arr['done'] = 'Готово!';
				echo json_encode($arr); // вернем полученное в ответе
				
				mysqli_close($connection);
				exit();
			} else {
				printf( "Error_AddEdit: %s\n", mysqli_error($connection) );
				exit();
			}
		} // ВЛАД
		
		
		if ( (int)$_SESSION['user_access'] === 4 ) { // ПДО
			include('functs.php');
			
			$id = $form_data['id'];
			$number_3d = $form_data['num3d'];
			
			// формируем строку model_material
			$model_material = makeModelMaterial($form_data['model_material'],$form_data['samplegold'],$form_data['whitegold'],$form_data['redgold'],$form_data['eurogold']);
			// конец model_material
			
			// формируем строку model_covering
			$model_covering = makeModelCovering($form_data['rhodium'],$form_data['golding'],$form_data['blacking'],$form_data['rhodium_fill'],$form_data['onProngs'],$form_data['onParts'],$form_data['rhodium_PrivParts']);
			// конец model_covering
			
			include_once('../db.php');
			
			$vendor_code = $form_data['vendor_code'];
			$description = $form_data['description'];
			$datas = "UPDATE stock SET default_img='default.jpg'";
			
			if ( isset($vendor_code) && !empty($vendor_code) ) $datas.=",vendor_code='$vendor_code'";
			if ( isset($description) && !empty($description) ) $datas.=",description='$description'";
			if ( isset($model_material) && !empty($model_material) ) $datas.=",model_material='$model_material'";
			if ( isset($model_covering) && !empty($model_covering) ) $datas.=",model_covering='$model_covering'";
			
			$where = " WHERE id='$id'";
			$queryStr = $datas.$where;
			$addEdit = mysqli_query($connection,$queryStr);
			
			if ( $addEdit ) {
				//mysqli_close($connection);
				//exit();
			} else {
				printf( "Error_AddEdit: %s\n", mysqli_error($connection) );
				exit();
			}
			
			// добавляем доп. артикулы
			if ( !empty(count($form_data['dop_vc_name_'])) ) { //если доп. артикулы есть то добавляем их
				
				mysqli_query($connection, "DELETE FROM vc_links WHERE pos_id='$id' ");
				
				for ( $i = 0; $i < count($form_data['dop_vc_name_']); $i++ ) {
				
					$dop_vc_name = $form_data['dop_vc_name_'][$i];
					$num3d_vc =  $form_data['num3d_vc_'][$i];
					$descr_dopvc =  $form_data['descr_dopvc_'][$i];
					
					if ( $dop_vc_name == "" && $num3d_vc == "" && $descr_dopvc == "" ) continue;
					
					$quer_dop_vc = mysqli_query($connection, " INSERT INTO vc_links (vc_names, 
																			 vc_3dnum,
																			 descript,
																			 pos_id,
																			 number_3d
																			) 
																	 VALUES ('$dop_vc_name',
																			 '$num3d_vc',
																			 '$descr_dopvc',
																			 '$id',
																			 '$number_3d'
																			) 
					");
					if ( $quer_dop_vc ) {
						
					} else {
						printf( "Ошибка: %s\n", mysqli_error($connection) );
						exit();
					}
				}
			} //конец добавляем доп. артикулы
			
			$arr['done'] = 'Готово!';
			echo json_encode($arr); // вернем полученное в ответе
		} // ПДО
	  
		exit();
	}
	
} // ELSE