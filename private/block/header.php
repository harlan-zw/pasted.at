<?php
$loggedIn = Account_AccountAPI::getLoggedIn(); 
?>
<style>
	.side-menu {
		right: 0 !important;
		width: 82px !important;
	}
</style>
<header class="row">
	<div class="col_1">
		<!-- padding -->
	</div>
	<!-- header is divided into 2, the logo has one side and the links has the other -->
	<div id="site_name" class="col_2">
		<h1><a class="unique_link" <?php generateLink('index'); ?>>pasted.at</a></h1>
		<!-- <h3>Easily Share Anything</h3> -->
	</div>
	<div class="col_6">
		<!-- padding -->

	</div>
	<!-- User panel drop-down -->
	<div class="header-account-container col_3" role="navigation">
		<i class="fa fa-user fa-fw fa-2x vertical-align"></i> <?php if ($loggedIn) echo '<span style="padding-right: 10px;">' . Account_AccountAPI::getUsername() . '</span>'; ?><i class="fa fa-caret-down"></i>

		<div class="account-dropdown">

			<ul>
				<?php if (!$loggedIn) { ?>
				<li><a href="javascript:void(0);" id="login-link">Login</a></li>
				<li><a <?php generateLink('register'); ?>>Register</a></li>
				<?php } else { ?>
				<li><a <?php generateLink('pastes'); ?>>My Pastes</a></li>
				<li><a <?php generateLink('changepass'); ?>>Change Password</a></li>
				<li><a href="javascript:void(0)" id="logout-link">Log out</a></li>
				<?php } ?>
			</ul>
		</div>
		<!-- <a href="#">Login</a>-->
	</div>
	<div class="col_1">
		<i class="fa fa-2x fa-bars" style="padding-top: 6px;"></i>
		<div class="account-dropdown side-menu">

			<ul>
				<li><a <?php generateLink('recent'); ?>>Recent Pastes</a></li>
				<li><a <?php generateLink('popular'); ?>>Popular Pastes</a></li>
			</ul>
		</div>
	</div>

	<!-- login form -->
	<?php if (!$loggedIn) contentBlock('login'); ?>
</header>
<script>
	$('#logout-link').click(function() {
		logout();
		console.log('logging out!');
	});
	function logout() {
		console.log('logout!');
		if(dataSent) {
			return false;
		}
		dataSent = true;

		$.ajax({
			type: 'POST',
			data : { type: 'logout' },
			url: "/a/",
			success: function(data){
				try {
					data = jQuery.parseJSON(data);
					if (data.sucess !== "false") {
									//refreshBlock('header', $('header'), setAccountNav);
									location.reload(true);
								}
								dataSent = false;
							}catch(e) {
								console.log(e);
								console.log(data);
							}
						}
					});
				}</script>