<?php
namespace Views\_AddEdit\Models;
use Views\_Globals\Models\General;

class AddEdit extends General
{

    public $id;
    public $row;
	public $workingCenters;
	public $users; //array - массив пользователей. Нужен для статусов

    /**
     * AddEdit constructor.
     * @param bool $id
     * @throws \Exception
     */
    public function __construct($id=false )
    {
        parent::__construct();
        if ( $id ) $this->id = (int)$id;

        $this->connectToDB();
    }

    public function connectToDB()
    {
        parent::connectToDB();

        $this->getWorkingCenters();
        $this->getAllUsers();

        return $this->connection;
    }


    /**
     * @return array
     * @throws \Exception
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

    /**
     * @param $component
     * @return array
     * @throws \Exception
     */
    public function getComplected($component)
    {
        $and = "";
        if ( $component === 2 ) $and = "AND st.id<>'{$this->id}'";
        $sql = " SELECT st.id, st.model_type, img.pos_id, img.img_name 
				FROM stock st 
					LEFT JOIN images img 
					ON (st.id = img.pos_id AND img.main=1) 
				WHERE st.number_3d='{$this->row['number_3d']}' 
				$and ";
        $complected = $this->findAsArray( $sql );
        foreach ($complected as &$complect)
        {
            $imagePath = $this->row['number_3d'].'/'.$complect['id'].'/images/'.$complect['img_name'];
            $complect['img_name'] = _stockDIR_HTTP_ . $imagePath;
            if ( !file_exists(_stockDIR_ . $imagePath) ) $complect['img_name'] = _stockDIR_HTTP_."default.jpg";
        }
        return $complected;
    }

	/*
	function printHeaderEditAddForm($component)
    {
		$header = '';
		if ( $component === 2 || $component === 3 ) 
		{
			$thisNum = $this->row['number_3d'];
			$thisMT = $this->row['model_type'];
			
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
	*/
	
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
	public function getNamesVCLi()
    {
		$vc_namesLI = '';
		$vc_names_quer = mysqli_query($this->connection, " SELECT name FROM vc_names ");
		while( $vc_names_row = mysqli_fetch_assoc($vc_names_quer) ) {
			$vc_namesLI .= "<li><a elemToAdd VCTelem>".$vc_names_row['name']."</a></li>";
		}
		return $vc_namesLI;
	}
	public function getNum3dVCLi( $row_dop_vc )
    {
		$num3DVC_LI = [];
		for ( $i = 0; $i < count($row_dop_vc); $i++ ) {
			
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

    /**
     * @return array|bool
     * @throws \Exception
     */
    public function getGeneralData()
    {
    	$this->row = $this->findOne( " SELECT * FROM stock WHERE id='$this->id' ");
    	if ( empty($this->row) ) return [];
    	$this->row['collections'] = explode(';',$this->row['collections']);
        foreach ( $this->statuses as $status )
        {
            if ( $status['id'] === $this->row['status'] )
            {
                $this->row['status'] = $status;
                break;
            }
        }
		return $this->row;
	}

    /**
     * @return array|bool
     * @throws \Exception
     */
    public function getStl()
    {
        return $this->findOne( " SELECT * FROM stl_files WHERE pos_id='$this->id' ");
	}
    /**
     * @return array
     * @throws \Exception
     */
    public function get3dm()
    {
        return $this->findOne( " SELECT * FROM rhino_files WHERE pos_id='$this->id' ");
    }
    /**
     * @return array|bool
     * @throws \Exception
     */
    public function getAi()
    {
        return $this->findOne( " SELECT * FROM ai_files WHERE pos_id='$this->id' ");
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
    /**
     * @param bool $row
     * @return array|bool
     * @throws \Exception
     */
	public function getMaterials($row=false)
	{
		$materials = $this->findAsArray(" SELECT * FROM metal_covering WHERE pos_id='$this->id' ");
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

    /**
     * @param bool $sketch
     * @return array
     * @throws \Exception
     */
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
                if ( $row_img['main'] ) $respArr[$i]['main'] = $row_img['main'];

                $imgPath = $this->row['number_3d'].'/'.$this->id.'/images/'.$row_img['img_name'];

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

    /**
     * @return array
     * @throws \Exception
     */
    public function getGems()
	{
		return $this->findAsArray( " SELECT * FROM gems WHERE pos_id='$this->id' ");
	}

    /**
     * @return array
     * @throws \Exception
     */
    public function getDopVC()
	{
		return $this->findAsArray( " SELECT * FROM vc_links WHERE pos_id='$this->id' ");
	}

    /**
     * @return array
     * @throws \Exception
     */
    public function getRepairs()
    {
		return $this->findAsArray( " SELECT * FROM repairs WHERE pos_id='$this->id' ");
	}


	public function getStatus($stockStatusID='', $selMode='')
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
		if ( !empty($stockStatusID) )
		{
			foreach ( $permittedStatuses as &$permittedStatus )
			{
				if ( $stockStatusID == $permittedStatus['id'] ) $permittedStatus['check'] = "checked";
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

	public function getLabels($str='') 
	{
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