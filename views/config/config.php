<?php

return [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'uploadPath' => '/uploads',
    'cachePath' => '/runtime/cache',
    'layout' => 'baseTheme',
    'baseController' => 'main',
    'multiLanguage'=> [
        'enable' => false, // false - disable
        //'language' => require_once 'languages.php', // список доступных языков
        'default' => 'ru',
    ],
    'version' => '2.002b',
    'dataCompression' => true,

    /**
     *  mode
     *  0 - продакшн Без E_NOTICE,
     *  1 - продакшн Без E_NOTICE и E_Warning,
     *  2 - DEV all Errors,
     *  3 - DEV без E_NOTICE,
     */
    'errors' => [
        'enable' => true, // включает перехват ошибок фреймворком DTW.  false - отключает
        'logs'   => '/runtime/logs', // false - отключает логи
        'mode'   => _DEV_MODE_ ? 3 : 1,
    ],
    'csrf' => false, // валидация данных для форм и JS
    'classes' => [  // подключаемые классы
        'cache' => 'dtw\Cache',
        'validator' => 'libs\valitron\src\Validator',
    ],
    'modules' => [
        'admin' => [
            'alias' => 'admin', // alias namespaces for module admin
            'defaultController' => 'user', // default route
            'layout' => 'admin',  // default layout
        ],
    ],
    'db' => require_once "db_config.php",
    'libraries' => [
        'jquery' => true,
        'bootstrap' => 'bootstrap3',
        'fontAwesome' => true,
    ],
    'css' => [
        'css/stylesTest.css?ver='.time(),
        'css/style.css?ver=105',
    ],
    'js' => [
        'js/scrpt.js?ver='.time(),
    ],
    'jsOptions' => [
        'position' => 'endBody',
    ],
    //'alias' => require_once 'aliases.php',
];