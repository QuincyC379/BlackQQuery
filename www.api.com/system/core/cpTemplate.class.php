<?php

class cpTemplate
{
    public $config = array(); //配置
    protected $vars = array();//存放变量信息

    public function __construct($config = array())
    {
        $this->config = array_merge(cpConfig::get('TPL'), (array)$config);//参数配置
        //$this->assign('cpTemplate', $this);
    }

    //模板赋值
    public function assign($name, $value = '')
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->vars[$k] = $v;
            }
        } else {
            $this->vars[$name] = $value;
        }
    }

    //执行模板解析输出
    public function display($tpl = '', $return = false, $is_tpl = true)
    {
        //如果没有设置模板，则调用当前模块的当前操作模板
        if ($is_tpl && ($tpl == "") && (!empty($_GET['_module'])) && (!empty($_GET['_action']))) {
            $tpl = $_GET['_module'] . "/" . $_GET['_action'];
        }

        $tplFile = $this->config['TPL_TEMPLATE_PATH'] . $tpl . $this->config['TPL_TEMPLATE_SUFFIX'];
        if (!file_exists($tplFile)) {
            throw new Exception($tplFile . "模板文件不存在");
        }

        extract($this->vars);
        ob_start();
        include($tplFile);
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
    }

}