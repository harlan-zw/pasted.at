<!DOCTYPE html>
<html class="no-js">
<head>
	<?php 

		publishHTMLInclude('all.min.css');//

		publishHTMLInclude('vendor/enquire.js');

		?>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php trigger('script_load'); ?>
		<?php trigger('meta_load'); ?>

		<script>
			webshim.polyfill('es5 mediaelement forms filereader canvas');

		</script>
	</head> 
	<!-- Container for the entire page -->
	<body>
		<div class="container">
			<div class="lights_off">
			</div>
			<!-- Container for the header elements such as logo and links -->
			<?php contentBlock('header') ?>
			<!-- Container for all content -->
			<div class="content">	
				<?php triggerContent(); ?>
			</div>
			<?php contentBlock('footer') ?>
		</div>
		<?php publishHTMLInclude('main.min.js');?>
		<!-- Piwik -->
		<script type="text/javascript">
			var _paq = _paq || [];
			_paq.push(['trackPageView']);
			_paq.push(['enableLinkTracking']);
			(function() {
				var u=(("https:" == document.location.protocol) ? "https" : "http") + "://pasted.at/analytics/";
				_paq.push(['setTrackerUrl', u+'piwik.php']);
				_paq.push(['setSiteId', 1]);
				var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
				g.defer=true; g.async=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
			})();
		</script>
		<noscript><p><img src="http://pasted.at/analytics/piwik.php?idsite=1" style="border:0;" alt="" /></p></noscript>
		<!-- End Piwik Code -->
	</body>

	</html>
