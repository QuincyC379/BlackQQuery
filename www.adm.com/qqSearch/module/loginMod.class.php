<?php
class loginMod extends commonMod{

	//用户登录页面
	public function index(){
		$this->display();
	}

	public function loginSubmit(){
		$msg = Check::rule(
            array(check::must($_POST['username']), '请输入用户名'),
            array(check::must($_POST['password']), '请输入密码')
        );
        if($msg!==true)
        {
           $this->msg($msg, '-1');
        }

        $channel = model('common')->_find('user_login', array('username'=>$_POST['username']));
        if(empty($channel)){
        	$this->msg('登录失败,用户不存在', '-1');
        }

        if($channel['password'] != $_POST['password']){
            $this->msg('登录失败! 密码错误!', '-1');
        }
        if($channel['status'] == '0'){
            $this->msg('登录失败! 帐号已被禁用!', '-1');
        }

        if(!empty($channel['channel_id'])){
            $parentInfo = model('common')->_find('channel', array('channel_id'=>$channel['parent_id']));
            if($parentInfo['channel_status'] == '0'){
                $this->msg('登录失败! 上级渠道已禁用!', '-1');
            }
        }

        //更新帐号信息
        $data['login_time'] = date('Y-m-d H:i:s');
        $data['login_ip'] = get_client_ip();
        model('common')->_update('user_login', array('user_id'=>$channel['user_id']), $data);

        //设置登录信息
        $_SESSION['channel'] = $channel;
        $this->msg('登录成功!',1);
	}

    public function logout(){
        session_unset();
        session_destroy();
        redirect(url('login','index'));
    }
}
