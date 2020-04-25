<?php
use Views\vendor\core\Autoloader;
use Views\vendor\core\Application;

require_once __DIR__ . '/vendor/libs/def.php';
require_once __DIR__ . '/vendor/libs/functions.php';

require_once _coreDIR_ . "Autoloader.php";
new Autoloader();

$config = require_once __DIR__ . '/vendor/config/config.php';
(new Application( $config ))->run();