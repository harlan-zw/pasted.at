<?php
$paste = getViewPaste();
if (!isset($paste) || empty($paste)) {
	Lunor::$base->router->throwError('404', false);
	return;
}
$info = getimagesize($paste->datapath);

?>
<!-- Container for the slide -->
<div id="image_slide">
	


	<div class="top-padding"></div>

	<div class="paste_info_container center row" style="width: 700px;">
		<div class="paste_info align_right" style="float:right; padding-right: 20px;">
			<span>VIEWS: <?php echo $paste->views; ?></span>
		</div>
		<div class="paste_info">
			<h2><?php echo $paste->getTitle(); ?></h2>
		</div>
		<div class="paste_info">
			<span>BY: <?php echo $paste->findAuthor(); ?> ON <?php echo $paste->getTextualSince(); ?> </span>
		</div>

		<div class="paste_info">
			<a href="#" id="edit-dynamic"><i class="fa fa-pencil"></i>Edit</a>
			<a href="#" id="download_blob"><i class="fa fa-cloud-download"></i>DOWNLOAD</a>
			<a <?php generateLink('contact'); ?>><i class="fa fa-exclamation-triangle"></i>REPORT ABUSE</a>
			<a href="#"><i class="fa fa-print"></i>PRINT</a>
		</div>
	</div>
	<div class="top-padding"></div>
	
	<div id="canvas_wrapper" style="margin-bottom: 20px;" class="center">
		<div id="image_toolbar" class="clearfix hidden">
			<table>
				<tr>
					<td>Colour</td>
					<td><input type="color"></td>
				</tr>
				<tr>
					<td>Tool</td>
					<td><select>
						<option selected>Line</option>
						<option>Rectangle</option>
						<option>Circle</option>
						<option>Pen</option>
						<option disabled>Crop (soon)</option>
						<option disabled>Text  (soon)</option>
					</select></td>
				</tr>
				<tr>

					<td>Line Width
					</td>
					<td>
						<input type="range" min="1" max="10" value="1">
					</td>
				</tr>
			</table>
			<a href="#" id="save-dynamic"><i class="fa fa-check"></i>Save</a>
			<a href="#" id="exit-dynamic"><i class="fa fa-times"></i>Cancel</a>

		</div>

		<img id="original_image" <?php echo $info[3]; ?> alt="Our original image, hidden." src="http://pasted.at/f/<?php echo $paste->id; ?>">

		<canvas id="text_image_zone" class="hidden center left"  width="700" height="500" class="center" style="z-index: 100; background-color: rgba(0, 0, 0, 0); position: absolute;">

		</canvas>
		<canvas id="crop_image_zone" class="hidden center left" width="700" height="500" class="center" style="z-index: 100; background-color: rgba(0, 0, 0, 0); position: absolute;">

		</canvas>
		<canvas id="image_zone"  width="700" height="500" class="hidden center left">
			Canvas not supported!
		</canvas>
		<canvas id="backing_image_zone" class="hidden center left"  width="700" height="500">
			<!-- this is our virtual canvas that we store drawings to -->
		</canvas>
		<div class="top-padding clearfix"></div>

	</div>
</div>

<script>
	var imgWidth = $('#original_image').width();
	var imgHeight = $('#original_image').height();

	$('canvas').attr('height', imgHeight);
	$('canvas').attr('width', imgWidth);
	$('#canvas_wrapper').css({
		'height': imgHeight,
		'width': imgWidth
	});

	$('#edit-dynamic').click(function() {
		$('#image_toolbar, #image_zone').removeClass('hidden');
		$('#original_image, .paste_info_container').addClass('hidden');
		$('#image_toolbar').css({
			'left': $('#original_image').position().left - $('#image_toolbar').width() - 30
		});
	});

	$('#image_slide').css('min-width', imgWidth);
	var getPasteId = function() {
		var re = /p\/([a-zA-Z0-9]*)/i; 
		var str = window.location;
		var result = re.exec(window.location);
		return result[1];
	};
	$('#download_blob').attr('href', "http://pasted.at/f/" + getPasteId());
</script>
<?php publishHTMLInclude('image/image.js'); ?>
