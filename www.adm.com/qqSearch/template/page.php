<?php if($count > 1 ) : ?>
<div style="text-align:right;">
	<ul class="pagination">
		<?php if($page != 1) :?>
		<li>
			<a href="<?php echo $pageurl.'&page='.($page - 1); ?>">
				<i class="icon-double-angle-left"></i>
			</a>
		</li>
		<?php endif; ?>

		<?php
			$pages = 9; #显示页数

			$leftpage_num=floor($pages/2);
        	$rightpage_num=$pages-$leftpage_num;

        	$left=$page-$leftpage_num;
	        $left=max($left,1); //左边最小不能小于1

	        $right=$left+$pages-1; //左边加显示页数减1就是右边显示数
	        $right=min($right, $count);  //右边最大不能大于总页数
	        $left=max($right-$pages+1,1); //确定右边再计算左边，必须二次计算

			for($i = $left; $i <= $right; $i++){
				if($page == $i){
					echo '<li class="active"><span>'.$i.'</span></li>';
				}else{
					echo '<li><a href="'.$pageurl.'&page='.$i.'">'.$i.'</a></li>'; 
				}
			}
		?>

		<?php if($page != $count) :?>
		<li>
			<a href="<?php echo $pageurl.'&page='.($page + 1); ?>">
				<i class="icon-double-angle-right"></i>
			</a>
		</li>
		<?php endif; ?>
	</ul>
</div>
<?php endif; ?>
