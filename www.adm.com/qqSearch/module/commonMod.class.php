<?php
class commonMod{
	protected $model = NULL; //数据库模型
	protected $layout = NULL; //布局视图
	protected $config = array();
	protected $viewData = array();
	protected $channel = array();
	private $_data = array();


	protected function init(){

		//正确提示信息
		$action_msg = $_SESSION['action_msg'];
		if(!empty($action_msg)) {
			$this->assign('action_msg', $action_msg);
			unset($_SESSION['action_msg']);
		}
		//错误提示信息
		$error_msg = $_SESSION['error_msg'];
		if(!empty($error_msg)) {
			$this->assign('error_msg', $error_msg);
			unset($_SESSION['error_msg']);
		}
	}

	public function __construct(){
		global $config;
        @setcookie('HUALIAO', time(), time() + 3600);
        @session_start();
		$this->config = $config;
		$this->model = self::initModel( $this->config );
		$this->channel = $_SESSION['channel'];

        if($_GET['_module'] != 'invite')
        {
            $this->check_login();
        }
		$this->init();
	}

	//初始化模型
	static public function initModel($config){
		static $model = NULL;
		if( empty($model) ){
			$model = new cpModel($config);
		}
		return $model;
	}

	public function __get($name){
		return isset( $this->_data[$name] ) ? $this->_data[$name] : NULL;
	}

	public function __set($name, $value){
		$this->_data[$name] = $value;
	}

	//获取模板对象
	public function view(){
		static $view = NULL;
		if( empty($view) ){
			$view = new cpTemplate( $this->config );
		}
		return $view;
	}

	//模板赋值
	protected function assign($name, $value){
		return $this->view()->assign($name, $value);
	}

	//模板显示
	protected function display($tpl = '', $return = false, $is_tpl = true ){
		if( $is_tpl ){
			$tpl = empty($tpl) ? $_GET['_module'] . '_'. $_GET['_action'] : $tpl;
			if( $is_tpl && $this->layout ){
				$this->__template_file = $tpl;
				$tpl = $this->layout;
			}
		}

		$this->assign('skin', __ROOT__.'/template/skin/');
		$this->assign('title', '欢迎');
		$this->assign('channel', $this->channel);
		$this->view()->assign( $this->viewData );
		$this->view()->assign( $this->_data );
		return $this->view()->display($tpl, $return, $is_tpl);
	}

	//判断是否是数据提交
	protected function isPost(){
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	public function msg($message,$status = 1,$url = array()) {
        if (is_ajax()){
            exit(json_encode(array('status' => $status, 'message' => $message)));
        }else{
        	if($status == 1 ){
        		$_SESSION['action_msg'] = $message;
        	}else{
        		$_SESSION['error_msg'] = $message;
        	}
        	if(!empty($url)){
        		redirect(url($url[0],$url[1],$url[2]));
        	}
        }
    }

	//登录检测
	protected function check_login(){
		if($_GET['_module']=='login'){
            return;
        }
        if(empty($this->channel)){
        	redirect(url('login','index'));
        }

        $channel = model('common')->_find('user_login', array('user_id'=>$this->channel['user_id']));

        if(empty($channel)){
        	redirect(url('login','index'));
        }

        if($channel['status'] == '0'){
        	redirect(url('login','index'));
        }
        $this->channel = $channel;
	}
}
