<?php

namespace Views\vendor\core;
use Views\vendor\core\Router;

/*
 * класс для испрользования в видах
 * создание ссылок, форм, скриптов
 * */
class HtmlHelper
{

    /*
     * @var string
     * содержит открывающий тег формы со всеми атрибутами
     * */
    protected static $attributes = '';
    protected static $enctype = "application/x-www-form-urlencoded";
    protected static $action = "";
    protected static $method = "POST";

    protected static $definedURLParams = [];

    /*
     * поставим дефолтные данные на старте новой формы
     */
    protected static function setDefaultValues()
    {
        self::$attributes = '';
        self::$enctype = "application/x-www-form-urlencoded";
        self::$action = "";
        self::$method = "POST";
    }

    public static function defineURLParams( array $params = [] ) : void
    {
        if (!empty($params)) 
            self::$definedURLParams = $params;
    }

    public static function URL( string $url, array $params = []) : string
    {
        $url = str_replace('\\', '/', trim($url));
        $urlTrimmed = trim($url,'/');
        // в константах в конце нет слэша, ставим его здесь
        $url = $urlTrimmed;

        // Если один слешь - подставим текущий контроллер
        if ( empty($url) )
        {
            $url = _rootDIR_HTTP_ . Router::getControllerNameOrigin() . '/';
        } else {
            $url = _rootDIR_HTTP_ . $url . '/';
        }

        $paramsStr = '';
        $paramCount = 0;
        $definedURLParams = self::$definedURLParams;
        foreach ($params as $paramName => $paramValue) 
        {
            if ( array_key_exists($paramName, $definedURLParams) )
                unset($definedURLParams[$paramName]);

            if ( $paramCount === 0 )
            {
                $paramsStr .= "?$paramName=$paramValue";
                 ++$paramCount;
                continue;
            }
            $paramsStr .= "&$paramName=$paramValue";
            ++$paramCount;
        }
        foreach ($definedURLParams as $dParamName => $dParamValue) 
            $paramsStr .= "&$dParamName=$dParamValue";

        return $url .= $paramsStr;
    }


    /*
     * @param string $url
     * @param bool $real - является флагом того что эта ссылка на реальный файл. Т.е будет игнорить наличие языка
     * создает валидный урл
     * return string
     * */
    public static function URL_old($url, $real = false)
    {
        $url = str_replace('\\', '/', trim($url));
        $urlTrimmed = trim($url,'/');
        // в константах в конце нет слэша, ставим его здесь
        $url = "/" . $urlTrimmed;

        $config = Config::getConfig();
        $alias = $config['alias']; // array

        /* проверим наличие языков */
        $langEnable = $config['multiLanguage']['enable'];
        //$language = AppProperties::getRout('language');
        $lang = '';

        if ( $url === '/' )
        {
            // вставим язык по умолчанию, если он включён и его нет в строке
            if ( $langEnable && !stristr($url, $language) && !$real ) $lang = "$language/";
            $url = _rootDIR_HTTP_ . '/' . $lang. AppProperties::getControllerName();
        } else {
            /* проверим наличие алиаса в первом параметре */
            $params = explode('/', $urlTrimmed);
            foreach ($alias as $key => $path)
            {
                if ( $key == $params[0] )
                {
                    $params[0] = trim($path,'/');
                    $url = "/" . implode('/', $params);
                    break;
                }
            }
            // вставим язык по умолчанию, если он включён и его нет в строке
            if ( $langEnable && !stristr($url, $language) && !$real ) $lang = "/$language";
            $url = _rootDIR_HTTP_ . $lang . $url;
        }
        return $url;
    }

    /*
     * a( $attributes = array )
     * ссылки
     * */
    public static function a( $text, $url='', $attributes=[] )
    {
        $url = self::URL($url);

        $href = 'href="'.$url.'"';
        $a = '<a '.$href;
        $attribStr = '';

        foreach ( $attributes as $attr => $val ) {
            $attribStr .= $attr.'="'.$val.'" ';
        }

        $a .= self::drawAttributes($attributes).'>';
        $a .= $text;
        $a .= '</a>';

        echo $a;

    }

    /*
    * избавляет от дублирования кода
    * */
    public static function drawAttributes($attributes) {
        $attribStr = '';
        if ( !empty($attributes) ) {
            foreach ( $attributes as $attr => $val ) {
                $attribStr .= $attr.'="'.$val.'" ';
            }
        }
        return $attribStr;
    }

    public static function setEnctype($enctype)
    {
        if ( is_string($enctype) && !empty($enctype) )
        {
            self::$enctype = $enctype;
        }
    }

    /*
     * стартует форму
     * @param string $action - url файла обработчика
     * @param array $attributes - доп аттрибуты
     * return object - методы по добалению полей
     * */
    public static function beginForm($action, $attributes=[]) {

        self::setDefaultValues();
        if ( !empty($action) ) self::$action = $action;
        if ( !empty($attributes) && is_array($attributes) )
        {
            foreach ( $attributes as $attr => $val ) {
                switch (strtolower($attr))
                {
                    case "action":
                        self::$action = $val;
                        continue;
                        break;
                    case "method":
                        self::$method = $val;
                        continue;
                        break;
                    case "enctype":
                        self::$enctype = $val;
                        continue;
                        break;
                }
                self::$attributes .= $attr.'="'.$val.'" ';
            }
        }

        ob_start();
        return new ActiveForm();
    }
    protected static function openFormTag()
    {
        $form = '<form ' . 'action="'.self::$action.'" ' . 'method="'.self::$method.'" ' . 'enctype="'. self::$enctype .'" ' . self::$attributes. '>';

        return $form;
    }
    /*
     * завершает форму
     * выводит на экран
     * return void
     * */
    public static function endForm() {
        $content = ob_get_contents();
        ob_end_clean();

        $inpt_csrf_ = '';
        // _csrf_ запишем в печеньку
        if ( AppProperties::getConfig()['csrf'] === true )
        {
            if ( !isset($_COOKIE['_csrf_']) ) {
                $_csrf_ = uniqid('csrf_').randomStringChars('en','12','all');
                setcookie("_csrf_", $_csrf_, time() + (3600*24), '/', $_SERVER['HTTP_HOST']);
            }
            $inpt_csrf_ = '<input type="hidden" name="_csrf_" value="'.$_COOKIE['_csrf_'].'" />';
        }

        echo self::openFormTag();
        echo $content;
        echo $inpt_csrf_;
        echo '</form>';
    }

}