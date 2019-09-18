<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title><?php echo $title; ?></title>
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
</head>

<body class="breadcrumbs-fixed">
	<div class="navbar navbar-default navbar-fixed-top" id="navbar">
		<div class="navbar-container" id="navbar-container">
            <div class="navbar-header pull-left">
				<a href="<?php echo url('index');?>" class="navbar-brand">
					<small>
						<i class="icon-leaf"></i>
						Cos面基避雷指南
					</small>
				</a>
			</div>

<!--			<div class="navbar-header pull-left" style="position:absolute; left:190px;">-->
<!--				<ul class="nav ace-nav">-->
<!--					<li class="light-blue">-->
<!--						<a  href="--><?php //echo url('index','index'); ?><!--">-->
<!--							<i class="icon-cog"></i>-->
<!--							系统管理-->
<!--						</a>-->
<!--					</li>-->
<!--				</ul>-->
<!--			</div>-->

			<div class="navbar-header pull-right" role="navigation">
				<ul class="nav ace-nav">
					<li class="light-blue">
						<a data-toggle="dropdown" href="javascript:;" class="dropdown-toggle">
							<span class="user-info">
								<?php echo $userInfo['user_name']; ?>
							</span>
							<i class="icon-caret-down"></i>
						</a>

						<ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
							<li>
								<a href="<?php echo url('channel','password');?>">
									<i class="icon-edit"></i>
									修改密码
								</a>
							</li>
							<li>
								<a href="<?php echo url('login','logout');?>">
									<i class="icon-off"></i>
									退出
								</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
