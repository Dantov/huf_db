<?php
namespace Views\_PaymentManager\Controllers;
use Views\_UserPouch\Controllers\UserPouchController;
use Views\_PaymentManager\Models\PaymentManager;
use Views\vendor\core\URLCrypt;

class PaymentManagerController extends UserPouchController
{

    public $title = 'Менеджмент Зарплат ХЮФ';

    public function beforeAction() : void
    {
        $request = $this->request;
        if ( $request->isAjax() )
        {
            exit;
        }


        //debug( $request->get('pm'),'PM');
        //debug( URLCrypt::decode($request->get('pm')),'',1);

        $this->workerID = (int)$request->get('worker');
        $this->tab = $this->getView( (int)$request->get('tab') );
        $this->month = (int)$request->get('month');
        $this->year = (int)$request->get('year');
    }

    /**
     * @throws \Exception
     */
    public function action()
    {
        $thisPage = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if ( $thisPage !== $_SERVER["HTTP_REFERER"] ) {
            $prevPage = $_SERVER["HTTP_REFERER"];
        }

        $pm = new PaymentManager($this->tab, $this->workerID, $this->month, $this->year);

        $stockInfo = $pm->getStockInfo();
        $modelPrices = $pm->getModelPrices();
        $statistic = $pm->getStatistic( $pm->worker );

        //debug($stockInfo,'$stockInfo');
        //debug($modelPrices,'$modelPrices',1);

        $usersList = $pm->getActiveUsers();
        $this->setWorkerByIDinList( $this->workerID, $usersList );


        $this->includeJSFile('paymentM.js',['defer','timestamp']);

        $tab = $this->tab;
        $workerID = $this->workerID;
        $monthID = $this->month;
        $yearID = $this->year;

        $compacts = compact([
            'modelPrices','stockInfo','tab','statistic','usersList','workerID','monthID','yearID',
        ]);
        return $this->render('paymentM', $compacts);
    }

    /**
    * ставит сессию работника
    */
    public function setWorkerByIDinList( $workerID, $usersList ) : void
    {
        $session = $this->session;

        //debug($workerID,'$workerID');
        //debug($usersList,'$usersList',1);

        if ( $workerID === 0 ) 
        {
            $session->currentWorker = [
                    'id' => 0,
                    'fio' => 'Все работники',
                ];
            return;
        }

        foreach ($usersList as $user) 
        {
            if ( $user['id'] == $workerID ) 
            {
                $session->currentWorker = [
                    'id' => $user['id'],
                    'fio' => $user['fio'],
                ];
                return;
            }
        }
    }

}