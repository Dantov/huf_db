<?php
	
	function translit($str,$alphabet) {
			
		$str = mb_strtolower($str,'UTF-8');
		$chars = preg_split('//u',$str,-1,PREG_SPLIT_NO_EMPTY);

		foreach ($chars as $key => $value) {
			$ff = false;
			foreach ($alphabet as $alph_key => $alph_value) {
				//$value = 1;
				if ( $value == $alph_key ) {
					$eng_arrmt[] = $alph_value;
					$ff = true;
					continue;
				}
			}
			if ( !$ff ) $eng_arrmt[] = $value;
		}
		return implode($eng_arrmt);
	};

	function showStatus($str) {
		
		if ( !$str ) return;
			
			if ( $str == "На проверке" )   { $class = "onVerifi"; $glyphi = "eye-close";}
			if ( $str == "В росте" )       { $class = "onPrint"; $glyphi = "print";}
			if ( $str == "Готовая ММ" )    { $class = "MMdone"; $glyphi = "ok";     }
			if ( $str == "Вышел сигнал!" ) { $class = "signalDone"; $glyphi = "thumbs-up"; }
			if ( $str == "В работе" || $str == "В работе(Монтировка)" ) { $class = "Wip"; $glyphi = "cog";}
			if ( $str == "В ремонте" )     { $class = "onRepaire"; $glyphi = "wrench"; }
			if ( $str == "Отложено" )      { $class = "defer"; $glyphi = "thumbs-down"; }
					
			return "<div class=\"$class main_status pull-right\" title=\"$str\">
						<span class=\"glyphicon glyphicon-$glyphi\"></span>
					</div>";
	};
	
	function showHot($str) {
		
		if ( !$str ) return;
		
		$arr_labels = explode(";",$str);
		$str = '';
		
		foreach( $arr_labels as &$value ) {
			if ( $value == "Срочное!" ) {
					
				$str .= " <span class=\"label label-warning\">
							<span class=\"glyphicon glyphicon-tag\"></span>
							Срочное!
						</span><br/>
					";
			}
			if ( $value == "Бриллианты" ) {
					
				$str .= " <span class=\"label label-info\">
							<span class=\"glyphicon glyphicon-tag\"></span>
							Бриллианты
						</span><br/>
					";
			}
			if ( $value == "Эксклюзив" ) {
					
				$str .= " <span class=\"label label-danger\">
							<span class=\"glyphicon glyphicon-tag\"></span>
							Эксклюзив
						</span><br/>
					";
			}
			if ( $value == "Литьё с камнями" ) {
					
				$str .= " <span class=\"label label-primary\">
							<span class=\"glyphicon glyphicon-tag\"></span>
							Литьё с камнями
						</span><br/>
					";
			}
		}
		return $str;
	};
	
	function drawModel($row,$connection,$userRow,$uploaddir,$comlectIdent) {
		//по дефолту
		if ( !empty($row['vendor_code']) ) $vc_show = " | ".$row['vendor_code'];
		$col_md = 2;
		$showN3DandVC = "<span>{$row['number_3d']}$vc_show</span>";
		$hr = "<hr class=\"hr-items\" />";
		// если смотрим по комплектам
		if (isset($comlectIdent) && !empty($comlectIdent)) {
			$col_md = 3;
			$showN3DandVC = "";
			$hr = "";
		}
		
		$rid = $row['id'];
	    $img_res = mysqli_query($connection, " SELECT img_name,main FROM images WHERE pos_id='$rid' ");
		$showimg = $uploaddir."/"."default.jpg";
		while ($images = mysqli_fetch_assoc($img_res)) {
			if ( empty($images['main']) ) continue;
			$showimg = $uploaddir.'/'.$row['number_3d'].'/'.$row['id'].'/images/'.$images['img_name'];
			break;
		};
		
		$stlQuer = mysqli_query($connection, " SELECT stl_name FROM stl_files WHERE pos_id='$rid' ");
		if ( $stlQuer -> num_rows > 0 ) {
			$btn3D = "/<span class=\"button-3D-pict-main\" title=\"Доступен 3D просмотр\"><span>";
		}

		// смотрим отрисовывать ли нам кнопку едит
		if ( isset($_SESSION['user_access']) && $_SESSION['user_access'] > 0 ) {
			$drawEdit  = "<a href=\"../AddEdit/index.php?id={$row['id']}&component=2\" class=\"btn btn-sm btn-default editbtnshow\">";
			$drawEdit .= "<span class=\"glyphicon glyphicon-pencil\"></span></a>";
			// весь доступ
			if ( $_SESSION['user_access'] == 1 ) $drawEdit1 = $drawEdit;
			// доступ только где юзер 3д моделлер или автор
			if ( $_SESSION['user_access'] == 2 ) { 
			
				$userRowFIO = $userRow['fio'];
				$authorFIO = $row['author'];
				$modellerFIO = $row['modeller3D'];
				
				if ( stristr($authorFIO, $userRowFIO) !== FALSE || stristr($modellerFIO, $userRowFIO) !== FALSE ) {
					$drawEdit1 = $drawEdit;
				} 
			}
		} else {
			unset($drawEdit,$drawEdit1);
		}
		$str = '';
		$str .= "<div id=\"{$row['id']}\" class=\"col-xs-6 col-sm-4 col-md-$col_md prj-item\">";
	    $str .= 	"<div class=\"ratio\">";
        $str .= 		"<div class=\"ratio-inner ratio-4-3\">";
        $str .= 			"<div class=\"ratio-content\">";
		$str .=				showStatus($row['status']); //ставим метки и статус
		$str .=				"<div class=\"main_hot\">";
		$str .=					showHot($row['labels']);
		$str .= 				"</div>";
		$str .= 				"<a href=\"../ModelView/index.php?id={$row['id']}\">";
		$str .= 					"<div class=\"text-primary txt-art\">";
		$str .= 						$showN3DandVC;
		$str .=					"</div>";
		$str .= 					"<img src=\"../../picts/loading_circle_low2.gif\" class=\"imgLoadCircle_main\" />";
        $str .= 					"<img src=\"$showimg\" class=\"img-responsive imgThumbs_main hidden\" onload=\"onImgLoad(this);\"/>";
		$str .= 				"</a>";
        $str .= 			"</div>";
		$str .= 			$drawEdit1;
		$str .= 			$btn3D;
        $str .= 		"</div>";
		$str .= 		"<a href=\"show_pos_adm.php?id={$row['id']}\">";
		$str .= 			"<div class=\"text-muted margtop\">";
		$str .= 				"<span class=\"glyphicon glyphicon-calendar pull-left\" title=\"дата создания\">";
		$str .=					date_create( $row['date'] )->Format('d.m.Y');
		$str .=				"</span>";
		$str .=				"<span class=\"pull-right\"> {$row['model_type']}</span>";
		$str .= 			"</div>";
		$str .= 			"<div class=\"clearfix\"></div>";
		$str .= 		"</a>";
		$str .= 		$hr;
		$str .= 	"</div>";
		$str .= "</div>";
		
		return $str;
	};

	function countComplects($row,$connection) {

		$numRows = count($row);
		$savedrow = array();
		$complects = array();
		$cIt = 0;
		for ( $i = 0; $i < $numRows; $i++ ) {
			if ( empty($row[$i]['number_3d']) ) continue;
			$number_3d = $row[$i]['number_3d'];
			
			foreach ( $savedrow as &$value ) { 
			// проверяем есть ли этот номер в массиве. если есть то пропускаем все такие номера, они уже посчитаны
				if ( $value == $number_3d ) continue(2);
			}

			for ( $j = 0; $j < $numRows; $j++ ) {
				
				$model_type = $row[$j]['model_type'];
				
				// если совпадают - значит это комплект
				if ( $number_3d == $row[$j]['number_3d'] ) {
					
					$complects[$cIt]['number_3d'] = $row[$j]['number_3d'];
					$complects[$cIt]['vendor_code'] = $row[$j]['vendor_code'];
					$complects[$cIt]['modeller3D'] = $row[$j]['modeller3D'];
					$complects[$cIt]['collection'] = $row[$j]['collections'];
					$complects[$cIt]['model_type'][$model_type]['id'] = $row[$j]['id'];
					$complects[$cIt]['model_type'][$model_type]['number_3d'] = $row[$j]['number_3d'];
					$complects[$cIt]['model_type'][$model_type]['author'] = $row[$j]['author'];
					$complects[$cIt]['model_type'][$model_type]['modeller3D'] = $row[$j]['modeller3D'];
					$complects[$cIt]['model_type'][$model_type]['model_type'] = $row[$j]['model_type'];
					$complects[$cIt]['model_type'][$model_type]['labels'] = $row[$j]['labels'];
					$complects[$cIt]['model_type'][$model_type]['status'] = $row[$j]['status'];
					$complects[$cIt]['model_type'][$model_type]['date'] = $row[$j]['date'];
					
					$savedrow[] = $number_3d; // сохранем номер в массив, как посчитанный
					
				}
			}
			$cIt++;
		}
		
		return $complects;
	};
	
	function rrmdir($src) {
		$dir = opendir($src);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				$full = $src . '/' . $file;
				if ( is_dir($full) ) {
					rrmdir($full);
				}
				else {
					unlink($full);
				}
			}
		}
		closedir($dir);
		rmdir($src);
	}
	
	function unsetSessions(){
		// удаляем автозаполнение при возврате на главную
		if ( isset($_SESSION['general_data']) ) unset($_SESSION['general_data']);
		//удаляем инфу из ворд файла и сами файлы
		if ( isset($_SESSION['fromWord_data']) ) { 
			rrmdir('../../'.$_SESSION['fromWord_data']['tempDirName']);
			unset($_SESSION['fromWord_data']);
		}
	}
	
	/*
	function setmainimg($connection) {
		$img_res = mysqli_query($connection, "  SELECT id,pos_id FROM images ");
		$count = mysqli_num_rows($img_res);
		
		$iter = 0;
		$last_pos_id = "";
		while ($images = mysqli_fetch_assoc($img_res)) {
			
			$iter++;
			
			if ($images['pos_id'] == $last_pos_id ) continue;
			$id = $images['id'];
			$quer_upd = mysqli_query($connection, " UPDATE images SET main='1'
																WHERE id='$id' 
		    ");
			$last_pos_id = $images['pos_id'];
			$procent = 100 / ($count / $iter);
			echo "<script>
					document.getElementById('progress').style.width = '$procent%';
					document.getElementById('progress').innerHTML = $procent + ' %';
				  </script>
			";
		};
		
	};
	function makenewmatcover($connection) {
		
		$mtc = mysqli_query($connection, " SELECT id,model_material,model_covering FROM stock ");

		while( $mtcs = mysqli_fetch_assoc($mtc) ) {
			
			$arr = explode(" ",trim($mtcs['model_material']));
			$arr_cov = explode(" ",trim($mtcs['model_covering']));
			
			$str2 = implode(";",$arr);
			$str_cov = implode(";",$arr_cov);
			
			$id = $mtcs['id'];
			
			$quertext = mysqli_query($connection, " UPDATE stock SET model_material='$str2', model_covering='$str_cov' WHERE id='$id' ");
		}
	};
	*/
?>