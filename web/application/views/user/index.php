<div id="user-header" class="row-fluid">
	<div class="span6"><fieldset id="user-information">
		<legend>
			Basic Information
			<a href="#users/<?=$data->name?>/statistic" class="pull-right">
				<small><strong>Statistic</strong></small>
			</a>
		</legend>
		
		<div class="row-fluid">
			<div id="user-picture" class="span6" style="text-align:center; height:339px; line-height: 339px">
<!--				<img src="images/avatar/<?=$data->userPicture?>"  -->
				<img src="images/school_logo.png"
					style="vertical-align:middle; margin:0 auto" width="225" height="300" />
			</div>
			
			<div class="span6" style="height:339px"><dl class="dl-horizontal">
			
				<dt class="user_specificator">uid</dt>
				<dd><span class="badge badge-info"><?=$data->uid?></span></dd>
				
				<dt class="user_specificator">Username</dt>
				<dd><span class="label label-info"><?=$data->name?></span></dd>
					
				<dt class="user_specificator">Rank</dt>
				<dd><span class="badge badge-info"><?=$data->rank?></span></dd>
						
				<dt class="user_specificator">AC Problems</dt>
				<dd><a href="#users/<?=$data->name?>/statistic">
					<span class="badge badge-info"><?=$data->acCount?></span>
				</a></dd>
				
				<dt class="user_specificator">Solved</dt>
				<dd><a href="#users/<?=$data->name?>/statistic">
					<span class="badge badge-info"><?=$data->solvedCount?></span>
				</a></dd>
				
				<dt class="user_specificator">Submit</dt>
				<dd><a href="#users/<?=$data->name?>/statistic">
					<span class="badge badge-info"><?=$data->submitCount?></span>
				</a></dd>
				
				<dt class="user_specificator">Rate</dt>
				<dd><span class="badge badge-info"><?=$data->rate . '%'?></span></dd>
				
			</dl></div>
		</div>
	</fieldset></div>
	
	<div class="span5" id="chart-container" style="height:400px">
	</div>
</div>

<script type="text/javascript">
	verdicts = [{
		type: 'pie',
		data: [
			[ 'Other',    <?=$data->count[-2] + $data->count[3] + $data->count[9]?> ],
			[ 'Pending',    <?=$data->count[-1]?> ],
			{
				name: 'Accepted',
				y: <?=$data->count[0]?>,
				sliced: true,
				selected: true
			},
			['PE',    <?=$data->count[1]?>],
			['WA',    <?=$data->count[2]?>],
			['OLE',    <?=$data->count[4]?>],
			['MLE',    <?=$data->count[5]?>],
			['TLE',    <?=$data->count[6]?>],
			['RE',    <?=$data->count[7]?>],
			['CE',    <?=$data->count[8]?>],
		]
	}]
			
	if ( typeof (Highcharts) == 'undefined') {
		$.getScript("js/highcharts.js", function(script, textStatus, jqXHR) {
			$.getScript("js/exporting.js", function(script, textStatus, jqXHR) {
				initialize()
				render_pie('#chart-container', 'Verdicts Chart', verdicts)
			})
		})

	} else render_pie('#chart-container', 'Verdicts Chart', verdicts)
	
</script>
