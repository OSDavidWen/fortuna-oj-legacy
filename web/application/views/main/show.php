<div class="row-fluid">
	<div id="header"><?php
		$average = 0;
		if ($data->submitCount > 0) $average = number_format($data->scoreSum / $data->submitCount, 2);
		
		if (!isset($data->data) || $data->data->IOMode == 0) $data->title .= '<sub> (Standard IO)</sub>';
		else if ($data->data->IOMode == 1) $data->title .= '<sub> (File IO)</sub>';
		else if ($data->data->IOMode == 2) $data->title .= '<sub> (Output Only)</sub>';
		else if ($data->data->IOMode == 3) $data->title .= '<sub> (Interactive)</sub>';
		echo "<div style=\"text-align:center\">";
		echo "<h2>$data->pid. $data->title</h2>";
		
		$is_accepted = $this->misc->is_accepted($this->session->userdata('uid'), $data->pid);

		echo '<div>';
		if (isset($data->timeLimit)){
			echo "Time Limit: <span class=\"badge badge-info\">$data->timeLimit ms</span> &nbsp;";
			echo "Memory Limit: <span class=\"badge badge-info\">$data->memoryLimit KB</span>";
		}else echo "Time & Memory Limits";
		
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"#main/limits/$data->pid\" style=\"text-align:left\">";
		echo '<span id="trigger"><i class="icon-chevron-down"></i></span></a>';
		
		if (isset($data->data->spjMode)) echo "<span style=\"color: red\">&nbsp;&nbsp;&nbsp;Special Judge</span>";
		
		echo '</div>';	
		echo '</div>';
	?></div>
</div>

<div class="row-fluid" style="margin-top:7px">
	<div id="mainbar" class="span10">
		<div class="problem">
			<div class="span12 well"><fieldset>
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
	
	<div id="sidebar" class="span2">
		<div class="well"><?php
			echo '<fieldset><legend>';
			echo '<h5><em>Statistic</em>';
			echo "<a class=\"pull-right\" href=\"#main/statistic/$data->pid\">more...</a>";
			echo '</h5></legend>';
			echo "Solved: <span class=\"badge badge-info\">$data->solvedCount</span><br />";
			echo "Submit: <span class=\"badge badge-info\">$data->submitCount</span><br />";
			echo "Average: <span class=\"badge badge-info\">$average pts</span><br />";
			echo '<div style="text-align:center; margin-top: 15px">';
			echo "<button class=\"btn btn-primary\" onclick=\"window.location.href='#main/submit/$data->pid'\">Submit</button>";
			echo '</div></section></fieldset>';
		?></div>
		<?php
		if ($this->session->userdata('show_category') == 1 || $is_accepted){
			echo '<div class="well">';
			echo '<fieldset id="tags">';
			echo '<legend><h5><em>Tags</em>';
			if ($is_accepted) echo ' <button id="add_tag_btn" class="btn btn-mini pull-right">add</button>';
			echo '</h5></legend>';
			foreach ($data->category as $id => $name)
				echo "<span class=\"label tag\" id=\"$id\" style=\"margin-right:11px\">" . 
					'<button class="close delete_tag" style="color: white;font-size:14px;opacity:0.8;height:14px">&times;</button>' .
					$name . '</span> ';
			
			echo '<form id="tag_form" ><select style="width:120px" name="tag">';
			foreach ($category as $id => $name) echo "<option value=\"$id\">$name</option>";
			echo '</select><br />';
			echo '<button class="btn btn-mini btn" id="cancel_add">cancel</button>';
			echo '<button class="btn btn-mini btn-primary pull-right" id="confirm_add">add</button>';
			echo '</form></fieldset></div>';	
		}
		?>
		<div class="well">
			<fieldset id="solutions">
				<legend><h5><em>Solutions</em>
				<?php if ($is_accepted) echo ' <button id="add_solution_btn" class="btn btn-mini pull-right">add</button>';?>
				</h5></legend>
			</fieldset>
		</div>
		
		<?php if ($data->source != ''){ ?>
			<div class="well"><fieldset>
				<legend><h5><em>Source</em></h5></legend>
				<div class="content"><?=nl2br($data->source)?></div>
			</fieldset></div>
		<?php } ?>		
	</div>
</div>

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
	$(document).ready(function(){
		$('.delete_tag').hide(),
		$('#tag_form').hide(),
		$('.tag').hover(
			function(){
				$(this).children('.close').show();
			},
			function(){
				$(this).children('.close').hide();
			}
		),
		$('.delete_tag').click(function(){
			access_page('main/deltag/<?=$data->pid?>/' + $(this).parent().attr('id'));
		}),
		$('#trigger').popover({html: true, content: dataconf, trigger: 'hover', placement: 'bottom'}),
		$('#trigger').click(function(){
			$('#trigger').popover('hide')
		}),
		$('#add_tag_btn').click(function(){
			$('#tag_form').show();
		}),
		$('#cancel_add').click(function(){
			$('#tag_form').hide();
			return false;
		}),
		$('#confirm_add').click(function(){
			$('#tag_form').hide();
			$('#tag_form').ajaxSubmit({
				type: "GET",
				url: "index.php/main/addtag/<?=$data->pid?>",
				success: function(){
					refresh_page();
					return false;
				}
			});
			return false;
		})
	})
</script>

<!-- End of file show.php -->