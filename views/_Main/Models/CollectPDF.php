<?php
include('Main.php');

class CollectPDF extends Main {
	
	function __construct( &$server, $assist, $user, $foundRow, $searchFor='', $collectionName='' ) 
	{
		parent::__construct($server, $assist, $user, $foundRow);
		$this->toPdf = true;
		if ( !empty($searchFor) ) $this->searchFor = $searchFor;
		if ( !empty($collectionName) ) $this->collectionName = $collectionName;
		if ( !empty($foundRow) ) $this->foundRow = $foundRow;
	}
	
	protected $toPdf;
	public $searchFor = '';
	public $collectionName = '';
	public $foundRow = '';
	
	protected function get_Images_FromPos($id){
		$images_src = array();
		$img_quer = mysqli_query($this->connection, " SELECT img_name, main, detail FROM images WHERE pos_id='$id' ");
		
		while ( $row_images = mysqli_fetch_assoc($img_quer) ){
			if ( $row_images['main'] == '1' || $row_images['detail'] == '1' ) {
				$images_src[] = $row_images['img_name'];
			}
		}
		
		return $images_src;
	}
	
	protected function get_DopVC_FromPos($id){
		
		$dopVC_src = array();
		$dopVC_quer = mysqli_query($this->connection, " SELECT vc_names, vc_3dnum FROM vc_links WHERE pos_id='$id' ");
		
		while ( $dopVC_rows = mysqli_fetch_assoc($dopVC_quer) ){
			
			if ( $dopVC_rows['vc_names'] == 'Швенза' || $dopVC_rows['vc_names'] == 'Закрутка' ) {
				$dopVC_src[$dopVC_rows['vc_names']] = $dopVC_rows['vc_3dnum'];
			}
			
		}
		
		return $dopVC_src;
	}
	
	public function nextPage_HalfPage( &$Xcell, &$Ycell, &$X_line_bott, &$Y_line_bott, &$X_Img, &$Y_Img, &$Info_Cells_X, $nextPage=false, $nextHalfPage=false ) {
		if ( $nextHalfPage === true ) {
			
			$Xcell = 151;
			$Info_Cells_X = array($Xcell,$Xcell+10,$Xcell+10+87); // координаты MultiCell по X
			$Ycell = 10; // координаты MultiCell по Y
			$X_Img = 152;
			$Y_Img = 17;
			$X_line_bott = 151;
			$Y_line_bott = 10;
			
		}
		
		if ( $nextPage === true ) {
			
			$Xcell = 11;
			$Info_Cells_X = array($Xcell,$Xcell+10,$Xcell+10+87);
			$Ycell = 10;
			$X_Img = 11;
			$Y_Img = 17;
			$X_line_bott = 10;
			$Y_line_bott = 10;

		}

	}
}