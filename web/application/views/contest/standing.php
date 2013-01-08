<div class="standing_table">
	<?php if ($data != FALSE){
		echo '<button onclick="download_result()" class="btn btn-small pull-right"><strong>export</strong></button>';
	}?>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Rank</th><th>Name</th>
				<?php
					if ($info->contestMode == 'OI'){
						echo '<th>Score</th>';
						foreach ($info->problemset as $row){
							$pid[] = $row->pid;
							echo "<th>$row->title</th>";
						}
					}else if ($info->contestMode == 'ACM'){
						echo '<th>Solved</th><th>Penalty</th>';
						for ($i = 0; $i < $info->count; $i++) echo '<th style="text-align: center">' . chr(65 + $i) . '</th>';
					}
				?>
			</tr>
		</thead>
		<tbody><?php
		if ($data != FALSE){
			foreach ($data as $row){
				echo "<tr><td><span class=\"label\">$row->rank</span></td><td><span class=\"label label-info\">$row->name</span></td><td><span class=\"badge badge-info\">$row->score</span></td>";
				if ($info->contestMode == 'OI'){
					foreach ($pid as $prob){
						echo '<td>';
						if (isset($row->acList[$prob])){
							if ($row->acList[$prob] == 0)
								echo '<span class="badge badge-important">' . $row->acList[$prob] . '</span>';
							else
								echo '<span class="badge badge-success">' . $row->acList[$prob] . '</span>';
						}
						echo '</td>';
					}
				}else if ($info->contestMode == 'ACM'){
					echo "<td><span class=\"badge badge-info\">$row->penalty</span></td>";
					foreach ($info->problemset as $prob){
						echo '<td style="text-align: center">';
						if (isset($row->attempt[$prob->pid])){
							if (isset($row->acList[$prob->pid])){
								echo '<span class="badge badge-success">' . $row->attempt[$prob->pid] . '/' . $row->acList[$prob->pid] . '</span>';
							}else{
								echo '<span class="badge badge-important">-' . $row->attempt[$prob->pid] . '</span>';
							}
						}
						echo '</td>';
					}
				}
				echo '</tr>';
			}
		}	
		?></tbody>
	</table>
</div>

<iframe id="downloader" style="display:none"></iframe>

<script type="text/javascript">
	function download_result(){
		$("#downloader").attr('src', 'index.php/contest/result/<?=$info->cid?>');
	}
</script>