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

    define('_vendorFWDIR_', _viewsDIR_.'vendor/');
    define('_coreDIR_', _vendorFWDIR_.'core/');


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
}

_init_();