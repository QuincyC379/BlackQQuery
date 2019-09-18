<?php include_once dirname(__FILE__)."/header.php"; ?>
<?php include_once dirname(__FILE__)."/menu.php"; ?>
<div class="row">
    <div class="col-xs-12">
        <form class="form-horizontal" id="form">

            <div class="form-group" style="display: none">
                <label class="col-sm-2 control-label no-padding-right"></label>
                <div class="col-sm-10">
                    <input type="text" id="qid" name="qid" value="<?php echo $info['id']; ?>" class="col-xs-10 col-sm-5">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-right">请输入QQ号码：</label>
                <div class="col-sm-10">
                    <input type="text" id="qq" name="qq" value="<?php echo $info['qq']; ?>" class="col-xs-10 col-sm-5">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-right">请输入手机号码：</label>
                <div class="col-sm-10">
                    <input type="text" id="phone" name="phone" value="<?php echo $info['tel']; ?>" class="col-xs-10 col-sm-5">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-right">请输入微信号码：</label>
                <div class="col-sm-10">
                    <input type="text" id="wx" name="wx" value="<?php echo $info['wx']; ?>" class="col-xs-10 col-sm-5">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-right">请输入阿里号码：</label>
                <div class="col-sm-10">
                    <input type="text" id="ali" name="ali" value="<?php echo $info['ali']; ?>" class="col-xs-10 col-sm-5">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-right">请添加描述信息：</label>
                <div class="col-sm-10">
                    <input type="text" id="remark" name="remark" value="<?php echo $info['remark']; ?>" class="col-xs-10 col-sm-5">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-offset-2 col-md-10">
                    <button class="btn btn-primary" type="submit" id="save">
                        <i class="icon-ok"></i>
                        保存
                    </button>
                    <a class="btn" href="<?php echo url('qq','add'); ?>">
                        <i class="icon-undo"></i>
                        取消
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
<?php include_once dirname(__FILE__)."/footer.php"; ?>
<script src="<?php echo $skin; ?>js/jquery.validate.min.js"></script>
<script type="text/javascript">
    $(function(){
        $("#form").validate({
            rules:{
                qq:{
                    required:true,
                    maxlength:20
                },
                remark:{
                    required:true,
                    maxlength:100000
                },

            },
            messages:{
                qq: {
                    required:'请输入QQ号码',
                    maxlength:'长度不能超过{0}个字'
                },
                remark: {
                    required:'请输入QQ描述',
                    maxlength:'长度不能超过{0}个字'
                }
            },
            errorElement: "span",
            highlight:function(element, errorClass, validClass) {
                $(element).parents('.form-group').addClass('has-error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass('has-error');
            },
            submitHandler: function(form)
            {
                params = $('form').serialize();
                $('#save').html('<i class="icon-ok"></i>保存中...').attr('disabled','true');
                $.ajax({
                    type: "POST",
                    <?php if(!empty($info)) : ?>
                    url: "<?php echo url('qq','ajaxEdit'); ?>",
                    <?php else : ?>
                    url: "<?php echo url('qq','ajaxAdd'); ?>",
                    <?php endif; ?>
                    data: params,
                    dataType: "json",
                    success: function(msg){
                        $('#save').html('<i class="icon-ok"></i> 保存').removeAttr('disabled');
                        if(msg.status === 1){
                            showMsg(msg.message,function(){
                                window.location.href = "<?php echo url('qq','detailstatic'); ?>"
                            });
                        }else{
                            showMsg(msg.message,true);
                        }
                    },
                    error:function(boj,info){
                        //alert(info);
                    },
                });
            }
        });

    })
</script>