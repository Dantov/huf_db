<?php
/**
 */

class Application
{

    public $controllerName;

    public function __construct($config=[])
    {

    }



    protected function parseQueryString()
    {
        $query_string = $_SERVER['QUERY_STRING'];
        $uri = $_SERVER['PHP_SELF'];

        $controllerName = explode('/',$uri)[2];


        debug($query_string,'$query_string');
        debug($controllerName,'$controllerName');
    }

    protected function getController()
    {
        //$query_string = $_SERVER['QUERY_STRING'];

        $controllerName = $this->controllerName;
        if ( empty($controllerName) )
        {
            $uri = $_SERVER['PHP_SELF'];
            $controllerName = explode('/',$uri)[2];
        }
        $class = $controllerName.'Controller';

        $path = _rootDIR_ .'views/'. $controllerName . '/classes/'.$class.'.php';
        if ( !file_exists( $path ) ) exit( "File " . $path . " not found!" );
        require_once $path;
        $controller = new $class($controllerName);

        if ( method_exists($controller, 'beforeAction') ) $controller->beforeAction();
        if ( method_exists($controller, 'action') )
        {
            $controller->action();
        } else {
            throw new Exception("Метод action() не найден в контроллере ". $controllerName ."!", 503 );
        }
        if ( method_exists($controller, 'afterAction') ) $controller->afterAction();
    }

    /**
     * Запуск приложения
     * @param string $controllerName
     * @throws Exception
     */
    public function run($controllerName='')
    {
        if ( !empty($controllerName) ) $this->controllerName = $controllerName;
        $this->getController();
    }
}