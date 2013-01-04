<?php
	if ($name)
		echo '<div style="margin: 15px 0">' . 
			"<a href=\"#users/$name\" id=\"username\"><span class=\"label label-info\">$name</span></a>" . 
			'<a id="setting" href="#users/' . $name . '/settings"><i class="icon-cog" style="margin: 7px"></i></a>' .
			'<a id="logout" href="#main/home"><i class="icon-off" style="margin: 5px"></i></a></div>';
