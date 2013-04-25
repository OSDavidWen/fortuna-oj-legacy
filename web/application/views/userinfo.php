<?php
	if ($user) {
?>
	<div style="margin: 8px 0">
		<a href="#users/<?=$user?>" id="username">
			<div style="text-align:center; padding:3px; margin: 0 auto" class="well">
				<img src='<?=$avatar?>' />
			</div>
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