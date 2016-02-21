<!-- Container for the slide -->
<?php
$paste = getViewPaste();
if (!isset($paste) || empty($paste)) {
	Lunor::$base->router->throwError('404', false);
	return;
}
$syntax = $paste->getSyntax();
?>
<div class="upload_slide">
	<div class="top-padding"></div>
	<!-- Text upload container -->
	<div class="static-content_container">
		<div class="paste_info_container row">
			<div class="paste_info align_right" style="float:right; padding-right: 20px;">
				<span>SYNTAX: <span id="syntax_name"></span> |  SIZE: <?php echo $paste->findDataLength(); ?>  |  VIEWS: <?php echo $paste->views; ?></span>
			</div>
			<div class="paste_info">
				<h2><?php echo $paste->getTitle(); ?></h2>
			</div>
			<div class="paste_info">
				<span>BY: <?php echo $paste->findAuthor(); ?> ON <?php echo $paste->getTextualSince(); ?> </span>
			</div>

			<div class="paste_info">

				<a href="#" id="copy-dynamic"><i class="fa fa-clipboard"></i>Copy</a>
				<a href="#" id="download_blob"><i class="fa fa-cloud-download"></i>DOWNLOAD</a>
				<a href="#" id="raw-text"><i class="fa fa-external-link"></i>RAW</a>
				<a <?php generateLink('contact'); ?>><i class="fa fa-exclamation-triangle"></i>REPORT ABUSE</a>
				<a href="#"><i class="fa fa-print"></i>PRINT</a>
			</div>


		</div>
		<i id="text_loading" class="fa fa-cog fa-4 fa-spin vertical-align-100"></i>
		
		<div class="editor_view_container center">
			<div class="hidden"><?php echo addslashes($paste->getFormattedData()); ?></div>
			<div id="editor"></div>
		</div>

	</div>

</div>
<!-- include our custom scripts -->

<?php
publishHTMLInclude('vendor/jquery.zclip.min.js');
publishHTMLInclude('vendor/FileSaver.min.js');
publishHTMLInclude('vendor/Blob.min.js');

publishHTMLInclude('vendor/ace/ace.js');
publishHTMLInclude('vendor/ace/ext-modelist.js');

?>


<!-- scripts required for ace -->
<script type="text/javascript">
	var editor = ace.edit("editor");
// load ace and extensions
$(document).ready(function() {


	editor.setTheme("ace/theme/monokai");
	editor.setOption("hScrollBarAlwaysVisible", false);
	editor.setOption("vScrollBarAlwaysVisible", false);
	editor.setShowPrintMargin(false);
	editor.setAnimatedScroll(true);
	editor.getSession().setUseWorker(false);

	editor.getSession().setValue(unescape($('.editor_view_container div:first-child').text()));
	/* for some reason is adding an '11' to the value, here we simply remove the last 2 characters */
	editor.getSession().setValue(editor.getSession().getValue().substring(0, editor.getSession().getValue().length -2 ));
	//editor.getSession().setMode("ace/mode/<?php echo $syntax; ?>");
	var modelist = ace.require("ace/ext/modelist");

	editor.resize(true);




	$('#text_loading').hide();


	$('#copy-dynamic').zclip({
		path: resourceURL + 'js/vendor/ZeroClipboard.swf',
		copy:function() {
			return editor.getSession().getValue();
		},
		afterCopy:function(){
			alert('Text copied to clipboard!');
		}
	});

	var findCaption = function(curMode) {
		/* find the extensions for the caption */
		for (var i = 0; i < modelist.modes.length; i++) {
			var mode = modelist.modes[i];
			if (mode.name === curMode) {
				return mode.caption;
			}
		}
		return false;
	}
	var getPasteId = function() {
		var re = /p\/([a-zA-Z0-9]*)/i; 
		var str = window.location;
		var result = re.exec(window.location);
		return result[1];
	}
	var syntax = "<?php echo $syntax; ?>";
	var caption = findCaption(syntax);
	$('#syntax_name').text(caption);
	editor.getSession().setMode("ace/mode/" + syntax);

	var findExtension = function(curMode) {
		var extension;
		/* find the extensions for the caption */
		for (var i = 0; i < modelist.modes.length; i++) {
			var mode = modelist.modes[i];
			if (mode.caption === curMode) {
				extension = mode.extensions;
				break;
			}
		}
		/* get the first extension type */
		extension = extension.substr(0, extension.indexOf('|')).toLowerCase();
		return extension;
	}
	$('#raw-text').click(function() {
		var w = window.open();
		
		$(w.document.body).html('<p>' + editor.getSession().getValue().replaceAll('\n', '<br>').replaceAll('\t', '&emsp;') + '</p>');
	});
	$('#download_blob').click(function() {

		var blob = new Blob([editor.getSession().getValue()], {type: "text/plain;charset=utf-8"});
		saveAs(blob, getPasteId() + "." + findExtension(caption));
	});
});
</script>
<!-- script used to make large select inputs easier for the user -->
