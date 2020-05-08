<?php
use Views\vendor\core\Autoloader;
use Views\vendor\core\Application;

require_once __DIR__ . '/vendor/config/def.php';
require_once __DIR__ . '/vendor/libs/functions.php';

require_once _coreDIR_ . "core/Autoloader.php";
new Autoloader();

require_once _vendorDIR_ . "autoload.php";

$config = require_once __DIR__ . '/vendor/config/config.php';
(new Application( $config ))->run();