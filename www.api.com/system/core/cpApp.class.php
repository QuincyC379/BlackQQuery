<?php

//应用控制类(完成网址解析、单一入口控制、静态页面缓存功能)
class cpApp
{
    static public $module;//模块名称
    static public $action;//操作名称
    private $appConfig = array(); //配置

    public function __construct($config = array())
    {

        define('CP_CORE_PATH', dirname(__FILE__));//当前文件所在的目录

        require(CP_CORE_PATH . '/cpConfig.class.php');//加载默认配置
        $this->appConfig = array_merge(cpConfig::get('APP'), $config);//参数配置
        cpConfig::set('APP', $this->appConfig);
        defined('DEBUG') or define('DEBUG', cpConfig::get('DEBUG'));
        date_default_timezone_set("Asia/Shanghai");

        if ($this->appConfig['DEBUG']) {
            ini_set('display_errors', 1);
            error_reporting(E_ALL ^ E_NOTICE);//除了notice提示，其他类型的错误都报告
        } else {
            ini_set('display_errors', 0);
            error_reporting(0);//把错误报告，全部屏蔽
        }

        //注册类的自动加载
        spl_autoload_register(array($this, 'autoload'));

        //加载错误处理类
        require(CP_CORE_PATH . '/cpError.class.php');

        //加载常用函数库
        require(CP_CORE_PATH . '/../lib/common.function.php');
    }

    //执行模块，单一入口控制核心
    public function run()
    {
        try {
            //网址解析
            $m = empty($_GET['m']) ? 'index_index' : $_GET['m'];
            $command = explode('_', preg_replace('/[^a-z0-9_.@]+/i', '', $m));

            //模块或操作为空，则设置默认值
            self::$module = empty($command[0]) ? $this->appConfig['MODULE_DEFAULT'] : $command[0];
            self::$action = empty($command[1]) ? $this->appConfig['ACTION_DEFAULT'] : $command[1];

            //在其他页面通过$_GET['_module']和$_GET['_action']获取得到当前的模块和操作名
            $_GET['_module'] = self::$module;
            $_GET['_action'] = self::$action;

            //如果存在初始程序，则先加载初始程序
            // if ( file_exists( $this->appConfig['MODULE_PATH'] . $this->appConfig['MODULE_INIT']) ) {
            // 	require( $this->appConfig['MODULE_PATH'] . $this->appConfig['MODULE_INIT'] );
            // }

            $this->_define();//常量定义

            //检查指定模块是否存在
            if (preg_match('#^[a-z0-9_]+$#i', self::$module) && $this->_checkModuleExists(self::$module)) {
                $module = self::$module;
            } else if ($this->_checkModuleExists($this->appConfig['MODULE_EMPTY'])) {
                //如果指定模块不存在，则检查是否存在空模块
                $module = $this->appConfig['MODULE_EMPTY'];
            } else {
                throw new Exception(self::$module . '模块不存在');//指定模块和空模块都不存在，则显示出错信息，并退出程序。
            }

            //执行模块操作
            $this->_execute($module);

            //如果存在回调函数cp_app_end，则在即将结束前调用
            // if ( function_exists('cp_app_end') ) {
            // 	cp_app_end();
            // }
        } catch (Exception $e) {
            cpError::show($e->getMessage());
        }
    }

    //常量定义
    private function _define()
    {
        $root = $this->appConfig['URL_HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
        //__ROOT__和__PUBLIC__常用于图片，css，js定位，__APP__和__URL__常用于网址定位

        define('__ROOT__', substr($root, 0, -1));//当前入口所在的目录，后面不带 "/"
        //define('__TPL__', __ROOT__ . '/' . 'template');

        //如果开启了重写，则网址不包含入口文件名，如index.php
        if ($this->appConfig['URL_REWRITE_ON']) {
            define('__APP__', __ROOT__);
        } else {
            define('__APP__', __ROOT__ . '/' . basename($_SERVER['SCRIPT_NAME']));//当前入口文件
        }
        define('__URL__', __APP__ . '/' . self::$module);//当前模块
    }

    //检查模块文件是否存在
    private function _checkModuleExists($module)
    {
        if (file_exists($this->appConfig['MODULE_PATH'] . $module . $this->appConfig['MODULE_SUFFIX'])) {
            require_once($this->appConfig['MODULE_PATH'] . $module . $this->appConfig['MODULE_SUFFIX']);//加载模块文件
            return true;
        } else {
            return false;
        }
    }

    //执行操作
    private function _execute($module)
    {
        $suffix_arr = explode('.', $this->appConfig['MODULE_SUFFIX'], 2);
        $classname = $module . $suffix_arr[0];//模块名+模块后缀组成完整类名

        if (!class_exists($classname)) {
            throw new Exception($classname . "类未定义");
        }

        $object = new $classname();//实例化模块对象
        //类和方法同名，直接返回，因为跟类同名的方法会当成构造函数，已经被调用，不需要再次调用
        if ($classname == self::$action) {
            return true;
        }

        if (method_exists($object, self::$action)) {
            $action = self::$action;
        } else if (method_exists($object, $this->appConfig['ACTION_EMPTY'])) {
            $action = $this->appConfig['ACTION_EMPTY'];
            //解决空操作的静态页面缓存读取
            // if( $this->_readHtmlCache($module, $action) ) {
            // 	return true;
            // }
        } else {
            throw new Exception(self::$action . '操作方法在' . $module . '模块中不存在');
        }
        //执行指定模块的指定操作
        $object->$action();

        //如果缓存开启，写入静态缓存，只有符合规则的，才会创建缓存
        //$this->_writeHtmlCache();
    }

    //实现类的自动加载
    public function autoload($classname)
    {
        //判断是否命名空间引用
        if (strpos($classname, '\\') !== false) {
            $file = CP_CORE_PATH . '/../lib/' . str_replace('\\', '/', $classname) . '.php';
            if (file_exists($file)) {
                require_once($file);
            }
        } else {
            $dir_array = array($this->appConfig['MODULE_PATH'],    //模块文件
                CP_CORE_PATH . '/../lib/',    //官方扩展库
                CP_CORE_PATH . '/',    //核心文件
                $this->appConfig['MODEL_PATH'],    //模型文件
            );
            //$dir_array = array_merge($dir_array, $this->appConfig['AUTOLOAD_DIR']);
            foreach ($dir_array as $dir) {
                $file = $dir . $classname . '.class.php';
                if (is_file($file)) {
                    require_once($file);
                    return true;
                }
            }
        }

        return false;
    }
}