<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
    <title>欢迎</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="<?php echo $skin; ?>css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo $skin; ?>css/font-awesome.min.css" />
	<link rel="stylesheet" href="<?php echo $skin; ?>css/ace.min.css" />
	<!--[if IE 7]>
	<link rel="stylesheet" href="<?php echo $skin; ?>css/font-awesome-ie7.min.css" />
	<![endif]-->

	<!--[if lte IE 8]>
	<link rel="stylesheet" href="<?php echo $skin; ?>css/ace-ie.min.css" />
	<![endif]-->

	<!--[if lt IE 9]>
	<script src="<?php echo $skin; ?>js/html5shiv.js"></script>
	<script src="<?php echo $skin; ?>js/respond.min.js"></script>
	<![endif]-->
	<style type="text/css">
		.login-error{ color: red;}
	</style>
</head>

<body class="login-layout">
<div class="login-container">
	<div class="center">
		<h1>
			<span class="white">Cos面基避雷后台</span>
		</h1>
	</div>


	<div class="position-relative">
		<div id="login-box" class="login-box visible widget-box no-border">
			<div class="widget-body">
				<div class="widget-main">
					<h4 class="header blue lighter bigger">
						<i class="icon-coffee green"></i>
						请输入帐号密码
					</h4>
					<div class="space-6"></div>
					<form id="login">
						<fieldset>
							<label class="block clearfix">
								<span class="block input-icon input-icon-right">
									<input type="text" id="username" name="username" class="form-control" placeholder="帐号" />
									<i class="icon-user"></i>
								</span>
							</label>

							<label class="block clearfix">
								<span class="block input-icon input-icon-right">
									<input type="password" id="password" name="password" class="form-control" placeholder="密码" />
									<i class="icon-lock"></i>
								</span>
							</label>

							<div class="space"></div>

							<div class="clearfix">
								<button type="submit" id="denglu" class="width-35 pull-right btn btn-sm btn-primary">
									<i class="icon-key"></i>
									登录
								</button>
							</div>
							<div class="space-4"></div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo $skin; ?>js/jquery-1.10.2.min.js"></script>
<script src="<?php echo $skin; ?>js/bootstrap.min.js"></script>
<script src="<?php echo $skin; ?>js/bootbox.min.js"></script>
<script src="<?php echo $skin; ?>js/plugs.js"></script>
<script src="<?php echo $skin; ?>js/ace.min.js"></script>
<script src="<?php echo $skin; ?>js/jquery.validate.min.js"></script>
<script type="text/javascript">

	$(function(){
		
		$("#login").validate({
			rules:{
				username:{
					required:true
				},
				password:{
					required:true
				}
			},
			messages:{
				username: '请输入用户名',
				password: '请输入密码',
			},
			errorClass: "login-error",
			errorElement: "span",
			submitHandler: function(form)
		   	{
		   		$('#denglu').attr('disabled',true);
		   		var params = {
		   			'username':$('#username').val(),
		   			'password':$('#password').val(),
		   		}
		    	$.ajax({
					type: "POST",
					url: "<?php echo url('login','loginSubmit'); ?>",
					data: params,
					dataType: "json",
					success: function(msg){
						$('#denglu').removeAttr('disabled');
						if(msg.status === 1){
							window.location.href = "<?php echo url('qq','add');?>";
						}else{
							showMsg(msg.message,true);
						}
					}
				});
		   	}
		});
	})
</script>
</body>
</html>
