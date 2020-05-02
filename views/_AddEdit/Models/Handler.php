<?php
namespace Views\_AddEdit\Models;
use Views\_Globals\Models\General;

/**
 * общий класс, для манипуляций с базой данных MYSQL, и файлами на сервере
 */
class Handler extends General {
	
	private $id;
	private $number_3d;
	private $vendor_code;
	private $model_type;
	private $model_typeEn;
	public  $date;
	private $isEdit;
	public  $forbiddenSymbols;

	function __construct( $id=false ) 
	{
		parent::__construct();
		if ( $id ) $this->id = $id;

		$this->forbiddenSymbols = ['/','\\',"'",'"','?',':','*','|','>','<',',','.'];
	}
	
	public function tities($str='')
    {
        if ( empty($str) ) return '';
        $titi =  htmlentities(strip_tags($str), ENT_QUOTES | ENT_IGNORE);
        return $titi;
    }

	public function setId($id) {
		if ( isset($id) ) $this->id = $id;
	}


	public function setNumber_3d($number_3d='')
    {
		if ( !empty($number_3d) )
		{
			$this->number_3d = $this->add000( $this->checkCyrillic( str_replace($this->forbiddenSymbols,'_',$number_3d) ) );
			return $this->number_3d;
		}

		$query = $this->baseSql(' SELECT max(number_3d) largestNum FROM stock ');
		//if ( isset($query['error']) ) throw new Error("Error finding last 3d number" . $query['error'], 1);
		
		$row = mysqli_fetch_assoc($query);
		$newNum = intval($row['largestNum']);

		return $this->number_3d = "000" . ++$newNum;
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
        if ( !is_array($collections) || empty($collections) ) return null;

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

	/*
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
	}*/

    public function makeLabels($labels=[])
    {
        if (empty($labels)) return '';

        $str_labels = '';
        $labelsOrigin = $this->getStatLabArr('labels');

        foreach ( $labels as $label )
        {
            foreach ( $labelsOrigin?:[] as $labelOrigin )
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
		if ( !trim($datas) ) return true; // в некоторых случаях в стоке обновлять нечего, только статус

		$where = " WHERE id='$id' ";
		$queryStr = "UPDATE stock SET ".$datas.$where;
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

	/*
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
	*/
	
	public function addImageFiles($files, $imgRows)
    {
        $imgCount = count($files['name']?:[]);

        /* для добавления эскиза */
        $c = 0;
        if ( !empty($imgRows[$c]['img_name']) && $imgRows[$c]['sketch'] == 1 )
        {
            $sketchNames = explode('#',$imgRows[$c]['img_name']);
            $num3D = $sketchNames[0];
            $modelID = $sketchNames[1];
            $imgName = $sketchNames[2];

            $pathFrom = _stockDIR_ . $num3D . "/" . $modelID . "/images/" . $imgName;
            if ( file_exists($pathFrom) )
            {
                $pathTo = $this->number_3d.'/'.$this->id.'/images/'.$imgName;
                if ( copy($pathFrom, $pathTo) )
                {
                    $imgRows[$c]['img_name'] = $imgName;
                }
            }
            $c++;
        }
        /* енд для добавления эскиза */


        for ( $i = 0; $i < $imgCount; $i++ )
        {
            $randomString = randomStringChars(8,'en','symbols');
            //если имя есть, это значит что добавили вручную
            if ( !empty( basename($files['name'][$i]) ) )
            {
                $info = new \SplFileInfo($files['name'][$i]);
                $extension = pathinfo($info->getFilename(), PATHINFO_EXTENSION);
                $uploading_img_name = $this->number_3d."_".$randomString.mt_rand(0,98764321).".".$extension;
                $destination = $this->number_3d.'/'.$this->id.'/images/'.$uploading_img_name;
                $tmpName = $files['tmp_name'][$i];

                if ( move_uploaded_file($tmpName, $destination) ) {
                    $imgRows[$c]['img_name'] = $uploading_img_name;
                    $c++;
                } else {
                    exit('Error moving image file '. $uploading_img_name);
                }
            }
        }

		return $imgRows;
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
		
		$zip = new \ZipArchive();
		$zip_name = $this->number_3d."-".$this->model_typeEn.".zip";
		$zip->open($folder.$zip_name, \ZIPARCHIVE::CREATE);
		$countSTls = count($filesSTL['name']);
		for ( $i = 0; $i < $countSTls; $i++ ) {
			
			$fileSTL_name = basename($filesSTL['name'][$i]);
			
			if ( !empty($fileSTL_name) ) {
				
				$info = new \SplFileInfo($filesSTL['name'][$i]);
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
		
		$zip = new \ZipArchive();
		$zip_name = $this->number_3d."-".$this->model_typeEn.".zip";
		$zip->open($folder.$zip_name, \ZIPARCHIVE::CREATE);
		$countAis = count($filesAi['name']);
		for ( $i = 0; $i < $countAis; $i++ ) {
			
			$fileAi_name = basename($filesAi['name'][$i]);
			
			if ( !empty($fileAi_name) ) {
				
				$info = new \SplFileInfo($filesAi['name'][$i]);
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
	//        debug($insertions,'$insertions');

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
                $which = $update['which'];
                $cost = '';
                if ( isset($update['cost']) ) $cost = ", cost='{$update['cost']}'";

                $queryStr = " UPDATE repairs SET repair_descr='$description', which='$which' $cost WHERE id='$id' ";

                $updQuery = mysqli_query($this->connection, $queryStr);
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
		
		//include(_globDIR_.'db.php');
		
		$names_quer = mysqli_query($this->connection, " SELECT id,number_3d,vendor_code FROM stock WHERE collections='Детали' AND model_type='$modelType' ");
		
		$resp = [];
		
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
        chdir(_stockDIR_);

		$selQuery = mysqli_query($this->connection, " SELECT number_3d,vendor_code,model_type FROM stock WHERE id='$this->id' ");
		$row = mysqli_fetch_assoc($selQuery);

		$result = [
		    'number_3d'   => $row['number_3d'],
		    'vendor_code' => $row['vendor_code'],
		    'model_type'  => $row['model_type'],
            'dell' => $row['number_3d']." / ".$row['vendor_code']." - ".$row['model_type'],
        ];

		mysqli_query($this->connection, " DELETE FROM stock          WHERE     id='$this->id' ");
		mysqli_query($this->connection, " DELETE FROM model_material WHERE pos_id='$this->id' ");
		mysqli_query($this->connection, " DELETE FROM images         WHERE pos_id='$this->id' ");
		mysqli_query($this->connection, " DELETE FROM gems           WHERE pos_id='$this->id' ");
		mysqli_query($this->connection, " DELETE FROM vc_links       WHERE pos_id='$this->id' ");
		mysqli_query($this->connection, " DELETE FROM statuses       WHERE pos_id='$this->id' ");
		mysqli_query($this->connection, " DELETE FROM pushnotice     WHERE pos_id='$this->id' ");

		$path = $row['number_3d'].'/'.$this->id;
		
		if ( file_exists($path) ) $this->rrmdir($path);

        $files = [];
		if ( file_exists( $row['number_3d']) ) $files = scandir( $row['number_3d'] );
		$is_empty = true;

		for ( $i = 0; $i < count($files); $i++ ) {
			if ( $files[$i] == '.' || $files[$i] == '..' ) continue;
			if ( !empty($files[$i]) ) $is_empty = false;
		}
		if ( $is_empty ) rmdir( $row['number_3d'] );
		
		return $result;
	}

    /**
     * @param $fileName string
     * @param $fileType string
     * @return bool
     * @throws \Exception
     */
    public function deleteFile( $fileName, $fileType )
    {
        if ( !is_string($fileName) || empty($fileName) || !is_string($fileType) || empty($fileType)  )
            throw new \Exception('Имя и тип файла должен быть не пусты и string.',444);

        $configs = [
            'stl' => [
                'table' => 'stl_files',
                'field' => 'stl_name',
                'folder' => 'stl',
                'text' => 'Stl файлы ',
            ],
            'image' => [
                'table' => 'images',
                'field' => 'img_name',
                'folder' => 'images',
                'text' => 'Картинка ',
            ],
            'ai' => [
                'table' => 'ai_files',
                'field' => 'name',
                'folder' => 'ai',
                'text' => 'Файлы накладки ',
            ],
            '3dm' => [
                'table' => '3dm_files',
                'field' => 'name',
                'folder' => '3dm',
                'text' => '3dm файлы ',
            ],
        ];
        if ( !array_key_exists($fileType, $configs) ) throw new \Exception('Передан не известный тип файла.',444);
        $config = $configs[$fileType];

        $modelData = $this->findOne(" SELECT number_3d FROM stock WHERE id='$this->id' ");

        $dellQuery = mysqli_query($this->connection, " DELETE FROM {$config['table']} WHERE {$config['field']}='$fileName' ");
        if ( !$dellQuery ) throw new \Exception(__METHOD__.' Error '. mysqli_error($this->connection));

        $file = _stockDIR_ . $modelData['number_3d']."/".$this->id."/{$config['folder']}/".$fileName;
        if ( file_exists($file) )
        {
            unlink($file);
            return $config['text'];
        } else {
            return false;
        }
    }
//
//    /**
//     * @param $imgName
//     * @return bool
//     * @throws \Exception
//     */
//    public function deleteImage($imgName)
//    {
//        $modelData = $this->findOne(" SELECT number_3d FROM stock WHERE id='$this->id' ");
//
//		$dellQuery = mysqli_query($this->connection, " DELETE FROM images WHERE img_name='$imgName' ");
//		if ( !$dellQuery ) throw new \Exception(__METHOD__.' Error '. mysqli_error($this->connection));
//
//		$file = _stockDIR_ . $modelData['number_3d']."/".$this->id."/images/".$imgName;
//		if ( file_exists($file) )
//		{
//			unlink($file);
//            return true;
//		} else {
//		    return false;
//        }
//	}
//
//    /**
//     * @param $stlName
//     * @return bool
//     * @throws \Exception
//     */
//    public function deleteStl($stlName)
//    {
//        $modelData = $this->findOne(" SELECT number_3d FROM stock WHERE id='$this->id' ");
//
//        $dellQuery = mysqli_query($this->connection, " DELETE FROM stl_files WHERE stl_name='$stlName' ");
//        if ( !$dellQuery ) throw new \Exception(__METHOD__.' Error '. mysqli_error($this->connection));
//
//        $file = _stockDIR_ . $modelData['number_3d']."/".$this->id."/stl/".$stlName;
//		if ( file_exists($file) )
//        {
//            unlink($file);
//            return true;
//        } else {
//            return false;
//        }
//	}
//
//    /**
//     * @param $aiName
//     * @return bool
//     * @throws \Exception
//     */
//    public function deleteAi($aiName) {
//        $modelData = $this->findOne(" SELECT number_3d FROM stock WHERE id='$this->id' ");
//
//        $dellQuery = mysqli_query($this->connection, " DELETE FROM ai_files WHERE name='$aiName' ");
//        if ( !$dellQuery ) throw new \Exception(__METHOD__.' Error '. mysqli_error($this->connection));
//
//        $file = _stockDIR_ . $modelData['number_3d']."/".$this->id."/ai/".$aiName;
//        if ( file_exists($file) )
//        {
//            unlink($file);
//            return true;
//        } else {
//            return false;
//        }
//	}

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
	public function setRepairPaid($repairID, $repairCost)
    {
        $query = mysqli_query($this->connection, " UPDATE repairs SET paid=1, cost='$repairCost' WHERE id='$repairID' ");
        if ($query) return true;

        return 'Error in setRepairPaid() ' . mysqli_error($this->connection);
    }

    /**
    * Формируте массив строк для пакетной вставки/обновления строк
    * в дополнительные таблицы
    */
    public function makeBatchInsertRow($data, $stockID, $tableName)
    {
        if ( !is_array($data) || empty($data) ) return false;
        $materials = [];

        $tableSchema = $this->getTableSchema($tableName);
        //debug($tableSchema,'$tableSchema');

        foreach ( $data as $mats )
        {  
            for( $i = 0; $i < count($mats); $i++ )
            {
                $materials[$i][] = $mats[$i];
            }
        }

        $removeRows = [];
        foreach ( $materials as $key => &$material )
        {
            // проверка на пустые строки
            $toDell = true;
            $toRemove = false;
            $materialAssoc = [];
            //$material - массив строк с полями
            foreach ( $material as $iter => &$mat )
            {
                // когда хоть одно поле заполнено - оставим для внесения в табл.
                if ( !empty($mat) ) {
                    $toDell = false;
                }
                // кандидат на удаление из Таблицы
                if ( (int)$mat === -1 ) {
                    $toRemove = true;
                    break;
                }
                $materialAssoc[$tableSchema[$iter]] = $this->tities($mat);
            }
            $materialAssoc[$tableSchema[++$iter]] = $stockID; // в конец добавим pos_id

            if ( $toDell )
            {
                unset($materials[$key]);
                continue;
            }
            if ( $toRemove )
            {
                $removeRows[] = $material[0];
                //$removeRows['table'] = $name;
                unset($materials[$key]);
                continue;
            }

            $materials[$key] = $materialAssoc;
            
        }
        return ['insertUpdate'=>$materials, 'remove'=>$removeRows];
    }

    /**
     * Пакетное удаление
     *
     * @param $toRemove
     * @param $tableName
     * @return array|bool
     * @throws \Exception
     */
    public function removeRows($toRemove, $tableName)
    {
    	if ( empty($toRemove) || !is_array($toRemove)) return [];
        if ( empty($tableName) || !is_string($tableName) ) throw new \Exception("Error removeRows() table name might be a string!", 1);
        
        $ids = '';
        foreach ( $toRemove as $id )
        {
            if ( !empty($id) ) $ids .= $id . ',';
        }

        if (empty($ids)) return false;

        $ids = '(' . trim($ids,',') . ')';

        try {
            $rem = $this->baseSql( "DELETE from $tableName WHERE id IN $ids" );
            if ( isset($rem['error']) ) throw new \Exception("Error removeRows() : " . $rem['error'], 1);
            return true;

        } catch ( \Exception $e) {
            echo 'removeRows() Выбросил исключение: ',  $e->getMessage(), "\n";
        }

        return false;
    }

    /**
     * Формируте массив строк для пакетной вставки картинок
     * @param $data
     * @return array|bool
     * @throws \Exception
     */
    public function makeBatchImgInsertRow($data)
    {
        $newImgRows = [];
        $imgRows = [];
        if ( !is_array($data) || empty($data) ) return false;

        /* для добавления эскиза */
        $sketchImgName = '';
        if ( isset($data['img_name']['sketch']) ) $sketchImgName = $data['img_name']['sketch'];


        foreach ( $data as $mats )
        {
            for( $i = 0; $i < count($mats); $i++ )
            {
                $imgRows[$i][] = $mats[$i];
            }
        }
        //debug($imgRows,'$imgRow');
        $images = $this->findAsArray("SELECT * FROM images WHERE pos_id='$this->id' ");
        //debug($images,'Images');

        foreach( $imgRows as $imgRowKey => &$imgRow )
        {
            $imgId = (int)$imgRow[0];
            $imgFor = (int)$imgRow[1];
            $isNEWImage = true;
            $modifiedImgRow = [];
            foreach( $images as $image )
            {
                if ( $imgId === (int)$image['id'] )
                {
                    $modifiedImgRow = $image;
                    $isNEWImage = false;
                    break;
                }
            }
            //
            if ( $isNEWImage )
            {
                $modifiedImgRow = ['id'=>'','img_name'=>$sketchImgName,'main'=>'','onbody'=>'','sketch'=>'','detail'=>'','scheme'=>'','pos_id'=>$this->id];
            }
            // уже новая $imgRow
            // сформируем массив флажков
            foreach( $modifiedImgRow as $keyCol => &$column )
            {
                switch ($keyCol)
                {
                    case 'main':
                        $column = ($imgFor == 22)  ? 1 : "";
                        break;
                    case 'onbody':
                        $column = ($imgFor == 23)  ? 1 : "";
                        break;
                    case 'sketch':
                        $column = ($imgFor == 24)  ? 1 : "";
                        break;
                    case 'detail':
                        $column = ($imgFor == 25)  ? 1 : "";
                        break;
                    case 'scheme':
                        $column = ($imgFor == 26)  ? 1 : "";
                        break;
                }
            }
            if ( $isNEWImage )
            {
                $newImgRows[] = $modifiedImgRow;
                unset($imgRows[$imgRowKey]);
            } else {
                $imgRow = $modifiedImgRow;
            }
        }

        return ['newImages'=>$newImgRows,'updateImages'=>$imgRows];
    }

    /**
     * Example:
     * INSERT INTO mytable (id, a, b, c)
     * VALUES  (1, 'a1', 'b1', 'c1'),
     * (2, 'a2', 'b2', 'c2'),
     * (3, 'a3', 'b3', 'c3'),
     * (4, 'a4', 'b4', 'c4'),
     * (5, 'a5', 'b5', 'c5'),
     * (6, 'a6', 'b6', 'c6')
     * ON DUPLICATE KEY UPDATE
     * id=VALUES(id),
     * a=VALUES(a),
     * b=VALUES(b),
     * c=VALUES(c)
     *
     * @param array $rows
     * массив строк
     * @param string $table
     * имя таблицы
     * @return bool|int
     * @throws \Exception
     */
    public function insertUpdateRows($rows, $table)
    {
        if ( empty($rows) || empty($table) ) return false;
        $values = '';
        $fields = [];
        foreach ($rows as $row)
        {
            $val = '';
            foreach ($row as $field => $value)
            {
                $fields[$field] = $field;

                $val .= "'".$value."'" . ',';
            }
            $values  .= '(' . trim($val,',') . '),';
        }
        $values  =  trim($values,',');
        $columns = '';
        $update = [];
        foreach ($fields as $field)
        {
            $columns .= $field . ',';
            $update[] = $field . '=VALUES(' . $field . ')';
        }
        $columns = '(' . trim($columns,',') . ')';
        $update = implode(',', $update);

        $sqlStr = "INSERT INTO $table $columns VALUES $values ON DUPLICATE KEY UPDATE $update";

        if ( is_array( $sql = $this->sql($sqlStr) ) ) return $sql;

        return true;
        //debug($sql,'$sql',1);


        /*
        $db = Yii::$app->db;
        $sql = $db->queryBuilder->batchInsert($table, $fields, $rows );

        $update = [];
        foreach ($fields as $field)
        {
            $field  = $db->quoteSql($field);
            $update[] = $field . '=VALUES(' . $field . ')';
        }
        $update = implode(',', $update);

        try {
            return $db->createCommand($sql . ' ON DUPLICATE KEY UPDATE '. $update )->execute();
        } catch ( Exception $e) {
            echo 'insertUpdateRows() Выбросил исключение: ',  $e->getMessage(), "\n";
        }

        return false;
        */
    }

}