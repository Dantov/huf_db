<?php
namespace Views\vendor\core;
use Views\vendor\core\Errors\ErrorHandler;

class Application
{

    public $controllerPath = '';
    public $controllerName = '';

    protected $config = [];

    /**
     * Application constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct($config=[])
    {
        if ( is_array($config) && !empty($config) ) $this->config = $config;

        Config::initConfig($config);

        new ErrorHandler($config['errors']);

        Router::parseRout($_SERVER['REQUEST_URI']);
        $this->controllerPath = Router::getRout();
        $this->controllerName = Router::getControllerName();
    }


    /**
     * @throws \Exception
     */
    protected function getController()
    {
        $class = $this->controllerPath;
        if ( !file_exists( _rootDIR_ .'/'. str_replace('\\','/', $class) . '.php' ) ) 
        {
            if ( !_DEV_MODE_ )
            {
                $class = $this->controllerPath = "Views\_Main\Controllers\MainController";
                $this->controllerName = "main";

            } else {
                throw new \Exception( "Controller " . $class . " not found!" , 111);
            }

        }

        $controller = new $class($this->controllerName);

        if ( method_exists($controller, 'setQueryParams') ) $controller->setQueryParams(Router::getParams());
        if ( method_exists($controller, 'beforeAction') ) $controller->beforeAction();
        if ( method_exists($controller, 'action') )
        {
            $controller->action();
        } else {
            throw new \Exception("Метод action() не найден в контроллере ". $class ."!", 503 );
        }

        if ( method_exists($controller, 'afterAction') ) $controller->afterAction();
    }


    /**
     * Запуск приложения
     * @throws \Exception
     */
    public function run()
    {
        $this->getController();
    }
}