<style>
	#image_container {
		width: 96px;
		height: 112px;
	}
	.uploaded {
		transition: 300ms;
	}
	#uploaded_link {
		color: #333 !important;
	}
	#uploaded_link i {
		color: #333 !important;
	}
	.last_50 {
		padding: 0 !important;
	}
</style>
<div class="block-content-container">
	<h2 class="inline">Popular Pastes</h2>
	<hr>
	<div class="last_50 grid clearfix">
		<?php

		PasteHandler_PasteAPI::getMostPopular(24, function($paste) {
			publish('<a href="http://pasted.at/p/' . $paste->id . '/" id="uploaded_link" class="unique_link">');
			publish('<div class="uploaded col_3">');
			$title = $paste->getTitle();
			if (strlen($title) > 14) {
				$title = substr($title, 0, 14) . '...';
			}
			publish('<h4>' . $title . '</h4>');
			if ($paste->type == 'FILE') {
				publish('<i class="fa fa-4 fa-file"></i>');
			} else if ($paste->type == 'TEXT') {
				publish('<i class="fa fa-4 fa-file-code-o"></i>');
			} else if ($paste->type == 'IMAGE') {
				publish('<div class="center" id="image_container">');
				publish('<img width="96" height="112" src="data:image/jpeg;base64,' . base64_encode( make_thumb($paste->datapath, 96) ) . '">');
				publish('</div>');
			}
			publish('<span style="display:block;">By: ' . $paste->findAuthor() . '</span>');
			publish('<span style="display:block;">Views: ' . $paste->views . '</span>');

			publish('</div>');
			publish('</a>');
		});
		?>
	</div>
</div>
<script>
	$('.uploaded').hover(function() {
		console.log('hover.');
		$(this).addClass('clearfix');
		$(this).css('background-color', 'rgb(166, 226, 46)');
	}, function() {
		$(this).removeClass('clearfix');
		$(this).css('background-color', 'white');
	});

</script>