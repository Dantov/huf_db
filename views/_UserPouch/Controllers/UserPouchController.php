<?php
namespace Views\_UserPouch\Controllers;
use Views\_UserPouch\Models\UserPouch;
use Views\_Globals\Controllers\GeneralController;
use Views\vendor\core\Cookies;

class UserPouchController extends GeneralController
{

    public $title = 'Кошелёк Работника :: ХЮФ';

    public $workerID;
    public $tab;
    public $month;
    public $year;

    public function beforeAction()
    {
        $request = $this->request;

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


        $thisPage = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if ( $thisPage !== $_SERVER["HTTP_REFERER"] ) {
            $_SESSION['prevPage'] = $_SERVER["HTTP_REFERER"];
        }

        $userPouch = new UserPouch($this->tab, $this->workerID, $this->month, $this->year);

        $stockInfo = $userPouch->getStockInfo();
        $modelPrices = $userPouch->getModelPrices();
        $statistic = $userPouch->getStatistic();

        $tab = $this->tab;
        $workerID = $this->workerID;
        $monthID = $this->month;
        $yearID = $this->year;
        $compacts = compact([
            'modelPrices','stockInfo','tab','statistic','workerID','monthID','yearID',
        ]);
        return $this->render('userpouch', $compacts);
    }


}