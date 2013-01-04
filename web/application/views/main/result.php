<?php
	echo '<button class="btn btn-mini" onclick="javascript:history.back()">Return</button>';
	echo '<div class="result">';
	
	if ($result->compileStatus){
		echo '<table class="table table-bordered table-condensed table-striped">' .
			'<thead><tr><th>Case</th><th id="score">Score</th><th id="result">Result</th><th id="time">Time</th><th id="memory">Memory</th></tr></thead>';
		
		$case_no = 1;
		foreach ($result->cases as $row1 => $case){
			echo "<tbody class=\"case\" id=\"toggle$row1\"><tr><td>$case_no</td><td>" . $case->score . '</td>';
			$case_memory = $case_time = $case_status = -1;
			$case_result = '<span style="color:green">Accepted</span>';
			foreach ($case->tests as $row2 => $test){
				if ($test->status > $case_status) $case_result = $test->result;
				$case_time = max($case_time, $test->time);
				$case_memory = max($case_memory, $test->memory);
			}
			$case_no++;
			echo "<td>$case_result</td><td>$case_time</td><td>$case_memory</td></tr></tbody>";
			
			echo "<tbody class=\"toggle$row1\" style=\"display: none;\">";
			foreach ($case->tests as $row2 => $test)
				echo "<tr><td></td><td></td><td>$test->result</td><td>$test->time</td><td>$test->memory</td></tr>";
			echo '</tbody>';
		}
		echo '</table>';
	} else
		echo "<pre>$result->compileMessage</pre>";

	echo '</div>';