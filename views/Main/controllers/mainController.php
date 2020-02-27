<?php
/// =====  Main Controller	=====///
    //debug($_SESSION);
//$_SESSION['row_pos'] = 1;
	// означает что в поиске что-то найдено, и он нуждается в обновлении
	if ( $_SESSION['countAmount'] && $_SESSION['re_search'] )
	{
		header("location: ../Glob_Controllers/search.php?searchFor={$_SESSION['searchFor']}");
	}
	$_SESSION['id_notice'] = 0;
	
	require( _viewsDIR_ . 'Main/classes/Main.php');
	$main = new Main($_SERVER, $_SESSION['assist'], $_SESSION['user'], $_SESSION['foundRow'] );
	
	if ( !$connection = $main->connectToDB() ) exit;


	$main->unsetSessions();

	if (!_DEV_MODE_) $main->backup(10);
	
	$variables   = $main->getVeriables();
	$chevron_    = $variables['chevron_'];
	$chevTitle   = $variables['chevTitle'];
	$showsort    = $variables['showsort'];
	$activeSquer = $variables['activeSquer'];
    $activeWorkingCenters = $variables['activeWorkingCenters'];
    $activeWorkingCenters2 = $variables['activeWorkingCenters2'];
	$activeList  = $variables['activeList'];
	$collectionName = $variables['collectionName'];
	$collectionList = $main->getCollections();

	// выборка статусов
    $status = $main->getStatusesSelect();
    $selectedStatusName = $_SESSION['assist']['regStat'];

	$toggleSelectedGroup = 'hidden';
	if ( $variables['activeSelect'] == 'btnDefActive' )
	{
		$toggleSelectedGroup = '';
		$selectedModelsByLi = Main::selectedModelsByLi();
	}

    if ( $_SESSION['user']['fio'] == 'Быков В.А.' )
    {
        require( _viewsDIR_ . '/Glob_Controllers/classes/PushNotice.php');
        $pn = new PushNotice();
        $pn->clearOldNotices();
    }
	
	//если нет поиска, выбираем из базы
	if ( !isset($_SESSION['foundRow']) || empty($_SESSION['foundRow']) ) $main->getModelsFormStock();


	// начинаем вывод моделей
	if ( !isset($_SESSION['nothing']) ) {
		$showModels = '';
        $drawBy_ = (int)$_SESSION['assist']['drawBy_']?:false;

        // **************=================*****************//

		// Плиткой
		if ( $drawBy_ === 1 )
		{
			
			$getterModels = $main->getModelsByTiles();
			$showModels = $getterModels['showByTiles'];
			$iter = $getterModels['iter'];
			$wholePos = $getterModels['wholePos'];
			
			$statsbottom = "<i>Сортировка по: </i>".$showsort." || "."<i>Найдено (Изделий):</i> ".$wholePos." || "."<i>Показано:</i> ".$iter;
		}

        // разбивка по комплектам
        if ( $drawBy_ === 2 ) {

            $getterModels = $main->getModelsByRows();
            $showModels = $getterModels['showByRows'];

            $posIter = $getterModels['posIter'];
            $wholePos = $getterModels['wholePos'];
            $ComplShown = $getterModels['ComplShown'];
            $iter = $getterModels['iter'];

            $statsbottom = "<i>Сортировка по: </i>".$showsort." || "."<i>Найдено (Комплектов):</i> ".$wholePos." <i>(Изделий):</i> ".$posIter." || "."<i>Показано: Комплектов &mdash; </i>".$ComplShown.". <i>Изделий &mdash; </i>".$iter;
        }


		// **************=================*****************//


		// Рабочие Центры
        if ( $drawBy_ === 3 || $drawBy_ === 4 )
        {
            // менюшка для выборки по рабочим центрам
            if ( $drawBy_ === 4 )
            {
                $workCentersSort = true;
                $workingCenters = $main->workingCentersDB;
            }

            $getterModels = $main->getModelsByWorkingCenters();
            $showModels = $getterModels['showByWorkingCenters'];
            $iter = $getterModels['iter'];
            $wholePos = $getterModels['wholePos'];

            $statsbottom = "<i>Сортировка по: </i>".$showsort." || "."<i>Найдено (Изделий):</i> ".$wholePos." || "."<i>Показано:</i> ".$iter;
        }



        //Табличка участов с просроченными
        if ( $drawBy_ === 5 )
        {
            $getterModels = $main->getWorkingCentersExpired();
            $showModels = $getterModels['showByWorkingCenters'];
            $wholePos = $getterModels['wholePos'];
        }


	
		if ( $wholePos == 0 )
		{
			$showModels = "<img src=\"../../picts/web1.png\" width=\"10%\"/>";
			$showModels .= "В этой коллекции изделий нет.";
		}
	
	} else { //если ничего не найдено
		$wholePos = 0;
		$showModels = "<img src=\"../../picts/web1.png\" width=\"10%\"/>";
		$showModels .= $_SESSION['nothing'];
		unset($_SESSION['nothing']);
	}
	
	
	// --- Пагинация --- //
    if ( $drawBy_ !== 5 )
    {
        $pagination = '';
        // начинаем рисовать пагинацию если кол-во отображаемых моделей больше кол-ва разрешенных к показу
        if ($wholePos > $_SESSION['assist']['maxPos']) $pagination = $main->drawPagination();
    }


	$addModel = '';
	if ( $_SESSION['user']['access'] == 1 || $_SESSION['user']['access'] == 2 ) {
        $addModel .= '
		<h4>
		    <p>
		      <a href="../AddEdit/index.php?id=0&component=1" class="btn btn-primary">
			    <span class="glyphicon glyphicon-file"></span>
				<strong> Добавить модель</strong>
			  </a>
			</p>
		</h4>';
	}

	$scriptsPN = '';
	if ( $_SESSION['assist']['PushNotice'] == 1 )
	{
		$scriptsPN = '<script src="../Glob_Controllers/js/PushNotice.js?ver='.time().'"></script>';
	}
