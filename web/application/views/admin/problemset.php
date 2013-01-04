<h4>
	Problemset
	<button class="btn btn-primary btn-mini pull-right" onclick="window.location.hash='admin/addproblem'">Add</button>
</h4>
<hr />

<div class="admin_problemset_table"><table class="table table-condensed table-striped table-bordered">
	<thead><tr><th class="pid">Problem ID</th><th class="title">Title</th><th class="source">Source</th>
	<th class="status">Status</th><th>Edit</th><th>Data</th><th></th></tr></thead><tbody>
<?php
	foreach ($data as $row){
		$pid = $row->pid;
		echo "<tr><td><a href=\"#main/show/$pid\">$row->pid</a></td><td><a href=\"#main/show/$pid\">$row->title</a>" .
			 "</td><td>$row->source</td>" .
			 "<td><a onclick=\"access_page('#admin/change_problem_status/$pid')\">$row->isShowed</a></td><td>" .
			 "<button class=\"btn btn-mini\" onclick=\"window.location.href='#admin/addproblem/$pid'\">Edit</button></td><td>" .
			 "<button class=\"btn btn-mini\" onclick=\"window.location.href='#admin/dataconf/$pid'\">Configure</button></td>" . 
			 "<td><button class=\"close\" onclick=\"delete_problem($pid, '$row->title')\">&times;</button></tr>";
	}
?>
</tbody></table></div>
	
<?=$this->pagination->create_links()?>

<div class="modal hide fade" id="modal_confirm">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Confirm</h3>
	</div>
	<div class="modal-body">
		<p>Are you sure to delete problem: </p>
		<h3><div id="info"></div></h3>
	</div>
	<div class="modal-footer">
		<a class="btn" data-dismiss="modal">Close</a>
		<a class="btn btn-danger" id="delete">Delete</a>
	</div>
</div>

<script type="text/javascript">
	function delete_problem(pid, title){
		$('#modal_confirm #delete').live('click', function(){
			$('#modal_confirm').modal('hide');
			access_page('admin/delete_problem/' + pid);
		});
		$('#modal_confirm #info').html(pid + '. ' + title);
		$('#modal_confirm').modal({backdrop: 'static'});
	}
</script>
	
<!-- End of file problemset.php -->