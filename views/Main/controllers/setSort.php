<?php
	session_start();

    function killSearch()
    {
        if ( isset($_SESSION['foundRow']) )    unset($_SESSION['foundRow']);
        if ( isset($_SESSION['countAmount']) ) unset($_SESSION['countAmount']);
        if ( isset($_SESSION['searchFor']) )   unset($_SESSION['searchFor']);
    }

    if ( isset($_GET['countedIds']) )
    {
        $countedIds = trim( htmlentities($_GET['countedIds'], ENT_QUOTES) );
        //$countedIds = explode(',',$countedIds);

        include_once _globDIR_.'classes/General.php';
        $general = new General();
        $connection = $general->connectToDB();

        $in = '('.$countedIds.')';

        $selectRow = "SELECT * FROM stock WHERE id IN ".$in." ORDER BY ".$_SESSION['assist']['reg']." ".$_SESSION['assist']['sortDirect'];
        $query = mysqli_query($connection, $selectRow);
        mysqli_close($connection);

        if ( !$query )
        {
            header("location: ../index.php");
            exit;
        }

        killSearch();

        while( $foundModel = mysqli_fetch_assoc($query) ) $_SESSION['foundRow'][] = $foundModel;

        //debug($_SESSION['foundRow'],'foundRow=',1);
        $_SESSION['countAmount'] = count($_SESSION['foundRow']);
        $_SESSION['assist']['page'] = 0;
        $_SESSION['assist']['startfromPage'] = 0;
        $_SESSION['assist']['drawBy_'] = 3;
        $_SESSION['re_search'] = false; // глючит без этого

        //$_SESSION['assist']['collectionName'] = 'Выбранные модели';
        //$_SESSION['assist']['collection_id'] = -1;

        header("location: "._views_HTTP_ ."Main/index.php");
        exit();
    }


	//
	if ( isset($_POST['searchInNum']) ) 
    {
        $searchInNum = (int)$_POST['searchInNum'];
        $resp = "";
        if ( $searchInNum === 1 ) {
                $_SESSION['assist']['searchIn'] = 1;
                $resp = "В Базе ";
        }
        if ( $searchInNum === 2 ) {
                $_SESSION['assist']['searchIn'] = 2;
                $resp = "В Коллекции ";
        }
        echo $resp;
        exit();
	}
	
	if ( isset($_GET['sortDirect']) && (int)$_GET['sortDirect'] === 1 )
    {
        $_SESSION['assist']['sortDirect'] = "ASC";
        $_SESSION['re_search'] = true;
    }
	if ( isset($_GET['sortDirect']) && (int)$_GET['sortDirect'] === 2 )
    {
        $_SESSION['assist']['sortDirect'] = "DESC";
        $_SESSION['re_search'] = true;
    }

    //========= Выбираем варианты отображения ==========//
    if ( isset($_GET['row_pos']) ) {
	    $row_pos = (int)$_GET['row_pos'];
        if ( $row_pos > 0 && $row_pos < 6  )
        {
            $_SESSION['assist']['drawBy_'] = $row_pos;
        }
        $_SESSION['assist']['page'] = 0;
        $_SESSION['assist']['startfromPage'] = 0;
        $_SESSION['re_search'] = true;
    }

	//========= Выбираем коллекцию ==========//
	if ( isset($_GET['coll_show']) || isset($_GET['sCollId']) )
	{
		
		$coll_id = isset($_GET['coll_show']) ? (int)trim($_GET['coll_show']) : (int)trim($_GET['sCollId']);
		
		require(_globDIR_.'db.php');
		
		function queryColl( &$connection, $id )
        {
			$quer = mysqli_query($connection, " SELECT name FROM collections WHERE id='$id' ");
			$coll_row = mysqli_fetch_assoc($quer);
			$_SESSION['assist']['collectionName'] = $coll_row['name'];
			$_SESSION['assist']['collection_id'] = $id;
			$_SESSION['assist']['page'] = 0;
			$_SESSION['assist']['startFromPage'] = 0;
		}

		
		if ( $coll_id !== -1 ) {
			queryColl( $connection, $coll_id );
		} else {
			$_SESSION['assist']['collectionName'] = "Все Коллекции";
			$_SESSION['assist']['collection_id'] = -1; // -1 означает все коллекции
			$_SESSION['assist']['page'] = 0;
			$_SESSION['assist']['startFromPage'] = 0;
		}
		// убрали информацию о поиске
		killSearch();
	}
	//========= END Выбираем коллекцию ==========//
	
	
	//========= Pagination ==========//
	// start_FromPage - это флаг с какого квадратика начинать отрисовывать 
	// st_prevPage - это флаг что нужно отрисовать кнопку назад на пред. часть страниц
	
	if ( isset($_GET['page']) )           $_SESSION['assist']['page']          = (int)trim($_GET['page']); // кликнули по квадратику пагинации
	if ( isset($_GET['start_FromPage']) ) $_SESSION['assist']['startFromPage'] = (int)trim($_GET['start_FromPage']);
	if ( isset($_GET['st_prevPage']) )    $_SESSION['assist']['st_prevPage']   = (int)trim($_GET['st_prevPage']);
	//=========END Pagination ==========//
	
	// сортировка по
	if ( isset($_GET['reg']) && !empty($_GET['reg']) )
	{
		$x = trim( htmlentities($_GET['reg'], ENT_QUOTES) );
		switch ($x) {
			case "number_3d":
			$_SESSION['assist']['reg'] = "number_3d";
			break;
			case "date":
			$_SESSION['assist']['reg'] = "date";
			break;
			case "vendor_code":
			$_SESSION['assist']['reg'] = "vendor_code";
			break;
			case "status":
			$_SESSION['assist']['reg'] = "status";
			break;
			default:
			$_SESSION['assist']['reg'] = "number_3d";
			break;
		}
        $_SESSION['re_search'] = true;
	}

	// выбор по рабочим центрам
    if ( isset($_GET['wcSort']) && !empty($_GET['wcSort']) )
    {
        $wcIDs = trim( htmlentities($_GET['wcSort'], ENT_QUOTES) );
        $wcIDs = explode('-',$wcIDs);

        include_once _globDIR_.'classes/General.php';
        $general = new General();
        $general->connectToDB();
        $workingCenters = $general->getWorkingCentersDB();

        // просто проверка, что б не пришли другие айдишники центров
        $wcIDsss = [];
        $wcIDsName = '';
        foreach ( $workingCenters as $workingCenter )
        {
            foreach ( $workingCenter as $key => $wcArr )
            {
                foreach ( $wcIDs as $wcID )
                {
                    if ( (int)$wcID === (int)$key )
                    {
                        $wcIDsss[] = (int)$wcID;
                        $wcIDsName = $wcArr['name'];
                    }
                }
            }
        }

        $_SESSION['assist']['wcSort']['ids'] = $wcIDsss;
        $_SESSION['assist']['wcSort']['name'] = $wcIDsName;

        $_SESSION['assist']['page'] = 0;
        $_SESSION['assist']['startfromPage'] = 0;
        $_SESSION['re_search'] = true;
    }
	
	// выбор колва отобр. позиций
	if ( isset($_GET['maxPos']) )
	{
		$_SESSION['assist']['maxPos'] = (int) trim($_GET['maxPos']);
		$_SESSION['assist']['page'] = 0;
		$_SESSION['assist']['startfromPage'] = 0;
		$_SESSION['re_search'] = true;
	}
	
	
	// взяли статус
	if ( isset($_GET['regStat']) && !empty($_GET['regStat']) ) 
	{
        $statusID = (int)$_GET['regStat'];
        include_once _globDIR_.'classes/General.php';
        $general = new General();
        $general->connectToDB();
        $statuses = $general->statuses;

        $flag = false;
        foreach ($statuses as $status)
        {
            if ( (int)$status['id'] === $statusID )
            {
                $_SESSION['assist']['regStat'] = $status['name_ru'];
                $flag = true;
            }
        }
        if ( !$flag ) $_SESSION['assist']['regStat'] = "Нет";

        $_SESSION['assist']['page'] = 0;
        $_SESSION['assist']['startfromPage'] = 0;
        if ( isset($_SESSION['searchFor']) && !empty($_SESSION['searchFor']) ) $_SESSION['re_search'] = true;
	}
	
	//countAmount
	if ( $_SESSION['countAmount'] && $_SESSION['re_search'] === true ) {   // означает что в поиске что-то найдено, и он нуждается в обновлении 
		header("location:"  . _rootDIR_HTTP_ . "Views/Glob_Controllers/search.php?searchFor={$_SESSION['searchFor']}");
	}

	header("location: ../index.php");
	exit();