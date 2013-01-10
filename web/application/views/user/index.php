<!-- 	echo $data->uid . ' ' . $data->name . "<br />"; -->
<!-- 	echo $data->solvedCount . ' / ' . $data->submitCount . "<br />"; -->
<!-- 	foreach ($data->accepted as $data->pid) echo $data->pid->pid . "<br />"; -->

<div class="hero-unit">
	<ul class="unstyled">
		<li>
			<span class="user_specificator">uid</span>
			<span class="badge badge-info" style="margin-left:20%"><?=$data->uid?></span>
		</li>
		<li>
			<span class="user_specificator">username</span>
			<span class="label label-info" style="margin-left:20%"><?=$data->name?></span>
		</li>
		<li>
			<span class="user_specificator">Rank</span>
			<span class="badge badge-info" style="margin-left:20%"><?=$data->rank?></span>
		</li>		
		<li>
			<span class="user_specificator">AC</span>
			<span class="badge badge-info" style="margin-left:20%"><?=$data->acCount?></span>
		</li>
		<li>
			<span class="user_specificator">Submission Solved</span>
			<span class="badge badge-info" style="margin-left:20%"><?=$data->solvedCount?></span>
		</li>
		<li>
			<span class="user_specificator">Submission </span>
			<span class="badge badge-info" style="margin-left:20%"><?=$data->submitCount?></span>
		</li>
		<li>
			<span class="user_specificator">Rate</span>
			<span class="badge badge-info" style="margin-left:20%"><?=$data->rate . '%'?></span>
		</li>
	</ul>
</div>