<?php

include(_viewsDIR_.'Main/classes/Main.php');

class Statistic extends Main 
{
	function __construct( $server=false ) {
		parent::__construct($server);
	}
	
	public $allModels;
	public $allComplects;
	
	public function getUsers() {
		$result = array();
		$query = mysqli_query($this->connection, " SELECT * FROM sessions " );

		while( $row = mysqli_fetch_assoc($query) ) $result[] = $row;
		
		return $result;
	}

	public function getModels() {
		$result = array();
		$query_coll = mysqli_query($this->connection, " SELECT id,name FROM collections ORDER BY name");
		$i = 0;
		while( $collRow = mysqli_fetch_assoc($query_coll) )
        {
			$coll = $collRow['name'];
			
			$result[$i]['name'] = $collRow['name'];
			$result[$i]['id'] = $collRow['id'];
			
			$query_stock = mysqli_query($this->connection, " SELECT * FROM stock WHERE collections='$coll' ");
			
			while( $this->row[] = mysqli_fetch_assoc($query_stock) ){}
			array_pop($this->row);
			
			$result[$i]['wholePos'] = count($this->row);
			$compl = $this->countComplects();
			$result[$i]['wholeCompl'] = count($compl);
			
			$i++;
			$this->row = array();
		}
		return $result;
	}

	public function getLikedModels() {
		$result = array();
		$query_stock = mysqli_query($this->connection, " SELECT id,number_3d,vendor_code,model_type,likes,dislikes FROM stock ");

		while( $row = mysqli_fetch_assoc($query_stock) ){
			$id = $row['id'].';'.$row['number_3d'].' / '.$row['vendor_code'].' - '.$row['model_type'];
			$result['likes'][$id] = $row['likes'];
			$result['dislikes'][$id] = $row['dislikes'];
		}
		
		arsort($result['likes']);
		arsort($result['dislikes']);
		
		return $result;
	}
	public function getModelsBy3Dmodellers() {
		$result = array();
		
		$query_coll = mysqli_query($this->connection, " SELECT id,name FROM modeller3d ORDER BY name");
		$i = 0;
		while( $collRow = mysqli_fetch_assoc($query_coll) ){
			$coll = $collRow['name'];
			
			$result[$i]['name'] = $collRow['name'];
			
			$query_stock = mysqli_query($this->connection, " SELECT * FROM stock WHERE modeller3D='$coll' ");
			
			while( $this->row[] = mysqli_fetch_assoc($query_stock) ){}
			array_pop($this->row);
			
			$result[$i]['wholePos'] = count($this->row);
			$compl = $this->countComplects();
			$result[$i]['wholeCompl'] = count($compl);
			
			$i++;
			$this->row = array();
		}
		return $result;
	}
	public function getModelsByAuthors() {
		$result = array();
		
		$query_coll = mysqli_query($this->connection, " SELECT id,name FROM author ORDER BY name");
		$i = 0;
		while( $collRow = mysqli_fetch_assoc($query_coll) ){
			$coll = $collRow['name'];
			
			$result[$i]['name'] = $collRow['name'];
			
			$query_stock = mysqli_query($this->connection, " SELECT * FROM stock WHERE author='$coll' ");
			
			while( $this->row[] = mysqli_fetch_assoc($query_stock) ){}
			array_pop($this->row);
			
			$result[$i]['wholePos'] = count($this->row);
			$compl = $this->countComplects();
			$result[$i]['wholeCompl'] = count($compl);
			
			$i++;
			$this->row = array();
		}
		return $result;
	}
	
	public function scanBaseFileSizes() {
		$result = array();
		$result['imgFileSizes'] = 0;
		$result['imgFileCounts'] = 0;
		$result['stlFileSizes'] = 0;
		$result['stlFileCounts'] = 0;
		$result['overalCounts'] = 0;
		$result['overalSizes'] = 0;
		
		$query_img = mysqli_query($this->connection, " SELECT img_name,pos_id FROM images ");
		$query_stl = mysqli_query($this->connection, " SELECT stl_name,pos_id FROM stl_files ");
		
		while( $imgRow = mysqli_fetch_assoc($query_img) ) {
			
			$tempArr = explode('-',$imgRow['img_name']);
			$filename = $imgRow['img_name'];
			$n3d = $tempArr[0];
			$id = $imgRow['pos_id'];

			if ( file_exists($_SERVER['DOCUMENT_ROOT'].$this->stockDir.'/'.$n3d.'/'.$id.'/images/'.$filename) ) {
				$result['imgFileSizes'] += filesize($_SERVER['DOCUMENT_ROOT'].$this->stockDir.'/'.$n3d.'/'.$id.'/images/'.$filename);
				$result['imgFileCounts']++;
			}
		}
		$result['overalCounts'] += $result['imgFileCounts'];
		$result['overalSizes'] += $result['imgFileSizes'];
		$result['imgFileSizes'] = $this->human_filesize($result['imgFileSizes'], $decimals = 2);
		
		while( $stlRow = mysqli_fetch_assoc($query_stl) ) {
			
			$tempArr = explode('-',$stlRow['stl_name']);
			$filename = $stlRow['stl_name'];
			$n3d = $tempArr[0];
			$id = $stlRow['pos_id'];

			if ( file_exists($_SERVER['DOCUMENT_ROOT'].$this->stockDir.'/'.$n3d.'/'.$id.'/stl/'.$filename) ) {
				$result['stlFileSizes'] += filesize($_SERVER['DOCUMENT_ROOT'].$this->stockDir.'/'.$n3d.'/'.$id.'/stl/'.$filename);
				$result['stlFileCounts']++;
			}
		}
		$result['overalCounts'] += $result['stlFileCounts'];
		$result['overalSizes'] += $result['stlFileSizes'];
		$result['stlFileSizes'] = $this->human_filesize($result['stlFileSizes']);
		
		$result['overalSizes'] =  $this->human_filesize($result['overalSizes']);
		
		return $result;
	}
	
	public function human_filesize($bytes, $decimals = 2) {
	  $sz = 'BKMGTP';
	  $factor = floor((strlen($bytes) - 1) / 3);
	  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}
}