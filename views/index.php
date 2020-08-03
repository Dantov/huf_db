<?php
use Views\vendor\core\Autoloader;
use Views\vendor\core\Application;

if (!defined( '_DEV_MODE_') ) define('_DEV_MODE_', true);
if (!defined( '_WORK_PLACE_') ) define('_WORK_PLACE_', false); // true - работа false - дом

require_once __DIR__ . '/config/defines.php';
require_once __DIR__ . '/vendor/libs/functions.php';

require_once _coreDIR_ . "core/Autoloader.php";
new Autoloader();

require_once _vendorDIR_ . "autoload.php";

$config = require_once _CONFIG_ . 'config.php';
(new Application( $config ))->run();