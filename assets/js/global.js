$(document).ready(load());


function load() {

	/* if our page contains a .chosen class */
	if (exists('.chosen')) {
		/* we load in the script on demand */
		$('.chosen').chosen({
			width: '85%',
			inherit_select_classes: true,
			search_contains: true,
			no_results_text: "Oops, syntax not found!",
			disable_search_threshold: 10,
		});
	}
	/* init ace script */
	if (exists('#editor')) {
		var editor = ace.edit("editor");		
		editor.setTheme("ace/theme/monokai");
		editor.getSession().setMode("ace/mode/javascript");
		editor.setReadOnly(true);
	}

	/* scripts are loaded, can remove loading gifs */
	$('#file_loading').hide();
	$('#text_loading').hide();

}


function exists(selector) {
	return $(selector).length > 0;
}