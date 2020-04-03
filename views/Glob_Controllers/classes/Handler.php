<?php
if (!class_exists('General', false)) include( _globDIR_ . 'classes/General.php' );

class Handler extends General { // общий класс, для манипуляций с базой данных MYSQL, и файлами на сервере

	function __construct( $id=false, $server=false ) {
		parent::__construct($server);
		if ( $id ) $this->id = $id;
	}
	
	private $id;
	private $number_3d;
	private $vendor_code;
	private $model_type;
	private $model_typeEn;
	public  $date;
	private $isEdit;
	
	public function setId($id) {
		if ( isset($id) ) $this->id = $id;
	}
	public function setNumber_3d($number_3d)
    {
		if ( isset($number_3d) )
		{
		    $needles = ['/','\\',"'",'"','?',':','*','|','>','<',',','.'];

			$this->number_3d = $this->add000( $this->checkCyrillic( str_replace($needles,'_',$number_3d) ) );
			return $this->number_3d;
		}
	}
	public function setVendor_code($vendor_code) {
		if ( isset($vendor_code) ) $this->vendor_code = $vendor_code;
	}
	public function setModel_type($model_type) {
		if ( isset($model_type) ) $this->model_type = $model_type;
	}
	public function setModel_typeEn($model_type) {
		if ( isset($model_type) ) $this->model_typeEn = $this->translit($model_type);
	}
	public function setDate($date) {
		if ( isset($date) ) $this->date = $date;
	}
	public function setIsEdit($isEdit) {
		if ( isset($isEdit) ) $this->isEdit = $isEdit;
	}
    public function setCollections($collections = [])
    {
        if ( !is_array($collections) || empty($collections) ) return;

        foreach ( $collections as &$collection )
        {
            $collection = htmlentities(trim($collection),ENT_QUOTES);
        }

        return implode(';',$collections);
    }
	
	protected function add000($str)
    {
		$arrChars = preg_split('//u',$str,-1,PREG_SPLIT_NO_EMPTY);
		foreach ( $arrChars as $key=>$value ) {
			if ( $value > 0 ) {
				$output = array_slice($arrChars, $key); // "c", "d", "e"
				
				$str = implode( "", $output );
				$str = "000".$str;
				return $str; 
			}
		}
		return null;
	}
	
	protected function checkCyrillic($number_3d) { // проверка на кирилические символы в номере3д
		$result = $this->translit($number_3d);
		$result = mb_convert_case($result, MB_CASE_UPPER, "UTF-8");
		
		return $result;
	}
	
	// новый вариант, переносит файлы модели в новую папку, если поменялся номер 3д
	public function checkModel()
    {
		$query_oldN3d = mysqli_query($this->connection, " SELECT number_3d FROM stock WHERE id='$this->id' " );
		$row = mysqli_fetch_assoc($query_oldN3d);
                
		if ( $row['number_3d'] != $this->number_3d ) {
			$oldN3d = $row['number_3d'];
			$newN3d = $this->number_3d;
			
			if ( !file_exists($newN3d) ) mkdir($newN3d, 0777, true);
			if ( !file_exists($newN3d.'/'.$this->id) ) mkdir($newN3d.'/'.$this->id, 0777, true);
			
			$newPath = $newN3d.'/'.$this->id;
			$oldPath = $oldN3d.'/'.$this->id;
			
			$folders = scandir($oldPath);
			
			for ( $i = 0; $i < count($folders); $i++ ) { // взяли папки Images и Stl если они есть
			
				if ( $folders[$i] == '.' || $folders[$i] == '..' ) continue;
				
				$filesToMove = scandir($oldPath.'/'.$folders[$i]); // сканируем каждую папку на предмет картинок или стл в ней
				
				for ( $j=0; $j < count($filesToMove); $j++ ) {
					
					if ( $filesToMove[$j] == '.' || $filesToMove[$j] == '..' ) continue;
					
					$oldCopyPath = $oldPath.'/'.$folders[$i].'/'.$filesToMove[$j];
					$newCopyPath = $newPath.'/'.$folders[$i].'/'.$filesToMove[$j];
					
					// если в новом пути нет папки Images или Stl, то создадим их
					if ( !file_exists($newPath.'/'.$folders[$i]) ) mkdir($newPath.'/'.$folders[$i], 0777, true);
					
					// копируем файлы из старого места в новую дир.
					copy( $oldCopyPath, $newCopyPath );
				}
				
			}
			$this -> rrmdir( $oldPath ); // удаляем все на старом месте
			$oldDirs = scandir($oldN3d); 
			$emptyDir = true;
			// если папка, после удаления, осталась пустая - то удаляем и ее
			for ( $i = 0; $i < count($oldDirs); $i++ ) {
				if ( $oldDirs[$i] == '.' || $oldDirs[$i] == '..' ) continue;
				if ( isset($oldDirs[$i]) && !empty($oldDirs[$i]) ) $emptyDir = false;
			}
			if ( $emptyDir ) rmdir($oldN3d);
		}
	}
	
	public function addVCtoComplects(&$vendor_code, &$number_3d){
		if ( isset($vendor_code) && !empty($vendor_code) ) {
			$queri = mysqli_query($this->connection, " SELECT id,vendor_code FROM stock WHERE number_3d='$number_3d' " );
			if ( $queri -> num_rows > 0 ) {
				while ( $row = mysqli_fetch_assoc($queri) ) {
					$id = $row['id'];
					if ( empty($row['vendor_code']) ) {
						$quertext = mysqli_query($this->connection, " UPDATE stock SET vendor_code='$vendor_code' WHERE id='$id' ");
					}
				}
			}
		}
	}
	
	public function makeModelMaterial(&$mod_mat,&$samplegold,&$whitegold,&$redgold,&$eurogold) {
		$model_mat_arr = array();
		if ( !empty($mod_mat) )    $model_mat_arr[] = $mod_mat;
		if ( !empty($samplegold) ) $model_mat_arr[] = $samplegold;
		if ( !empty($whitegold) )  $model_mat_arr[] = $whitegold;
		if ( !empty($redgold) )    $model_mat_arr[] = $redgold;
		if ( !empty($eurogold) )   $model_mat_arr[] = $eurogold;
		$model_material = implode(';',$model_mat_arr);
		return $model_material;
	}

	public function makeModelCovering(&$rhodium,&$golding,&$blacking,&$rhodium_fill,&$onProngs,&$onParts,&$rhodium_PrivParts) {
		$mod_cov_arr = array();
		if ( !empty($rhodium) )      $mod_cov_arr[] = $rhodium;
		if ( !empty($golding) )      $mod_cov_arr[] = $golding;
		if ( !empty($blacking) )     $mod_cov_arr[] = $blacking;
		if ( !empty($rhodium_fill) ) $mod_cov_arr[] = $rhodium_fill;
		if ( !empty($onProngs) )     $mod_cov_arr[] = $onProngs;
		if ( !empty($onParts) )      $mod_cov_arr[] = $onParts;
		
		if ( !empty($rhodium_PrivParts) )  $rhodium_PP = strip_tags(trim($rhodium_PrivParts));
		
		$model_covering  = implode(';',$mod_cov_arr);
		$model_covering .= !empty($rhodium_PP ) ? ";-".$rhodium_PP : "";
		
		return $model_covering;
	}

    public function makeLabels($labels=[])
    {
        if (empty($labels)) return '';

        $str_labels = '';
        $labelsOrigin = $this->getStatLabArr('labels');

        foreach ( $labels as $label )
        {
            foreach ( $labelsOrigin as $labelOrigin )
            {
                if ( $label === $labelOrigin['name'] )
                {
                    $str_labels .= $labelOrigin['name'] . ';';
                }
            }
        }
        return trim($str_labels,';');
    }

	public function updateStatus($status, $creator_name="")
	{
		if (empty($status)) return;
		$quer_status =  mysqli_query($this->connection, " SELECT status FROM stock WHERE id='$this->id' " );

		$_status_old = mysqli_fetch_assoc($quer_status);

		// если старый статус строка - нужен КОСТЫЛЬ!!
		if ( !((int)$_status_old['status']) )
        {
            foreach ($this->statuses as $tStatus)
            {
                if ( $tStatus['name_ru'] == $_status_old['status'] )
                {
                    $_status_old['status'] = $tStatus['id'];
                    break;
                }
            }
        }

		if ( $_status_old['status'] != $status )
		{
			$updQuery = " UPDATE stock SET status='$status', status_date='$this->date' WHERE id='$this->id' ";
			$quertext = mysqli_query($this->connection, $updQuery);
			//04,07,19 - вносим новый статус в таблицу statuses
			if (empty($creator_name)) $creator_name = "Guest";
			$statusT = [
				'pos_id' => $this->id,
				'status' => $status,
				'creator_name'   => $creator_name,
				'UPdate'   => $this->date
			];
			$this->addStatusesTable($statusT);
		}
	}

    public function addStatusesTable($statusT = [])
    {
        //04,07,19 - вносим новый статус в таблицу statuses
        if ( empty($statusT) ) return;

        $pos_id = $statusT['pos_id'];
        $status = $statusT['status'];
        $name = $statusT['creator_name'];
        $date = $statusT['UPdate'];
        $querStr = "INSERT INTO statuses (pos_id,status,name,date) VALUES('$pos_id','$status','$name','$date')";

        $quer_status =  mysqli_query($this->connection, $querStr );
            
	}
	
	//добавляет только номер 3д и тип, чтобы получить айди для дальнейших манипуляций
	public function addNewModel(&$number_3d, &$model_type) {
		$addNew = mysqli_query($this->connection, "INSERT INTO stock (number_3d,model_type) VALUES('$number_3d','$model_type') ");
		if ( !$addNew ) {
			printf( "Error_AddModel: %s\n", mysqli_error($this->connection) );
			return false;
		}
		$id = mysqli_insert_id($this->connection);
		$this->setId($id);
		return $this->id;
	}
	
	public function updateDataModel($datas, $id=false) {
		if (!$id) $id = $this->id;
		
		$where = " WHERE id='$id' ";
		$queryStr = $datas.$where;
		//debug($queryStr,'$queryStr');
		$addEdit = mysqli_query($this->connection, $queryStr);
		if ( !$addEdit ) {
			printf( "Error updateDataModel() in class ".get_class($this)." : %s\n", mysqli_error($this->connection) );
			return false;
		}

		return true;
	}

	
	public function updateCreater(&$creator_name) {
		$quer_CN =  mysqli_query($this->connection, " SELECT creator_name FROM stock WHERE id='$this->id' " );
		$row_CN = mysqli_fetch_assoc($quer_CN);
		if ( !$row_CN['creator_name'] ) {
			$quertext = mysqli_query($this->connection, " UPDATE stock SET creator_name='$creator_name' WHERE id='$this->id' ");
		}
	}
	
	private function findLastNum() {
		$findQuer = mysqli_query($this->connection, " SELECT img_name FROM images WHERE pos_id='$this->id' ");
		if ($findQuer) {
			while ($found_row[] = mysqli_fetch_assoc($findQuer)) {};
			// уменьшаем на 1 т.к. созданный таким способом масив, содержит пустой последний элемент
			$numrows = count($found_row) - 1; //- это реальная длинна массива
			// уменьшаем еще раз т.к посл. элем. массива = его длинна - 1
			$img_name_str = $found_row[$numrows-1]['img_name'];
			
			$str1 = explode(".",$img_name_str);
			$str2 = explode("-",$str1[0]);
			$last_number = $str2[2];
			return $last_number;
		}
	}
	
	public function addImage(&$files, &$imgWord, $i)
    {
		$iter = $i;
		/*
		if ( $this->isEdit === true ) {
			$last_number = $this->findLastNum();
			$last_number++;
			$iter = $last_number;
		}
		*/
		$randomString = randomStringChars(8,'en','symbols');
		//если имя есть, это значит что добавили вручную
		if ( !empty( basename($files['name'][$i]) ) )
		{
			$info = new SplFileInfo($files['name'][$i]);
			$extension = pathinfo($info->getFilename(), PATHINFO_EXTENSION);
			$uploading_img_name = $this->number_3d."_".$randomString.mt_rand(0,98764321).".".$extension;
			move_uploaded_file($files['tmp_name'][$i], $this->number_3d.'/'.$this->id.'/images/'.$uploading_img_name);	
			
		} else { //иначе, пришло из ворд файла
			
			//$fullPath = $imgWord[$i];
			// т.к. находится в /Stock нужно отобрать один переход
			$fullPath = explode('../',$imgWord[$i]);
			
			if ( empty($fullPath[0]) ) {         //if ( $fullPath[0] == '../' ) {
				$fullPath = '../'.$fullPath[2];
			} else {
				$fullPath = $fullPath[0];
			}
			
			if ( !empty($fullPath) ) { //если файл есть то добавляем запись
				$path_parts = pathinfo($fullPath);
				$extension = $path_parts['extension'];
				$uploading_img_name = $this->number_3d."-".$this->model_typeEn."_".$randomString.time().".".$extension;
				copy($fullPath, $this->number_3d.'/'.$this->id.'/images/'.$uploading_img_name);
			}
		}
		
		$quer = mysqli_query($this->connection, " INSERT INTO images (img_name, pos_id) VALUES ('$uploading_img_name','$this->id') ");
		if ( !$quer ) {
			printf( "Error add img: %s\n", mysqli_error($this->connection) );
			return false;
		}
		return true;
	}



	public function updateImageFlags($imgFlags)
	{
	    if ( empty($imgFlags) ) return true;
		$querFlags = mysqli_query($this->connection, " SELECT id FROM images WHERE pos_id='$this->id' ");
		
		if ( $querFlags->num_rows > 0 ) {
			$i = 0;
			while ($img = mysqli_fetch_assoc($querFlags)) {
				$id = $img['id']; // id картинки в которой нужно проапдейтить флажки
				// индексы картинок выставляются от 0 до их кол-ва в базе
				
				$mainImg_bool   = ( $imgFlags[$i] == 1 ) ? 1 : "";
				$onBodyImg_bool = ( $imgFlags[$i] == 2 ) ? 1 : "";
				$sketchImg_bool = ( $imgFlags[$i] == 3 ) ? 1 : "";
				$detailImg_bool = ( $imgFlags[$i] == 4 ) ? 1 : "";
				$schemeImg_bool = ( $imgFlags[$i] == 5 ) ? 1 : "";
				
				// обновляем в базе флажки для старых картинок
				$quer_upd = mysqli_query($this->connection, " UPDATE images SET main='$mainImg_bool',
																				onbody='$onBodyImg_bool',
																				sketch='$sketchImg_bool',
																				detail='$detailImg_bool',
																				scheme='$schemeImg_bool'
																				WHERE id='$id' 
				");
				if ( !$quer_upd ) {
					printf( "Error add imgFlags: %s\n", mysqli_error($this->connection) );
					return false;
				}
				$i++;
			}
		}
		return true;
	}

	
	public function addSTL( &$filesSTL ) {
		$folder = $this->number_3d.'/'.$this->id.'/stl/';
		
		$zip = new ZipArchive();
		$zip_name = $this->number_3d."-".$this->model_typeEn.".zip";
		$zip->open($folder.$zip_name, ZIPARCHIVE::CREATE);
		$countSTls = count($filesSTL['name']);
		for ( $i = 0; $i < $countSTls; $i++ ) {
			
			$fileSTL_name = basename($filesSTL['name'][$i]);
			
			if ( !empty($fileSTL_name) ) {
				
				$info = new SplFileInfo($filesSTL['name'][$i]);
				$extension = pathinfo($info->getFilename(), PATHINFO_EXTENSION);
				
				$uploading_fileSTL_name[$i] = $this->number_3d."-".$this->model_typeEn."-".$i.".".$extension;
				move_uploaded_file($filesSTL['tmp_name'][$i], $folder.$uploading_fileSTL_name[$i]);
				
				$zip->addFile( $folder.$uploading_fileSTL_name[$i], $uploading_fileSTL_name[$i] );
			}

		}
		$zip->close();
		
		for ( $i = 0; $i < count($filesSTL['name']); $i++ ) {
			unlink($folder.$uploading_fileSTL_name[$i]);
		}
		if ( $countSTls ) {
			$quer = mysqli_query($this->connection, " INSERT INTO stl_files (stl_name, pos_id) VALUES ('$zip_name', '$this->id') ");
			if ( !$quer ) {
				printf( "Error Add STL: %s\n", mysqli_error($this->connection) );
				return false;
			}
		}
		return true;
	}
	
	public function addAi( &$filesAi ) {
		$folder = $this->number_3d.'/'.$this->id.'/ai/';
		
		$zip = new ZipArchive();
		$zip_name = $this->number_3d."-".$this->model_typeEn.".zip";
		$zip->open($folder.$zip_name, ZIPARCHIVE::CREATE);
		$countAis = count($filesAi['name']);
		for ( $i = 0; $i < $countAis; $i++ ) {
			
			$fileAi_name = basename($filesAi['name'][$i]);
			
			if ( !empty($fileAi_name) ) {
				
				$info = new SplFileInfo($filesAi['name'][$i]);
				$extension = pathinfo($info->getFilename(), PATHINFO_EXTENSION);
				
				$uploading_fileAi_name[$i] = $this->number_3d."-".$this->model_typeEn."-".$i.".".$extension;
				
				$size = filesize($filesAi['tmp_name'][$i]);
				
				move_uploaded_file($filesAi['tmp_name'][$i], $folder.$uploading_fileAi_name[$i]);
				
				$zip->addFile( $folder.$uploading_fileAi_name[$i], $uploading_fileAi_name[$i] );
			}

		}
		$zip->close();
		
		for ( $i = 0; $i < count($filesAi['name']); $i++ ) {
			unlink($folder.$uploading_fileAi_name[$i]);
		}
		
		if ( $countAis ) {
			$quer = mysqli_query($this->connection, " INSERT INTO ai_files (name,size,pos_id) VALUES ('$zip_name', '$size', '$this->id') ");
			if ( !$quer ) {
				printf( "Error Add Ai: %s\n", mysqli_error($this->connection) );
				return false;
			}
		}
		return true;
	}
	
	public function addGems( &$gems ) {
		if ( $this->isEdit === true ) mysqli_query($this->connection, " DELETE FROM gems WHERE pos_id='$this->id' ");
		
		for ( $i = 0; $i < count($gems['name']?:[]); $i++ ) {
			
			$gemsName  = trim($gems['name'][$i]);
			$gemsCut   = trim($gems['cut'][$i]);
			$gemsVal   = trim($gems['val'][$i]);
			$gemsDiam  = trim($gems['diam'][$i]);
			$gemsColor = trim($gems['color'][$i]);

			if ( $gemsName == "" ) continue;
			
			$quer_gem = mysqli_query($this->connection, " INSERT INTO gems (gems_names, 
																	 gems_cut,
																	 value,
																	 gems_sizes,
																	 gems_color,
																	 pos_id,
																	 number_3d
																	) 
															 VALUES ('$gemsName',
																	 '$gemsCut',
																	 '$gemsVal',
																	 '$gemsDiam',
																	 '$gemsColor',
																	 '$this->id',
																	 '$this->number_3d'
																	)
			");
			if ( !$quer_gem ) {
				printf( "Error Add Gems: %s\n", mysqli_error($this->connection) );
				return false;
			}
		}
		return true;
	}
	
	public function addDopVC( &$vc ) {
		if ( $this->isEdit === true ) mysqli_query($this->connection, " DELETE FROM vc_links WHERE pos_id='$this->id' ");
		
		for ( $i = 0; $i < count($_POST['dop_vc_name_']?:[]); $i++ ) {
		
			$dop_vc_name = trim($vc['dop_vc_name'][$i]);
			$num3d_vc =  trim($vc['num3d_vc'][$i]);
			$descr_dopvc =  trim($vc['descr_dopvc'][$i]);

			if ( $dop_vc_name == "" ) continue;
			
			$quer_dop_vc = mysqli_query($this->connection, " INSERT INTO vc_links (vc_names, 
                                                                vc_3dnum,
                                                                descript,
                                                                pos_id,
                                                                number_3d
                                                               ) 
                                                VALUES ('$dop_vc_name',
                                                                '$num3d_vc',
                                                                '$descr_dopvc',
                                                                '$this->id',
                                                                '$this->number_3d'
                                                               ) 
			");
			if ( !$quer_dop_vc ) {
				printf( "Error Add dop_vc: %s\n", mysqli_error($this->connection) );
				return false;
			}
		}
		return true;
	}

	protected function parseRepairs($repairs)
    {
        if ( !is_array($repairs) ) return [];
        $parsedRepairs = [];

        foreach ( $repairs as $field => $records )
        {
            foreach ( $records as $key => $value )
            {
                $parsedRepairs[$key][$field] = $value;
            }
        }
        return $parsedRepairs;
    }
	public function addRepairs( $repairs )
    {
        if ( !is_array($repairs) || empty($repairs) ) return [];
        $repairs = $this->parseRepairs($repairs);
        //$repairsJew = $this->parseRepairs(isset($repairs['jew'])?$repairs['jew']:[]);
        //$repairs = array_merge($repairs3D, $repairsJew);

        //debug($repairs,'$repairs');

        $deletions = [];
        $updates = [];
        $insertions = [];

        foreach ( $repairs as $repair )
        {
            $repID = (int)$repair['id'];
            $repDescr = trim($repair['description']);

            if ( $repID > 0 )
            {
                $repQuery = mysqli_query($this->connection, " SELECT COUNT(1) FROM repairs WHERE id='$repID' ");
                if ( $repQuery->num_rows && (empty($repDescr) || $repDescr == -1) ) { // кандидат на удаление
                    $deletions[] = $repair;
                } elseif ($repQuery->num_rows)
                {
                    $updates[] = $repair;
                }
            }
            if ( $repID === 0 )
            {
                $insertions[] = $repair;
                continue;
            }
        }

//        debug($deletions,'$deletions');
//        debug($updates,'$updates');
//        debug($insertions,'$insertions',1);

        $result = [];
        if ( !empty($deletions) )
        {
            $dellIds = '(';
            foreach ( $deletions as $deletion ) $dellIds .= $deletion['id'] . ',';
            $dellIds = trim($dellIds,',') . ')';

            $dellQuery = mysqli_query($this->connection, " DELETE FROM repairs WHERE id in $dellIds ");
            if ($dellQuery) {
                $result['deletions'] = $dellIds . ' - deleted.';
            } else {
                printf( "Error Delete repairs: %s\n", mysqli_error($this->connection) );
                $result['deletions'] = 'error';
            }
        }
        if ( !empty($updates) )
        {
            foreach ( $updates as $update )
            {
                $id = $update['id'];
                $description = $update['description'];
                $cost = $update['cost'];
                $which = $update['which'];
                $updQuery = mysqli_query($this->connection, " UPDATE repairs SET repair_descr='$description', which='$which', cost='$cost' WHERE id='$id' ");
                if ($updQuery) {
                    $result['updates'][] = $id . ' - success.';
                } else {
                    printf( "Error Update repairs: %s\n", mysqli_error($this->connection) );
                    $result['updates'][] = $id . ' - update error!';
                }
            }
        }
        if ( !empty($insertions) )
        {
            foreach ( $insertions as $insertion )
            {
                $num = $insertion['num'];
                $description = $insertion['description'];
                $cost = $insertion['cost'];
                $which = $insertion['which'];
                $insertQuery = mysqli_query($this->connection, " INSERT INTO repairs (rep_num, repair_descr, cost, which, date, pos_id) 
		                                                                 VALUES ('$num','$description','$cost','$which','$this->date','$this->id') ");
                if ($insertQuery) {
                    $result['insertions'][] = $id . ' - success.';
                } else {
                    printf( "Error Insert repairs: %s\n", mysqli_error($this->connection) );
                    $result['insertions'][] = ' Insert error!';
                }
            }
        }

        return $result;
        /*
		for ( $i = 0; $i < count($repairs['repairs_descr']); $i++ ) {
			
			$repairs_descr = trim($repairs['repairs_descr'][$i]);
			$repairs_num = trim($repairs['repairs_num'][$i]);
			
			if ( $repairs_descr == -1 ) { // кандидат на удаление
				mysqli_query($this->connection, " DELETE FROM repairs WHERE pos_id='$this->id' AND rep_num='$repairs_num' ");
				continue;
			}
			
			$repQuer = mysqli_query($this->connection, " SELECT repair_descr FROM repairs WHERE pos_id='$this->id' AND rep_num='$repairs_num' ");

			if ( mysqli_num_rows($repQuer) ) { // если уже есть такой ремонт
			
				$repRow = mysqli_fetch_assoc($repQuer);
				if (empty($repairs_descr)) { // если пришла пустая запись удаляем ремонт
					mysqli_query($this->connection, " DELETE FROM repairs WHERE pos_id='$this->id' AND rep_num='$repairs_num' ");
					continue;
				}
				if ( $repRow['repair_descr'] != $repairs_descr ) { // если в нем были изменения
					
					$repQuer_upd = mysqli_query($this->connection, " UPDATE repairs SET repair_descr='$repairs_descr' WHERE pos_id='$this->id' AND rep_num='$repairs_num' ");
					
					if (!$repQuer_upd) {
						printf( "Error Upd repair: %s\n", mysqli_error($this->connection) );
						return false;
					} else {
						//echo "Well done update repair!";
					}
				}
			
			} else { //иначе добавляем ремонт
				if (empty($repairs_descr)) continue; // пустые записи не добавляем
				$repQuer_ins = mysqli_query($this->connection, " INSERT INTO repairs (rep_num, 
		                                                                        repair_descr,
																	            date,
																	            pos_id
																	           ) 
		                                                         VALUES ('$repairs_num',
															             '$repairs_descr',
																    	 '$this->date',
															             '$this->id'
																	    ) 
				");
				if (!$repQuer_ins) {
					printf( "Error Add repair: %s\n", mysqli_error($this->connection) );
					return false;
				} else {
					//echo "Well done insert repair!";
				}
			}
		}
		return true;
        */
	}
	
	public function getModelsByType($modelType)
    {
		
		include(_globDIR_.'db.php');
		
		$names_quer = mysqli_query($this->connection, " SELECT id,number_3d,vendor_code FROM stock WHERE collections='Детали' AND model_type='$modelType' ");
		
		$resp = array();
		
		while( $names_row = mysqli_fetch_assoc($names_quer) ) {
			$id = $names_row['id'];
			
			$img_quer = mysqli_query($this->connection, " SELECT img_name,main FROM images WHERE pos_id='$id' ");
			while( $img_row = mysqli_fetch_assoc($img_quer) ) {
				if ( (int)$img_row['main'] == 1  ) $imgtoshow = $img_row['img_name'];
			}

            $file = $names_row['number_3d'].'/'.$id.'/images/'.$imgtoshow;
            $fileImg = _stockDIR_HTTP_.$file;
            if ( !file_exists(_stockDIR_.$file) ) $fileImg = _stockDIR_HTTP_."default.jpg";

            $nameVC = $names_row['vendor_code'] ?: $names_row['number_3d'];
			$resp[] = '<li><a class="imgPrev" elemToAdd imgtoshow="'. $fileImg .'">'.$nameVC.'</a></li>';
		}
		return $resp;
	}
	
	public function deleteModel()
    {
		$selQuery = mysqli_query($this->connection, " SELECT number_3d,vendor_code,model_type FROM stock WHERE id='$this->id' ");
		$row = mysqli_fetch_assoc($selQuery);
		$result = [
		    'number_3d'   => $row['number_3d'],
		    'vendor_code' => $row['vendor_code'],
		    'model_type'  => $row['model_type'],
            'dell' => $row['number_3d']." / ".$row['vendor_code']." - ".$row['model_type'],
        ];

		mysqli_query($this->connection, " DELETE FROM stock      WHERE     id='$this->id' ");
		mysqli_query($this->connection, " DELETE FROM images     WHERE pos_id='$this->id' ");
		mysqli_query($this->connection, " DELETE FROM gems       WHERE pos_id='$this->id' ");
		mysqli_query($this->connection, " DELETE FROM vc_links   WHERE pos_id='$this->id' ");
		mysqli_query($this->connection, " DELETE FROM statuses   WHERE pos_id='$this->id' ");
		mysqli_query($this->connection, " DELETE FROM pushnotice WHERE pos_id='$this->id' ");
		$path = $row['number_3d'].'/'.$this->id;
		
		if ( file_exists($path) ) $this->rrmdir($path);

        $files = [];
		if ( file_exists( $row['number_3d']) ) $files = scandir( $row['number_3d'] );
		$is_empty = true;

		for ( $i = 0; $i < count($files); $i++ ) {
			if ( $files[$i] == '.' || $files[$i] == '..' ) continue;
			if ( !empty($files[$i]) ) $is_empty = false;
		}
		if ( $is_empty ) rmdir($row['number_3d']);
		
		return $result;
	}
	
	public function deleteImage($imgname) {
		$result = mysqli_query($this->connection, " SELECT number_3d,vendor_code,model_type FROM stock WHERE id='$this->id' ");
		$row = mysqli_fetch_assoc($result);
		
		mysqli_query($this->connection, " DELETE FROM images WHERE img_name='$imgname' ");
		
		if (file_exists($row['number_3d']."/".$this->id."/images/".$imgname)) {
			unlink($row['number_3d']."/".$this->id."/images/".$imgname);
		}

		return true;
	}
	
	public function deleteStl($stlname) {
		$result = mysqli_query($this->connection, " SELECT number_3d,vendor_code,model_type FROM stock WHERE id='$this->id' ");
		$row = mysqli_fetch_assoc($result);
		
		mysqli_query($this->connection, " DELETE FROM stl_files WHERE stl_name='$stlname' ");
		
		if (file_exists($row['number_3d']."/".$this->id."/stl/".$stlname)) {
			unlink($row['number_3d']."/".$this->id."/stl/".$stlname);
		}
		
		return true;
	}
	
	public function deleteAi($aiName) {
		$result = mysqli_query($this->connection, " SELECT number_3d,vendor_code,model_type FROM stock WHERE id='$this->id' ");
		$row = mysqli_fetch_assoc($result);
		
		mysqli_query($this->connection, " DELETE FROM ai_files WHERE name='$aiName' ");
		
		if (file_exists($row['number_3d']."/".$this->id."/ai/".$aiName)) {
			unlink($row['number_3d']."/".$this->id."/ai/".$aiName);
		}
		
		return true;
	}
	
	public function deletePDF($pdfName) {
		
		$name = _rootDIR_."Pdfs/".$pdfName;
		if ( file_exists($name) ) 
		{
			unlink($name);
			return true;
		}
		
		return false;
	}
	
	public function likePos($id) {
		
		function addtostock($id, $connection) {
			$result = mysqli_query($connection, " SELECT likes FROM stock WHERE id='$id' ");
		
			$row = mysqli_fetch_assoc($result);
			$likes = $row['likes']+1;
			$addIPS = mysqli_query($connection, " UPDATE stock SET likes='$likes' WHERE id='$id' ");
			if ($addIPS) return $likes;
			return false;
		}
		
		$ipsQuer = mysqli_query($this->connection, " SELECT * FROM ips WHERE ip='$this->IP_visiter' ");
		if ( $ipsQuer->num_rows > 0 ) {
			
			$row = mysqli_fetch_assoc($ipsQuer);
			$str = $row['liked_pos'];
			$str .= $id.';';
			$quer = mysqli_query($this->connection, " UPDATE ips SET liked_pos='$str' WHERE ip='$this->IP_visiter' ");
			if ($quer) {
				$likes = addtostock($id,$this->connection);
			} else {
				printf( "Error Add repair: %s\n", mysqli_error($this->connection) );
				return false;
			}
			
		} else {
			$str = $id.';';
			$quer = mysqli_query($this->connection, " INSERT INTO ips (ip, liked_pos) VALUES ('$this->IP_visiter','$str') ");
			
			if ($quer) {
				$likes = addtostock($id,$this->connection);
			} else {
				printf( "Error Add repair: %s\n", mysqli_error($this->connection) );
				return false;
			}
		}
		
		return $likes;
	}
	public function dislikePos($id) {
		$result = mysqli_query($this->connection, " SELECT dislikes FROM stock WHERE id='$id' ");
		
		$row = mysqli_fetch_assoc($result);
		$dislikes = $row['dislikes']+1;
		$addIPS = mysqli_query($this->connection, " UPDATE stock SET dislikes='$dislikes' WHERE id='$id' ");
		
		$ipsQuer = mysqli_query($this->connection, " SELECT * FROM ips WHERE ip='$this->IP_visiter' ");
		if ( $ipsQuer->num_rows > 0 ) {
			
			$row = mysqli_fetch_assoc($ipsQuer);
			$str = $row['liked_pos'];
			$str .= $id.';';
			mysqli_query($this->connection, " UPDATE ips SET liked_pos='$str' WHERE ip='$this->IP_visiter' ");
			
		} else {
			$str = $id.';';
			$quer = mysqli_query($this->connection, " INSERT INTO ips (ip, liked_pos) VALUES ('$this->IP_visiter','$str') ");
		}
		
		return $dislikes;
	}
	public function setRepairPaid($repairID)
    {
        $query = mysqli_query($this->connection, " UPDATE repairs SET paid=1 WHERE id='$repairID' ");
        if ($query) return true;
        return false;
    }
}