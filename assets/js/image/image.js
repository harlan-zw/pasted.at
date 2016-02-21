$(document).ready(load);

var renderer = false, canvas = false, context = false;

var tools = { 'pen':null, 'line':null, 'rectangle':null, 'circle':null, 'text':null};


function load() {
	if (typeof resourceURL === 'undefined') {
		resourceURL = '';
	}
	console.log(resourceURL);
	/* we need to renderer.js file to continue */
	/* loading the dependent scripts dynamically allows to only include this file in the html */
	if (typeof Renderer !== 'undefined') {
		console.log('not using get script renderer');
		rendererLoaded();
	} else
	$.getScript(resourceURL + 'js/image/renderer.js', rendererLoaded).fail(function(e) {
		console.log('Failed to load renderer.js!');
		console.log(e);
	});

}

function rendererLoaded() {
	/* the different tools available */
	/* due to how jquery selects work we need to use get(0) to get
	real canvas object */
	canvas = $('#image_zone').get(0);
	/* we use a 2d context */
	context = canvas.getContext('2d')
	var backingContext = $('#backing_image_zone').get(0).getContext('2d');
	var cropContext = $('#crop_image_zone').get(0).getContext('2d');
	var textContext = $('#text_image_zone').get(0).getContext('2d');

	var image = new Image();
	image.src = $('#original_image').attr('src');
	image.width = $('#original_image').attr('width');
	image.height = $('#original_image').attr('height');

	console.log(image);

	context.drawImage(image, 0, 0);
	backingContext.drawImage(image, 0, 0);

	/* we use the line renderer as our default renderer */
	renderer = new Renderer(canvas, context, backingContext,textContext, cropContext);
	/* set to use the line tool as default */
	if (typeof window['Line'] !== 'undefined') {
		tools['line'] = new Line();
		renderer.setTool(tools.line);
	} else {
		$.getScript(resourceURL + 'js/image/includes/line.js', function() {
			tools['line'] = new Line();
			renderer.setTool(tools.line);
		});
	}
	$('canvas').mousedown(renderer.mouseDown);
	$('canvas').mouseup(renderer.mouseUp);
	$('canvas').mousemove(renderer.mouseMoved);
	$('html').keypress(function(e) {
		e.preventDefault();
		console.log(e);
		renderer.keyPress(e);
		return false;
	});
	$('html').keydown(function(e) {
		//renderer.keyPress(e);
		if (e.keyCode == 8) {
			//backspace
			renderer.keyPress('backspace');
			e.preventDefault();
			return false;
		}
		return true;
	});

	$('#backing_image_zone').mousedown(mouseDown);

	/* our input types for our canvas */
	$('select').change(toolSelected);
	$('input[type="color"]').change(colorSelected);
	$('input[type="range"]').change(lineWidthChange);

	$('#save-dynamic').click(function() {
		exitImageEditor(true);
	});
	$('#exit-dynamic').click(function() {
		exitImageEditor(false);
	});
}

function exitImageEditor(save) {
	$('#image_toolbar, #image_zone').addClass('hidden');
	$('#canvas_wrapper').css({
		'width': imgWidth,
	});
	$('.paste_info_container, #original_image').removeClass('hidden');
	if (save === true) {
		/* need to make a requestot change the source image */
		$('#original_image').attr('src', $('#backing_image_zone').get(0).toDataURL("image/png"));
	}
}


function toolSelected(e) {
	var toolName = $('option:selected').text()
	if (typeof window[toolName] !== 'undefined') {
		console.log(window[toolName]);
		console.log('not using get script ' + toolName);

		renderer.setTool(tools[toolName.toLowerCase()] = new window[toolName]);
	} else 
	$.getScript(resourceURL + 'js/image/includes/' + toolName.toLowerCase() + '.js', function() {
		/*  accesses the class dynamically */
		console.log(toolName.toLowerCase());
		console.log(window[toolName]);
		renderer.setTool(tools[toolName.toLowerCase()] = new window[toolName]);
	}).fail(function() {
		console.log('Failed to load: ' . toolName);
	});
}

function lineWidthChange(e) {
	renderer.setDrawOption('lineWidth', $('input[type="range"]').val());
}

function colorSelected(e) {
	renderer.setDrawOption('strokeStyle', e.originalEvent.srcElement.value);
}

function mouseDown(e) {
	renderer.switchContext(false);
	/* when the user puts mouse down on the backing buffer we switch to the temp one */
	canvas.mousedown(e);
}

String.prototype.splice = function( idx, rem, s ) {
	return (this.slice(0,idx) + s + this.slice(idx + Math.abs(rem)));
};