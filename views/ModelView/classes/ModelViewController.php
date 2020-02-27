<?php
require _globDIR_ . "classes/GeneralController.php";
/**
 */

class ModelViewController extends GeneralController
{

    public $title = 'ХЮФ 3Д - Просмотр Модели ';

    public function __construct($controllerName='')
    {
        parent::__construct();

        if ( !empty($controllerName) ) $this->controllerName = $controllerName;
    }

    public $connection;

    public function action()
    {

        require(_viewsDIR_ . $this->controllerName.'/classes/ModelView.php');

        $modelView = new ModelView($id, $_SERVER, $_SESSION['user']);
        $connection = $modelView->connectToDB();

        $this->varBlock['connection'] = $connection;

        return $this->render('modelView', compact(['connection']));
    }

}