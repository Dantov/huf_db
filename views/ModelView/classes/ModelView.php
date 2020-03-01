<?php
include( _globDIR_ . 'classes/General.php');

class ModelView extends General {
	function __construct( $id=false, $server=false, $user=false ) {
		parent::__construct($server);
		if ( isset($id) ) $this->id = $id;
		//if ( isset($user) ) $this->user = $user;
	}
	
	private $id;
	public $number_3d;
	//private $user;
	
	public  $row;
	public  $coll_id;
	public  $rep_Query;
	
	private $img_Query;
	private $gems_Query;
	private $dopVc_Query;
	private $stl_Query;
	private $complect_Query;
	private $ai_Query;
	
	public function dataQuery()
    {
		$stock_Query       = mysqli_query($this->connection, " SELECT * FROM stock     WHERE     id='$this->id' ");
		$this->img_Query   = mysqli_query($this->connection, " SELECT * FROM images    WHERE pos_id='$this->id' ");
		$this->gems_Query  = mysqli_query($this->connection, " SELECT * FROM gems      WHERE pos_id='$this->id' ");
		$this->dopVc_Query = mysqli_query($this->connection, " SELECT * FROM vc_links  WHERE pos_id='$this->id' ");
		$this->stl_Query   = mysqli_query($this->connection, " SELECT * FROM stl_files WHERE pos_id='$this->id' ");
		$this->ai_Query    = mysqli_query($this->connection, " SELECT * FROM ai_files  WHERE pos_id='$this->id' ");
		$this->rep_Query   = mysqli_query($this->connection, " SELECT * FROM repairs   WHERE pos_id='$this->id' ");
		
		$this->row = mysqli_fetch_assoc($stock_Query);
		$this->number_3d = $this->row['number_3d'];
		
		$this->complect_Query  = mysqli_query($this->connection, " SELECT id,model_type FROM stock WHERE number_3d='{$this->number_3d}' ");
	}

    public function getCollections()
    {
        $collections = explode(';',$this->row['collections']);
        $collectionStr = '';
        foreach ( $collections as $collection ) $collectionStr .= "'".$collection."',";
        $collectionStr = "(". trim($collectionStr,",") . ")";

        $collId_Query = mysqli_query($this->connection, " SELECT id,name FROM collections WHERE name IN $collectionStr ");
        $res = [];
        while( $coll = mysqli_fetch_assoc($collId_Query) ) $res[] = $coll;
        return $res;
    }
	
	public function getStl() {
		
		$result = array();
		$result['dopBottomScripts'] = '';
		$result['button3D'] = '';
		
		//$stlrow = mysqli_num_rows($this->stl_quer);
		
		if ( $this->stl_Query -> num_rows > 0 ) {
			
			$stl_file = mysqli_fetch_assoc($this->stl_Query);
			$result['button3D'] = '
				<a type="button" id="butt3D" class="btn btn-default button-3D pull-left" title="Доступен 3D просмотр">
					<span class="button-3D-pict"></span>
				</a>
				<form method="post" id="extractform" class="hidden">
					<input type="hidden" name="zip_name" value="'.$this->number_3d.'/'.$this->id.'/stl/'.$stl_file['stl_name'].'" />
					<input type="hidden" name="zip_path" value="'.$this->number_3d.'/'.$this->id.'/stl/'.'" />
				</form>
				<form method="post" id="dellstlform" class="hidden"></form>
			';
			$result['dopBottomScripts'] = '
				<script src="'._rootDIR_HTTP_.'web/js_lib/three.min.js"></script>
				<script src="'._rootDIR_HTTP_.'web/OrbitControls.js"></script>
				<script src="'._rootDIR_HTTP_.'web/TrackballControls.js"></script>
				<script src="'._rootDIR_HTTP_.'web/TransformControls.js"></script>
				<script src="'._rootDIR_HTTP_.'web/STLLoader.js"></script>
				<script src="js/extractzip.js?ver=13"></script>
			';
		}
		return $result;
	}

	public function getAi()
    {
		if ( $this->ai_Query->num_rows > 0 )
		{
			$ai_file = mysqli_fetch_assoc($this->ai_Query);
			return $ai_file;
		}
		return false;
	}
	
	public function getComplects() {
		$complStr = '';
		$mass = [];
		while( $complects = mysqli_fetch_assoc($this->complect_Query) ) {
			if ( $complects['id'] == $this->row['id'] ) continue;
			$mass[] = $complects['model_type'];
			$compl_quer = mysqli_query($this->connection, " SELECT img_name FROM images WHERE pos_id='{$complects['id']}' AND main='1' ");
			$compl_row = mysqli_fetch_assoc($compl_quer);

            $fileImg = $this->number_3d.'/'.$complects['id'].'/images/'.$compl_row['img_name'];
            $img = _stockDIR_HTTP_ .$this->number_3d.'/'.$complects['id'].'/images/'.$compl_row['img_name'];
            if ( !file_exists(_stockDIR_.$fileImg) ) $img = _stockDIR_HTTP_."default.jpg";
                $complStr .= '<a imgtoshow="'. $img .'" href="index.php?id='.$complects['id'].'">'.$complects['model_type'].' </a>';
		}
		if ( count($mass) == 0 ) $complStr = "Нет";
		return $complStr;
	}
	
	public function getImages() {
		$result = array();
		$result['mainSrcImg'] = _stockDIR_HTTP_."default.jpg";
		$result['dopImg'] = array();
		
		while( $row_img = mysqli_fetch_assoc($this->img_Query) ) {
			if ( (int)$row_img['main'] == 1 )
			{
			    $fileImg = $this->number_3d.'/'.$this->id.'/images/'.$row_img['img_name'];
                $result['mainSrcImg'] = _stockDIR_HTTP_.$fileImg;

			    if ( !file_exists(_stockDIR_.$fileImg) ) $result['mainSrcImg'] = _stockDIR_HTTP_."default.jpg";
				continue;
			}

            $dopImg = $this->number_3d.'/'.$this->id.'/images/'.$row_img['img_name'];
            if ( !file_exists(_stockDIR_.$dopImg) )
            {
                $result['dopImg'][] = _stockDIR_HTTP_."default.jpg";
            } else {
                $result['dopImg'][] = _stockDIR_HTTP_.$this->number_3d.'/'.$this->id.'/images/'.$row_img['img_name'];
            }

		}
		return $result;
	}
	public function getModelMaterial() {
		$str_material_arr = explode(";",$this->row['model_material']);
		$metalColor = '';
		foreach ( $str_material_arr as &$value ) {
			
			switch ( $value ) {
				case "585":
					$gSample = $value.'&#176;';
					break;
				case "750":
					$gSample = $value.'&#176;';
					break;
				case "Золото":
					$metal = $value.' ';
					break;
				case "Серебро":
					$metal = $value;
					break;
				case "Белое":
					$gColorWhite = 'style="background-color: #FAF0E6; padding:3px;  border-left: 2px solid #C71585;"';
					$metalColor .= "<span $gColorWhite>&nbsp;$value&nbsp;</span>";
					break;
				case "Красное":
					$gColorRed = 'style="background-color: #f9c0a9; padding:3px; border-left: 2px solid #C71585;"';
					$metalColor .= "<span $gColorRed>&nbsp;$value&nbsp;</span>";
					break;
				case "Желтое(евро)":
					$gColorYell = 'style="background-color: #FFD700; padding:3px;  border-left: 2px solid #C71585;"';;
					$metalColor .= "<span $gColorYell>&nbsp;$value&nbsp;</span>";
					break;
			}
		}
		return  "<span>$metal&nbsp;$gSample</span>&nbsp;$metalColor";
	}
	public function getModelCovering() {
		$str_mod_cov_arr = explode(";",$this->row['model_covering']);
		$matCoveringR = '';
		$coveringType = '';
		foreach ( $str_mod_cov_arr as &$value ) {
			switch ( $value ) {
				case "Родирование":
					$gColorR = 'style="background-color: #efebeb; border-left: 2px solid #C71585;"';;
					$matCoveringR = "<span $gColorR>&nbsp;$value&nbsp;</span>";
					break;
				case "Золочение":
					$gColorYell = 'style="background-color: #FFD700; border-left: 2px solid #C71585;"';;
					$matCoveringG = "<span $gColorYell>&nbsp;$value&nbsp;</span>";
					break;
				case "Чернение":
					$gColorB = 'style="background-color: #2F4F4F; color:#FDF5E6; border-left: 2px solid #C71585;"';;
					$matCoveringB = "<span $gColorB>&nbsp;$value&nbsp;&nbsp;</span>";
					break;
				case "Полное":
					$coveringType = $value;
					break;
				case "Частичное":
					$coveringType = $value;
					break;
				case "По крапанам":
					$coveringType .= ', '.$value.'.';
					break;
			}
		}
		$str_mod_priv_arr = explode("-",$this->row['model_covering']);
		if ( $str_mod_priv_arr[1] ) $coveringPrivParts = '<span style="background-color: #00FFFF; border-bottom: 2px solid #C71585;">Части: <i>'.$str_mod_priv_arr[1].' </i></span>';
		$str_Covering = "<span>$matCoveringR&nbsp;$coveringType</span>&nbsp;$coveringPrivParts<span>$matCoveringG$matCoveringB</span>";
		if ( !$matCoveringR && !$matCoveringB && !$matCoveringB ) $str_Covering = 'Нет';
		return $str_Covering;
	}
	public function getGems() {
		$result = array();
		$c = 0;
		while( $row_gems = mysqli_fetch_assoc($this->gems_Query) ) {
			if ( !empty($row_gems['gems_sizes']) ) {
				$sizeGem = is_numeric($row_gems['gems_sizes']) ? "Ø".$row_gems['gems_sizes']." мм" : $row_gems['gems_sizes']." мм";	
			}
			if ( !empty($row_gems['value']) ) $valueGem = $row_gems['value']." шт";
			$result[$c]['gem_num'] = $c+1;
			$result[$c]['gem_size'] = $sizeGem;
			$result[$c]['gem_value'] = $valueGem;
			$result[$c]['gem_cut'] = $row_gems['gems_cut'];
			$result[$c]['gem_name'] = $row_gems['gems_names'];
			$result[$c]['gem_color'] = $row_gems['gems_color'];
			$c++;
		}
		return $result;
	}
	public function getDopVC() {
		
		function links($connection, $id, $vc_3dnum, $stockDir) {
			$compl_quer = mysqli_query($connection, " SELECT img_name FROM images WHERE pos_id='$id' AND main='1' ");
			$querN3D = mysqli_query($connection, " SELECT number_3d FROM stock WHERE id='$id' ");
			$n3d_row = mysqli_fetch_assoc($querN3D);
			$compl_row = mysqli_fetch_assoc($compl_quer);

            $file = $n3d_row['number_3d'].'/'.$id.'/images/'.$compl_row['img_name'];
            $fileImg = _stockDIR_HTTP_.$n3d_row['number_3d'].'/'.$id.'/images/'.$compl_row['img_name'];
            if ( !file_exists(_stockDIR_.$file) ) $fileImg = _stockDIR_HTTP_."default.jpg";

			return '<a imgtoshow="'.$fileImg.'" href="index.php?id='.$id.'">'.$vc_3dnum.'</a>';
		}
		
		function vc_3dnumExpl($connection, $vc_3dnum, $vc_name, $stockDir) {
			$arr = explode('/',$vc_3dnum);
			$quer = mysqli_query($connection, " SELECT id,number_3d,vendor_code FROM stock WHERE model_type='$vc_name' ");
			
			$link  = null;
			
			if ( $quer->num_rows > 0 ) {
				while( $row_vc = mysqli_fetch_assoc($quer) ) {
					
					if ( !empty($row_vc['vendor_code']) ) {
						if ( trim($arr[0]) == $row_vc['vendor_code'] ) {
							$link = links($connection, $row_vc['id'], $vc_3dnum, $stockDir);
							break;
						}
					}
					if ( trim($arr[0]) == $row_vc['number_3d'] ) {
						$link = links($connection, $row_vc['id'], $vc_3dnum, $stockDir);
						break;
					}
					
					if ( isset($arr[1]) ) {
						if ( !empty($row_vc['vendor_code']) ) {
							if ( trim($arr[1]) == $row_vc['vendor_code'] ) {
								$link = links($connection, $row_vc['id'], $vc_3dnum, $stockDir);
								break;
							}
						}
						if ( trim($arr[1]) == $row_vc['number_3d'] ) {
							$link = links($connection, $row_vc['id'], $vc_3dnum, $stockDir);
							break;
						}
					}
				}
			}
			return $link;
		}
		
		$result = array();
		$c = 0;
		while( $row_dop_vc = mysqli_fetch_assoc($this->dopVc_Query) ) {
			
			$linkVCnum = vc_3dnumExpl($this->connection, $row_dop_vc['vc_3dnum'], $row_dop_vc['vc_names'], $this->stockDir );
			$linkVCnum = $linkVCnum ? $linkVCnum : $row_dop_vc['vc_3dnum'];
			
			$result[$c]['vc_num'] = $c+1;
			$result[$c]['vc_names'] = $row_dop_vc['vc_names'];
			$result[$c]['vc_link'] = $linkVCnum;
			$result[$c]['vc_descript'] = $row_dop_vc['descript'];
			$c++;
		}
		return $result;
	}
	
	public function checklikePos() {
		$ipsQuer = mysqli_query($this->connection, " SELECT * FROM ips WHERE ip='$this->IP_visiter' ");
		if ( $ipsQuer->num_rows > 0 ) {
			$row = mysqli_fetch_assoc($ipsQuer);
			$str = explode(';',$row['liked_pos']);
			if ( in_array($this->id, $str) ) return true;
		}
		return false;
	}
        
        public function getStatuses( $id = false, $status_name = '', $status_date = '' )
        {
            $statuses = $this->getStatLabArr('status');
            $result = [];
            $stats_quer = mysqli_query($this->connection, " SELECT status,name,date FROM statuses WHERE pos_id='{$this->id}' ");
            
            if ( !mysqli_num_rows($stats_quer) )
            {
                $statusT = [];
                $statusT['pos_id'] = $this->id;
                $statusT['status'] = $this->row['status'];
                $statusT['creator_name'] = "";
                $statusT['UPdate'] = $this->row['status_date'];
                $this->addStatusesTable($statusT);
                foreach ( $statuses as $status )
                {
                    if ( $statusT['status'] === $status['name_ru'] )
                    {
                        $result[0]['class'] = $status['class'];
                        $result[0]['classMain'] = $status['name_en'];
                        $result[0]['glyphi'] = $status['glyphi'];
                        $result[0]['title'] = $status['title'];
                        $result[0]['status'] = $status['name_ru'];
                        $result[0]['name'] = $statusT['name'];
                        $result[0]['date'] = ($statusT['date'] == "0000-00-00") ? "" : date_create( $statusT['UPdate'] )->Format('d.m.Y')."&#160;";
                        break;
                    }
                }

                //debug($result,'$result',1);
                return $result;
            }
            
            $c = 0;
            while( $statuses_row = mysqli_fetch_assoc($stats_quer) ) 
            {
                foreach ( $statuses as $status )
                {
                    if ( $statuses_row['status'] === $status['id'] )
                    {
                        $result[$c]['class'] = $status['class'];
                        $result[$c]['classMain'] = $status['name_en'];
                        $result[$c]['glyphi'] = $status['glyphi'];
                        $result[$c]['title'] = $status['title'];
                        $result[$c]['status'] = $status['name_ru'];
                        $result[$c]['name'] = $statuses_row['name'];
                        $result[$c]['date'] = ($statuses_row['date'] == "0000-00-00") ? "" : date_create( $statuses_row['date'] )->Format('d.m.Y')."&#160;";
                        $c++;
                        break;
                    }
                }

            }
            return $result;
        }
}