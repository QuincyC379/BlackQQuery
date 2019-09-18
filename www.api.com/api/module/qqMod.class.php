<?php

// 制定允许其他域名访问
header("Access-Control-Allow-Origin:*");
// 响应类型
header('Access-Control-Allow-Methods:POST,GET,OPTIONS');


class qqMod extends commonMod
{


    public function query()
    {

        $msg = Check::rule(
            array(Check::inArray($_POST["type"], array(0, 1, 2)), '暂无该查询类型'),
            array(Check::must($_POST['val']), '请输入您要查询的值')
        );
        if ($msg !== true) {
            echo json_encode(array('status' => 400, 'msg' => $msg));
            exit();
        }

        $common = model('common');
        if ($_POST['type'] == 0) {
            $type = 'qq';
        } elseif ($_POST['type'] == 1) {
            $type = 'wx';
        } else {
            $type = 'ali';
        }
        $info = $common->_find('black_qq', array($type => $_POST['val']));

        if (empty($info)) {
            $msg = "暂无此号的任何记录";
        } else {
            $msg = $info['remark'];
        }
        echo json_encode(array('status' => 200, 'msg' => $msg));

    }
}