<div class="row-fluid">
	<div id="header"><?php
		$average = 0;
		if ($data->submitCount > 0) $average = number_format($data->scoreSum / $data->submitCount, 2);
		if ($data->data->IOMode == 0) $data->title .= '<sub> (Standard IO)</sub>';
		else if ($data->data->IOMode == 1) $data->title .= '<sub> (File IO)</sub>';
		
		echo "<div style='text-align:center'><h2>$data->id.  $data->title</h2>";
		
		echo '<div>';
		if (isset($data->timeLimit)){
			echo "Time Limit: <span class=\"badge badge-info\">$data->timeLimit ms</span> &nbsp;";
			echo "Memory Limit: <span class=\"badge badge-info\">$data->memoryLimit KB</span>";
		}else echo "Time & Memory Limits";
		
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"#main/limits/$data->pid\" style=\"text-align:left\">";
		echo '<span id="trigger"><i class="icon-chevron-down"></i></span></a>';
		
		if (isset($data->data->spjMode)) echo '&nbsp;&nbsp;&nbsp;<span class="label label-important">Special Judge</span>';
		echo '</div>';
		
		if ($info->contestMode != 'OI'){
			echo "<div>Solved: <span class=\"badge badge-info\">$data->solvedCount</span> &nbsp;";
			echo "Submit: <span class=\"badge badge-info\">$data->submitCount</span></div>";
		}
		
		if (strtotime($info->endTime) > time()){
			echo "<button style='margin-top:3px' class='btn btn-primary' onclick=\"load_page('main/submit/$data->pid/$cid')\">Submit</button>";
		}
		
		echo '</div>';
	?></div>
</div>


<div class="row-fluid" style="margin-top:7px">
	<div id="mainbar">
		<div class="problem">
			<div class="well"><fieldset>
				<legend><h4>Description</h4></legend>
				<div class="content"><?=nl2br($data->problemDescription)?></div>
			</fieldset></div>
			<div class="clearfix"></div>
			
			<div>
				<div class="span6 well"><fieldset>
					<legend><h4>Input</h4></legend>
					<div class="content"><?=nl2br($data->inputDescription)?></div>
				</fieldset></div>
			
				<div class="span6 well"><fieldset>
					<legend><h4>Output</h4></legend>
					<div class="content"><?=nl2br($data->outputDescription)?></div>
				</fieldset></div>
			</div>
			<div class="clearfix"></div>
			
			<div>
				<div class="span6 well"><fieldset>
					<legend><h4>Sample Input</h4></legend>
					<div class="content"><?=nl2br($data->inputSample)?></div>
				</fieldset></div>
			
				<div class="span6 well"><fieldset>
					<legend><h4>Sample Output</h4></legend>
					<div class="content"><?=nl2br($data->outputSample)?></div>
				</fieldset></div>
			</div>
			<div class="clearfix"></div>
		
			<div class="well"><fieldset>
				<legend><h4>Data Constraint</h4></legend>
				<div class="content"><?=nl2br($data->dataConstraint)?></div>
			</fieldset></div>
			<div class="clearfix"></div>
			
			<?php if ($data->hint != ''){ ?>
				<div class="well"><fieldset>
					<legend><h4>Hint</h4></legend>
					<div class="content"><?=nl2br($data->hint)?></div>
				</fieldset></div>
			<?php } ?>
		</div>
	</div>
</div>

<?php if (strtotime($info->endTime) >= strtotime(date('now'))){ ?>
<div style="text-align:center">
	<button style='margin-top:3px' class='btn btn-primary' onclick="load_page('main/submit/<?="$data->pid/$cid"?>')">Submit</button>
</div>
<?php } ?>

<script type="text/javascript">
	var dataconf = "<?php
		echo '<pre>';
		$caseCnt = 1;
		if (isset($data->data->cases)){
			foreach ($data->data->cases as $case){
				echo "Case $caseCnt: " . number_format($case->score, 2) . ' pts<br />';
				$testCnt = 1;
				foreach ($case->tests as $test){
					echo "<i class='icon-arrow-right'></i>Test $testCnt:<span class='badge badge-info'>$test->timeLimit ms</span>";
					echo "<span class='badge badge-info'>$test->memoryLimit KB</span><br />";
					$testCnt++;
				}
				$caseCnt++;
			}
		}
		echo '</pre>';
	?>";
	$('#trigger').popover({html: true, content: dataconf, trigger: 'hover', placement: 'bottom'});
	$('#trigger').click(function(){
		$('#trigger').popover('hide')
	});
</script>
