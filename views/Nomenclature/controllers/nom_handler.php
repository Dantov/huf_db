<?php
	date_default_timezone_set('Europe/Kiev');
if( !isset($_POST['val']) && !isset($_POST['coll']) || empty($_POST['val']) ) exit();
	
	$quer_coll = $_POST['coll'];
	$quer_id = $_POST['id'];
	$dell = (int)$_POST['dell'];
	$quer_val = trim($_POST['val']);

	
	$date = date('Y-m-d');
	
    include('../../Glob_Controllers/db.php');
	
	if( isset($dell) && !empty($dell) ) {  // Удаление
		$modelsQuery = mysqli_query($connection, " SELECT collections FROM stock WHERE collections='$quer_val' ");
		$count = $modelsQuery -> num_rows;
		
		mysqli_query($connection, " UPDATE stock SET collections='-' WHERE collections='$quer_val' ");

		mysqli_query($connection, " DELETE FROM $quer_coll WHERE id='$quer_id' ");
		
		$arr['count'] = $count;
		$arr['dell'] = 1;
		echo json_encode($arr);
		exit();
	}
	
	if (!empty($quer_id)) {  // Изменение
		
		if ( $quer_coll == 'collections' )
		{
			$queryName = mysqli_query($connection, " SELECT name FROM $quer_coll WHERE id='$quer_id' ");
			$oldName = mysqli_fetch_assoc($queryName);

			$oldName = $oldName['name'];
			$newCollectionName = $quer_val;

			if ( $newCollectionName === $oldName ) exit();

			$stockQuery = mysqli_query($connection, " SELECT id,collections FROM stock WHERE collections LIKE '%$oldName%' ");
            $newCollectionsStrArr = [];
			while( $stockRow = mysqli_fetch_assoc($stockQuery) )
            {
                $collArr = explode(';',$stockRow['collections']);
                foreach ( $collArr as &$coll )
                {
                    if ( $oldName == $coll )
                    {
                        $coll = $newCollectionName;
                        $newCollectionsStrArr[$stockRow['id']] = implode(';',$collArr);
                        continue;
                    }
                }
            }
            //debug($newCollectionsStrArr);

            $collIdStr = 'VALUES ';
            foreach ( $newCollectionsStrArr as $idModel=>$newCollStr ) $collIdStr .= "('".$idModel."','".$newCollStr."'),";
            $collIdStr = trim($collIdStr,',');

            //debug($collIdStr,'$collIdStr');

            $queryString = " INSERT INTO stock (id,collections) $collIdStr
            ON DUPLICATE KEY UPDATE collections=VALUES(collections)";

            $queryUPDATEColl = mysqli_query($connection, $queryString);
            if (!$queryUPDATEColl)
            {
                printf( "Error: %s\n", mysqli_error($connection) );
            }
            //debug($queryString,'$queryString');
            //INSERT INTO table (id,Col1,Col2) VALUES (1,1,1),(2,2,3),(3,9,3),(4,10,12)
            //ON DUPLICATE KEY UPDATE Col1=VALUES(Col1),Col2=VALUES(Col2);
		}
		
		mysqli_query($connection, " UPDATE $quer_coll SET name='$quer_val' WHERE id='$quer_id' ");
		
	} else { // Добавление
		$querFind = mysqli_query($connection, " SELECT * FROM $quer_coll WHERE name='$quer_val' ");
		
		if ( $querFind -> num_rows !== 0 ) { // совпадение найдено т.е коллекция существует
			$arr['status'] = -1;
			$arr['coll'] = $quer_coll;
			echo json_encode($arr);
			exit();
		}
		$quer = mysqli_query($connection, " INSERT INTO $quer_coll (name,date) VALUES ('$quer_val', '$date') ");
		if ( $quer ) {
			$querid = mysqli_query($connection, " SELECT id FROM $quer_coll WHERE name='$quer_val' ");
			$newId = mysqli_fetch_assoc($querid);
			$arr['add'] = 1;
			$arr['id'] = $newId['id'];
			$arr['date'] = date_create( $date )->Format('d.m.Y');
		}
		
		if (!$quer ) {
			printf("Error: %s\n", mysqli_error($connection));
		}
	}
	
	mysqli_close($connection);

	if ( $quer ) {
		$arr['status'] = 1;
		echo json_encode($arr);
	} else {
		$arr['status'] = 0;
		echo json_encode($arr);
	}
?>