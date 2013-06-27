<?php
	echo '<div><table id="contest_problems" class="table table-condensed table-bordered table-striped">' . 
		 '<thead><tr><th class="span2">Problem ID</th><th>Title</th>';
	if ($info->contestMode != 'OI' && $info->contestMode != 'OI Traditional') echo '<th class="span2">Statistic</th>';
	echo '</tr></thead><tbody>';

	foreach ($data as $row){
		echo '<tr><td>' .
			($info->contestMode == 'ACM' ? chr(65 + $row->id) : $row->id) .
			"</td><td><a href=\"#contest/show/$cid/$row->id\">$row->title</a></td>" .
			($info->contestMode == 'OI' || $info->contestMode == 'OI Traditional' 
				? '' : '<td>' . $row->statistic->solvedCount . '/' . $row->statistic->submitCount . '</td>') .
			'</tr>';
	}
	
	echo '</tbody></table></div>';