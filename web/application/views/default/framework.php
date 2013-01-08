<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?=OJ_TITLE?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="description" content="Fortuna Online Judge System Default Framework" />
		<meta name="author" content="moreD" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
		<link href="css/style.css" rel="stylesheet">

		<script src="js/jquery.js"></script>
		<script src="js/jquery.form.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/framework.js"></script>
		<script src="js/jquery.hashchange.min.js"></script>
		<script src="application/third_party/ckeditor/ckeditor.js"></script>
		<script src="application/third_party/ckfinder/ckfinder.js"></script>
		<?php if (isset($head)) echo $head?>
		
		<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>

<body onload="init_framework()">
	<div class="container-fluid">
		<div class="row-fluid">
		
			<!-- Header -->
			<div class="span12">
				<div class="well">
					<div class="tabbable tabs-left">
						<ul id="navigation" class="nav nav-tabs pull-left">
							<li>
								<img src="images/school_logo.png" class="visible-desktop" width="83" style="margin-right: 20px" alt="ZSJZ OJ"/>
								<div id="userinfo"></div>
								<div class="clearfix"></div>
							</li>
							<li class="nav_bar" id="nav_home"><a href="#main/home">Home</a></li>
							<li class="nav_bar" id="nav_problemset"><a href="#main/problemset">ProblemSet</a></li>
							<li class="nav_bar" id="nav_status"><a href="#main/status">Status</a></li>
							<li class="nav_bar" id="nav_contest"><a href="#contest">Contest</a></li>
							<li class="nav_bar" id="nav_task"><a href="#task/task_list">Task</a></li>
							<li class="nav_bar" id="nav_group"><a href="#group/group_list">Groups</a></li>
							<li class="nav_bar" id="nav_ranklist"><a href="#main/ranklist">Ranklist</a></li>
							<li id="nav_admin" class="dropdown" style="display:none">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Administer</a>
								<ul class="dropdown-menu">
									<li class="nav_bar"><a href="#admin/problemset">Problemset</a></li>
									<li class="nav_bar"><a href="#admin/contestlist">Contest List</a></li>
									<li class="nav_bar"><a href="#admin/task_list">Task List</a></li>
									<li class="nav_bar"><a href="#admin/users">Manage User</a></li>
								</ul>
							</li>
							<li>
								<div id="scroll_tip" class="well" style="text-align:center; padding: 7px; position:fixed" onclick="javascript:scroll(0,0)">
									<i class="icon-arrow-up"></i>
								</div>
							</li>
						</ul>
						<div id="page_content" class="tab-content" style="float:none; margin-left:123px"></div>
						<div class="clearfix"></div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<!-- footer -->
		<div class="row-fluid">
			<div class="span12" id="copyleft">
				<p>Project fortuna-oj hosting on <a href="http://code.google.com/p/fortuna-oj/">Google Code</a>.
				Powered by Codeigniter / Bootstrap<br />
				Author: moreD (<?=safe_mailto('moreDatPublic@gmail.com', 'Contact me');?>)</p>
				<label id="server_time"></label>
			</div>
		</div>
		
		<script type="text/javascript">
			timer = Date.parse('<?=date(DATE_ATOM);?>');
			setInterval("server_time.innerHTML=('Server Time: ' + (new Date(timer).toString())); timer += 1000;", 1000);
			
			var browser = navigator.userAgent;
			if (browser.indexOf('MSIE 6.0') > 0 || browser.indexOf('MSIE 7.0') > 0) $('#scroll_tip').affix();
			else $('#navigation').affix();
			//$('#scroll_tip').tooltip({placement:'right'});
			<?php
				if ( ! $logged_in) echo 'var logged_in = false;';
				else echo 'load_userinfo(); var logged_in = true;';
			?>
			
			var hash = window.location.hash;
			if (hash.indexOf('main/problemset') != -1) $('#nav_problemset').addClass('active');
			else if (hash.indexOf('main/status') != -1) $('#nav_status').addClass('active');
			else if (hash.indexOf('task') != -1) $('#nav_task').addClass('active');
			else if (hash.indexOf('group') != -1) $('#nav_group').addClass('active');
			else if (hash.indexOf('contest') != -1) $('#nav_contest').addClass('active');
			else if (hash.indexOf('main/ranklist') != -1) $('#nav_ranklist').addClass('active');
			else if (hash.indexOf('main/home') != -1) $('#nav_home').addClass('active');
			else if (hash.indexOf('admin') != -1) $('#nav_admin').addClass('active');
			$('.nav_bar').mouseup(function(){
				if ($(this).hasClass('active')) refresh_page();
			});
		</script>
	</div>
</body>

</html>