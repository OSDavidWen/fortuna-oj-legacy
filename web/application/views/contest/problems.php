<?php
	echo '<div><table id="contest_problems" class="table table-condensed table-bordered table-striped">' . 
		 '<thead><tr><th class="span2">Problem ID</th><th>Title</th><th class="span2">Statistic</th></tr></thead><tbody>';

	foreach ($data as $row){
		echo '<tr><td>' .
			($info->contestMode == 'ACM' ? 1000 + $row->id : $row->id) .
			"</td><td><a href=\"#contest/show/$cid/$row->id\">$row->title</a></td><td>" .
			($info->contestMode == 'OI' ? '' : $row->statistic->solvedCount . '/' . $row->statistic->submitCount) .
			'</td></tr>';
	}
	
	echo '</tbody></table></div>';