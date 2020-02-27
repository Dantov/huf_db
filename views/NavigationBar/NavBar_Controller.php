<?php
    $connection = $this->varBlock['connection'];

	$userid = $_SESSION['user']['id'];
	$resultUser = mysqli_query($connection, " SELECT * FROM users WHERE id='$userid' ");
	
	$userRow = mysqli_fetch_assoc($resultUser);
	
	if ( !isset($_SESSION['user']['fio']) ) $_SESSION['user']['fio']=$userRow['fio'];
	
	$glphsd = '<span class="glyphicon glyphicon-user"></span>';
	if ( $userRow['fio'] == 'Монтировка' ) $glphsd = '<i class="fas fa-cogs"></i>';
	if ( $userRow['fio'] == 'Участок ПДО' ) $glphsd = '<span class="glyphicon glyphicon-paperclip"></span>';
	
	
	if ( $_SESSION['assist']['searchIn'] == 1 ) $searchInStr = "В Базе";
	if ( $_SESSION['assist']['searchIn'] == 2 ) $searchInStr = "В Коллекции";

	// флаг для удаления ворд сесии, если нажали добавить модель, с этой сессией
	$dell = ''; 
	if ( isset($_SESSION['fromWord_data']) ) $dell = "&dellWD=1";
	$searchStyle='style="margin-left:100px;"';
	$navbarStats = '';
	$topAddModel = 'hidden';
	
	if ( $_SESSION['user']['access'] == 1 || $_SESSION['user']['access'] == 2 ) {
		$searchStyle='';
		$topAddModel = '';
		
		$navbarStats = '<li><a href="../Statistic/index.php"><span class="glyphicon glyphicon-stats"></span>&#160; Статистика</a></li>';
	}
        $navbarDev = '';
        if ( (int)$_SESSION['user']['id'] === 1 || (int)$_SESSION['user']['id'] === 4 ) {
            $navbarDev = '<li><a target="_blank" href="'. _rootDIR_HTTP_ .'hufdb-new"><span class="glyphicon glyphicon-wrench"></span>&#160; Dev</a></li>';
	}

	/*
         * пришлось создать еще один экземпляр Маин вверху для вывода коллекций в навигации
         */
        $self = explode('/',$_SERVER['PHP_SELF']);

        if ( $self[3] != 'Main' )
        {
            function getCollections($connection) {
		
		$collectionListDiamond = '';
		$collectionListGold = '';
		$collectionListSilver = '';
		$other = '';
		$coll_res = mysqli_query($connection, " SELECT * FROM collections ORDER BY name");
		while( $coll_row = mysqli_fetch_assoc($coll_res) ) {
			
			$ok = false;
			$haystack = mb_strtolower($coll_row['name']);
			
			
			if ( stristr( $haystack, 'сереб' ) || stristr( $haystack, 'silver' ) ) {
				
				$collectionListSilver .= '<a href="../Main/controllers/setSort.php?coll_show='.$coll_row['id'].'">';
				$collectionListSilver .= '<div coll_block class=" collItem">'.$coll_row['name'].'</div>';
				$collectionListSilver .= '</a>';
				$collectionListSilver_cn++;
				continue;
			}
			if ( stristr( $haystack, 'золото' ) || stristr( $haystack, 'невесомость циркон' ) || stristr( $haystack, 'невесомость с ситалами' ) || stristr( $haystack, 'gold' ) ) {
				
				$collectionListGold .= '<a href="../Main/controllers/setSort.php?coll_show='.$coll_row['id'].'">';
				$collectionListGold .= '<div coll_block class=" collItem">'.$coll_row['name'].'</div>';
				$collectionListGold .= '</a>';
				$collectionListGold_cn++;
				continue;
				
			}
			if ( stristr( $haystack, 'брилл' ) || stristr( $haystack, 'diam' ) ) {
				
				$collectionListDiamond .= '<a href="../Main/controllers/setSort.php?coll_show='.$coll_row['id'].'">';
				$collectionListDiamond .= '<div coll_block class=" collItem">'.$coll_row['name'].'</div>';
				$collectionLiDstDiamond .= '</a>';
				$collectionListDiamond_cn++;
				continue;
				
			}
				
				$other .= '<a href="../Main/controllers/setSort.php?coll_show='.$coll_row['id'].'">';
				$other .= '<div coll_block class=" collItem">'.$coll_row['name'].'</div>';
				$other .= '</a>';
				$other_cn++;
		}
		
		$res['collectionListSilver'] = $collectionListSilver;
		$res['collectionListSilver_cn'] = $collectionListSilver_cn;
		
		$res['collectionListGold'] = $collectionListGold;
		$res['collectionListGold_cn'] = $collectionListGold_cn;
		
		$res['collectionListDiamond'] = $collectionListDiamond;
		$res['collectionListDiamond_cn'] = $collectionListDiamond_cn;
		$res['other'] = $other;
		$res['other_cn'] = $other_cn;
		
		return $res;
	}
        
            $collectionList = getCollections($connection);
        }
        
        mysqli_close($connection);

// Перекинем массив для добавления в JS
$wsUserData = [];
$wsUserData['id'] = $_SESSION['user']['id'];
$wsUserData['fio'] = $_SESSION['user']['fio'];
$wsUserData = json_encode($wsUserData,JSON_UNESCAPED_UNICODE);
$wsUserDataJS = <<<JS
    let wsUserData = $wsUserData;
JS;
?>