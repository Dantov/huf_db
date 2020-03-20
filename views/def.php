<?php
ini_set('date.timezone', 'Europe/Kiev');

	function _init_()
    {
        define('_rootDIR_', $_SERVER['DOCUMENT_ROOT'].'/');  // подключить скрипты

        define('_viewsDIR_', $_SERVER['DOCUMENT_ROOT'].'/views/');  // подключить скрипты
        define('_globDIR_', $_SERVER['DOCUMENT_ROOT'].'/views/Glob_Controllers/');  // подключить скрипты
        define('_stockDIR_', _rootDIR_.'Stock/');
		
        define('_vendorDIR_', _rootDIR_.'vendor/');

        define('_rootDIR_HTTP_', 'http://'.$_SERVER['HTTP_HOST'].'/'); // для ссылок
        define('_webDIR_HTTP_', _rootDIR_HTTP_ . 'web/'); // для ссылок

        define('_views_HTTP_', _rootDIR_HTTP_.'views/'); // для ссылок
        define('_glob_HTTP_', _rootDIR_HTTP_.'views/Glob_Controllers/'); // для ссылок
        define('_stockDIR_HTTP_', _rootDIR_HTTP_.'Stock/'); // http://192.168.0.245/HUF_DB/Stock/


        if ( explode('/', _rootDIR_)[4] == 'HUF-DB-DEV' )
        {
            define('_DEV_MODE_', true);
            define('_titlePage_', 'HUF-3d Developing mode');
            define('_brandName_', '3D модели "ХЮФ" Developer mode');

            define('_libDIR_HTTP_', _rootDIR_HTTP_.'libs/');
            define('_libDIR_', _rootDIR_.'libs/');

            define('DATABASE', 'huf_models_dev');

        } else {
            define('_DEV_MODE_', false);
            define('_titlePage_', 'HUF 3D models');
            define('_brandName_', '3D модели "ХЮФ"');

            define('_libDIR_', '');
            define('_libDIR_HTTP_', '');

            define('DATABASE', 'huf_models');
        }

        /**
         * @param mode
         *  0 - продакшн Без E_NOTICE,
         *  1 - продакшн Без E_NOTICE и E_Warning,
         *  2 - DEV all Errors,
         *  3 - DEV без E_NOTICE,
         */
        $errorsConfig = [
            'enable' => true, // включает перехват ошибок фреймворком DTW.
            'logs'   => 'runtime/logs',// false - отключает логи
            'mode'   => 3//_DEV_MODE_ ? 3 : 1,
        ];

        require_once _globDIR_ . 'classes/error/ErrorHandler.php';
        new ErrorHandler($errorsConfig);
	}

_init_();

    function debug($arr, $str='', $die=false)
    {
        $strrr = '';
        if ( !empty($str) ) $strrr = $str . " = ";

        echo '<pre style="display: inline-block !important;">';
        echo $strrr;
        print_r($arr);
        echo '</pre>';
        if ($die) exit;
    }
    if (!function_exists('array_key_first'))
    {
        function array_key_first(array $array)
        {
            if (count($array))
            {
                reset($array);
                return key($array);
            }
            return null;
        }
    }
    function validateDate($date, $format = 'Y-m-d') //Y-m-d H:i:s
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    function formatDate($date)
    {
        $fdate = is_int($date) ? '@'.$date : $date;
        return date_create( $fdate )->Format('d.m.Y');
    }

define('_NUMBERS_', '0123456789' );
define('_SYMBOLS_', '!@#№$%^&?_=+,-^:;{}[]' );
define('_CHARS_RU_', 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя'. strtoupper('абвгдеёжзийклмнопрстуфхцчшщъыьэюя') );
define('_CHARS_EN_', 'abcdefghijklmnopqrstuvwxyz'. strtoupper('abcdefghijklmnopqrstuvwxyz') );
/*
 * генератор случайной строки
 * @var string $language - ru|en
 * @var number $length - желаемая длинна
 *
 * @var string $method - метод генерации символов
 * all - все доступные символы;
 * symbols - буквы и цифры;
 * chars - только буквы;
 * numbers - только цифры;
 * return string
 */
function randomStringChars( $length=null, $language='en', $method='chars' )
{
    $mixedCharsEn = '';
    $mixedCharsRu = '';

    /* если в параметрах передали только число - то это будет длинна строки */
    /*
    if ( func_num_args() == 1 )
    {
        $arg = func_get_args();
        if ( is_int($arg[0]) ) $length = $arg[0];
    }
    */
    if ( $length === null ) $length = mt_rand(1,10);

    switch ($method)
    {
        case "all":
            $mixedCharsEn = _CHARS_EN_._NUMBERS_._SYMBOLS_;
            $mixedCharsRu = _CHARS_RU_._NUMBERS_._SYMBOLS_;
            break;
        case "symbols":
            $mixedCharsEn = _CHARS_EN_._NUMBERS_;
            $mixedCharsRu = _CHARS_RU_._NUMBERS_;
            break;
        case "chars":
            $mixedCharsEn = _CHARS_EN_;
            $mixedCharsRu = _CHARS_RU_;
            break;
        case "numbers":
            $mixedCharsEn = _NUMBERS_;
            $mixedCharsRu = _NUMBERS_;
            break;
    }
    if (!function_exists('setChars')) {
        function setChars( $chars )
        {
            $characters = $chars;
            $characters = preg_split( '//u', $characters, -1, PREG_SPLIT_NO_EMPTY );
            shuffle( $characters );
            return implode( $characters );
        }
    }
    switch ($language)
    {
        case 'ru':
            $characters = setChars($mixedCharsRu);
            break;
        case 'en':
            $characters = setChars($mixedCharsEn);
            break;
        default:
            $characters = setChars($mixedCharsEn);
            break;
    }
    $str = '';
    if ( !$length ) $length = mt_rand(2, iconv_strlen($characters));
    for ($i = 0; $i < $length; $i++) {
        $str .= mb_substr( $characters, mt_rand(0, iconv_strlen($characters)), 1);
    }
    return $str;
}