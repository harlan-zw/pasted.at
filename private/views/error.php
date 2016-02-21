<div class="var-content-container">
	<?php
	switch($_SESSION['error_id']){ 
		case '404':
		default:
		?>
		<h2 class="inline">Page not found</h2>
		<hr>
		<p>It looks like the page you were looking for is not here!</p>
		<p>If you were linked to this page by the website please report the following: </p><br>
		<p>Name: <?php echo wordwrap($_GET['page'], 70, "<br />\n", true); ?> </p>
		<p>ID: <?php echo wordwrap($_GET['extra'],  70, "<br />\n", true); ?> </p>
		<?php 
		break;
		case 'private':?>
		<h2 class="inline">Private Paste</h2>
		<hr>
		<p>The paste you're trying to view has been set to private by the paste owner.</p>
		<p>If you pasted this and are seeing this, make sure you are logged in.</p><br>
		
		<?php
		break; 
		case 'needlogin':?>
		<h2 class="inline">Invalid Privelage</h2>
		<hr>
		<p>The page you are trying to view requires that you are logged in!</p><br>
		<p>Please login and refresh the page.</p>
		<?php
		break;

	}
	?>
	<br><br><button id="return_home">Return Home</button><div class="top-padding"></div>

</div>

<script>
	$('#return_home').click(function() {
		window.location = 'http://pasted.at';
	});

</script>