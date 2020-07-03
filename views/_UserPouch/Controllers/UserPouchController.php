<?php
namespace Views\_UserPouch\Controllers;
use Views\_UserPouch\Models\UserPouch;
use Views\_Globals\Controllers\GeneralController;
use Views\vendor\core\Cookies;

class UserPouchController extends GeneralController
{

    public $title = 'Кошелёк Работника :: ХЮФ';

    public $tab;

    public function beforeAction()
    {
        $request = $this->request;
        if ( $request->isAjax() )
        {
            exit;
        }

        $this->tab = $this->getView( (int)$request->get('tab') );
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

        $userPouch = new UserPouch($this->tab);

        $stockInfo = $userPouch->getStockInfo();
        $modelPrices = $userPouch->getModelPrices();
        $statistic = $userPouch->getStatistic();


        $tab = $this->tab;
        $compacts = compact([
            'modelPrices','stockInfo','tab','statistic'
        ]);
        return $this->render('userpouch', $compacts);
    }


}