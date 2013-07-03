<div class="status_table"><table class="table table-condensed table-striped">
	<thead><tr>
		<th class="sid">Submission</th>
		<th class="pid">Problem</th>
		<th class="user">User</th>
		<th class="result">Result</th>
		<th class="time">Time</th>
		<th class="memory">Memory</th>
		<th class="language">Language</th>
		<th class="codeLength">Code Length</th>
		<th class="submitTime">Submit Time</th>
		<th>Access</th>
	</tr></thead>
	
	<tbody><?php
		foreach ($data as $row){
			if ($row->isShowed = 0 && ! $is_admin) continue;
			
			echo "<tr><td>$row->sid</td><td><a href='#contest/show/$info->cid/$row->id'>" . 
				($info->contestMode == 'ACM' ? chr(65 + $row->id) : $row->id) . '</a></td><td>' . 
				"<span class=\"label label-info\"><a href=\"#users/$row->name\">$row->name</a></span></td><td>";
				
			if ($row->status < 0 && $row->status > -3) echo $row->result;
			elseif ($row->status == 8 || $row->status == 9) echo "<a href=\"#main/result/$row->sid\">$row->result</a>";
			else{
				if ($info->running && $info->contestMode == 'OI Traditional' && ! $is_admin){
					echo '<span class="label label-success">Compiled</span>';
				}else{
					if ($info->contestMode == 'OI' || $info->contestMode == 'OI Traditional') {
						switch ($row->status) {
							case 0: $tag = 'label-success'; break;
							case 1: ;
							case 2: ;
							case 7: $tag = 'label-important'; break;
							case 3: $tag = 'label-info'; break;
							case 4:
							case 5:
							case 6: $tag = 'label-warning'; break;
							default: $tag = '';
						}
						$sname = "$row->result <span class=\"label $tag\">" . round($row->score, 1) . '</span>';
						
						echo "<a href=\"#main/result/$row->sid\">$sname</a>"; //
					} else {
						$sname = $row->result;
						
						echo "<a href=\"#main/result/$row->sid\">$sname</a>"; //
					}
				}
			}
			//echo "<td><a href=\"#main/code/$row->sid\">$row->language</a>";
			if ($row->codeLength > 0) {
				if ($info->running && $info->contestMode == 'OI Traditional' && ! $is_admin){
					echo "</td>---<td></td><td>---</td>";
				}else{
					echo "</td><td><span class=\"label label-info\">$row->time</span></td>";
					echo "<td><span class=\"label label-info\">$row->memory</span></td>";
				}
				echo "<td><a href=\"#main/code/$row->sid\">$row->language</a></td>";
				echo "</td><td>$row->codeLength</td>";
				
			} else echo '<td>---</td><td>---</td><td>---</td><td>---</td>';
			echo "<td>$row->submitTime</td>";
				
			echo '<td>';
			if ($this->user->uid() == $row->uid || $this->user->is_admin()){
				echo "<a onclick=\"access_page('main/submission_change_access/$row->sid')\">";
				if ($row->private == 1) echo '<i class="icon-lock"></i>'; else echo '<i class="icon-globe"></i>';
				echo '</a>';
			} else if ($row->private == 0) echo '<i class="icon-globe"></i>';
			echo '</td></tr>';
		}
	?></tbody>
</table></div>

<?=$this->pagination->create_links()?>


<script type="text/javascript">
	//refresh_flag = setTimeout("refresh_page()", 30000);
</script>

<!-- End of file status.php -->
