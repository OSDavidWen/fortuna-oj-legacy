<?php
	if ($user) {
?>
	<div style="margin: 8px 0">
		<a href="#users/<?=$user?>" id="username" style="display: inline">
<!--			<img style='width:24px; height:32px' src='data:image/jpeg;base64,<?=$avatar?>' /> -->
			<span class="label label-info"><?=$user?></span>
		</a>
		<a id="setting" href="#users/<?=$user?>/settings">
			<i class="icon-cog" style="margin-left: 3px"></i>
		</a>
		<a id="logout" href="#main/home">
			<i class="icon-off" style="margin-left: 3px"></i>
		</a>
	</div>
<?php
	}
?>