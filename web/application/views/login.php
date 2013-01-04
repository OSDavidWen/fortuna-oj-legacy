<div id="login_field" class="modal">
	<div class="modal-header"><h3>Login</h3></div>

	<form action="/index.php/main/login" id="login_form" method="post">
		<div class="modal-body" style="text-align:center">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-user"></i></span>
				<input type="text" name="username" placeholder="Username" value="<?=set_value('username')?>"/>
				<?=form_error('username')?>
			</div>
			
			<div class="input-prepend">
				<span class="add-on"><i class="icon-briefcase"></i></span>
				<input type="password" name="password" placeholder="Password" />
				<?=form_error('password')?>
			</div>
		</div>

		<div class="modal-footer">
			<button class="btn btn-primary pull-right" onclick="return login_submit()">Login</button>
			<button class="btn" onclick="return register()">Register</button>
		</div>
	</form>
</div>

<script type="text/javascript">
	$('#login_field').modal({backdrop: 'static', keyboard: false});
	function register(){
		$('#login_field').modal('hide');
		load_page('main/register');
		return false;
	}
</script>