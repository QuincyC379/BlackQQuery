<?php
//cp错误类
class cpError extends Exception {
    private $errorFile = '';
    private $errorLine = 0;
    private $errorCode = '';
	private $errorLevel = '';
 	private $trace = '';

    public function __construct($errorMessage, $errorCode = 0, $errorFile = '', $errorLine = 0) {
        parent::__construct($errorMessage, $errorCode);
		$this->errorCode = $errorCode == 0 ? $this->getCode() : $errorCode;
        $this->errorFile = $errorFile == '' ? $this->getFile() : $errorFile;
        $this->errorLine = $errorLine == 0 ? $this->getLine() : $errorLine;
      	$this->errorLevel = $this->getLevel();
 	    $this->trace = $this->trace();
        $this->showError();
    }

	//获取trace信息
	protected function trace() {
        $trace = $this->getTrace();
        $traceInfo='';
        $time = date("Y-m-d H:i:s");
        foreach($trace as $t) {
            $traceInfo .= '['.$time.'] ' . $t['file'] . ' (' . $t['line'] . ') ';
            $traceInfo .= $t['class'] . $t['type'] . $t['function'] . '(';
            $traceInfo .= ")<br />\r\n";
        }
		return $traceInfo ;
    }

	//错误等级
	protected function getLevel() {
	  	$Level_array = array(
	  		1 		=> '致命错误(E_ERROR)',
			2 		=> '警告(E_WARNING)',
			4 		=> '语法解析错误(E_PARSE)',
			8 		=> '提示(E_NOTICE)',
			16 		=> 'E_CORE_ERROR',
			32 		=> 'E_CORE_WARNING',
			64 		=> '编译错误(E_COMPILE_ERROR)',
			128 	=> '编译警告(E_COMPILE_WARNING)',
			256 	=> '致命错误(E_USER_ERROR)',
			512 	=> '警告(E_USER_WARNING)',
			1024	=> '提示(E_USER_NOTICE)',
			2047	=> 'E_ALL',
			2048	=> 'E_STRICT'
		);
		return isset( $Level_array[$this->errorCode] ) ? $Level_array[$this->errorCode] : $this->errorCode;
	}

	//抛出错误信息，用于外部调用
	static public function show($message="") {
		new cpError($message);
    }

	//记录错误信息
	static public function write($message){
		$log_path = cpConfig::get('LOG_PATH');

		//检查日志记录目录是否存在
		if( !is_dir($log_path) ) {
			//创建日志记录目录
			@mkdir($log_path, 0777, true);
		}

		$time=date('Y-m-d H:i:s');
		$ip= function_exists('get_client_ip') ? get_client_ip() : $_SERVER['REMOTE_ADDR'];
		$destination =$log_path  . date('Y-m-d_') . md5($log_path). '.log';

		//写入文件，记录错误信息
		@error_log("{$time} | {$ip} | {$_SERVER['REQUEST_URI']} |{$message}\r\n", 3,$destination);
	}

	static public function log($message, $file)
	{
		$log_path = cpConfig::get('LOG_PATH');
		//检查日志记录目录是否存在
		if( !is_dir($log_path) )
		{
			//创建日志记录目录
			@mkdir($log_path, 0777, true);
		}

		$time=date('Y-m-d H:i:s');
		$ip= function_exists('get_client_ip') ? get_client_ip() : $_SERVER['REMOTE_ADDR'];
		$destination =$log_path  . $file .'_'. date('Ymd'). '.log';

		//写入文件，记录错误信息
		@error_log("{$time} | {$ip} | {$message}\r\n", 3, $destination);
	}

	//输出错误信息
    protected function showError(){
		//如果开启了日志记录，则写入日志
		if( cpConfig::get('LOG_ON') ) {
			self::write($this->message);
		}

		$error_url = cpConfig::get('ERROR_URL');
		//错误页面重定向
		if($error_url != ''){
			echo '<script language="javascript">
				if(self!=top){
				  parent.location.href="'.$error_url.'";
				} else {
				 window.location.href="'.$error_url.'";
				}
				</script>';
			exit;
		}

		if( defined('DEBUG') && false == DEBUG) {
			@header('HTTP/1.1 404 Not Found');
			exit;
		}

		echo  '<!DOCTYPE html>
				<html>
				<head>
					<meta charset="utf-8">
					<meta name="viewport" content="width=device-width,target-densitydpi=high-dpi,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
					<title>错误提示</title>
					<style>body{font-family: verdana, arial, helvetica, sans-serif; color: #333; background:#fff; font-size: 12px;padding:0px;margin:0px;}a{text-decoration:none;color:#3071bf}a:hover{text-decoration:underline;}.error_title{border-bottom:1px #eee solid;font-size:20px;line-height:28px; height:28px;font-weight:600}.error_box{border-left:3px solid #fc0;font-size:14px; line-height:22px; padding:6px 15px;background:#ffe}.error_tip{margin-top:15px;padding:6px;font-size:12px;padding-left:15px;background:#f7f7f7}</style>
				</head>
				<body>
					<div style="margin:30px auto; width:100%; max-width:800px; min-width:320px;">
						<div class="error_title">错误提示</div>
						<div style="height:10px"></div>
						<div class="error_box">出错信息：'.$this->message.'</div>';
						//开启调试模式之后，显示详细信息
						if(cpConfig::get('DEBUG')) {
						 	echo '<div class="error_box">出错文件：'.$this->errorFile.'</div>
								<div class="error_box">错误行：'.$this->errorLine.'</div>
								<div class="error_box">错误级别：'.$this->errorLevel.'</div>
								<div class="error_box">Trace信息：<br>'.$this->trace.'</div>';
						}
				echo '</div>
				</body>
				</html>';
		exit;
    }
}