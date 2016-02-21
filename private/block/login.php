<div id="login-div" class="hidden var-content-container">

	<h2 class="inline">Login</h2>
	<i class="fa fa-times-circle login-exit"></i>
	<hr>
	<i class="hidden fa fa-cog fa-5 fa-spin vertical-align-100"></i>

	<form id="login_form" class="vertical">
		<div class="notice error hidden">	<i class="fa fa-times-circle"></i>
			<span>Passwords did not match!</span>

		</div>

		<table>
			<tr>
				<td><label for="login_name">Username</label></td>
				<td><input spellcheck='false' required name="login_name" id="login_name" type="text"></td>
			</tr>
			<tr>
				<td><label for="login_pass">Password</label></td>
				<td><input required id="login_pass" name="login_pass" type="password"></td>
			</tr>
		</table>
		<label class="inline" style="padding-right: 20px;" for="remember_me">Remember Me</label>
		<input id="remember_me" name="remember_me" value="true" type="checkbox">
		<input type="text" class="hidden" value="login" name="type">
		<div class="top-padding"></div>
		<a href="#" class="inline no-move">Forgot your password? Click here.</a>
		<div class="top-padding"></div>

		<input type="submit" class="inline center small green" value="Sign in">
	</form>
</div>
<script>
	/* register our click/submit events */
	
	$('#login_form').submit(handleLoginForm);
	
	function handleLoginForm(e) {
		e.preventDefault();
		e.stopPropagation();

		if(dataSent) {
			return false;
		}

	//cancel current event
	var formData =  $("#login_form").serialize();
	dataSent = true;
	//return false;
	$('#login_form div:first-child').addClass('hidden');
	$('#login_form').addClass('hidden');
	$('#login-div i.fa-cog').removeClass('hidden');

	$.ajax({
		type: 'POST',
		url: "/a/",
		data: formData ,
		success: function(data){
			try {
				dataSent = false;
				data = jQuery.parseJSON(data);
				if (data.sucess === "false") {
					/* data failed */
					if (typeof data.field !== 'undefined') {
						/* if the field responsible for failure is sent we give it the error class */
						$('#' + data.field).addClass('error');
					}
					if (typeof data.error !== 'undefined') {
						/* if the error is sent we display the error box */
						$('#login_form div:first-child').removeClass('hidden');
						$('#login_form div:first-child span').text(data.error);
					}
				} else {
					/* sucess! tell them to login. */
					$('#login_form div:first-child').removeClass('hidden').removeClass('error').addClass('success');
					$('#login_form div:first-child span').text('You have sucesfully logged in.');
					location.reload(true);
				}
			} catch(e) {
				$('#login_form div:first-child').removeClass('hidden');
				$('#login_form div:first-child span').text('An error has occured, please contact us!');
				return false;
			} finally {
				/* we should always reshow our login form */
				$('#login-div i.fa-cog').addClass('hidden');
				$('#login_form').removeClass('hidden');
			}
		}
	});
return false;
}

</script>