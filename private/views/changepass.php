<div class="var-content-container">
	<div class="var-content-container2">

		<h2 class="inline">Change Password</h2>
		<hr>
		<div id="changepwnotice" class="notice error hidden">	<i class="fa fa-times-circle"></i>
			<span>Passwords did not match!</span>

		</div>
		<form id="change_pass_form" class="vertical">
			
			<table summary="change password">
				<tr>
					<td><label for="cur_pass">Current password</label></td>
					<td><input required id="cur_pass" name="cur_pass" type="password" pattern=".{6,50}" title="Must be greater then 6 characters!"></td>
				</tr>
				<tr>
					<td><label for="new_pass">New password</label></td>
					<td><input required id="new_pass" name="new_pass" type="password" pattern=".{6,50}" title="Must be greater then 6 characters!"></td>
				</tr>
				<tr>
					<td><label for="conf_new">Confirm new password</label></td>
					<td><input required id="conf_new" name="conf_new" type="password" pattern=".{6,50}" title="Must be greater then 6 characters!"></td>
				</tr>
			</table>
			<input type="text" value="changepass" name="type" class="hidden"> 
			<div class="top-padding"></div>
			<input type="submit" class="inline center" val="Change password">

		</form>
	</div>
</div>
<script>
	$('#change_pass_form').submit(handlePassForm);
	function handlePassForm(e) {
		console.log('method!');
		e.preventDefault();
		e.stopPropagation();

		if(dataSent) {
			return false;
		}
		console.log('hee2');
		if ($('#conf_new').val() !== $('#new_pass').val()) {
			console.log($('#conf_new').val());
			console.log($('#new_pass').val());

			$('#changepwnotice').removeClass('hidden');
			return false;
		}
		if ($('#new_pass').val() === $('#cur_pass').val()) {
			console.log('same pass!');
			$('#changepwnotice').removeClass('hidden');
			$('#changepwnotice').text('Please supply a different new password to current.');
			return false;
		}

	//cancel current event
	$("#change_pass_form").addClass('hidden');
	var formData =  $("#change_pass_form").serialize();
	dataSent = true;
	//return false;

	$.ajax({
		type: 'POST',
		url: "/a/",
		data: formData ,
		success: function(data){
			try {
				dataSent = false;
				data = jQuery.parseJSON(data);
				console.log(data);
				if (data.sucess === "false") {
					if (typeof data.error !== 'undefined') {
						/* if the error is sent we display the error box */
						$('#changepwnotice').removeClass('hidden');
						$('#changepwnotice').text(data.error);
					}
				} else {
					/* sucess! tell them to login. */
					$('#changepwnotice').removeClass('hidden').removeClass('error').addClass('success');
					$('#changepwnotice').html('You have sucesfully changed your password!.<br>You must use this new password when you login next.');
				}
			} catch(e) {
				$('#changepwnotice').removeClass('hidden');
				$('#changepwnotice').text('An error has occured, please contact us!');
				return false;
			}
		}
	});
	return false;
}
</script>