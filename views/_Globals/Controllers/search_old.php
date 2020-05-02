<?php
session_start();
$searchFor = isset($_POST['searchFor']) ? mb_strtolower( strip_tags( trim($_POST['searchFor']) ) ) : trim($_GET['searchFor']);
	// если нажали на кнопку, или установлен флаг репоиска => заходим в поиск
if ( isset($_POST['search']) || $_SESSION['re_search'] === true ) {

    // если в строке пусто то удаляем все переменные поиска и вернемся на коллекцию
	if ( !isset($searchFor) || empty($searchFor) )
	{
		unset($_SESSION['searchFor'],$_SESSION['foundRow'],$_SESSION['countAmount']);
		$_SESSION['re_search'] = false;
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		exit();
	}

	$_SESSION['searchFor'] = $searchFor;
	unset($_SESSION['countAmount'], $_SESSION['foundRow']);

    require_once _globDIR_ . "classes/Search.php";
    require_once _globDIR_ . "classes/General.php";
	$general = new General();
    $connection = $general->connectToDB();
    $statuses = $general->statuses;

	$where = "";

    $regStat = 0;
    $regStat_str = $_SESSION['assist']['regStat'];
    foreach ($statuses as $status)
    {
        if ( $status['name_ru'] === $regStat_str )
        {
            $regStat = (int)$status['id'];
            break;
        }
    }
    //debug($regStat_str,'$regStat_str');
    //debug($regStat,'$regStat');

	if ( $_SESSION['assist']['searchIn'] === 2 && isset($_SESSION['assist']['collectionName']) && !empty($_SESSION['assist']['collectionName']) ) {

	    $collectionName = $_SESSION['assist']['collectionName'];
		$where = "WHERE collections like '%$collectionName%' ";

		if ( $_SESSION['assist']['byStatHistory'] != 1 && $regStat_str != "Нет" )
		{
			$where .= "AND status='$regStat' ";
		}

	} else if ( $_SESSION['assist']['byStatHistory'] != 1 && $regStat_str != "Нет" ) {

		$where = "WHERE status='$regStat' ";
	}

	$selectRow = "SELECT * FROM stock ".$where."ORDER BY ".$_SESSION['assist']['reg']." ".$_SESSION['assist']['sortDirect'];
	$result_sort = mysqli_query($connection, $selectRow);
	if ( !$result_sort ) header("location: ../Main/index.php");

    $row = [];
    while( $resRow = mysqli_fetch_assoc($result_sort) ) { $row[] = $resRow; }

    if ( $_SESSION['assist']['byStatHistory'] == 1 )
    {
        $dates = [];
        if ( isset($_SESSION['assist']['byStatHistoryFrom'])) $dates['from'] = $_SESSION['assist']['byStatHistoryFrom'];
        if ( isset($_SESSION['assist']['byStatHistoryTo'])) $dates['to'] = $_SESSION['assist']['byStatHistoryTo'];
        Search::byStatusesHistory($connection, $regStat, $row, $dates);
    }

    $general->closeDB();

    // если дата
    $date = false;
    if ( stristr( $searchFor, '::' ) !== false )
    {
        $searchFor = str_ireplace("::", "", $searchFor);
        $date = true;
        $exx = explode('.',$searchFor);
        $count = count($exx);
        switch ($count)
        {
            case 1:
                $year = $exx[0];
                break;
            case 2:
                $month = $exx[0];
                $year = $exx[1];
                break;
            case 3:
                $day = $exx[0];
                $month = $exx[1];
                $year = $exx[2];
                break;
        }
        if ( $day ) {
            $toFindDate = $year.'-'.$month.'-'.$day;
        } else if ( $month && $year ) {
            $toFindDate = $year.'-'.$month;
        } elseif ( $year ) {
            $toFindDate = $year;
        }
        /*
        debug($searchFor,'$searchFor');
        debug($day,'$day');
        debug($month,'$month');
        debug($year,'$year');
        debug($toFindDate,'$toFindDate',1);
        */
    }

    $wholePos = count($row);
    // цикл поиска позиций
    for ( $i = 0; $i < $wholePos; $i++ )
    {
        // если подстрока найдена, то она записывается в переменную
        $serch_number_3d   = stristr( mb_strtolower($row[$i]['number_3d']), $searchFor ); //mb_strtolower-  приводим к нижнему регистру, ищем подстроку - stristr
        $serch_vendor_code = stristr( mb_strtolower($row[$i]['vendor_code']), $searchFor );
        $serch_collection  = stristr( mb_strtolower($row[$i]['collections']), $searchFor );
        $serch_author      = stristr( mb_strtolower($row[$i]['author']), $searchFor );
        $serch_jeweller    = stristr( mb_strtolower($row[$i]['jewelerName']), $searchFor );
        $serch_modeller3d  = stristr( mb_strtolower($row[$i]['modeller3D']), $searchFor );
        $serch_model_type  = stristr( mb_strtolower($row[$i]['model_type']), $searchFor );
        $serch_status      = stristr( mb_strtolower($row[$i]['status']), $searchFor );
        $serch_labels      = stristr( mb_strtolower($row[$i]['labels']), $searchFor );
        $serch_description = stristr( mb_strtolower($row[$i]['description']), $searchFor );
        $search_date = $date ? stristr( $row[$i]['date'], $toFindDate ) : false;

        //if ( $search_date !== false  ) debug($search_date,'$search_date',1);
		/* если переменная не пустая (совпадение найдено), т.е. не содержит 0 "" или false, то позиция будет отрисована */
      if ( $serch_number_3d   !== false ||
           $serch_vendor_code !== false ||
           $serch_collection  !== false ||
           $serch_author      !== false ||
           $serch_jeweller    !== false ||
           $serch_modeller3d  !== false ||
           $serch_model_type  !== false ||
           $serch_status      !== false ||
           $serch_labels      !== false ||
           $serch_description !== false ||
           $search_date       !== false ) {

         $_SESSION['foundRow'][] = $row[$i]; // главный массив с найденными элементами
         $_SESSION['countAmount']++;
        } else { // иначе пропускаем итерацию
            continue;
        }
	}
	if ( !isset($_SESSION['foundRow']) ) $_SESSION['nothing'] = "Ничего не найдено";

    //debug($_SESSION['foundRow'],'foundRow',1);

	$_SESSION['re_search'] = false;
	$_SESSION['assist']['page'] = 0;
	$_SESSION['assist']['startfromPage'] = 0;
	
	header("location: ../Main/index.php");
	//header("Location: ".$_SERVER["HTTP_REFERER"]);
	exit;
} else {
	header("Location: ".$_SERVER["HTTP_REFERER"]);
}// конец поска
