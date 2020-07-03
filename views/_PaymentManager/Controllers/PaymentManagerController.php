<?php
namespace Views\_PaymentManager\Controllers;
use Views\_Globals\Controllers\GeneralController;
use Views\vendor\core\Cookies;

class PaymentManagerController extends GeneralController
{

    public $title = 'Менеджмент Зарплат ХЮФ';

    public function beforeAction() : void
    {
        $request = $this->request;
        if ( $request->isAjax() )
        {

        }
    }

    /**
     * @throws \Exception
     */
    public function action()
    {
        $prevPage = '';
        $thisPage = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if ( $thisPage !== $_SERVER["HTTP_REFERER"] ) {
            $prevPage = $_SERVER["HTTP_REFERER"];
        }


        $compacts = ['prevPage'];
        return $this->render('paymentM', $compacts);
    }

}