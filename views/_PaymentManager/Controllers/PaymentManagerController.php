<?php
namespace Views\_PaymentManager\Controllers;
use Views\_AddEdit\Models\Handler;
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
            if ( $this->isQueryParam('getPrices') && $request->post('priceIDs') && $request->post('posID') )
                $this->getPricesByID( $request->post('priceIDs'), $request->post('posID') );

            if ( $this->isQueryParam('getPricesAll') && $request->post('priceIDs') && $request->post('posID') )
                $this->getPricesByIDAll( $request->post('priceIDs'), $request->post('posID') );

            if ( (int)$this->getQueryParam('payPrice') === 1 && $request->post('prices') )
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
        $statistic = $pm->getStatistic( $pm->worker );

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
     * @param int $priceID
     * @throws \Exception
     */
    protected function getPriceByID( int $priceID )
    {
        /*
        $codes = Registry::init()->appCodes;
        $pm = new PaymentManager();
        if (!$pm->checkID($priceID,'model_prices','id'))
            exit( json_encode( ['error'=>$codes->getCodeMessage($codes::PRICE_DOES_NOT_EXIST)] ) );

        $sql = "
            SELECT mp.id as pID, mp.pos_id as posID, mp.user_id as uID, mp.gs_id as gsID, mp.is3d_grade as is3dGrade, mp.cost_name as costName, mp.value as value, mp.status as status,
              mp.paid as paid, mp.pos_id as posID, mp.date as date, i.img_name as imgName, u.fio, st.number_3d as number_3d, st.vendor_code as vendorCode, st.model_type as modelType
                FROM model_prices as mp
                  LEFT JOIN images as i ON mp.pos_id = i.pos_id AND i.main='1'
                  LEFT JOIN users as u ON mp.user_id = u.id
                  LEFT JOIN stock as st ON mp.pos_id = st.id
			        WHERE mp.id = '$priceID'";
        $arr = $pm->findOne($sql);

        if ( (int)$arr['status'] === 1 && (int)$arr['paid'] === 0 )
        {
            $arr['date'] = $pm->formatDate($arr['date']);
            $imagePath = $arr['number_3d'].'/'.$arr['posID'].'/images/'.$arr['imgName'];
            $arr['imgName'] = _stockDIR_HTTP_ . $imagePath;
            if ( !file_exists(_stockDIR_ . $imagePath) ) $arr['imgName'] = _stockDIR_HTTP_."default.jpg";

            exit( json_encode($arr) );
        } else {
            exit( json_encode( ['error'=>$codes->getCodeMessage($codes::WRONG_PRICE)] ) );
        }
        */
    }

    /**
     * @param string $pricesID
     * @param int $posID
     * @throws \Exception
     */
    protected function getPricesByID(string $pricesID, int $posID )
    {
        $codes = Registry::init()->appCodes;
        if ( !User::permission('paymentManager') ) exit( json_encode( ['error'=>$codes->getCodeMessage($codes::NO_PERMISSION)] ) );

        $pm = new PaymentManager();
        $pricesID = explode(';', $pricesID);
        $pricesArr = $pm->getPricesByID( $pricesID, $posID );

        exit( json_encode( $pricesArr ) );
    }

    /**
     * @param string $pricesID
     * @param string $posID
     * @throws \Exception
     */
    protected function getPricesByIDAll(string $pricesID, string $posID )
    {
        $codes = Registry::init()->appCodes;
        if ( !User::permission('paymentManager') )
            exit( json_encode( ['error'=>$codes->getCodeMessage($codes::NO_PERMISSION)] ) );

        $pricesID = explode(';', URLCrypt::decode( $pricesID ));
        $posID = explode(';', URLCrypt::decode( $posID ));

        $pm = new PaymentManager();
        $pricesArr = $pm->getPricesByIDAll( $pricesID, $posID );

        exit( json_encode( $pricesArr ) );
    }

    /**
     * @param array $priceIDs
     * @throws \Exception
     */
    protected function payPrices( array $priceIDs )
    {
        //debug($priceIDs,'$priceIDs',1);

        $h = new Handler();
        $h->connectToDB();
        foreach ($priceIDs as $pID)
            if ( !$h->checkID($pID, 'model_prices', 'id') )
                exit( json_encode(['error'=>AppCodes::getMessage(AppCodes::WRONG_PRICE)]) );

        exit( json_encode($h->payPrices( $priceIDs )) );
    }

}