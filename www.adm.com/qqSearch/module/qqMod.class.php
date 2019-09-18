<?php

class qqMod extends commonMod
{
    protected $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = model('common');
    }

    public function add()
    {
        $data = array(
            'qudao' => true,
            'htitle' => '添加管理',
            'nav' => array(
                '添加管理' => '',
                '添加' => ''
            )
        );

        $this->viewData = $data;
        $this->display();
    }

    public function ajaxAdd()
    {


        $temp = model('common')->_find('black_qq', array('qq' => $_POST['qq']));

        if (!empty($temp)) {
            $this->msg('此QQ账号已存在,请重试！', '-1');
        }
        $bean = array(

            'qq' => $_POST['qq'],
            'tel' => $_POST['phone'],
            'wx' => $_POST['wx'],
            'ali' => $_POST['ali'],
            'remark' => $_POST['remark'],
            'create_time' => time(),
            'is_del' => 1,
        );

        if (model('common')->_insert('black_qq', $bean)) {
            $this->msg('添加成功');
        } else {
            $this->msg('添加失败', '-1');
        }
    }

    public function ajaxEdit()
    {

        $info = model('common')->_find('black_qq', array('qq' => $_POST['qq']));
        if (empty($info)) {
            $this->msg('不存在此内容', '-1');
        }
        $bean = array(

            'qq' => $_POST['qq'],
            'tel' => $_POST['tel'],
            'wx' => $_POST['wx'],
            'ali' => $_POST['ali'],
            'remark' => $_POST['remark'],
            'create_time' => time(),
        );

        if (model('common')->_update('black_qq', array('id' => $info['id']), $bean)) {
            $this->msg('修改成功');
        } else {
            $this->msg('修改失败，请重试！', '-1');
        }
    }

    public function detailstatic()
    {

        $data = array(
            'static' => true,
            'htitle' => '查看数据',
            'nav' => array(
                '数据统计' => '',
                '查看数据' => '',
            )
        );

        $page = intval($_GET['page']) <= 0 ? 1 : intval($_GET['page']);
        $pagesize = 20;
        $limit = ($page - 1) * $pagesize . ',' . $pagesize;

        $data['list'] = array();
        $count = 0;
        $date = date('Y-m-d');
        $where = '1=1';

        $sql = "SELECT id,qq,tel,wx,ali,remark,create_time,is_del FROM hl_black_qq WHERE " . $where . " ORDER BY create_time DESC LIMIT " . $limit;
        $data['list'] = $this->model->_query($sql);


        $data['date'] = $date;
        $data['page'] = $page;
        $data['count'] = ceil($count / $pagesize);
        $data['pageurl'] = url('qq', 'detailstatic');
        $this->viewData = $data;
        $this->display();
    }

    public function password()
    {
        $this->display();
    }

    public function ajaxUpdateUserPass()
    {
        if ($_POST['surepass'] != $_POST['newpass']) {
            $this->msg('新密码和确认密码不一样', -1);
        }

        if ($this->channel['password'] != $_POST['oldpass']) {
            $this->msg('旧密码不正确', -1);
        }

        if ($this->model->_update('user_login', array('user_id' => $this->channel['user_id']), array('password' => $_POST['newpass'])) !== false) {
            $this->msg('密码修改成功，需退出重新登录');
        }

        $this->msg('密码修改失败', -1);
    }

    //QQ号删除
    public function ajaxDelete()
    {
        $temp = model('common')->_find('black_qq', array('id' => $_POST['cpid']));

        if (empty($temp)) {
            $this->msg('暂无此号的任何记录！', '-1');
        } else {
            $bean = array(
                'is_del' => 0
            );
            model('common')->_update('black_qq', array('id' => $_POST['cpid']), $bean);
            $this->msg('删除成功!');
        }
    }

    //QQ编辑
    public function edit()
    {

        $info = model('common')->_find('black_qq', array('id'=>intval($_GET['id'])));
        if(empty($info)){
            $this->msg('不存在此内容','-1');
            redirect(url('index'));
        }

        $data = array(
            'static' => true,
            'htitle' => '编辑数据',
            'nav' => array(
                '数据处理' => '',
                '编辑数据' => '',
            )
        );

        $this->assign('info', $info);
        $this->viewData = $data;
        $this->display();
    }

}
