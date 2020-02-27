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
            'mode'   => _DEV_MODE_ ? 3 : 1,
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