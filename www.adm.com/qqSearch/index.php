<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

define('PROJECT_PATH', dirname((dirname(__FILE__))));
define('DS', DIRECTORY_SEPARATOR);
define('LIBRARY_PATH', PROJECT_PATH . DS . 'system');
define('DATA_PATH', PROJECT_PATH . DS . 'data');
define('APP_NAME', 'qqSearch');

require(PROJECT_PATH . DS . 'config' . DS . 'config.php');//加载配置
require(LIBRARY_PATH . DS . 'core' . DS . 'cpApp.class.php');//加载应用控制类

$app=new cpApp($config);//实例化单一入口应用控制类
//执行项目
$app->run();
