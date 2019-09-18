<?php
class indexMod extends commonMod{
	//公众号列表页
	public function index()
	{
	    redirect(url('qq','add'));
	    exit;
		$page = intval($_GET['page']) <= 0 ? 1 : intval($_GET['page']);
		$pagesize = 20;
		$limit = ($page-1) * $pagesize.','.$pagesize;

		$data = array(
			'index' => true,
			'htitle' => '渠道统计',
			'nav' => array(
				'渠道统计' => ''
			)
		);

		if(empty($_GET['cid']))
		{
			$where = array(
				'channel_id' => $this->channel['channel_id']
			);
		}
		else
		{
			$cid = intval($_GET['cid']);
			$info = model('common')->_find('channel', array('channel_id'=>$cid));
			if(empty($info) || ($info['channel_id'] != $this->channel['channel_id'] && $info['parent_id'] != $this->channel['channel_id'])){
				$this->msg('参数错误', '-1', array('index','index'));
			}
			$where = array(
				'channel_id' => $info['channel_id']
			);
		}

        $data['advertise_show'] = 0;

        if(empty($_GET['is_show']))
        {
            $sql = "SELECT * FROM hl_advertise WHERE status=1 AND send_status=1 ORDER BY pub_time DESC LIMIT 1";
            $res = model('common')->_query($sql);
            $data['advertise'] = array();

            if($res && $this->channel['aid'] != $res[0]['id'])
            {
                $data['advertise'] = $res[0];
                $data['advertise_show'] = 1;
            }
        }

		$data['list'] = model('common')->_select('channel_report', $where, 'report_date DESC', $limit);
		$count = model('common')->_count('channel_report', $where);

		$data['cid'] = $this->channel['channel_id'];
		$data['page'] = $page;
		$data['count'] = ceil($count/$pagesize);
		$data['pageurl'] = url('index','index', array('is_show'=>1));
		$data['user_id'] = $this->channel['user_id'];

		$this->viewData = $data;
		$this->display();
	}
}