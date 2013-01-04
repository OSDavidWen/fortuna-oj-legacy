<?php
	echo '<button class="btn btn-mini" onclick="javascript:history.back()">Return</button>';
	echo '<table id="limits" class="table table-bordered table-striped table-condensed"' .
		 '<thead><tr><th>Case/Test No.</th><th>Score</th><th>Time</th><th>Memory</th></tr></thead><tbody>';
	$caseCnt = 1;
	foreach ($data->data->cases as $case){
		echo "<tr><td class=\"case_no\">Case $caseCnt</td><td>" . number_format($case->score, 2) . ' pts</td><td></td><td></td></tr>';
		$testCnt = 1;
		foreach ($case->tests as $test){
			echo "<tr><td>Test $testCnt</td><td></td><td>$test->timeLimit ms</td><td>$test->memoryLimit KB</td></tr>";
			$testCnt++;
		}
		$caseCnt++;
	}
	echo '</tbody></table>';

// End of file limits.php
