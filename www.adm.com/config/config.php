<?php

//日志和错误调试配置
$config['DEBUG']=false;	//是否开启调试模式，true开启，false关闭
$config['LOG_ON']=true;//是否开启出错信息保存到文件，true开启，false不开启
$config['LOG_PATH'] = DATA_PATH . '/' . APP_NAME . '/log/';//出错信息存放的目录，出错信息以天为单位存放，一般不需要修改
$config['ERROR_URL']='';//出错信息重定向页面，为空采用默认的出错页面，一般不需要修改
//日志和错误调试配置结束

//应用配置
//网址配置
$config['URL_REWRITE_ON']=false;//是否开启重写，true开启重写,false关闭重写

//模块配置
$config['MODULE_PATH']= PROJECT_PATH . '/' . APP_NAME . '/module/';//模块存放目录，一般不需要修改
$config['MODULE_SUFFIX']='Mod.class.php';//模块后缀，一般不需要修改
$config['MODULE_INIT']='init.php';//初始程序，一般不需要修改
$config['MODULE_DEFAULT']='index';//默认模块，一般不需要修改
$config['MODULE_EMPTY']='empty';//空模块	，一般不需要修改

$config['MODEL_PATH'] = PROJECT_PATH . '/model/';

//操作配置
$config['ACTION_DEFAULT']='index';//默认操作，一般不需要修改
$config['ACTION_EMPTY']='_empty';//空操作，一般不需要修改

//静态页面缓存
$config['HTML_CACHE_ON']=false;//是否开启静态页面缓存，true开启.false关闭
$config['HTML_CACHE_PATH']= DATA_PATH . '/' . APP_NAME . '/html_cache/';//静态页面缓存目录，一般不需要修改
$config['HTML_CACHE_SUFFIX']='.html';//静态页面缓存后缀，一般不需要修改
$config['HTML_CACHE_RULE']['index']['index']=1000;//缓存时间,单位：秒
//应用配置结束

//数据库配置
$config['DB_TYPE']='mysqli';//数据库类型，一般不需要修改
$config['DB_HOST']='localhost';//数据库主机，一般不需要修改
$config['DB_USER']='root';//数据库用户名
$config['DB_PWD']='ZSO9mYQKxCScWcz4';//数据库密码
$config['DB_PORT']=3306;//数据库端口，mysql默认是3306，一般不需要修改
$config['DB_NAME']='mlyl2019';//数据库名
$config['DB_CHARSET']='utf8mb4';//数据库编码，一般不需要修改
$config['DB_PREFIX']='hl_';//数据库前缀
$config['DB_PCONNECT']=false;//true表示使用永久连接，false表示不适用永久连接，一般不使用永久连接

//从数据库配置
$config['DB_SLAVE'] = array(

);

$config['DB_CACHE_ON']=true;//是否开启数据库缓存，true开启，false不开启
$config['DB_CACHE_PATH'] = DATA_PATH . '/' . APP_NAME . '/db_cache/';//数据库查询内容缓存目录，地址相对于入口文件，一般不需要修改
$config['DB_CACHE_TIME']=0;//缓存时间,0不缓存，-1永久缓存
$config['DB_CACHE_CHECK']=true;//是否对缓存进行校验，一般不需要修改
$config['DB_CACHE_FILE']='cachedata';//缓存的数据文件名
$config['DB_CACHE_SIZE']='15M';//预设的缓存大小，最小为10M，最大为1G
$config['DB_CACHE_FLOCK']=true;//是否存在文件锁，设置为false，将模拟文件锁，一般不需要修改
//数据库配置结束

//Memcached配置
$config['MEM_SERVER'] = array(
	// array('127.0.0.1', 11211)
);
//Redis配置
$config['REDIS_SERVER']= array(
	//'host' => '127.0.0.1',	//REDIS主机
	'host' => 'r-8vbi06kr2hybcszpm6.redis.zhangbei.rds.aliyuncs.com',	//REDIS主机
	'port' => 6379,			//REDIS端口
	//'pwd' => 'quincy1qaz2WSX!@#',
	'pwd' => '',
);

//模板配置
$config['TPL_TEMPLATE_PATH']= PROJECT_PATH . '/' . APP_NAME . '/template/';//模板目录，一般不需要修改
$config['TPL_TEMPLATE_SUFFIX']='.php';//模板后缀，一般不需要修改
$config['TPL_CACHE_ON']=true;//是否开启模板缓存，true开启,false不开启
$config['TPL_CACHE_PATH'] = DATA_PATH . '/' . APP_NAME . '/tpl_cache/';//模板缓存目录，一般不需要修改
$config['TPL_CACHE_SUFFIX']='.php';//模板缓存后缀,一般不需要修改
//模板配置结束

$config['KEY']='hualiao';
$config['HOST']='http://www.xsunjia.com';
$config['IMAGE_PATH']='http://img.mengluyuliao.com';
//$config['IMAGE_PATH']='http://img-mlyl.test.upcdn.net';
$config['UPLOAD_PATH'] = 'http://cdn.mengluyuliao.com';//图文回复中的图片上传
$config['LIMIT']='20';
$config['IOS_USER']=180200; #IOS审核用户
$config['IOS_VERSION']=1; #IOS版本
$config['ANDROID_VERSION']=1; #安卓版本

$config['USER_CACHE_TIMES']=2592000; #用戶信息緩存時長 30天
$config['USER_TOKEN_TIMES']=1296000; #用戶token緩存時長 15天
$config['USER_EXT_TIMES']=604800; #用戶擴展緩存時長 7天
