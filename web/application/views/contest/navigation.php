<div class="navbar">
	<div class="navbar-inner">
		<ul class="nav">
			<li><a href="#contest"><i class="icon-arrow-left"></i></a></li>
			<li><a href="#contest/home/<?=$cid?>">Home</a></li>
			<li><a href="#contest/problems/<?=$cid?>">Problems</a></li>
			<li><a href="#contest/declaration_list/<?=$cid?>">Declaration
				<?php if ($declaration_count > 0) echo "<span class=\"badge badge-info>$declaration_count</span>"; ?>
			</a></li>
			<li><a href="#contest/status/<?=$cid?>">Status</a></li>
			<li><a href="#contest/standing/<?=$cid?>">Standing</a></li>
			<li><a href="#contest/statistic/<?=$cid?>">Statistic</a></li>
		</ul>
	</div>
</div>