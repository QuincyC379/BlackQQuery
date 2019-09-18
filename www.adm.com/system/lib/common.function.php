<?php

function build_order_no($user_id)
{
    return date('Ymd').$user_id.mt_rand(100000,999999);
}

//计算经纬度距离
function getDistance($lat1, $lng1, $lat2, $lng2){
	if(!empty($lat1)
		&& !empty($lng1)
		&& !empty($lat2)
		&& !empty($lng2)
		){

		//将角度转为狐度
	    $radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度
	    $radLat2=deg2rad($lat2);
	    $radLng1=deg2rad($lng1);
	    $radLng2=deg2rad($lng2);
	    $a=$radLat1-$radLat2;
	    $b=$radLng1-$radLng2;

	    $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137;
	    return $s;
	}
    return 0;
}

//获取到今晚12点剩余的秒数
function getZeroPoint(){
	return strtotime(date('Y-m-d', strtotime('+1 day')))-time();
}

/**
 *  计算.星座
 *
 * @param int $month 月份
 * @param int $day 日期
 * @return str
 */
function get_constellation($birthday){
	$birthday = strtotime($birthday);
    $day   = date('d', $birthday);
    $month = date('m', $birthday);
    if ($month < 1 || $month > 12 || $day < 1 || $day > 31){
    	return false;
    }
    $signs = array(
        array('20'=>'宝瓶座'),
        array('19'=>'双鱼座'),
        array('21'=>'白羊座'),
        array('20'=>'金牛座'),
        array('21'=>'双子座'),
        array('22'=>'巨蟹座'),
        array('23'=>'狮子座'),
        array('23'=>'处女座'),
        array('23'=>'天秤座'),
        array('24'=>'天蝎座'),
        array('22'=>'射手座'),
        array('22'=>'摩羯座')
    );
    list($start, $name) = each($signs[$month-1]);
    if ($day < $start)
    {
        list($start, $name) = each($signs[($month-2 < 0) ? 11 : $month-2]);
    }
    return $name;
}

//计算年龄
function calcAge($birthday){
    $age = strtotime($birthday);

    list($y1,$m1,$d1) = explode('-', date('Y-m-d', $age));

    list($y2,$m2,$d2) = explode('-', date('Y-m-d'), time());

    $age = $y2 - $y1;
    if((int)($m2.$d2) < (int)($m1.$d1)){
        $age -= 1;
    }

    return $age;
}

function url($module,$action = '',$params = array()){
    $url = '';
    $url = empty($script) ? $_SERVER['SCRIPT_NAME'] : $script;
    $url .= '?m=' . rawurlencode($module).'_'.rawurlencode($action);
    if (is_array($params) && !empty($params)) {
        $url .= '&' . encode_url_args($params);
    }
    return $url;
};

function encode_url_args($args) {
    $str = '';
    foreach ($args as $key => $value) {
        $str .= '&' . rawurlencode($key) . '=' . rawurlencode($value);
    }
    return substr($str, 1);
}

//判断ajax提交
function is_ajax() {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') return true;
    if (isset($_POST['ajax']) || isset($_GET['ajax'])) return true;
    return false;
}

function getUploadUrl($path){
	if(preg_match('/^http/',$path)){
		return $path;
	}
    return cpConfig::get('UPLOAD_PATH').$path;
}

function getMediaUrl($path, $ext = '')
{
	$ext = $ext == '' ? '' : ($ext == '!w30' ? '!w30' : '!w250');		//先强制写成250  by jack 2018-10-28
	if(preg_match('/^http/',$path))
	{
		return $path;
	}

	if(!empty($ext))
	{
		return cpConfig::get('IMAGE_PATH').$path.$ext;
	}
	else
	{
		$temp = explode('.', $path);
		if(in_array(end($temp), array('jpg','png', 'jpeg')))
		{
			return cpConfig::get('IMAGE_PATH').$path.'!w250';
		}
		else
		{
			return cpConfig::get('IMAGE_PATH').$path;
		}
	}
}

function redirect($url, $code=302) {
	header('location:' . $url, true, $code);
	exit;
}

function mdate($time = NULL) {
    $text = '';
    $curTime = time();
    $time = $time === NULL || $time > $curTime ? $curTime : intval($time);
    $t = $curTime - $time; //时间差 （秒）
    $y = date('Y', $time)-date('Y', $curTime);//是否跨年
    switch($t){
		case $t < 60:
			$text = $t . '秒前在线'; // 一分钟内
			break;
		case $t < 60 * 60:
			$text = floor($t / 60) . '分钟前在线'; //一小时内
			break;
		case $t < 60 * 60 * 24:
			$text = floor($t / (60 * 60)) . '小时前在线'; // 一天内
			break;
		case $t < 60 * 60 * 24 * 365 && $y==0:
			$text = date('m-d', $time); //一年内
			break;
		default:
			$text = date('Y-m-d', $time); //一年以前
			break;
    }
    return $text;
}

function http_get_request($url, $param = NULL, $header = NULL) {
	if(!empty($param)) {
		if(strpos($url, "?") ===false) $url .= "?".http_build_query($param);
		else $url .= "&".http_build_query($param);
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	if(is_array($header)) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, TRUE);
	$res = curl_exec( $ch );
	curl_close( $ch );
	return $res;
}

function http_post_request($url, $param = NULL, $header = NULL) {
	if(is_array($param)){
		$param = http_build_query($param);
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	if(is_array($header)) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, TRUE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $param);

	$res = curl_exec( $ch );
	curl_close( $ch );
	return $res;
}


//数据过滤函数库
/*
功能：用来过滤字符串和字符串数组，防止被挂马和sql注入
参数$data，待过滤的字符串或字符串数组，
$force为true，忽略get_magic_quotes_gpc
*/
function in($data,$force=false){
	if(is_string($data)){
		$data=trim(htmlspecialchars($data));//防止被挂马，跨站攻击
		if(($force==true)||(!get_magic_quotes_gpc())) {
		   $data = addslashes($data);//防止sql注入
		}
		return  $data;
	} else if(is_array($data)) {
		foreach($data as $key=>$value){
			 $data[$key]=in($value,$force);
		}
		return $data;
	} else {
		return $data;
	}
}

//用来还原字符串和字符串数组，把已经转义的字符还原回来
function out($data){
	if(is_string($data)){
		return $data = stripslashes($data);
	} else if(is_array($data)){
		foreach($data as $key=>$value){
			 $data[$key]=out($value);
		}
		return $data;
	} else {
		return $data;
	}
}

//文本输入
function text_in($str){
	$str=strip_tags($str,'<br>');
	$str = str_replace(" ", "&nbsp;", $str);
	$str = str_replace("\n", "<br>", $str);
	if(!get_magic_quotes_gpc()) {
  	  $str = addslashes($str);
	}
	return $str;
}

//文本输出
function text_out($str){
	$str = str_replace("&nbsp;", " ", $str);
	$str = str_replace("<br>", "\n", $str);
    $str = stripslashes($str);
	return $str;
}

//html代码输入
function html_in($str){
	$search = array ("'<script[^>]*?>.*?</script>'si",  // 去掉 javascript
					 "'<iframe[^>]*?>.*?</iframe>'si", // 去掉iframe
					);
	$replace = array ("",
					  "",
					);
	$str=@preg_replace ($search, $replace, $str);
	$str=htmlspecialchars($str);
	if(!get_magic_quotes_gpc()) {
		$str = addslashes($str);
	}
   return $str;
}

//html代码输出
function html_out($str){
	if(function_exists('htmlspecialchars_decode'))
		$str=htmlspecialchars_decode($str);
	else
		$str=html_entity_decode($str);

    $str = stripslashes($str);
	return $str;
}

// 获取客户端IP地址
function get_client_ip(){
   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
       $ip = getenv("HTTP_CLIENT_IP");
   else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
       $ip = getenv("HTTP_X_FORWARDED_FOR");
   else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
       $ip = getenv("REMOTE_ADDR");
   else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
       $ip = $_SERVER['REMOTE_ADDR'];
   else
       $ip = "unknown";
   return $ip;
}

//中文字符串截取
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){
	switch($charset){
		case 'utf-8':$char_len=3;break;
		case 'UTF8':$char_len=3;break;
		default:$char_len=2;
	}
	//小于指定长度，直接返回
    if(strlen($str)<=($length*$char_len)){
		return $str;
	}
	if(function_exists("mb_substr")){
	 	$slice= mb_substr($str, $start, $length, $charset);
	} else if(function_exists('iconv_substr')){
        $slice=iconv_substr($str,$start,$length,$charset);
    } else {
	   $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
	}
    if($suffix) return $slice."…";
    return $slice;
}

//检查是否是正确的邮箱地址，是则返回true，否则返回false
function is_email($user_email){
    $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
    if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false){
        if (preg_match($chars, $user_email)){
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}

//模块之间相互调用
function  module($module){
	static $module_obj=array();
	static $config=array();
	if(isset($module_obj[$module])){
		return $module_obj[$module];
	}
	if(!isset($config['MODULE_PATH'])){
		$config['MODULE_PATH']=cpConfig::get('MODULE_PATH');
		$config['MODULE_SUFFIX']=cpConfig::get('MODULE_SUFFIX');
		$suffix_arr=explode('.',$config['MODULE_SUFFIX'],2);
		$config['MODULE_CLASS_SUFFIX']=$suffix_arr[0];
	}
	if(file_exists($config['MODULE_PATH'].$module.$config['MODULE_SUFFIX'])){
		require_once($config['MODULE_PATH'].$module.$config['MODULE_SUFFIX']);//加载模型文件
		$classname=$module.$config['MODULE_CLASS_SUFFIX'];
		if(class_exists($classname)){
			return  $module_obj[$module]=new $classname();
		}
	}else{
		return false;
	}
}

//模型调用函数
if(!function_exists('model')){
    /**
     *
     * @date 2018-07-18 huangweigang 修改  这样调用这个方法产生的对象可以支持代码提示
     * @param $model
     * @return alipayModel|anchorModel|avatarModel|bannerModel|blackModel|channelModel|channelipModel|chatModel|commonModel|dfamapModel|easemobModel|excelModel|exchangeModel|familyModel|fansModel|feedbackModel|filterModel|friendModel|giftModel|goldModel|goldproductModel|hongbaoModel|integralModel|integralproductModel|invite2Model|inviteModel|labelModel|mapModel|messageModel|mmcModel|monitorModel|mygiftModel|neteaseModel|orderModel|photoModel|rankModel|redis2Model|redisModel|sealModel|sendModel|shareModel|smsModel|statisModel|transactionModel|userModel|useripModel|videoModel|vipModel|visitModel|workerModel|wxapiModel|wxpayModel|cpModel|alipayDBTModel|alipayGNModel|alipayXSModel|antipornModel|btltpayModel|hlbpayModel|lockModel|lxpgpayModel|payModel|transactionformModel|wtlpayModel|wxpay2Model|wxpayAZBModel|wxpayHYLModel|wxpayLFModel|wxpayQMSCModel|wxpayRRXModel|wxpayTGHModel|wxpayYMZModel|xwhdpayModel|yeaipayModel
     */
	function  model($model){
		static $model_obj=array();
		static $config=array();
		if(isset($model_obj[$model])){
			return $model_obj[$model];
		}
		if(!isset($config['MODEL_PATH'])){
			$config['MODEL_PATH']=cpConfig::get('MODEL_PATH');
			$config['MODEL_SUFFIX']=cpConfig::get('MODEL_SUFFIX');
			$suffix_arr=explode('.',$config['MODEL_SUFFIX'],2);
			$config['MODEL_CLASS_SUFFIX']=$suffix_arr[0];
		}
		if(file_exists($config['MODEL_PATH'].$model.$config['MODEL_SUFFIX'])){
			require_once($config['MODEL_PATH'].$model.$config['MODEL_SUFFIX']);//加载模型文件
			$classname=$model.$config['MODEL_CLASS_SUFFIX'];
			if(class_exists($classname)){
				return $model_obj[$model]=new $classname();
			}
		}
		return false;
	}
}

// 检查字符串是否是UTF8编码,是返回true,否则返回false
function is_utf8($string){
	if( !empty($string) ) {
		$ret = json_encode( array('code'=>$string) );
		if( $ret=='{"code":null}') {
			return false;
		}
	}
	return true;
}

// 自动转换字符集 支持数组转换
function auto_charset($fContents,$from='gbk',$to='utf-8'){
    $from   =  strtoupper($from)=='UTF8'? 'utf-8':$from;
    $to       =  strtoupper($to)=='UTF8'? 'utf-8':$to;
    if( strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents)) ){
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    if(is_string($fContents) ) {
        if(function_exists('mb_convert_encoding')){
            return mb_convert_encoding ($fContents, $to, $from);
        }elseif(function_exists('iconv')){
            return iconv($from,$to,$fContents);
        }else{
            return $fContents;
        }
    }
    elseif(is_array($fContents)){
        foreach ( $fContents as $key => $val ) {
            $_key =     auto_charset($key,$from,$to);
            $fContents[$_key] = auto_charset($val,$from,$to);
            if($key != $_key )
                unset($fContents[$key]);
        }
        return $fContents;
    }
    else{
        return $fContents;
    }
}

// 浏览器友好的变量输出
function dump($var, $exit=false){
	$output = print_r($var, true);
	$output = "<pre>" . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
	echo $output;
	if($exit) exit();
}

//获取微秒时间，常用于计算程序的运行时间
function utime(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

//生成唯一的值
function cp_uniqid(){
	return md5(uniqid(rand(), true));
}

/**
 * 返回指定长度的随机字符串
 * @param int $length 长度
 * @return string
 */
function randStr($length) {
	$str = str_pad('', $length, '0');
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$max = 35;
	for ($i = 0; $i < $length; $i++) {
		$str[$i] = $chars[mt_rand(0, $max)];
	}
	return $str;
}

//加密函数，可用cp_decode()函数解密，$data：待加密的字符串或数组；$key：密钥；$expire 过期时间
function cp_encode($data, $key='', $expire = 0)
{
	$string=serialize($data);
	$ckey_length = 4;
	$key = md5($key);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = substr(md5(microtime()), -$ckey_length);

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string =  sprintf('%010d', $expire ? $expire + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++)
	{
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++)
	{
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++)
	{
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	return $keyc.str_replace('=', '', base64_encode($result));
}
//cp_encode之后的解密函数，$string待解密的字符串，$key，密钥
function cp_decode($string,$key='')
{
	$ckey_length = 4;
	$key = md5($key);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = substr($string, 0, $ckey_length);

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string =  base64_decode(substr($string, $ckey_length));
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++)
	{
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++)
	{
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++)
	{
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
		return unserialize(substr($result, 26));
	}
	else
	{
		return '';
	}
}

//遍历删除目录和目录下所有文件
function del_dir($dir){
	if (!is_dir($dir)){
		return false;
	}
	$handle = opendir($dir);
	while (($file = readdir($handle)) !== false){
		if ($file != "." && $file != ".."){
			is_dir("$dir/$file")?	del_dir("$dir/$file"):@unlink("$dir/$file");
		}
	}
	if (readdir($handle) == false){
		closedir($handle);
		@rmdir($dir);
	}
}

//如果json_encode没有定义，则定义json_encode函数，常用于返回ajax数据
if (!function_exists('json_encode')) {
     function format_json_value(&$value){
        if(is_bool($value)) {
            $value = $value?'true':'false';
        }else if(is_int($value)){
            $value = intval($value);
        }else if(is_float($value)){
            $value = floatval($value);
        }else if(defined($value) && $value === null){
            $value = strval(constant($value));
        }else if(is_string($value)){
            $value = '"'.addslashes($value).'"';
        }
        return $value;
    }

    function json_encode($data){
        if(is_object($data)){
            //对象转换成数组
            $data = get_object_vars($data);
        }else if(!is_array($data)) {
            // 普通格式直接输出
            return format_json_value($data);
        }
        // 判断是否关联数组
        if(empty($data) || is_numeric(implode('',array_keys($data)))) {
            $assoc  =  false;
        }else {
            $assoc  =  true;
        }
        // 组装 Json字符串
        $json = $assoc ? '{' : '[' ;
        foreach($data as $key=>$val) {
            if(!is_null($val)) {
                if($assoc){
                    $json .= "\"$key\":".json_encode($val).",";
                }else{
                    $json .= json_encode($val).",";
                }
            }
        }
        if(strlen($json)>1) {// 加上判断 防止空数组
            $json  = substr($json,0,-1);
        }
        $json .= $assoc ? '}' : ']' ;
        return $json;
    }
}

//POST表单处理函数,$post_array:POST的数据,$null_value:是否删除空表单,$delete_value:删除指定表单
function postinput($post_array,$null_value = null,$delete_value = array()){
        //清除值为空或者为0的元素
        if($null_value){
            foreach($post_array as $key=>$value){
                $value = in($value);
                if($value == ''){
                    unset($post_array[$key]);
                }
            }
        }
        //清除不需要的元素
        $default_value = array('action','button','fid','submit');
        $clear_array = array_merge($default_value,$delete_value);
        foreach($post_array as $key=>$value){
                if(in_array($key,$clear_array)){
                    unset($post_array[$key]);
                }
        }
        return $post_array;
}

//获取datetime格式的日期间隔
function intervalDays($date1, $date2)
{
    $d = (strtotime($date2) - strtotime($date1)) / (3600*24);

    return $d;
}

// 获取pdo链接
function getPdo()
{
    try {
        $dsn = "mysql:host=" . cpConfig::get('DB_HOST') . ";port=" . cpConfig::get('DB_PORT') . ';dbname=' . cpConfig::get('DB_NAME');
        $pdo = new PDO($dsn, cpConfig::get('DB_USER'), cpConfig::get('DB_PWD'));
        $pdo->query('set names utf8;');
        return $pdo;
    }catch (PDOException $e){
        echo 'Connection failed: ' . $e->getMessage();
    }
}