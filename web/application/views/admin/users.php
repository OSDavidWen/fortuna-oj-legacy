<table class="table table-bordered table-condensed table-stripped">
	<thead>
		<th>uid</th><th>Name</th><th>School</th><th>Status</th><th>Priviledge</th><th>Groups</th><th></th>
	</thead>
	<tbody><?php
		foreach ($data as $row){
			echo "<tr><td>$row->uid</td><td><span class='label label-info'>$row->name</span></td><td>$row->idSchool</td><td>";
			echo "<span onclick=\"access_page('admin/change_user_status/$row->uid');\" ";
			if ($row->isEnabled) echo 'class="label label-success">Enabled';
			else echo 'class="label label-important">Disabled';
			echo '</span></td><td>';
			if ($row->priviledge == 'admin') echo '<span class="label label-warning">Administrator</span>';
			else echo '<span class="label">User</span>';
			echo '</td><td>';
			foreach ($row->groups as $group) echo "<span class=\"label\">$group->name</span> ";
			echo "</td><td><button class='close' onclick=\"delete_user($row->uid, '$row->name')\">&times;</button></td></tr>";
		}
	?></tbody>
</table>

<div class="modal hide fade" id="modal_confirm">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Confirm Action</h3>
	</div>
	<div class="modal-body">
		<p>Are you sure to delete user: </p>
		<h3><div id="info"></div></h3>
	</div>
	<div class="modal-footer">
		<a class="btn" data-dismiss="modal">Close</a>
		<a class="btn btn-danger" id="delete">Delete</a>
	</div>
</div>

<script type="text/javascript">
	function delete_user(uid, name){
		$('#modal_confirm #delete').live('click', function(){
			$('#modal_confirm').modal('hide');
			access_page('admin/delete_user/<?=$row->uid?>');
		});
		$('#modal_confirm #info').html(uid + '. ' + name);
		$('#modal_confirm').modal({backdrop: 'static'});
	}
</script>