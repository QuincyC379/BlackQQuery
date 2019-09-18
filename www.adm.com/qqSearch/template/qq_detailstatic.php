<?php include_once dirname(__FILE__) . "/header.php"; ?>
<?php include_once dirname(__FILE__) . "/menu.php"; ?>

<div class="row">
    <div class="col-xs-12" style="margin-bottom:20px;">
        <form action="index.php" method="GET">
            <input type="hidden" name="m" value="qq_detailstatic"/>
            QQ查找：
            <input type="text" name="keyword" id="keyword" placeholder="请输入要查找的QQ号"
                   value="<?php echo $_GET['keyword']; ?>">

            <button type="submit" id="search" class="btn btn-xs btn-primary"
                    style="vertical-align:initial; height:28px;"><i class="icon-search"></i> 搜索
            </button>
        </form>
    </div>
</div>


<div class="row">
    <div class="col-xs-12" style="margin-bottom:10px;">
        <h5></h5>
    </div>

    <div class="col-xs-12">

        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th width="2%">编号</th>
                <th width="10%">QQ号码</th>
                <th width="10%">电话</th>
                <th width="10%">微信</th>
                <th width="10%">支付宝</th>
                <th width="10%">说明</th>
                <th width="10%">创建时间</th>
                <th width="10%">操作</th>

            </tr>
            </thead>
            <tbody>
            <?php if (!empty($list)) : ?>
                <?php foreach ($list as $var) : ?>
                    <?php if ($var['is_del'] ==1) : ?>
                    <tr>
                        <td><?php echo $var['id']; ?></td>
                        <td><?php echo $var['qq']; ?></td>
                        <td><?php echo $var['tel']; ?></td>
                        <td><?php echo $var['wx']; ?></td>
                        <td><?php echo $var['ali']; ?></td>
                        <td><?php echo $var['remark']; ?></td>
                        <td><?php echo date('Y-m-d h:i:s', $var['create_time']); ?></td>
                        <td>
                            <button class="btn btn-xs myUpd" data-id="<?php echo $var['id']; ?>"><i
                                        class="icon-edit ">编辑</i>
                            </button>
                            <button class="btn btn-xs btn-danger delete" data-id="<?php echo $var['id']; ?>">删除
                            </button>
                        </td>

                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="17" style="text-align:center;">暂无数据</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        <?php include_once dirname(__FILE__) . '/page.php'; ?>
    </div>
</div>
<?php include_once dirname(__FILE__) . "/footer.php"; ?>
<link rel="stylesheet" href="<?php echo $skin; ?>css/daterangepicker.css"/>
<script src="<?php echo $skin; ?>js/moment.min.js"></script>
<script src="<?php echo $skin; ?>js/daterangepicker.min.js"></script>
<script type="text/javascript">

    $(document).on('click', '.delete', function () {
        var $this = $(this);
        bootbox.confirm({
            buttons: {
                confirm: {
                    label: '确认'
                },
                cancel: {
                    label: '取消',
                }
            },
            message: '<p>您确定要删除吗?</p>',
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo url('qq', 'ajaxDelete'); ?>',
                        data: {cpid: $this.attr('data-id')},
                        dataType:"JSON",
                        success: function (msg) {
                            if (msg.status === 1) {
                                showMsg(msg.message, function () {
                                    window.location.reload();
                                });
                            } else {
                                showMsg(msg.message, true);
                            }
                        }
                    });
                }
            }
        });
    });

    $(document).on('click', '.myUpd', function () {
        var cpid = $(this).attr('data-id');

        window.location = "<?php echo url('qq', 'edit', array('id' => '')); ?>" + cpid;
    });
</script>
