<?php

//日志和错误调试配置
$config['DEBUG'] = false;    //是否开启调试模式，true开启，false关闭
$config['LOG_ON'] = true;//是否开启出错信息保存到文件，true开启，false不开启
$config['LOG_PATH'] = DATA_PATH . '/' . APP_NAME . '/log/';//出错信息存放的目录，出错信息以天为单位存放，一般不需要修改
$config['ERROR_URL'] = '';//出错信息重定向页面，为空采用默认的出错页面，一般不需要修改
//日志和错误调试配置结束

//应用配置
//网址配置
$config['URL_REWRITE_ON'] = false;//是否开启重写，true开启重写,false关闭重写

//模块配置
$config['MODULE_PATH'] = PROJECT_PATH . '/' . APP_NAME . '/module/';//模块存放目录，一般不需要修改
$config['MODULE_SUFFIX'] = 'Mod.class.php';//模块后缀，一般不需要修改
// $config['MODULE_INIT']='init.php';//初始程序，一般不需要修改
$config['MODULE_DEFAULT'] = 'index';//默认模块，一般不需要修改
$config['MODULE_EMPTY'] = 'empty';//空模块	，一般不需要修改

$config['MODEL_PATH'] = PROJECT_PATH . '/model/';

//操作配置
$config['ACTION_DEFAULT'] = 'index';//默认操作，一般不需要修改
$config['ACTION_EMPTY'] = '_empty';//空操作，一般不需要修改

//数据库配置
$config['DB_TYPE'] = 'mysqli';//数据库类型，一般不需要修改
$config['DB_HOST'] = '127.0.0.1';//数据库主机，一般不需要修改
$config['DB_USER'] = 'root';//数据库用户名
$config['DB_PWD'] = 'ZSO9mYQKxCScWcz4';//数据库密码
$config['DB_PORT'] = 3306;//数据库端口，mysql默认是3306，一般不需要修改
$config['DB_NAME'] = 'mlyl2019';//数据库名
$config['DB_CHARSET'] = 'utf8mb4';//数据库编码，一般不需要修改
$config['DB_PREFIX'] = 'hl_';//数据库前缀
$config['DB_PCONNECT'] = false;//true表示使用永久连接，false表示不适用永久连接，一般不使用永久连接

//从数据库配置
$config['DB_SLAVE'] = array(
    // array(
    //     'DB_HOST' => 'localhost',
    //     'DB_USER' => 'weiqi',
    //     'DB_PWD' => 'jc8YpYF5DwSP77N9',
    // ),
    // array(
    //    'DB_HOST' => '112.74.17.203',
    //    'DB_USER' => 'root',
    //    'DB_PWD' => '123456',
    // )
);

//Memcached配置
// $config['MEM_SERVER'] = array(
// 	array('127.0.0.1', 11211)
// );

//Redis配置
$config['REDIS_SERVER'] = array(
    'host' => 'r-8vbi06kr2hybcszpm6.redis.zhangbei.rds.aliyuncs.com',    //REDIS主机
    'port' => 6379,            //REDIS端口
    'pwd' => '',
);

$config['KEY'] = 'hualiao';
$config['HOST'] = 'http://www.xsunjia.com';
$config['IMAGE_PATH']='http://img.mengluyuliao.com';
//$config['IMAGE_PATH'] = 'http://img-mlyl.test.upcdn.net';
$config['UPLOAD_PATH'] = 'http://cdn.mengluyuliao.com';//图文回复中的图片上传
$config['LIMIT'] = '20';
$config['IOS_USER'] = 180200; #IOS审核用户
$config['IOS_VERSION'] = 1; #IOS版本
$config['ANDROID_VERSION'] = 1; #安卓版本

$config['USER_CACHE_TIMES'] = 2592000; #安卓版本#用戶信息緩存時長 30天
$config['USER_TOKEN_TIMES'] = 1296000; #安卓版本#用戶token緩存時長 15天
$config['USER_EXT_TIMES'] = 86400; #安卓版本#用戶擴展緩存時長 1天
