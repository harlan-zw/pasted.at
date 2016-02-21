jQuery.prototype.exists = function() {
	return this.length > 0;
}
var relPath = '/';
$(document).ready(load());
/* used for hacky dragging events */
var timeout = -1, det = -1, showDrag = false;
var dataSent = false;
var hadEditor = false;

/**
 * Function is called when the document is ready, we should bind our
 * events here.
 */
 function load() {

 	handleResize();
 	/* misc event handlers */
 	$(window).on('resize', handleResize);
 	$(window).on('keydown', handleKeyDown);
 	$(window).on('paste', pasteInterpreter);
 	$(window).on('imagePaste', imagePaste);
 	$(window).on('textPaste', textPaste);
 	/* make icons within anchor tags display hover with them */
 	$('a').hover(function() {
 		$(this).children('i').css('color', 'black');
 		$(this).children('i').css('font-size', '1.3em');

 	}, function(){
 		$(this).children('i').css('color', 'rgb(166, 226, 46)');
 		$(this).children('i').css('font-size', '1em');

 	});


	// simple fade in for main box, gives box main visual focus
	$( ".content_container" ).css('display', 'none');
	$( ".content_container" ).fadeIn(1000);

	/* handles the dragging of images / files to the window */
	$('html').on('dragenter', function(e) {
		if (getTabInUse() == 'both') {
			showDrag = true; 
			lightsOff();
		}
	});
	$('html, #editor').bind('dragover', function(e){
		showDrag = true; 

		e.preventDefault();

		if ($(this).attr('id') !== 'editor') {
			/* if we had the editor and then lost it */
			/* we will wait 200ms and see if we regained it in that time */
			/* if we did then we don't clear the effect */
			/* i'm a genius XD */
			if (hadEditor) {
				hadEditor = false;
				clearTimeout( det );
				det = setTimeout(function() {
					if(!hadEditor){
						e.originalEvent.dataTransfer.effectAllowed = 'none';
						e.originalEvent.dataTransfer.dropEffect = 'none';
					}
				}, 200);
			} else {
				e.originalEvent.dataTransfer.effectAllowed = 'none';
				e.originalEvent.dataTransfer.dropEffect = 'none';
			}

		} else {
			hadEditor = true;
			e.originalEvent.dataTransfer.effectAllowed = 'copy';
			e.originalEvent.dataTransfer.dropEffect = 'copy';
		}
	});
	$('#editor').bind('drop', function(e) {
		e.preventDefault();
		/* we are putting content into text editor, switch to it */
		switchToTextEditor();
		/* process file into the editors content */
		var files = e.originalEvent.dataTransfer.files;
		/* we only process the first file supplied */
		var file = files[0];
		/* need the ace modelist module to get the syntax highlighting for extension */
		var modelist = ace.require("ace/ext/modelist");
		var mode = modelist.getModeForPath(file.name);
		if (mode != false)
			/* set the syntax to that of the file we're uploading */
		editor.getSession().setMode(mode.mode);
		else
			/* otherwise we just use plain text */
		editor.getSession().setMode("ace/mode/text");

		/* we use the file reader class to read the contents of the file, then we set it as the editors value */
		var reader = new FileReader();
		reader.onload = function(e) {
			var text = e.target.result;
			editor.getSession().setValue(text);
		}
		reader.readAsText(file);
		$('#paste_name').val(file.name);
		$('#mode').val(mode.name);
		$("#mode").trigger("chosen:updated");


		lightsOn()
	});


/* for this to work we need to use some hacky timeout stuff*/
	/*
	 after we leave the html element we will wait 200 ms to see if we have still left it if we have then we will turn the lights back on
	 */
	 $('html').on('dragleave', function(e) {
	 	showDrag = false;
	 	clearTimeout( timeout );
	 	timeout = 
	 	setTimeout(
	 		function() {
	 			if(!showDrag){
	 				lightsOn();
	 			}
	 		}
	 		, 200);
	 });
	 /* put content inside our text editor, do it here because html messes up the tab indexing */
	 $('#mode').change(modeChange);
	 $('#expiration').change(expirationChange);

	 $("#text_form").submit(handleTextFormSubmit);
	 $("#register_form").submit(handleRegisterForm);
	 /* account dropdown handling */
	 setAccountNav();

	 $('.fa-times-circle').click(function() {
	 	$(this).parent('div').addClass('hidden');
	 });
	 $('.login-exit').click(function() {
	 	lightsOn();
	 	$(this).parent('div').addClass('hidden');
	 });

	 $('.table_paste_delete').click(function() {		
	 	var id = $(this).parent().parent().children('td:first-child').text();
	 	$.ajax({
	 		type: 'POST',
	 		url : relPath + 'p/',
	 		data : { 
	 			paste_id: id,
	 			type: 'DELETE'
	 		},
	 		success: function(data) {
	 			location.reload(true);
	 		}
	 	});
	 });
	 $('#file_form').submit(function(e) {
	 	e.preventDefault();
	 	Dropzone.forElement('.dropzone').processQueue(); // Providing a selector string.
	 	return false;
	 });
	 	enquire.register("all and (max-width: 470px)", handleMobileChanges, true); //

	enquire.register("all and (max-width: 768px)", handleTightWidth, true); //

	$('.account-dropdown li').hover(function() {
		$(this).css('background-color', '#a6e22e');
	}, function() {
		$(this).css('background-color', 'white');
	});
}

function handleMobileChanges() {
	$('.header-account-container').hide();
}


function handleTightWidth() {
	if (getTabInUse() == 'text' || getTabInUse() == 'file' ) {
		$('.content_container').css('height', '97%');
	}

}


function setAccountNav() {
	$('.header-account-container').hover(function() {
		$('.account-dropdown:not(.side-menu)').fadeIn(100);
	}, function() {
		$('.account-dropdown:not(.side-menu)').fadeOut(100);
	});
	$('.side-menu').parent().hover(function() {
		$('.side-menu').parent().children('i').css('opacity', '0.7');
		$('.side-menu').fadeIn(100);
	}, function() {
		$('.side-menu').parent().children('i').css('opacity', '1');
		$('.side-menu').fadeOut(100);
	});
	$('#login-link').click(function() {
		lightsOff(false);
		$('#login-div').removeClass('hidden');
	});

	
}

function handleRegisterForm(e) {
	e.preventDefault();
	e.stopPropagation();

	if(dataSent) {
		return false;
	}
	if (!validEmail($('#register_email').val())) {
		$('#register_form div:first-child').removeClass('hidden');
		$('#register_form div:first-child span').text('Invalid email address!');
		return false;
	}
	if ($('#register_pass').val().length < 6) {
		$('#register_form div:first-child').removeClass('hidden');
		$('#register_form div:first-child span').text('Password must be 6 characters long.');
		return false;

	}
	if ($('#register_pass').val() != $('#register_conf_pass').val()) {
		$('#register_form div:first-child').removeClass('hidden');
		$('#register_form div:first-child span').text('Passwords did not match!');
		return false;
	}

	//cancel current event
	var formData =  $("#register_form").serialize();
	dataSent = true;
	//return false;
	$('#register_form div:first-child').addClass('hidden');

	$.ajax({
		type: 'POST',
		url: relPath + "a/",
		data: formData ,
		success: function(data){
			try {
				data = jQuery.parseJSON(data);
				if (data.sucess === "false") {
					/* data failed */
					if (typeof data.field !== 'undefined') {
						/* if the field responsible for failure is sent we give it the error class */
						$('#' + data.field).addClass('error');
					}
					if (typeof data.error !== 'undefined') {
						/* if the error is sent we display the error box */
						$('#register_form div:first-child').removeClass('hidden');
						$('#register_form div:first-child span').text(data.error);
					}
					dataSent = false;
				} else {
					/* sucess! tell them to login. */
					$('#register_form div:first-child').removeClass('hidden').removeClass('error').addClass('success');
					$('#register_form div:first-child span').text('Your account has been sucesfully created. You will be redirected to the main page in 5 seconds.');
					var countdown = 5;
					setInterval(function() {
						countdown--;
						$('#register_form div:first-child span').text('Your account has been sucesfully created. You will be redirected to the main page in ' + countdown + ' seconds.');
						if (countdown <= 0) {
							window.location = relPath;
						}
					}, 1000);
				}
			} catch(e) {
				$('#register_form div:first-child').removeClass('hidden');
				$('#register_form div:first-child span').text('An error has occured, please contact us!');
				return false;
			}
		}
	});
return false;
}

function handleTextFormSubmit(e){
	e.preventDefault();
	e.stopPropagation();
	if(dataSent) {
		return false;
	}
	//no need to validate form? maybe check that there is text
	if (editor.getSession().getValue() <= 0) {
		//please enter something to upload...
		return false;
	}
	var formData = $("#text_form").serializeArray();
	formData.push({
		name: "data",
		value: editor.getSession().getValue().replaceAll("\n", '</br>')
	});
	formData = $.param(formData);
	dataSent = true;
	//return false;
	$('#editor_settings, .editor_home_container').hide();
	var windowHeight = $(window).height();
	$('.container').css('height', windowHeight + 'px'); 

	$('#text_upload_load').fadeIn();
	$.ajax({
		xhr: function() {
			var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function(evt) {
				/* uploading data percent */
				if (evt.lengthComputable) {
					var percentComplete = (evt.loaded / evt.total)*100;
					$('#upload_percent').text(percentComplete);
					$('#progressbar div:last-child').css('width', percentComplete + '%');
				}
			}, false);
			return xhr;
		},
		type: 'POST',
		url: relPath + "p/",
		data: formData ,
		success: function(data){
			try {
				console.log(data);
				data = jQuery.parseJSON(data);
				if (data.sucess) {
					window.location = relPath + "p/" + data.id;
				} else {
					if (data.error !== false) {
						$('#text_upload_error span').text(data.error);
					}
					$('#text_upload_error').fadeIn();
					$('#text_upload_load').hide();
				}
			} catch(e) {
				console.log(e);
				$('#text_upload_error').fadeIn();
				$('#text_upload_load').hide();
			}
		},
		fail: function(e){
			console.log(e);
			$('#text_upload_error').fadeIn();
			$('#text_upload_load').hide();
		}
	});
return false;
}

function refreshBlock(name, replace, callback) {
	$.ajax({
		type: 'POST',
		data : { type: 'content_block', content_block: name},
		url: relPath + "lunor/",
		success: function(data){
			try {
				$(replace).replaceWith(data);
				callback();
			}catch(e) {
				
			}
		}
	});
}

function validEmail(email) {
	var atpos = email.indexOf("@");
	var dotpos = email.lastIndexOf(".");
	if (atpos< 1 || dotpos<atpos+2 || dotpos+2>=email.length) {
		return false;
	}
	return true;
}

function handleResize() {
	var windowHeight = $(window).height();
	var footerHeight = $('footer').height() + $('header').height() + 20;
	if (getTabInUse() === 'text') {
		/* we need to change the editor height depending on container size */
		var editorHeight = $('#editor_settings').height() + 65;
		$('.editor_home_container').css('height', 'calc(100% - ' + editorHeight + 'px)');

	} else if (getTabInUse() == 'file') {
		var editorHeight = $('#file_editor_settings').height() + 65;
		$('#drop_zone_container').css('height', 'calc(100% - ' + editorHeight + 'px)');
	}
	if (!$('#original_image, .block-content-container').exists()) {
		$('.content').css('height',  'calc(100% - ' + footerHeight + 'px)');
		$('.container').css('height', windowHeight + 'px'); 
	}
	if (typeof editor !== 'undefined')
		editor.resize(true);


}

function switchToTextEditor(text) {
	tabSelected = true;
	$('.file_tab').hide();
	//$('.text_tab').css('height', '600px');
	$('#editor_settings').removeClass('hidden');
	$('.editor_home_container').addClass('editor_home_editing');
	$('.text_tab').removeClass('col_6');
	$('.text_tab').addClass('col_12');
	//$('.upload_slide').removeClass('grid');
	$('.upload_slide .top-padding').hide();

	handleTightWidth();
	handleResize();
	editor.focus();
	editor.getSession().setMode("ace/mode/text");
	editor.setValue(text);
	editor.setReadOnly(false);
	editor.resize(true);
	editor.gotoLine(99999, 0, true);
}
function switchToFileUpload() {
	tabSelected = true;
	$('.text_tab').hide();
	//$('.text_tab').css('height', '600px');
	$('.file_tab').removeClass('col_6');
	$('.file_tab').addClass('col_12');
	$('#file_editor_settings').removeClass('hidden');

	handleTightWidth();
	handleResize();
	lightsOn();
}
/**
* Handles the change of the expiration input.
* Will add the views input box or remove it if required.
*/
function expirationChange(e) {
	var component = '#expiration';
	if (getTabInUse() === 'file') 
		component = '#file_expiration';
	var newMode = $(component + ' option:selected').val();
	var container = $(component).parent().parent();
	/* need to enable view input box */
	if (newMode == 'views') {
		/* we use parent() parent() so we can change class in future */
		/* make the inputs div visible */
		container.find('input').parent().css('display','visible');
		/* make chosen div a column */
		$('#expiration_chosen').parent().addClass('col_6');
	} else if ($('#expiration_chosen.col_6')) {
		/* need to remove the view input box and restore width */
		$('#expiration_chosen').parent().removeClass('col_6');
		container.find('input').parent().css('display','none');
	}

}
function modeChange(e) {
	/* only if the editor has been enabled by typing / paste event */
	if (getTabInUse() === 'text') {
		var newMode = $('#mode option:selected').val();
		editor.getSession().setMode("ace/mode/" + newMode);
	}
}

function handleKeyDown(e) {
	if (e.which >= 32 && e.which <= 126 && !e.ctrlKey) {
		var keyChar = String.fromCharCode(e.which);
		/* if we're not in the file tab then we need to check for keys */
		if (typeof editor !== 'undefined' && getTabInUse() === 'both') {
			if (editor.getReadOnly() && editor.isFocused()) {
				switchToTextEditor(keyChar);
				return false;
			}
		}
	}
	return true;
}
function lightsOn() {
	/* need to figure out, if we are dropping, if are dropping editable text file */
	$('.lights_off').css('display', 'none');
	$('#editor').css('z-index', '1');
	$('.dropzone').css('z-index', '1');
}
function lightsOff(drag) {
	if (typeof drag === 'undefined' || drag === true) {
		$('#editor').css('z-index', '10000');	
		$('.dropzone').css('z-index', '10000');
	}
	$('.lights_off').css('display', 'block');
}
function isLightOn() {
	return  $('.lights_off').css('display') == 'none';
}
/* 
* Event handler for the paste of an image.
* @param Event event the event data
* @param Blob image the data for the image
*/
function imagePaste(event, args) {
	$('.upload_slide').prepend('<img src="' + args + '" />')
}

/* 
* Event handler for the paste of an image.
* @param Event event the event data
* @param String text the text that was posted
*/
function textPaste(event, text) {
	if (typeof editor !== 'undefined' && getTabInUse() == 'both') {
		switchToTextEditor(text);
	}
	/* anything else will be picked up by the paste event of ace */
}
/**
 * Function used to get the name of the tab in use.
 * @return 
 *		will return 'file' if the file tab is in use
 *		will return 'text' if the text tab is in use
 *		otherwise will return 'none' if neither is in use (base screen)
 */
 function getTabInUse() {
 	var textTab = $('.text_tab').length > 0 ? $('.text_tab').css('display') !== 'none' : false;
 	var fileTab = $('.file_tab').length > 0 ? $('.file_tab').css('display') !== 'none' : false;
 	if (textTab && fileTab) {
 		return 'both';
 	} else if (textTab) {
 		return 'text';
 	} else if (fileTab) {
 		return 'file';
 	}
 	return 'none';
 }

 /* Handle paste events */
 function pasteInterpreter(e) { 
 	/* in case one of them fails we add fallbacks */
 	var clipboard = e.originalEvent.clipboardData || e.clipboardData || window.clipboardData;

	// We need to check if event.clipboardData is supported (Chrome)
	// Get the items from the clipboard
	var items = clipboard.items;
	if (items) {
		// Loop through all items, looking for any kind of image
		for (var i = 0; i < items.length; i++) {
			/* if type of file contains the word image then we presume it's an image */
			if (items[i].type.indexOf("image") !== -1) {
				// We need to represent the image as a file,
				var blob = items[i].getAsFile();
				// and use a URL or webkitURL (whichever is available to the browser)
				// to create a temporary URL to the object
				var URLObj = window.URL || window.webkitURL;
				var source = URLObj.createObjectURL(blob);
				e.preventDefault();

				// The URL can then be used as the source of an image
				$(document).trigger('imagePaste', source);

				return;
			}
		}
	}
	/* if the code got to this point we are posting text instead of an image */
	var text = clipboard.getData("Text");
	$(document).trigger('textPaste', text);
	return true;
}


if (typeof window['Dropzone'] !== 'undefined') {
	/* Configures the options for Dropzone container */

	Dropzone.options.fileDropZone = {
		paramName: "tmp.file", // The name that will be used to transfer the file
		parallelUploads: 1, //how many files can be uploaded at once
		maxFilesize: 16, // MB
		maxFiles: 1,
		/* size of the thumbnail & quality */
		maxThumbnailFilesize: 16,
		thumbnailWidth: 300,
		addRemoveLinks: true,
		autoProcessQueue: false,
		thumbnailHeight: 300,
		url: relPath + 'p/',

		accept: function(file, done) {
			switchToFileUpload();
			done();
		},
		success: function(file, data) {
			try {
				data = jQuery.parseJSON(data);
				if (data.sucess) {
					window.location = relPath + "p/" + data.id;
				} else {
					if (data.error !== false) {
						$('#text_upload_error span').text(data.error);
					}
					$('#text_upload_error').fadeIn();
					$('#text_upload_load').hide();
				}
			}catch(e) {
				$('#text_upload_error').fadeIn();
				$('#text_upload_load').hide();
			}
		}

	};
}

/**
 * ReplaceAll by Fagner Brack (MIT Licensed)
 * Replaces all occurrences of a substring in a string
 */
 String.prototype.replaceAll = function( token, newToken, ignoreCase ) {
 	var _token;
 	var str = this + "";
 	var i = -1;

 	if ( typeof token === "string" ) {

 		if ( ignoreCase ) {

 			_token = token.toLowerCase();

 			while( (
 				i = str.toLowerCase().indexOf(
 					token, i >= 0 ? i + newToken.length : 0
 					) ) !== -1
 				) {
 				str = str.substring( 0, i ) +
 			newToken +
 			str.substring( i + token.length );
 		}

 	} else {
 		return this.split( token ).join( newToken );
 	}

 }
 return str;
};
