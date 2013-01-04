<div class="contest_list">
	<table id="contest_table" class="table table-condensed table-bordered table-striped">
		<thead><tr><th class="cid">Contest ID</th><th id="title">Title</th><th>Mode</th><th id="start_time">Start Time</th>
		<th class="end_time">End Time</th><th class="status">Status</th><th class="register">Register</th></tr></thead><tbody>
	<?php
		foreach ($data as $row){
			$cid = $row->cid;
			echo "<tr><td>$cid</td><td class=\"title\">" . 
				(isset($row->running) ? "<a href=\"#contest/problems/$cid\">$row->title</a>" : "<a href=\"#contest/home/$cid\">$row->title</a>") .
				"<td><span class=\"label label-info\">$row->contestMode</span></td>" . 
				"</td><td>$row->startTime</td><td>$row->endTime</td><td>$row->status</td><td>";
			if ($row->private) echo anchor("#contest/register/$cid", "<span class=\"btn btn-success btn-mini\" style=\"font-weight:bold\">Register</span><span class=\"badge badge-info\">x$row->count</span>");
			echo '</td></tr>';
		}
	?>
	
	</tbody></table>
	<?=$this->pagination->create_links()?>
</div>

	
<!-- End of file index.php  -->
