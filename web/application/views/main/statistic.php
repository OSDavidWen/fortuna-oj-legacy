<?php
	echo '<button class="btn btn-mini" onclick="javascript:history.back()">Return</button>';

	echo '<div class="status"><table class="table table-striped table-condensed table-bordered">';
	echo '<thead><tr>';
	echo '<th class="sid">Submission ID</th>';
	echo '<th class="user">User</th>';
	echo '<th class="result">Result</th>';
	echo '<th class="time">Time</th>';
	echo '<th class="memory">Memory</th>';
	echo '<th class="language">Language</th>';
	echo '<th class="codeLength">Code Length</th>';
	echo '<th class="submitTime">Submit Time</th>';
	echo '<th>Access</th>';
	echo '<th></th>';
	echo '</tr></thead>';
	
	echo '<tbody>';
	foreach ($data as $row){
		echo "<tr><td>$row->sid ($row->count)</td><td><a href=\"#users/$row->name\"><span class=\"label label-info\">$row->name</span></a></td><td>";
		if ($row->status == -1) echo $row->result;
		elseif ($row->status == 8 || $row->status == 9) echo "<a href=\"#main/result/$row->sid\">$row->result</a>";
		else{
			$sname = "$row->result <span class=\"label label-info\">" . round($row->score, 1) . '</span>';
			echo "<a href=\"#main/result/$row->sid\">$sname</a>";
		}
		
		echo "</td><td><span class=\"label label-info\">$row->time</span></td>
			<td><span class=\"label label-info\">$row->memory</span></td>
			<td><a href=\"#main/code/$row->sid\">$row->language</a></td>
			<td>$row->codeLength</td><td>$row->submitTime</td>";

		echo '<td>';
		if ($this->user->uid() == $row->uid || $this->user->is_admin()){
			echo "<a onclick=\"access_page('main/submission_change_access/$row->sid')\">";
			if ($row->private == 1) echo '<i class="icon-lock"></i>'; else echo '<i class="icon-globe"></i>';
			echo '</a>';
		} else if ($row->private == 0) echo '<i class="icon-globe"></i>';
		echo '</td>';
		
		echo '<td>';
		if ($this->user->is_admin()){
			echo "<a onclick=\"access_page('admin/change_submission_status/$row->sid')\">";
			if ($row->isShowed == 1) echo '<i class="icon-eye-open"></i>';
			else echo '<i class="icon-eye-close"></i>';
			echo '</a>';
		}
		echo '</td>';
		
		echo '</tr>';
	}
	
	echo '</tbody></table></div>';
	
	echo $this->pagination->create_links();

// End of file statistic
