<div class="block-content-container">
	<h2 class="inline">Contact us</h2>
	<hr>
	<div id="contact_notice" class="notice error hidden"><i class="icon-remove-sign icon-large"></i>
		Please enter more then 20 characters!
		<a href="#close" class="icon-remove"></a>
	</div>
	<form class="vertical clearfix" id="contact_form" style="text-align: left;">
		<label for="contact_email">Email</label>
		<input id="contact_email" name="contact_email" required type="email" placeholder="myemail@bla.com" value="<?php if (isLoggedIn()) echo Account_AccountAPI::getEmail(); ?>">
		
		<label for="contact_text">Message</label>
		<input type="text" value="contactus" name="type" class="hidden"> 

		<textarea name="contact_text" id="contact_text" min="20" required placeholder="My message"></textarea>
		<input type="submit" value="Submit" class="center small green" style="width:25%; margin-left: auto; margin-right: auto;">
	</form>
</div>

<script>
	var re = /[\S]{20,}/; 

	$('#contact_form').submit(function(e) {
		e.preventDefault();
		e.stopPropagation();
		if(dataSent) {
			return false;
		}
		var val = $('#contact_text').val();
		if (re.exec(val) == null) {
			//does not have any matches!
			$('#contact_notice').removeClass('hidden');
			return false;
		}
		$('#contact_notice').addClass('hidden');
		var formData =  $("#contact_form").serialize();
		dataSent = true;
	//return false;

	$.ajax({
		type: 'POST',
		url: "/lunor/",
		data: formData ,
		success: function(data){
			try {
				dataSent = false;
				console.log(data);
				data = jQuery.parseJSON(data);
				console.log(data);
				if (data.sucess === false) {
					console.log('sucess: false');
					if (typeof data.error !== 'undefined') {
						/* if the error is sent we display the error box */
						$('#contact_notice').removeClass('hidden');
						$('#contact_notice').html(data.error);
					}
				} else {
					console.log('sucess: true');

					/* sucess! tell them to login. */
					$('#contact_notice').removeClass('hidden').removeClass('error').addClass('success');
					$('#contact_notice').text('Thank you for contacting us!');
				}
			} catch(e) {
				$('#contact_notice').removeClass('hidden');
				$('#contact_notice').text('An error has occured, please contact us!');
				return false;
			}
		}
	});
	return false;
});
</script>