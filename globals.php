<?php
//ini_set('display_errors', 1);
error_reporting(E_ALL);
define('ROOT_PATH', realpath(dirname(__FILE__)));

require_once 'config/config.php';
require_once 'source/Utils/AutoLoad.class.php';
$autoload = new \Utils\Autoload();
$autoload->addNameSpaceMap(ROOT_PATH .'/source');
\Utils\Config::init($config);
$DB = new \Utils\DB(\Utils\Config::get('mysql'));
