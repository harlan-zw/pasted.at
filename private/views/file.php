<!-- Container for the slide -->
<?php
$paste = getViewPaste();
if (!isset($paste) || empty($paste)) {
	Lunor::$base->router->throwError('404', false);
	return;
}
?>
<!-- Text upload container -->
<div class="block-content-container">
	<div class="paste_info_container row">
		<div class="paste_info align_right" style="float:right; padding-right: 20px;">
			<span>SIZE: <?php echo $paste->findDataLength(); ?>  |  VIEWS: <?php echo $paste->views; ?></span>
		</div>
		<div class="paste_info">
			<h2><?php echo $paste->getTitle(); ?></h2>
		</div>
		<div class="paste_info">
			<span>BY: <?php echo $paste->findAuthor(); ?> ON <?php echo $paste->getTextualSince(); ?> </span>
		</div>

		<div class="paste_info">

			<a href="#" id="download_blob" type="_blank"><i class="fa fa-cloud-download"></i>DOWNLOAD</a>
			<a <?php generateLink('contact'); ?>><i class="fa fa-exclamation-triangle"></i>REPORT ABUSE</a>
			<a href="#"><i class="fa fa-print"></i>PRINT</a>
		</div>


	</div>
	<div class="top-padding"></div>
	<hr>
	<div class="top-padding"></div>

	<i class="fa fa-cog fa-5 fa-file"></i>
	<div class="top-padding"></div>

</div>

<!-- include our custom scripts -->

<!-- scripts required for ace -->
<script type="text/javascript">
	var getPasteId = function() {
		var re = /p\/([a-zA-Z0-9]*)/i; 
		var str = window.location;
		var result = re.exec(window.location);
		return result[1];
	};
	$('#download_blob').attr('href', "http://pasted.at/f/" + getPasteId());
</script>
<!-- script used to make large select inputs easier for the user -->
