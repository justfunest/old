<?php

namespace Test;

define('ROOT_PATH', './');
require_once ROOT_PATH . 'config/config.php';
require_once ROOT_PATH . 'source/Utils/AutoLoad.class.php';
$autoload = new \Utils\Autoload();
$autoload->addNameSpaceMap(ROOT_PATH .'/unittest');
\Utils\Config::init($config);
