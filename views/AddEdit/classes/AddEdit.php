<?php
	require_once( _globDIR_ . 'classes/General.php');
	class AddEdit extends General
    {
		
		function __construct( $id=false, $server ) {
			parent::__construct($server);
			if ( $id ) $this->id = $id;
		}

		public function connectToDB()
        {
            parent::connectToDB();

            $this->getWorkingCenters();
            $this->getAllUsers();

            return $this->connection;
        }

        public $id;
        public $row;
		public $workingCenters;
		public $users; //array - массив пользователей. Нужен для статусов


		/**
		*
		*/
		public function getDataTables()
        {
			$tabs = [
			    'author',
                'modeller3d',
                'model_type',
                'model_material',
                'model_covering',
                'handling',
                'metal_color',
                'vc_names',
                'gems_color',
                'gems_cut',
                'gems_names',
                'gems_sizes',
            ];
			$tables = [];

            $service_data = $this->findAsArray("select * from service_data ORDER BY name");

			foreach ( $service_data as $row )
			{
                foreach ( $tabs as $tab )
                {
                    if ( $row['tab'] === $tab ) $tables[$tab][] = $row;
                }
			}

			//debug($tables,'',1);

            foreach ( $tables['gems_sizes'] as $size )
            {
                if ( is_numeric($size['name']) )
                {
                    $tables['gems_sizes']['num'][] = $size['name'];
                } else {
                    $tables['gems_sizes']['notnum'][] = $size['name'];
                }
            }
            sort($tables['gems_sizes']['num']);

            //$tables['collections'] = $this->general->collections_arr;
            //$tables['vc_names'] = $this->getNum3dVCList($tables['vc_names']);

			return $tables;
		}


        /**
         *  Проверим на существование этой модели
         */
        public function checkID()
        {
            $query = mysqli_query($this->connection, " select 1 from stock where id='$this->id' limit 1 ");
            if ( $query->num_rows ) return true;
            return false;
        }

		public function getAllUsers()
        {
            $query = mysqli_query($this->connection, " SELECT id,fio,fullFio,location,access FROM users ");
            while( $userRow = mysqli_fetch_assoc($query) )
            {
                $this->users[] = $userRow;
            }
        }

		public function getWorkingCenters()
        {
            if ( isset($this->workingCenters) ) return $this->workingCenters;
            $query = mysqli_query($this->connection, " SELECT id,name,descr,user_id FROM working_centers ");
            while( $centerRow = mysqli_fetch_assoc($query) )
            {
                $this->workingCenters[$centerRow['name']][$centerRow['id']] = $centerRow;
            }
            return $this->workingCenters;
        }


		public function setPrevPage()
        {
            $pp = '';
			$thisPage = 'http://'.$this->server['HTTP_HOST'].$this->server['REQUEST_URI'];
			if ( $thisPage !== $this->server["HTTP_REFERER"] ) {
				$_SESSION['prevPage'] = $this->server["HTTP_REFERER"];
				$pp = $_SESSION['prevPage'];
			}
			return $pp;
		}
		
		function printHeaderEditAddForm($component)
        {
			$header = '';
			if ( $component === 2 || $component === 3 ) {
				$thisNum = $_SESSION['general_data']['number_3d'];
				$thisMT = $_SESSION['general_data']['model_type'];
				
				if ( $component === 2 ) {
					$str = "Редактировать Модель <strong>".$thisNum." - ".$thisMT."</strong>";
					$duplG = "pencil";
				}
				if ( $component === 3 ) {
					$str = "Добавить комплект к <strong>".$thisNum."</strong>";
					$duplG = "duplicate";
				}
				
				$header .= "<span class=\"glyphicon glyphicon-$duplG\"></span> $str";
				$header .= " (<i>В Комплекте: </i>";
				$complect = mysqli_query($this->connection, " SELECT id,model_type FROM stock WHERE number_3d='$thisNum' ");

                $mass = 0;
				while( $complects = mysqli_fetch_assoc($complect) )
                {
					if ( ( $component === 2 ) && ( $complects['id'] == $this->id ) ) continue;
					$mass++;
					
					$compl_id = $complects['id'];
					$compl_quer = mysqli_query($this->connection, " SELECT img_name FROM images WHERE pos_id='$compl_id' AND main='1' ");
					$compl_row = mysqli_fetch_assoc($compl_quer);

                    $file = "$thisNum/{$complects['id']}/images/{$compl_row['img_name']}";
                    $fileImg = _stockDIR_HTTP_.$file;
                    if ( !file_exists(_stockDIR_.$file) ) $fileImg = _stockDIR_HTTP_."default.jpg";

				    $header .= " <a class=\"imgPrev\" imgtoshow=\"$fileImg\" href=\"../modelView/index.php?id={$complects['id']}\">{$complects['model_type']}</a>";
				}
				if ( empty($mass) ) $header .= "Нет"; // если нет комплекта
				$header .= ")";
			}

			return $header;
		}
		
		public function getDataLi()
        {
            // список таблиц
			$querArr = array('collections', 'author', 'modeller3d', 'model_type', 'jeweler_names');
			$data_Li = array();
			
			for ( $i = 0; $i < count($querArr); $i++ )
			{
				$table = $querArr[$i];
				$res = mysqli_query($this->connection, " SELECT * FROM $table ORDER BY name");
				
				$goldAI = '';
				$coll = '';
				
				if ( $table == 'collections' )  $coll = 'coll';
				while( $row = mysqli_fetch_assoc($res) )
                {
					if ( (int)$row['id'] === 22 || (int)$row['id'] === 53 )  $goldAI = 'aiblock';
					$data_Li[$table] .= '<li><a elemToAdd '.$coll.' '.$goldAI.' collId="'.$row['id'].'">'.$row['name'].'</a></li>';
					$goldAI = '';
				}
			}
			
			return $data_Li;
		}

		public function getGemsLi()
        {
			
			$querArr = array('gems_sizes', 'gems_cut', 'gems_names', 'gems_color');
			$gems_Li = array();
			
			$num_arr = array();
			$notnum_arr = array();
			
			for ( $i = 0; $i < count($querArr); $i++ ){
				$table = $querArr[$i];
				$gem_quer = mysqli_query($this->connection, " SELECT name FROM $table ");	
				while( $gem_row = mysqli_fetch_assoc($gem_quer) ) {
					
					if ( $table == 'gems_sizes' ) {
						if ( is_numeric($gem_row['name']) ) {
						   $num_arr[] = $gem_row['name'];
						} else {
						   $notnum_arr[] = $gem_row['name'];
						}
					} else {
						$gems_Li[$table] .= '
							<li style="position:relative;">
								<a elemToAdd>'.$gem_row['name'].'</a>
								<div class="addElemMore" addElemMore>+</div>
							</li>
						';
					}
				}
			}
			sort($num_arr);
			sort($notnum_arr);
			foreach ($num_arr as &$value) {
			   $gems_Li['gems_sizes'] .= '<li><a elemToAdd>'.$value.'</a></li>';
			};
			$gems_Li['gems_sizes'] .= '<li role="separator" class="divider"><a></a></li>';
			foreach ($notnum_arr as &$value) {
			   $gems_Li['gems_sizes'] .= '<li><a elemToAdd>'.$value.'</a></li>';
			};
			
			return $gems_Li;
		}
		public function getNamesVCLi(){
			$vc_namesLI = '';
			$vc_names_quer = mysqli_query($this->connection, " SELECT name FROM vc_names ");
			while( $vc_names_row = mysqli_fetch_assoc($vc_names_quer) ) {
				$vc_namesLI .= "<li><a elemToAdd VCTelem>".$vc_names_row['name']."</a></li>";
			}
			return $vc_namesLI;
		}
		public function getNum3dVCLi( &$vc_Len, &$row_dop_vc )
        {
			$num3DVC_LI = array();
			for ( $i = 0; $i < $vc_Len; $i++ ) {
				
				$prnt_vc_names = $row_dop_vc[$i]['vc_names'];
				$details_quer = mysqli_query($this->connection, " SELECT id,number_3d,vendor_code FROM stock WHERE collections='Детали' AND model_type='$prnt_vc_names' ");
				
				while( $vc_det_row = mysqli_fetch_assoc($details_quer) ) {
					
					$id_det = $vc_det_row['id'];
					$img_det_quer = mysqli_query($this->connection, " SELECT img_name,main FROM images WHERE pos_id='$id_det' ");
					
					while( $img_det_row = mysqli_fetch_assoc($img_det_quer) ) {
						if ( (int)$img_det_row['main'] == 1  ) $imgtoshow = $img_det_row['img_name'];
					}

					$file = $vc_det_row['number_3d'].'/'.$id_det.'/images/'.$imgtoshow;
                    $fileImg = _stockDIR_HTTP_.$file;
					if ( !file_exists(_stockDIR_.$file) ) $fileImg = _stockDIR_HTTP_."default.jpg";

                    $nameVC = $vc_det_row['vendor_code'] ?: $vc_det_row['number_3d'];
					$num3DVC_LI[$i] .= '<li><a class="imgPrev" elemToAdd imgtoshow="'.$fileImg.'">'.$nameVC.'</a></li>';
				}
			}
			return $num3DVC_LI;
		}

		public function getGeneralData()
        {
			$result = mysqli_query($this->connection, " SELECT * FROM stock WHERE id='$this->id' ");
			$row = mysqli_fetch_assoc($result);
			// автозаполнение для добавления комплекта
			$_SESSION['general_data']['id']             = $this->id;
			$_SESSION['general_data']['number_3d']      = $row['number_3d'];
			$_SESSION['general_data']['vendor_code']    = $row['vendor_code'];
			$_SESSION['general_data']['author']         = $row['author'];
			$_SESSION['general_data']['modeller3d']     = $row['modeller3D'];
			$_SESSION['general_data']['jewelerName']    = $row['jewelerName'];
			$_SESSION['general_data']['model_weight']   = $row['model_weight'];
			$_SESSION['general_data']['model_covering'] = $row['model_covering'];
			$_SESSION['general_data']['model_material'] = $row['model_material'];
			$_SESSION['general_data']['model_type']     = $row['model_type'];
			$_SESSION['general_data']['description']    = $row['description'];
			$_SESSION['general_data']['status']   		= $row['status'];
			$_SESSION['general_data']['labels']   		= $row['labels'];


            $_SESSION['general_data']['collection'] 	= $row['collections'];

            $this->row = $row;
			return $row;
		}
		public function getStl(){
			$respArr = array();	
			$respArr['haveStl'] = "hidden";
			$respArr['noStl'] = "";
			$stl_quer = mysqli_query($this->connection, " SELECT * FROM stl_files  WHERE pos_id='$this->id' ");	
			if ( $stl_quer -> num_rows > 0 ) {
				$stl_file = mysqli_fetch_assoc($stl_quer);
				$respArr['stl_name'] = $stl_file['stl_name'];
				$respArr['haveStl'] = "";
				$respArr['noStl'] = "hidden";
			}
			return $respArr;
		}
		
		public function getAi() {
			$respArr = array();	
			$respArr['haveAi'] = "hidden";
			$respArr['noAi'] = "";
			$ai_quer = mysqli_query($this->connection, " SELECT * FROM ai_files WHERE pos_id='$this->id' ");	
			if ( $ai_quer->num_rows > 0 ) {
				$ai_file = mysqli_fetch_assoc($ai_quer);
				$respArr['name'] = $ai_file['name'];
				$respArr['haveAi'] = "";
				$respArr['noAi'] = "hidden";
			}
			return $respArr;
		}


		/*	Старый вариант
		public function getMaterial($str_material) 
		{
			$material = array();
			if ( !empty($str_material) ) {
				$material_arr = explode(";",$str_material);
				foreach ( $material_arr as &$value ) {
					if ( "Золото" == $value )       $material['metall_gold'] = "checked";
					if ( "Серебро" == $value )      $material['metall_silv'] = "checked";
					
					if ( "585" == $value )          $material['probe585'] = "checked";
					if ( "750" == $value )          $material['probe750'] = "checked";
					
					if ( "Белое" == $value )        $material['gold_white'] = "checked";
					if ( "Красное" == $value )      $material['gold_red'] = "checked";
					if ( "Желтое(евро)" == $value ) $material['gold_yellow'] = "checked";
				}
			} else {
				$material['metall_silv'] = "checked";
			}
			return $material;
		}
		public function getCovering($str_covering) {
			$covering = array();
			if ( !empty($str_covering) ) {
				$covering_arr = explode(";",$str_covering);
				foreach ( $covering_arr as &$value ) {
					if ( "Родирование" == $value )      $covering['rhodium']  = "checked";
					if ( "Золочение" == $value )        $covering['golding']  = "checked";
					if ( "Чернение" == $value )         $covering['blacking'] = "checked";
					
					if ( "Полное" == $value )           $covering['full']     = "checked";
					if ( "Частичное" == $value )        $covering['onPartical']    = "checked";
					if ( "По крапанам" == $value )      $covering['onProngs'] = "checked";
					if ( "Отдельные части" == $value )  $covering['parts'] = "checked";
				}
				$covering_part = explode("-",$str_covering);
				if ( $covering_part[1] ) $covering['partsStr'] = $covering_part[1];
			}

			return $covering;
		}
		*/
		public function getMaterials($row=false)
		{
			$materials = $this->findAsArray(" SELECT * FROM metal_covering WHERE pos_id='$this->id' ");
			if ( isset($materials['error']) ) throw new Exception("Error in getMaterials(): " . $materials['error'], 500);
			if (!empty($materials)) return $materials;

			if ( $row ) $this->row = $row;

			 $materials = [
	            [
	                'part' => '',
	                'type' => '',
	                'probe' => '',
	                'metalColor' => '',
	                'covering' => '',
	                'area' => '',
	                'covColor' => '',
	                'handling' => '',
                ],
	        ];

	        $privParts = stristr($this->row['model_covering'], 'Отдельные части');
	        $hasDetail = false; // есть деталировка
	        if ( $privParts )
	        {
	        	$str_mod_priv_arr = explode("-",$this->row['model_covering']);
	        	$hasDetail = true;
	        	$materials[1]['part'] = $str_mod_priv_arr[1]?:'';
	        }

	        $str_material_arr = explode(";",$this->row['model_material']);

	        foreach ( $str_material_arr as $value )
	        {
	        	$i = 0;
	            switch ( $value )
	            {
	                case "585":
	                    $materials[0]['probe'] = $value;
	                    if ( $hasDetail ) $materials[1]['probe'] = $value;
	                    break;
	                case "750":
	                    $materials[0]['probe'] = $value;
	                    if ( $hasDetail ) $materials[1]['probe'] = $value;
	                    break;
	                case "Золото":
	                    $materials[0]['type'] = $value;
	                    if ( $hasDetail ) $materials[1]['type'] = $value;
	                    break;
	                case "Серебро":
	                    $materials[0]['type'] = $value;
	                    break;
                    case "Красное":
                    	$materials[0]['metalColor'] = $value;
	                    break;
	                case "Белое":
	                	if ( $hasDetail ) $i = 1;
	                    $materials[$i]['metalColor'] = $value;
	                    break;	               
	                case "Желтое(евро)":
	                	if ( $hasDetail ) $i = 1;
	                    $materials[$i]['metalColor'] = $value;
	                    break;
	            }
	        }

	        $i = $hasDetail?1:0;
	        $str_mod_cov_arr = explode(";",$this->row['model_covering']);
	        foreach ( $str_mod_cov_arr as $value )
	        {
	            switch ( $value )
	            {
	                case "Родирование":
	                    $materials[$i]['covering'] = $value;
	                    break;
	                case "Золочение":
	                    $materials[$i]['covering'] = $value;
	                    break;
	                case "Чернение":
	                    $materials[$i]['covering'] = $value;
	                    break;
	                case "Полное":
	                    $materials[$i]['area'] = $value;
	                    break;
	                case "Частичное":
	                    $materials[$i]['area'] = $value;
	                    break;
	                case "По крапанам":
	                    $materials[$i]['area'] = $value;
	                    break;
	            }
	        }
	        

	        return $materials;
		}
		
		public function getImages($sketch = false)
        {
			$respArr = array();
			if ( $sketch === true ) {
				$img = mysqli_query($this->connection, " SELECT * FROM images WHERE pos_id='$this->id' AND sketch='1' ");
			} else {
				$img = mysqli_query($this->connection, " SELECT * FROM images WHERE pos_id='$this->id' ");
			}
			
			if ( $img->num_rows > 0 ) {
                $this->getStatLabArr('image');
				$i = 0;
				while( $row_img = mysqli_fetch_assoc($img) ) {
					$respArr[$i]['id'] = $row_img['id'];
                    $respArr[$i]['imgName'] = $row_img['img_name'];

                    $imgPath = $_SESSION['general_data']['number_3d'].'/'.$this->id.'/images/'.$row_img['img_name'];

                    if ( !file_exists(_stockDIR_.$imgPath) )
                    {
                        $respArr[$i]['imgPath'] = _stockDIR_HTTP_."default.jpg";
                    } else {
                        $respArr[$i]['imgPath'] = _stockDIR_HTTP_.$imgPath;
                    }

                    //debug($row_img,'$row_img');

					// проставляем флажки
                    $img_arr = $this->imageStatuses;
                    //debug($img_arr,'$img_arr',1);
                    foreach ( $row_img as $key => $value )
                    {
                        // нижний ходит по статусам из табл и сверяет имена с ключом из картинок
                        $flagToResetNo = false;
                        foreach ( $img_arr as &$option )
                        {
                            if ( $key === $option['name_en'] && (int)$value === 1 )
                            {
                                $option['selected'] = $value;
                                $flagToResetNo = true;
                            }
                            // уберем флажек с "НЕТ" если был выставлен на чем-то другом
                            if (  (int)$option['id'] === 27 && $flagToResetNo === true ) $option['selected'] = 0;
                        }
                    }
                    $respArr[$i]['imgStat'] = $img_arr;
                    $i++;
				}
			}
			return $respArr;
		}


		public function getGems(){
			$respArr = array();	
			$gems = mysqli_query($this->connection, " SELECT * FROM gems WHERE pos_id='$this->id' ");
			while( $rowGems[] = mysqli_fetch_assoc($gems) ){}
			array_pop($rowGems); 
			$respArr['gs_len'] = count($rowGems);
			$respArr['row_gems'] = &$rowGems;
			return $respArr;
		}
		public function getDopVC(){
			$respArr = array();	
			$dop_vc = mysqli_query($this->connection, " SELECT * FROM vc_links WHERE pos_id='$this->id' ");
			while( $rowVC[] = mysqli_fetch_assoc($dop_vc) ){}
			array_pop($rowVC);
			$respArr['vc_Len'] = count($rowVC);
			$respArr['row_dop_vc'] = &$rowVC;
			return $respArr;
		}

		public function getRepairs()
        {
			$result = [];
			$repQuery = mysqli_query($this->connection, " SELECT * FROM repairs WHERE pos_id='$this->id' ");

			if ( $repQuery->num_rows > 0 ) {
				while($repRow = mysqli_fetch_assoc($repQuery)) $result[] = $repRow;

			}
			return $result;
		}

		public function getWordData()
        {
			$respArr = array();
			
			$respArr['stonesFromWord'] = 'hidden';
			$respArr['imgFromWord'] = '';
			$respArr['vcDopFromWord'] = '';
			$respArr['stonesScript'] = '';
			
			if ( isset($_SESSION['fromWord_data']['filesImg']) ) {
				$mass = $_SESSION['fromWord_data']['filesImg'];
				foreach( $mass as $key=>$value ){
					$fnameExt = explode('.', $value);
					if ( $fnameExt[1] == 'jpeg' || $fnameExt[1] == 'jpg' || $fnameExt[1] == 'png' ) $respArr['imgFromWord'] .= '<span class="hidden">'.$value.'</span>';
					$fnameExt = '';
				}
			}
			
			if ( isset($_SESSION['fromWord_data']['stones']) ) {
				$respArr['stonesFromWord'] = '';
				$respArr['stonesScript'] = '<script defer src="js/addImgFromWord.js?ver=008"></script>';
			}
			
			if ( isset($_SESSION['fromWord_data']['vcDop']) ) $respArr['vcDopFromWord'] = "<div>{$_SESSION['fromWord_data']['vcDop']}</div>";
			
			return $respArr;
		}


		



		public function getStatus($row=[], $selMode='')
        {
			$locations = explode(',',$this->user['location']); // ID участки к которому относится юзер

			$statuses = $this->statuses; // все возможные статусы
            $permittedStatuses = []; // разрешенные статусы на участок


            if ( $this->user['access'] > 1 )
            {
                foreach ( $statuses as $status )
                {
                    foreach ( $locations as $location )
                    {
                        // возьмем статусы которые подходят этому юзеру
                        if ( (int)$status['location'] === (int)$location ) $permittedStatuses[] = $status;
                    }
                }
            } else {
                //иначе возьмем все статусы
                $permittedStatuses = $statuses;
            }


			// Этот код ставит галочку на текущем статусе в соответствии со статусом в таблице Stock
			if ( isset($row['status']) && !empty($row['status']) )
			{

                //  КОСТЫЛЬ!!!!
                // при добавлении новых моделей в stock status заходит ID
                // возьмём этот Id из статусов
                if ( $rowStatus = $this->getStatusCrutch($row['status']) ) $row['status'] = $rowStatus;

				$stockStatus = trim($row['status']);
				for ( $i = 0; $i < count($permittedStatuses); $i++ )
				{
					if ( $stockStatus == $permittedStatuses[$i]['name_ru'] ) $permittedStatuses[$i]['check'] = "checked";
				}


			} else {
                if ( $selMode !== 'selectionMode' ) $permittedStatuses[0]['check'] = "checked";
			}


            $permittedStatuses = $this->sortStatusesByWorkingCenters($permittedStatuses);

			return $permittedStatuses;
		}

		/**
         * отсортируем статусы по участкам, добавим описание, ответственных.
         * @param $statuses array
		 */
		private function sortStatusesByWorkingCenters($statuses)
        {
            //debug($this->workingCenters,'workingCenters');
            //debug($statuses,'$statuses');

            foreach ( $this->workingCenters as $key => &$workingCenters )
            {
                foreach ( $workingCenters as $wcKey => &$subUnit )
                {
                    $subUnit['statuses'] = []; // массив доступных статусов
                    $subUnit['user'] = ''; // ответственный из Users
                    foreach ( $statuses as $status )
                    {
                        if ( $status['location'] == $subUnit['id'] ) $subUnit['statuses'][] = $status;

                        // проверим есть ли Ответственный
                        foreach ( $this->users as $user )
                        {
                            if ($user['id'] == $subUnit['user_id'])
                            {
                                $subUnit['user'] = $user['fio'];
                                break;
                            }
                        }
                        if (empty($subUnit['user']) ) $subUnit['user'] = 'Нет';

                    }

                    // удалим подУчастки с пустыми статусами
                    if ( empty($subUnit['statuses']) ) unset($workingCenters[$wcKey]);
                }

                // удалим пустые Участки
                if ( empty($workingCenters) ) unset($this->workingCenters[$key]);
            }

            //debug($this->workingCenters,'workingCenters');
            return $this->workingCenters;
        }

		public function getLabels($str) {
			$labels = $this->getStatLabArr('labels');
			if ( isset($str) && !empty($str) ) {
				$arr_labels = explode(";",$str);
				for ( $i = 0; $i < count($arr_labels); $i++ )
				{
					for ( $j = 0; $j < count($labels); $j++ ) {
						if ( $arr_labels[$i] == $labels[$j]['name'] ) $labels[$j]['check'] = "checked";
					}
				}
			}
			return $labels;
		}


	}