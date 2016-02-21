<div class="var-content-container">
	<div class="var-content-container2">

		<h2 class="inline">Register</h2>
		<hr>
		<form id="register_form" autocomplete="off" class="vertical">
			<div class="notice error hidden">	<i class="fa fa-times-circle"></i>
				<span>Passwords did not match!</span>

			</div>
			<table summary="registration table">
				<tr>
					<td><label for="register_name">Username</label></td>
					<td><input spellcheck='false' required placeholder="foo" id="register_name" name="register_name" type="text" maxlength="20"></td>
				</tr>
				<tr>
					<td><label for="register_email">Email</label></td>
					<td><input spellcheck='false' required placeholder="foo@bar.com" id="register_email" name="register_email" type="email" maxlength="60"></td>
				</tr>
				<tr>
					<td><label for="register_pass">Password</label></td>
					<td><input spellcheck='false' required id="register_pass" name="register_pass" type="password" minlength="6" maxlength="50"></td>
				</tr>
				<tr>
					<td><label for="register_conf_pass">Confirm Password</label></td>
					<td><input spellcheck='false' required id="register_conf_pass" name="register_conf_pass" type="password" minlength="6" maxlength="50"></td>
				</tr>
			</table>
			<input type="text" value="register" name="type" class="hidden"> 
			<div class="top-padding"></div>
			<input type="submit" class="inline center" value="Create Account">

		</form>
	</div>
</div>

