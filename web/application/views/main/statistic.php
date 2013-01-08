<?php
	echo '<button class="btn btn-mini" onclick="javascript:history.back()">Return</button>';

	echo '<div class="status"><table class="table table-striped table-condensed table-bordered">';
	echo '<thead><tr><th class="sid">Submission ID</th><th class="user">User</th>' . 
		 '<th class="result">Result</th><th class="time">Time</th><th class="memory">Memory</th><th class="language">Language</th>' . 
		 '<th class="codeLength">Code Length</th><th class="submitTime">Submit Time</th></tr></thead><tbody>';
		 
	foreach ($data as $row){
		echo "<tr><td>$row->sid ($row->count)</td><td><a href=\"#users/$row->name\"><span class=\"label label-info\">$row->name</span></a></td><td>";
		if ($row->status == -1) echo $row->result;
		elseif ($row->status == 8 || $row->status == 9) echo "<a href=\"#main/result/$row->sid\">$row->result</a>";
		else{
//			if (round($row->score, 0) == 100)
//				$sname = "$row->result<span class=\"badge badge-success\">" . round($row->score, 1) . '</span>';
//			elseif (round($row->score, 0) == 0)
//				$sname = "$row->result<span class=\"badge badge-important\">" . round($row->score, 1) . '</span>';
//			else
			$sname = "$row->result <span class=\"label label-info\">" . round($row->score, 1) . '</span>';
			echo "<a href=\"#main/result/$row->sid\">$sname</a>";
		}
		echo "</td><td>$row->time</td>
			<td>$row->memory</td><td>$row->language</td><td>$row->codeLength</td><td>$row->submitTime</td></tr>";
	}
	
	echo '</tbody></table></div>';
	
	echo $this->pagination->create_links();

// End of file statistic
