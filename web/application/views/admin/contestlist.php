<h4>
	Contest List
	<button class="btn btn-primary btn-mini pull-right" onclick="window.location.hash='admin/newcontest'">Add</button>
</h4>
<hr />

<div class="contest_list">
	<table id="contest_table" class="table table-condensed table-bordered table-striped">
		<thead>
			<th>Contest ID</th><th>Title</th><th>Start Time</th><th>End Time</th><th>Status</th>
			<th>Mode</th><th>Type</th><th>Reg</th><th>Edit</th><th></th>
		</thead>
		
		<tbody><?php
			foreach ($data as $row){
				$cid = $row->cid;
				echo "<tr><td>$cid</td><td>" . 
					(isset($row->running) ? "<a class='title' href=\"#contest/problems/$cid\">$row->title</a>"
											: "<a class='title' href=\"#contest/home/$cid\">$row->title</a>") .
					"</td><td>$row->startTime</td><td>$row->endTime</td><td>$row->status</td>" . 
					"<td><div class=\"badge badge-info\">$row->contestMode</div></td>" . 
					'<td><div class="badge badge-info">' . ($row->private ? 'Private' : 'Public') .
					"</div></td><td><div class=\"badge badge-info\"><i class=\"icon-user icon-white\"></i>x$row->count</div></td>" . 
					"<td><button class=\"btn btn-mini\" onclick='window.location.href=\"#admin/newcontest/$cid\"'>Edit</button></td>" . 
					"<td><button class=\"close\" onclick=\"delete_contest($cid, $(this))\">&times;</button></td></tr>";
			}
		?></tbody>
	</table>
	<?=$this->pagination->create_links()?>
</div>
 
<div class="modal hide fade" id="modal_confirm">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Confirm Action</h3>
	</div>
	<div class="modal-body">
		<p>Are you sure to delete contest: </p>
		<h3><div id="info"></div></h3>
		<p>All data related to this contest including <strong>submissions</strong> will be lost!</p>
	</div>
	<div class="modal-footer">
		<a class="btn" data-dismiss="modal">Close</a>
		<a class="btn btn-danger" id="delete">Delete</a>
	</div>
</div>

<script type="text/javascript">
	function delete_contest(cid, selector){
		$('#modal_confirm #delete').live('click', function(){
			$('#modal_confirm').modal('hide');
			access_page('admin/delete_contest/' + cid);
		});
		$('#modal_confirm #info').html(cid + '. ' + selector.parent().parent().find('.title').html());
		$('#modal_confirm').modal({backdrop: 'static'});
	}
</script>