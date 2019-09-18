	</div></div></div>
	<a href="javascript:;" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
		<i class="icon-double-angle-up icon-only bigger-110"></i>
	</a>
</div>
<script src="<?php echo $skin; ?>js/jquery-1.10.2.min.js"></script>
<script src="<?php echo $skin; ?>js/bootstrap.min.js"></script>
<script src="<?php echo $skin; ?>js/jquery.slimscroll.min.js"></script>
<script src="<?php echo $skin; ?>js/bootbox.min.js"></script>
<script src="<?php echo $skin; ?>js/plugs.js"></script>
<script src="<?php echo $skin; ?>js/ace-extra.min.js"></script>
<script src="<?php echo $skin; ?>js/ace.min.js"></script>

<script type="text/javascript">
	<?php if(!empty($action_msg)) : ?>
	showMsg('<?php echo $action_msg;?>');
	<?php endif; ?>
	<?php if(!empty($error_msg)) : ?>
	showMsg('<?php echo $error_msg;?>',true);
	<?php endif; ?>
	var __APP__ = '<?php echo __APP__;?>';
</script>
</body>
</html>