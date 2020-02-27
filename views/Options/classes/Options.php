<?php

include(_viewsDIR_.'Main/classes/Main.php');

class Options extends Main {
	
	function __construct( $server=false ) {
		parent::__construct($server);
	}

	
	public function scanBGfolder() {
		$result = array();
		/*
		$bgDir = $_SERVER['DOCUMENT_ROOT'].'/HUF_DB/picts/bg';
		
		$dir = opendir($bgDir);
		while(false !== ( $file = readdir($dir)) ) {
			
			if (( $file != '.' ) && ( $file != '..' )) {
				$result[] = '/HUF_DB/picts/bg/' . $file;
			}
			
		}
		closedir($dir);
		*/
		
		for( $i = 0; $i < 12; $i++ ) {
			$result[$i]['body'] = "bodyimg".($i);
			$result[$i]['prev'] = "bodyimgPrev".($i);
			$result[$i]['checked'] = '';
		}
		return $result;
	}

}

?>