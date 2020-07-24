<?php
namespace Views\_PaymentManager\Controllers;
use Views\_AddEdit\Models\Handler;
use Views\_AddEdit\Models\HandlerPrices;
use Views\_Globals\Models\User;
use Views\_UserPouch\Controllers\UserPouchController;
use Views\_PaymentManager\Models\PaymentManager;
use Views\vendor\core\Registry;
use Views\vendor\core\Router;
use Views\vendor\libs\classes\AppCodes;
use Views\vendor\libs\classes\URLCrypt;

class PaymentManagerController extends UserPouchController
{

    public $title = 'Менеджмент Зарплат ХЮФ';

    /**
     * @throws \Exception
     */
    public function beforeAction() : void
    {
        $request = $this->request;
        if ( $request->isAjax() )
        {
            if ( $this->isQueryParam('getPricesAll') && $request->post('priceIDs') && $request->post('posID') )
                $this->getPricesByID( $request->post('priceIDs'), $request->post('posID') );

            if ( $this->isQueryParam('payPrice') && $request->post('prices') )
                $this->payPrices( $request->post('prices') );
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
        $statistic = $pm->getStatistic();

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
     * @param $workerID
     * @param $usersList
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


    /**
     * @param string $pricesID
     * @param string $posID
     * @throws \Exception
     */
    protected function getPricesByID(string $pricesID, string $posID )
    {
        $codes = Registry::init()->appCodes;
        if ( !User::permission('paymentManager') )
            exit( json_encode( ['error'=>$codes->getCodeMessage($codes::NO_PERMISSION)] ) );

        $pricesID = explode(';', URLCrypt::strDecode( $pricesID ));
        $posID = explode(';', URLCrypt::strDecode( $posID ));

        $pm = new PaymentManager();
        $pricesArr = $pm->getPricesByID( $pricesID, $posID );

        exit( json_encode( $pricesArr ) );
    }

    /**
     * @param array $priceIDs
     * @throws \Exception
     */
    protected function payPrices( array $priceIDs )
    {
        if ( !User::permission('paymentManager') )
            exit( json_encode( ['error'=>AppCodes::getMessage(AppCodes::NO_PERMISSION_TO_PAY)] ) );
        //debug($priceIDs,'$priceIDs',1);

        $h = new HandlerPrices();
        $h->connectToDB();
        foreach ($priceIDs as &$pID)
        {
            $pID = (int)URLCrypt::strDecode( $pID );
            if ( !$h->checkID($pID, 'model_prices', 'id') )
                exit( json_encode(['error'=>AppCodes::getMessage(AppCodes::WRONG_PRICE)]) );
        }

        exit( json_encode($h->payPrices( $priceIDs )) );
    }

}