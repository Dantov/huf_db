<?php
namespace Views\vendor\core;
use Views\vendor\core\error\ErrorHandler;

class Application
{

    public $controllerPath = '';
    public $controllerName = '';

    /**
     * Application constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct($config=[])
    {
        /**
         * @param mode
         *  0 - продакшн Без E_NOTICE,
         *  1 - продакшн Без E_NOTICE и E_Warning,
         *  2 - DEV all Errors,
         *  3 - DEV без E_NOTICE,
         */
        $errorsConfig = [
            'enable' => true, // включает перехват ошибок фреймворком DTW.
            'logs'   => false,//'runtime/logs', // false - отключает логи
            'mode'   => 3//_DEV_MODE_ ? 3 : 1,
        ];
        new ErrorHandler($errorsConfig);

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
        if ( !file_exists( _rootDIR_ .'/'. str_replace('\\','/', $class) . '.php' ) ) throw new \Exception( "File " . $class . " not found!" , 333);

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