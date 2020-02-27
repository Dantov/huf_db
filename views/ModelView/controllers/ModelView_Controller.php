<?php
	//============= ModelView_Controller ============//
	
	if ( filter_has_var(INPUT_GET, 'id') ) {
		$id = (int)$_GET['id'];
	} else {
		header("location: ../index.php");
	}
	if ( isset($_SESSION['id_progr']) ) unset($_SESSION['id_progr']); // сессия id пдф прогресс бара

	require('classes/ModelView.php');
	
	$modelView = new ModelView($id, $_SERVER, $_SESSION['user']);
	if ( !$connection = $modelView->connectToDB() ) exit;
	
	$modelView->unsetSessions();
	
	$modelView->dataQuery();
	
	$row = $modelView->row;
	$coll_id = $modelView->getCollections();
	
	$getStl = $modelView->getStl();
	$button3D = $getStl['button3D'];
	$dopBottomScripts = $getStl['dopBottomScripts'];
	
	$complStr = $modelView->getComplects();
	$images = $modelView->getImages();
	$labels = $modelView->getLabels($row['labels']);
	$str_mat = $modelView->getModelMaterial();
	$str_Covering = $modelView->getModelCovering();
	$gemsTR = $modelView->getGems();
	$dopVCTr = $modelView->getDopVC();
	
	$stts = $modelView->getStatus($row);
	$stat_name = $stts['stat_name'];
	$stat_date = $stts['stat_date'];
	$stat_class = $stts['class'];
    $stat_title = $stts['title'];
	//$stat_glyphi = $stts['glyphi'];

    if ( $stts['glyphi'] == 'glyphicons-ring' ) {
        $stat_glyphi = $stts['glyphi'];
    } else {
        $stat_glyphi = 'glyphicon glyphicon-' . $stts['glyphi'];
    }
    
    $statuses = $modelView->getStatuses();

	$stillNo = !empty($row['vendor_code']) ? $row['vendor_code'] : "Еще нет";
	$vcEditbtn = '';
	if ( $_SESSION['user']['access'] == 4 ) {
		$vcEditbtn = '
			<a  class="btn btn-sm btn-default" id="vc_create_btn" title="Изменить"><span class="glyphicon glyphicon-pencil"></span></a>
		';
	}
	
	$srDt = '';
	$size_range = trim($row['size_range']);
	if ( isset($size_range) && !empty($size_range) ) {
		$srDt = "
			<dt>Размерный Ряд &#160;<i class=\"fab fa-quinscape\"></i></dt><dd>$size_range</dd>
		";
	}
	
	$print_costDD = '';
	if ( isset($row['print_cost']) && !empty($row['print_cost']) && $_SESSION['user']['access'] > 0 ) {
		$print_costDD = '
			<dt>Печать &#160;<span class="glyphicon glyphicon-usd"></span></dt>
			<dd id="print_cost">'.$row['print_cost'].'</dd>
		';
	}

    $jeweler_costDD = '';
    if ( isset($row['model_cost']) && !empty($row['model_cost']) && $_SESSION['user']['access'] > 0 )
    {
        $jeweler_costDD = '
                <dt>Доработка &#160;<span class="glyphicon glyphicon-usd"></span></dt>
                <dd id="print_cost">'.$row['model_cost'].'</dd>
            ';
    }


	
	$ai_file = '';
    foreach ( $coll_id as $coll )
    {
        switch ( $coll['name'] )
        {
            case "Серебро с Золотыми накладками":
                $ai_file = $modelView->getAi();
                if (!$ai_file) $ai_file = 'Нет';
                break;
            case "Серебро с бриллиантами":
                $ai_file = $modelView->getAi();
                if (!$ai_file) $ai_file = 'Нет';
                break;
            case "Золото ЗВ":
                $ai_file = $modelView->getAi();
                if (!$ai_file) $ai_file = 'Нет';
                break;
        }
    }

	$thisPage = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	if ( $thisPage !== $_SERVER["HTTP_REFERER"] ) {
		$_SESSION['prevPage'] = $_SERVER["HTTP_REFERER"];
	}
	
	$bottomBtns = '';
	if ( isset($_SESSION['user']['access']) && $_SESSION['user']['access'] > 0 ) {
		$editBtn = '<a href="../AddEdit/index.php?id='.$id.'&component=2" class="btn btn-default">
			<span class="glyphicon glyphicon-pencil"></span> 
			Редактировать</a>
		';
		/*
		$editOtherBtn = '<a href="../AddEdit/editOtherForm.php?id='.$id.'&component=2" class="btn btn-default">
			<span class="glyphicon glyphicon-pencil"></span> 
			Редактировать</a>
		';
		*/
		if ( (int)$_SESSION['user']['access'] === 1 || (int)$_SESSION['user']['id'] === 33 ) { // весь доступ
			$bottomBtns = $editBtn;
		} 
		if ( (int)$_SESSION['user']['access'] === 2 ) { // доступ только где юзер 3д моделлер или автор
			$userRowFIO = $_SESSION['user']['fio'];
			$authorFIO = $row['author'];
			$modellerFIO = $row['modeller3D'];
			if ( stristr($authorFIO, $userRowFIO) !== FALSE || stristr($modellerFIO, $userRowFIO) !== FALSE ) {
				$bottomBtns = $editBtn;
			}
		}
		
		if (
		    (int)$_SESSION['user']['access'] === 3
            || (int)$_SESSION['user']['access'] === 4
            || (int)$_SESSION['user']['access'] === 5
        )
		{
			$bottomBtns = $editBtn;
		}
		
	}
	
	$btnlikes = 'btnlikes';
	if ( $modelView->checklikePos() ) $btnlikes = 'btnlikesoff';
	
	if ( isset($_SESSION['user']['access']) && $_SESSION['user']['access'] == -2 ) {
		$bottomBtns = '<a class="btn btn-default" onclick="statusChange();">
		<span class="glyphicon glyphicon-pencil"></span> 
		Изменить статус</a>';
	}
	$scriptsPN = '';
	if ( $_SESSION['assist']['PushNotice'] == 1 ) {
		$scriptsPN = '<script src="../Glob_Controllers/js/PushNotice.js?ver='.time().'"></script>';
	}