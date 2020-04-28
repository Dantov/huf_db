<?php

return [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'uploadPath' => '/uploads',
    'cachePath' => '/runtime/cache',
    'layout' => 'baseTheme',
    'baseController' => 'Main',
    'multiLanguage'=> [
        'enable' => true, // false - disable
        //'language' => require_once 'languages.php', // список доступных языков
        'default' => 'en',
    ],
    'version' => '1001',
    'dataCompression' => true,
    'errors' => [
        'enable' => true, // включает перехват ошибок фреймворком DTW.  false - отключает
        'logs'   => '/runtime/logs', // false - отключает логи
        'mode'   => 2, // 1 - показ все ошибки, 0 - не показ. ошибкии, 2 - показ. нотации в жопу
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
    'db' => [
        'dsn' => 'localhost',
        'dbname' => 'dtv_fw_test',
        'username' => 'adm_test',
        'password' => 'V7L0QJk3YOHvMqnC',
        'charset' => 'utf8',
    ],
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