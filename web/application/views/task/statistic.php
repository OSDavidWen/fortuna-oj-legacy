<button class="btn btn-mini" onclick="javascript:history.back()">Return</button>

<table class="table table-condensed table-bordered table-stripped">
	<thead><tr>
		<td>Name</td>
		<?php 
			foreach ($problems as $problem)
				echo "<td style='text-align:center'><a href='#task/show/$problem->pid/$info->gid/$info->tid'>$problem->pid</a></td>";
		?>
	</tr></thead>
	
	<tbody>
		<?php
			foreach ($data as $user) {
				echo "<tr><td>$user[1]</td>";
				foreach ($problems as $problem) {
					echo '<td style="text-align:center">';
					if (isset($user[$problem->pid])) {
						$score = round($user[$problem->pid]->score, 1);
						$time = $user[$problem->pid]->submitTime;
						echo "<span class='label label-info'>$score</span> / $time";
					}
					echo '</td>';
				}
				echo '</tr>';
			}
		?>
	</tbody>
</table>
