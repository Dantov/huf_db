<?php

include(_viewsDIR_.'Main/classes/Main.php');

class Options extends Main {
	
	function __construct( $server=false ) {
		parent::__construct($server);
	}
	
	public function scanBGFolder()
    {
		$result = array();
		
		for( $i = 0; $i < 12; $i++ )
		{
			$result[$i]['body'] = "bodyimg".($i);
			$result[$i]['prev'] = "bodyimgPrev".($i);
			$result[$i]['checked'] = '';
		}
		return $result;
	}

}