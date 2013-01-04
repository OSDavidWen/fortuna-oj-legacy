<!-- 	echo $data->uid . ' ' . $data->name . "<br />"; -->
<!-- 	echo $data->solvedCount . ' / ' . $data->submitCount . "<br />"; -->
<!-- 	foreach ($data->accepted as $data->pid) echo $data->pid->pid . "<br />"; -->

<div class="hero-unit">
	<ul class="unstyled">
		<li>
			<span class="user_specificator">uid</span>
			<span class="label label-info"><?=$data->uid?></span>
			<div class="clearfix"></div>
		</li>
		<li>
			<span class="user_specificator">username</span>
			<span class="label label-info"><?=$data->name?></span>
			<div class="clearfix"></div>
		</li>
		<li>
			<span class="user_specificator">Rank</span>
			<span class="badge badge-info"><?=$data->rank + 1?></span>
			<div class="clearfix"></div>
		</li>		
		<li>
			<span class="user_specificator">Solved</span>
			<span class="badge badge-info"><?=$data->solvedCount?></span>
			<div class="clearfix"></div>
		</li>
		<li>
			<span class="user_specificator">Submit</span>
			<span class="badge badge-info"><?=$data->submitCount?></span>
			<div class="clearfix"></div>
		</li>
		<li>
			<span class="user_specificator">Rate</span>
			<span class="badge badge-info"><?=$data->rate . '%'?></span>
			<div class="clearfix"></div>
		</li>
	</ul>
</div>