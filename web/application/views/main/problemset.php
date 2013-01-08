	<form class="form-inline form-search" id="action_form" style="margin-left:10px; margin-right:10px">
		<div id="div_goto_pid" class="control-group input-prepend input-append">
			<span class="add-on">Problem ID</span>
			<input type="text" id="goto_pid" class="input-mini" />
			<button id="goto_button" class="btn">Go</button>
		</div>
		
		<div id="div_search" class="control-group input-append">
			<input type="text" id="search_content" class="input-medium" placeholder="Title or Source..." />
			<button id="search_button" class="btn">Search</button>
		</div>
		
		<div id="div_goto_page" class="control-group input-prepend input-append pull-right">
			<span class="add-on">Page</span>
			<input type="number" id="goto_page" min=1 class="input-mini" />
			<button id="btn_goto_page" class="btn">Go</button>
		</div>
	</form>

	<div class="problemset_table"><table class="table table-bordered table-striped table-condensed table-hover">
		<thead><tr>
			<th class="status">Status</th>
			<th class="pid">Problem ID</th>
			<th class="title">Title</th>
			<th class="source">Source</th>
			<th class="solvedCount">Solved</th>
			<th class="submitCount">Submit</th>
			<th class="avg">Average</th>
		</tr></thead>
		
		<tbody><?php
			$category = $this->session->userdata('show_category') == 1;
			foreach ($data as $row){
				if ($row->isShowed == 0) continue;
				$pid = $row->pid;
				echo "<tr><td>$row->status</td><td><a href=\"#main/show/$pid\">$row->pid</a>" . 
					"</td><td class=\"title\"><a href=\"#main/show/$pid\">$row->title</a>";
				if ($category || $row->ac){
					foreach ($row->category as $tag) echo "<span class=\"label pull-right\">$tag</span>";
				}
				echo "</td><td class=\"source\">$row->source</td><td>" . 
					"<a href=\"#main/statistic/$pid\"><span class=\"badge badge-info\">$row->solvedCount</span></a></td><td>" .
					"<a href=\"#main/statistic/$pid\"><span class=\"badge badge-info\">$row->submitCount</span></a></td>" . 
					"<td><span class=\"badge badge-info\">$row->average pts</span></td></tr>";
			}
		?></tbody>
	</table></div>
	
	<?=$this->pagination->create_links()?>
	
<script type="text/javascript">
$(document).ready(function(){
	$('#goto_button').live('click', function(){
		var pid = $('#goto_pid').val();
		window.location.hash = '#main/show/' + pid;
		return false;
	}),
	
	$('#search_button').live('click', function(){
		var content = $('#search_content').val();
		window.location.hash = "#main/problemset?search=" + content;
		return false;
	}),
	
	$('#btn_goto_page').live('click', function(){
		var page = $('#goto_page').val();
		load_page("main/problemset/" + page);
		return false;
	}),
	
	$('#goto_pid').live('focus', function(){
		$('#action_form').die();
		$('#action_form').live('keypress', function(event){
			if (event.keyCode == 13 && $('#goto_pid').val() != ''){
				$('#goto_button').click();
				return false;
			}
		})
	}),
	
	$('#search_content').live('focus', function(){
		$('#action_form').die();
		$('#action_form').live('keypress', function(event){
			if (event.keyCode == 13 && $('#search_content').val() != ''){
				$('#search_button').click();
				return false;
			}
		})
	}),
	
	$('#goto_page').live('focus', function(){
		$('#action_form').die();
		$('#action_form').live('keypress', function(event){
			if (event.keyCode == 13 && $('#goto_page').val() != ''){
				$('#btn_goto_page').click();
				return false;
			}
		})
	})
})
</script>
	
<!-- End of file problemset.php -->