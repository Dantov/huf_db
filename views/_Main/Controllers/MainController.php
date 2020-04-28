<?php
namespace Views\_Main\Controllers;
use Views\_Main\Models\Main;
use Views\_Main\Models\SetSortModel;
use Views\_Globals\Controllers\GeneralController;

class MainController extends GeneralController
{
	
    public $title = 'ХЮФ 3Д База';

    public function __construct($controllerName)
    {
        parent::__construct($controllerName);
    }

    public function beforeAction()
    {
        $params = $this->getQueryParams();

        if ( empty($params) ) return;

        $setSort = new SetSortModel($this->session);

        // вернет адрес для редиректа, или false если редирект не нужен
        if ( $url = $setSort->setSort($params) ) $this->redirect($url);
    }

    /**
     * @throws \Exception
     */
    public function action()
    {
        $session = $this->session;
		// означает что в поиске что-то найдено, и он нуждается в обновлении
		if ( $session->getKey('countAmount') && $session->getKey('re_search') ) {
                    $this->redirect('/search/?searchFor=' . $session->getKey('searchFor'));
                    //header("location:". _glob_HTTP_ ."search.php?searchFor={$_SESSION['searchFor']}");
		}
		$_SESSION['id_notice'] = 0;
		$main = new Main( $_SERVER, $session->getKey('assist'), $session->getKey('user'), $session->getKey('foundRow') );
		
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
		//$collectionList = $main->getCollections();

		// выборка статусов
		$status = $main->getStatusesSelect();
		$selectedStatusName = $_SESSION['assist']['regStat'];

		$toggleSelectedGroup = 'hidden';
		if ( $variables['activeSelect'] == 'btnDefActive' ) 
		{
			$toggleSelectedGroup = '';
			$selectedModelsByLi = Main::selectedModelsByLi();
		}
		
		// почистим старые уведомления
		if ( $_SESSION['user']['id'] == 1 ) 
		{
			$pn = new \Views\_Globals\Models\PushNotice();
			$pn->clearOldNotices();
		}

		//если нет поиска, выбираем из базы
		if ( !isset($_SESSION['foundRow']) || empty($_SESSION['foundRow']) )
			$main->getModelsFormStock();

		// начинаем вывод моделей
		if ( !isset($_SESSION['nothing']) ) {
			$showModels = '';
			$drawBy_ = (int)$_SESSION['assist']['drawBy_']?:false;


			// **************=================*****************//
			// Плиткой
			if ( $drawBy_ === 1 ) {

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
			if ( $drawBy_ === 3 || $drawBy_ === 4 ) {
				// менюшка для выборки по рабочим центрам
				if ( $drawBy_ === 4 ) {
					$workCentersSort = true;
					$workingCenters = $main->workingCentersDB;
				}

				$getterModels = $main->getModelsByWorkingCenters();
				$showModels = $getterModels['showByWorkingCenters'];
				$iter = $getterModels['iter'];
				$wholePos = $getterModels['wholePos'];

				$statsbottom = "<i>Сортировка по: </i>".$showsort." || "."<i>Найдено (Изделий):</i> ".$wholePos." || "."<i>Показано:</i> ".$iter;
			}
            if ( $drawBy_ === 3 ) $this->varBlock['container'] = 2; //уберем класс container в шаблоне чтоб стало шире

			//Табличка участов с просроченными
			if ( $drawBy_ === 5 ) {
				$getterModels = $main->getWorkingCentersExpired();
				$showModels = $getterModels['showByWorkingCenters'];
				$wholePos = $getterModels['wholePos'];
			}

		} else {
			 //если ничего не найдено
			$wholePos = 0;
		}

		// --- Пагинация --- //
		if ( $drawBy_ !== 5 ) {
			$pagination = '';
			// начинаем рисовать пагинацию если кол-во отображаемых моделей больше кол-ва разрешенных к показу
			if ($wholePos > $_SESSION['assist']['maxPos'])
				$pagination = $main->drawPagination();
		}
		
		$compacted = compact(['variables','chevron_','chevTitle','showsort','activeSquer','activeWorkingCenters',
		'activeWorkingCenters2','activeList','collectionName','collectionList','status','selectedStatusName',
		'toggleSelectedGroup','selectedModelsByLi','showModels','drawBy_','iter','wholePos','statsbottom',
		'posIter','ComplShown','workingCenters','workCentersSort','pagination']);
		
		return $this->render('main', $compacted);
	}
	
}