<div class="main-container" id="main-container">
<div class="main-container-inner">
	<a class="menu-toggler" id="menu-toggler" href="javascript:;">
		<span class="menu-text"></span>
	</a>
	<div class="sidebar sidebar-fixed" id="sidebar">

		<ul class="nav nav-list">

			<li <?php echo $index ? 'class=""' : ''; ?> >
				<a href="<?php echo url('qq','add'); ?>">
					<i class="icon-dashboard"></i>
					<span class="menu-text">数据管理</span>
				</a>
			</li>


            <li <?php echo $index ? 'class=""' : '';?>>
                <a href="<?php echo url('qq','detailstatic'); ?>" >
                    <i class="icon-key"></i>
                    <span class="menu-text">数据查看</span>
                </a>

            </li>


		</ul>
	</div>

	<div class="main-content">
		<div class="breadcrumbs breadcrumbs-fixed" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<i class="icon-home home-icon"></i>
					<a href="<?php echo url('index');?>">首页</a>
				</li>
				<?php if(!empty($nav)){
					foreach ($nav as $k => $var) {
						if($var != end($nav)){
							echo '<li class="active"><a href="'.$var.'">'.$k.'</a></li>';
						}else{
							echo '<li class="active">'.$k.'</li>';
						}
					}
				}?>

			</ul>
		</div>

		<div class="page-content">
			<div class="page-header">
				<h1>
					<?php echo $htitle; ?>
				</h1>
			</div>