<?php 

	include('Glob_Controllers/db.php');
	
	function countComplects($row,$connection) {

		$savedrow = array();
		$complects = array(); // ассоц массив типа 0004423 => 2(кол-во этих номеров) и т.д.
		$cIt = 0;
		for ( $i = 0; $i < count($row); $i++ ) {
			if (empty($row[$i]['number_3d'])) continue;
			$number_3d = $row[$i]['number_3d'];
			
			foreach ( $savedrow as &$value ) { // проверяем есть ли этот номер в массиве. если есть то пропускаем все такие номера, они уже посчитаны
				if ( $value == $number_3d ) continue(2);
			}

			for ( $j = 0; $j < count($row); $j++ ) {
				
				$model_type = $row[$j]['model_type'];
				
				// если совпадают - значит это комплект
				if ( $number_3d == $row[$j]['number_3d'] ) {
					
					$model_id = $row[$j]['id'];
					$complects[$cIt]['number_3d'] = $row[$j]['number_3d'];
					$complects[$cIt]['model_type'][$model_id]['images'] = get_Images_FromPos($model_id,$connection);
					$complects[$cIt]['model_type'][$model_id]['stl'] = get_stl_FromPos($model_id,$connection);
					$savedrow[] = $number_3d; // сохранем номер в массив, как посчитанный
					
				}
			}
			$cIt++;
		}
		return $complects;
	};
	
	function get_Images_FromPos($id,$connection){
		$images_src = array();
		$img_quer = mysqli_query($connection, " SELECT img_name FROM images WHERE pos_id='$id' ");
		while ( $row_images = mysqli_fetch_assoc($img_quer) ){
			
			$images_src[] = $row_images['img_name'];
		}		
		return $images_src;
	};
	function get_stl_FromPos($id,$connection){
		
		$stl_src = array();
		$stl_quer = mysqli_query($connection, " SELECT stl_name FROM stl_files WHERE pos_id='$id' ");
		
		$row_stl = mysqli_fetch_assoc($stl_quer);
		$stl_src[] = $row_stl['stl_name'];
		
		return $stl_src;
	};
    
	$row_quer = mysqli_query($connection, "SELECT number_3d,model_type,id FROM stock");
	
	while( $row[] = mysqli_fetch_assoc($row_quer) ){}
	array_pop($row);
	
	$complArray = countComplects($row,$connection);
	
	
	echo 'this dir = '.getcwd();
	echo '<br/>';
	echo '<div id="progressBar">0</div>';
	chdir('../Stock');
	$len = count($complArray);
	$overalProgress = 0;
	$counter = 0;
	for ( $i=0; $i<$len; $i++ ) {
		
		$dirName = $complArray[$i]['number_3d'];
		mkdir($dirName, 0777, true);
		
		foreach ( $complArray[$i]['model_type'] as $id_key => $id_value ) {
			mkdir($dirName.'/'.$id_key, 0777, true);
			foreach ( $id_value as $key => $val ) {
				
				if ( !empty($val) ) {
					$dir = $dirName.'/'.$id_key.'/'.$key;
					//echo 'dir = '.$dir;
					//echo '<br/>';
					mkdir($dir, 0777, true);
					
					if ( $key == 'images' ) $path = '../Stockimages/';
					if ( $key == 'stl' ) $path = '../STLModels/';
					//echo 'path = '.$path;
					//echo '<br/>';
					//echo 'key = '.$key;
					//echo '<br/>';
					foreach ( $val as $k => $v ) {
						if ( !empty($v) ) {
							copy ( $path.'/'.$v , $dir.'/'.$v );
						}
						
						//mkdir($dirName.'/'.$id_key.'/'.$key.'/'.$v, 0777, true);
						
						//echo 'val = '.$v;
						//echo '<br/>';
					}
				}
			}
		
		/*
		echo '<br/>';
		echo 'ertertert = '.$complName;
		echo '<br/>';
		*/
		}
		$counter++;
		$overalProgress =  ceil( ( $counter * 100 ) / $len );
		echo"
			<script>
				progressBar.innerHTML = $overalProgress + \"%\";
			</script>
		";
	}

	//echo 'this dir = '.getcwd();
	//echo '<br/>';
	echo 'Всего комплектов = '.count($complArray);
	echo '<br/>';
	echo 'Всего изделий = '.count($row);

	echo '<pre>';
	print_r($complArray);
	echo '</pre>';
	

	
?>