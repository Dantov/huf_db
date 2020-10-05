<?php
namespace Views\_UserPouch\Controllers;
use Views\_UserPouch\Models\UserPouch;
use Views\_Globals\Controllers\GeneralController;
use Views\vendor\core\Cookies;
use Views\_Globals\Widgets\Pagination;

class UserPouchController extends GeneralController
{

    public $title = 'ХЮФ::Кошелёк Работника';

    /**
     * $start - от пагинации, позиция с которой начать выборку 
     */
    public $start;
    /**
     * $perPage - кол-во выбираемых позиций для отобр. на одной странице
     */
    public $page;

    public $workerID;
    public $tab;
    public $month;
    public $year;

    public function beforeAction()
    {
        $request = $this->request;

        $this->page = (int)$request->get('page');
        $this->workerID = $this->session->getKey('user')['id'];
        $this->tab = $this->getView( (int)$request->get('tab') );
        $this->month = (int)$request->get('month');
        $this->year = (int)$request->get('year');
    }

    /**
     * @param $tab
     * @return string
     */
    protected function getView( $tab )
    {
        switch ($tab)
        {
            case 1: return 'all'; break;
            case 2: return 'paid'; break;
            case 3: return 'notpaid'; break;
            default : return 'all'; break;
        }
    }

    /**
     * @throws \Exception
     */
    public function action()
    {
        $userPouch = new UserPouch($this->tab, $this->workerID, $this->month, $this->year);

        ///*** Паганация *** ///
        $totalM = $userPouch->totalModelsHasPrices();
        $totalMP = $userPouch->totalPrices();
        $perpage = 30;
        $pagination = new Pagination( $totalM, $perpage, $this->page );
        $userPouch->start = $pagination->getStart();
        $userPouch->perPage = $perpage;

        $stockInfo = $userPouch->getStockInfo();
        $modelPrices = $userPouch->getModelPrices();
        $statistic = $userPouch->getStatistic();

        $tab = $this->tab;
        $workerID = $this->workerID;
        $monthID = $this->month;
        $yearID = $this->year;
        $page = $this->page;

        $this->includeJSFile('UserPouch.js',['defer','timestamp']);

        $compacts = compact([
            'modelPrices','stockInfo','tab','statistic','workerID','monthID','yearID','page','pagination','totalM','totalMP',
        ]);
        return $this->render('userpouch', $compacts);
    }


}