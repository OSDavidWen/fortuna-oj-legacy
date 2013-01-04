<fieldset>
<legend><strong><em>Settings:</em></strong></legend>

<form action="/index.php/users/<?=$user->name?>/settings" method="post" id="user_settings" class="form-horizontal">
	<div class="control-group">
		<label for="old_password" class="control-label">Old Password</label>
		<div class="controls controls-row">
			<input type="password" name="old_password" id="old_password" class="input-large" />
			<?=form_error('old_password')?>
		</div>
	</div>
	<div class="control-group">
		<label for="new_password" class="control-label">New Password</label>
		<div class="controls controls-row">
			<input type="password" name="new_password" id="new_password" class="input-large" />
		</div>
	</div>
	<div class="control-group">
		<label for="confirm_new_password" class="control-label">Confirm New Password</label>
		<div class="controls controls-row">
			<input type="password" name="confirm_new_password" id="confirm_new_password" class="input-large" />
			<span id="not_match" class="alert" style="display:none"></span>
		</div>
	</div>
	<div class="control-group">
		<label for="email" class="control-label">Email</label>
		<div class="controls controls-row">
			<input type="text" name="email" id="email" class="input-large" value="<?=set_value('email', $config->email)?>" />
		</div>
	</div>
	<div class="control-group">
		<label for="show_category[]" class="control-label">Unsolved Categorization</label>
		<div class="controls controls-row">
			<input type="checkbox" name="show_category" class="input-large" value="1" <?=set_checkbox('show_category', '1', $config->showCategory == 1);?> />
		</div>
	</div>
	
	<button id="submit" class="btn btn-primary pull-right">Save</button>
</form>

<fieldset>

<script type="text/javascript">
	$('#user_settings').submit(function(){
		if ($('#old_password').parent().parent().hasClass('error'))
			$('#old_password').parent().parent().removeClass('error');
			
		if ($('#confirm_new_password').parent().parent().hasClass('error'))
			$('#confirm_new_password').parent().parent().removeClass('error');
		$('#not_match').css("display", "none");
			
		if ($('#new_password').val() != '' && $('#old_password').val() == ''){
			$('#old_password').parent().parent().addClass('error');
			return false;
		}
		
		if ($('#new_password').val() != $('#confirm_new_password').val()){
			$('#confirm_new_password')
			$('#confirm_new_password').parent().parent().addClass('error');
			$('#not_match').html('Password NOT match!');
			$('#not_match').css("display", "");
			return false;
		}
		
		$('#user_settings').ajaxSubmit({		
			success: function(responseText, statusText){
				if (responseText == 'success') load_page('users/<?=$user->name?>');
				else $('#page_content').html(responseText);
			}
		})
		return false;
	})
</script>