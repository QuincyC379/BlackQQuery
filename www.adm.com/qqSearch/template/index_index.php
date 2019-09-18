<?php include_once dirname(__FILE__)."/header.php"; ?>
<?php include_once dirname(__FILE__)."/menu.php"; ?>

<style>

    .modal-title {
        display: block;
        width: 100%;
        height: 70px;
        border-bottom: 1px solid #eff3f8;
    }

    .bootbox-title {
        display: block;
        font-size: 35px;
        width: 100px;
        height: 50px;
        margin: auto;
        line-height: 180%;
    }
</style>

<div class="row">
	<div class="col-xs-12">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>日期</th>
					<th>注册人数</th>
					<th>充值人数</th>
					<th>订单数</th>
					<th>充值金额</th>
					<th>分成金额</th>
					<th>ARPPU值</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				<?php if(!empty($list)) : ?>
				<?php foreach ($list as $var) : ?>
				<tr>
					<td><?php echo $var['report_date'];?></td>
					<td><?php echo $var['register_num'];?></td>
					<td><?php echo $var['recharge_num'];?></td>
					<td><?php echo $var['order_num'];?></td>
					<td><?php echo $var['recharge_amount'];?></td>
					<td><?php echo $var['recharge_amount']*$channel['divided_rate'];?></td>
                    <?php if(!$user_id) : ?>
					<td><?php echo $var['recharge_num'] > 0 ? floor($var['recharge_amount']/$var['recharge_num']) : 0;?></td>
					<td><a href="<?php echo url('channel','report', array('cid'=>$cid, 'day'=>$var['report_date'])); ?>">充值记录</a></td>
                    <?php else : ?>
                    <td><?php echo $var['recharge_num'] > 0 ? floor($var['recharge_amount']/$var['recharge_num']) : 0;?></td>
                    <td><a href="<?php echo url('channel','userreport', array('cid'=>$cid, 'day'=>$var['report_date'])); ?>">主播明细</a></td>
                    <?php endif; ?>
				</tr>
				<?php endforeach; ?>
				<?php else : ?>
				<tr>
					<td colspan="8" style="text-align:center;" >暂无数据</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
		<?php include_once dirname(__FILE__).'/page.php'; ?>
	</div>
</div>

<?php if($advertise_show) : ?>
    <div class="bootbox modal fade in myadver" tabindex="-1" role="dialog" aria-hidden="false" style="display: block;background: rgba(0,0,0,0.5)">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-title">
                    <div class="bootbox-title">
                        公告
                    </div>
                </div>
                <div class="modal-body">
                    <div class="bootbox-body">
                        <div><span style="font-size: 20px;"><?php echo $advertise['title']?></span></div><br>
                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $advertise['content']?></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="btn btn-primary myKnown">
                        我知道了
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php include_once dirname(__FILE__)."/footer.php"; ?>

<script>
    $('.myKnown').on('click', function () {
        var url = '<?php echo url("index", "addvertise"); ?>';
        var aid = '<?php echo $advertise['id'] ? $advertise['id'] : 0; ?>';

        if(aid)
        {
            $.ajax({
                type: 'post',
                url : url,
                data: {aid: aid},
                dataType: "JSON",
                success: function (msg) {

                    if(msg.status == 1)
                    {
                        $('.myadver').css('display', 'none');
                    }
                },
                error: function (e) {
                    showMsg('系统错误');
                }
            });
        }
    });
</script>
