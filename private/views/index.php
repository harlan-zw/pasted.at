<?php
//session_unset();
?>


<!-- Container for the slide -->
<div class="upload_slide grid">
	<div class="top-padding"></div>
	<!-- Text upload container -->
	<div class="content_container text_tab col_6">
		<h2>Text Upload</h2>
		<hr>
		<i id="text_loading" class="fa fa-cog fa-4 fa-spin vertical-align-100"></i>
		<div id="text_upload_error" class="vertical-align-100">
			<i class="fa fa-exclamation-triangle fa-4"></i>
			<div class="top-padding"></div>
			<h3>Paste Failed</h3>
			<p>It looks like the text you were trying to paste has failed to upload!</p>
			<span>This could be due to a server problem, network problem or something else.<br>
				If this problem persists please contact us.</span>
				<div class="top-padding"></div>
				<p>Please return to <a href="index.php">Home</a>.</p>
			</div>
			<div id="text_upload_load" class="vertical-align-100">
				<i class="fa fa-cog fa-4 fa-spin"></i>
				<div class="top-padding"></div>
				<h3>Uploading Paste. Please wait!</h3>
				<div class="top-padding"></div>

				<div class="center" id="progressbar">
					<div>
						<!-- text center -->
						<span id="upload_percent">0</span><span>%</span>
					</div>
					<div>
						<!-- progress amount -->
					</div>
				</div>


			</div>
			<div class="editor_home_container center">
				<div id="editor"></div>
			</div>
			<div id="editor_settings" class="clearfix hidden">
				<form id="text_form" class="col_12">

					<div class="setting col_3">
						<label for="mode" id="syntax" form="text_form">Syntax</label>
						<hr>

						<select id="mode" class="chosen" style="width: 100% !important;" name="paste_mode">
							<optgroup label="Common">
								<option value="text" selected>Plain Text</option>
								<option value="c_cpp">C/C++</option>
								<option value="java">Java</option>
								<option value="javascript">JavaScript</option>
								<option value="php">PHP</option>
								<option value="python">Python</option>
								<option value="ruby">Ruby</option>
								<option value="html">HTML</option>
								<option value="css">CSS</option>
							</optgroup>

							<optgroup label="Other">
								<option value="abap">ABAP</option><option value="actionscript">ActionScript</option><option value="ada">ADA</option><option value="apache_conf">Apache Conf</option><option value="asciidoc">AsciiDoc</option><option value="assembly_x86">Assembly x86</option><option value="autohotkey">AutoHotKey</option><option value="batchfile">BatchFile</option><option value="c9search">C9Search</option><option value="cirru">Cirru</option><option value="clojure">Clojure</option><option value="cobol">Cobol</option><option value="coffee">CoffeeScript</option><option value="coldfusion">ColdFusion</option><option value="csharp">C#</option><option value="curly">Curly</option><option value="d">D</option><option value="dart">Dart</option><option value="diff">Diff</option><option value="dot">Dot</option><option value="erlang">Erlang</option><option value="ejs">EJS</option><option value="forth">Forth</option><option value="ftl">FreeMarker</option><option value="gherkin">Gherkin</option><option value="glsl">Glsl</option><option value="golang">Go</option><option value="groovy">Groovy</option><option value="haml">HAML</option><option value="handlebars">Handlebars</option><option value="haskell">Haskell</option><option value="haxe">haXe</option><option value="html_ruby">HTML (Ruby)</option><option value="ini">INI</option><option value="jack">Jack</option><option value="jade">Jade</option><option value="json">JSON</option><option value="jsoniq">JSONiq</option><option value="jsp">JSP</option><option value="jsx">JSX</option><option value="julia">Julia</option><option value="latex">LaTeX</option><option value="less">LESS</option><option value="liquid">Liquid</option><option value="lisp">Lisp</option><option value="livescript">LiveScript</option><option value="logiql">LogiQL</option><option value="lsl">LSL</option><option value="lua">Lua</option><option value="luapage">LuaPage</option><option value="lucene">Lucene</option><option value="makefile">Makefile</option><option value="matlab">MATLAB</option><option value="markdown">Markdown</option><option value="mel">MEL</option><option value="mysql">MySQL</option><option value="mushcode">MUSHCode</option><option value="nix">Nix</option><option value="objectivec">Objective-C</option><option value="ocaml">OCaml</option><option value="pascal">Pascal</option><option value="perl">Perl</option><option value="pgsql">pgSQL</option><option value="powershell">Powershell</option><option value="prolog">Prolog</option><option value="properties">Properties</option><option value="protobuf">Protobuf</option><option value="r">R</option><option value="rdoc">RDoc</option><option value="rhtml">RHTML</option><option value="rust">Rust</option><option value="sass">SASS</option><option value="scad">SCAD</option><option value="scala">Scala</option><option value="smarty">Smarty</option><option value="scheme">Scheme</option><option value="scss">SCSS</option><option value="sh">SH</option><option value="sjs">SJS</option><option value="space">Space</option><option value="snippets">snippets</option><option value="soy_template">Soy Template</option><option value="sql">SQL</option><option value="stylus">Stylus</option><option value="svg">SVG</option><option value="tcl">Tcl</option><option value="tex">Tex</option><option value="textile">Textile</option><option value="toml">Toml</option><option value="twig">Twig</option><option value="typescript">Typescript</option><option value="vbscript">VBScript</option><option value="velocity">Velocity</option><option value="verilog">Verilog</option><option value="xml">XML</option><option value="xquery">XQuery</option><option value="yaml">YAML</option>
							</optgroup>
						</select>
					</div>
					<div class="setting col_3">
						<label for="expiration" form="text_form">Expiration</label>
						<hr>

						<div style="margin: 0 !important;">
							<select id="expiration" class="chosen" name="expiration">
								<option value="1_day">1 Day</option>
								<option value="1_week" selected>1 Week</option>
								<option value="1_month">1 Month</option>
								<option value="forever">Forever</option>
								<option value="views">Views..</option>
							</select>
						</div>
						<div class="col_6" style="display: none; margin: 0 !important;">
							<input type="number" min="1" max="100" value="1" name="views">
						</div>
					</div>
					<div class="setting col_3">
						<label for="exposure" form="text_form">Exposure</label>
						<hr>

						<select id="exposure" class="chosen" name="exposure">
							<option value="public" selected>Public</option>
							<option value="unlisted">Unlisted</option>
							<option value="password_protected" disabled>Password Protected</option>
							<option value="private" <?php if (!Account_AccountAPI::getLoggedIn()) { echo 'disabled'; }?>>Private</option>
						</select>
					</div>
					<div class="setting col_3">
						<label for="paste_name" form="text_form">Title</label>
						<hr>
						<input id="paste_name" type="text" style="width: 85%;" maxlength="64" name="title">

					</div>
					<input id="paste_text"  class="hidden" name="data">
					<input id="paste_type" type="text" class="hidden" value="upload" name="type">
					<input type="text" class="hidden" value="text" name="meta">

					<div class="col_12">
						<!-- submitting the form -->
						<input class="green" type="submit" value="Upload">
					</div>
				</form>
				<!-- make horizontol rule take up whole container -->

			</div>
		</div>
		<!-- File upload container -->
		<div class="content_container file_tab col_6">
			<h2>File Upload</h2>
			<hr>
			<div id="drop_zone_container">
				<i id="file_loading" class="fa fa-cog fa-4 fa-spin"></i>
				<!-- Form required for dropzone.js -->
				<form action="/file-upload"
				class="dropzone dz-square 
				center"
				id="file-drop-zone">
				<input type="text" class="hidden" value="upload" name="type">
				<input type="text" class="hidden" value="file" name="meta">
				<input type="text" class="hidden" value="1_week" name="expiration">
				<input type="text" class="hidden" value="public" name="exposure">

			</form>
		</div>
		<!-- the editor settings for the file upload, only different is no syntax highlighting -->
		<div id="file_editor_settings" class="clearfix hidden">



			<!-- make horizontol rule take up whole container -->
			<hr class="col_12">
			<form id="file_form" class="col_12">
				<div class="setting col_6">
					<label for="file_expiration" form="file_form">Expiration</label>
					<hr>
					<!-- All the different syntax highlightings -->
					<select id="file_expiration" class="chosen" name="expiration">
						<option value="1_week" selected>1 Week</option>
						<option value="1_day">1 Day</option>
						<option value="1_month">1 Month</option>
						<option value="forever">Forever</option>
						<option value="views">Views..</option>
					</select>
					<input type="number" min="1" max="100" value="1" style="display: none; margin-top: 20px;">
				</div>
				<div class="setting col_6">
					<label for="file_exposure" form="file_form">Exposure</label>
					<hr>
					<!-- All the different paste exposures -->
					<select id="file_exposure" class="chosen" name="exposure">
						<option value="public" selected>Public</option>
						<option value="unlisted">Unlisted</option>
						<option value="password_protected" disabled>Password Protected</option>
						<option value="private" disabled>Private</option>
					</select>
				</div>

				<div class="top-padding"></div>
				<div class="col_12">
					<!-- submitting the form -->
					<input class="green" type="submit" value="Upload">
				</div>
			</form>
		</div>
	</div>
</div>

<!-- include our custom scripts -->
<script type="text/javascript">
	$('#editor').hide();
	$('#editor').text("function foobar() {\n\tvar foo = \"Start typing here!\";\n\tvar bar = \"Or paste!\";\n\talert(foo+bar);\n\t//OR just drop the file\n\t//directly into here!\n }");

</script>
<?php
publishHTMLInclude('vendor/dropzone.js');
publishHTMLInclude('vendor/ace/ace.js');
publishHTMLInclude('vendor/ace/ext-modelist.js');
?>
<!-- scripts required for ace -->
<script type="text/javascript">
	$('#file_loading').hide();

	var editor = ace.edit("editor");
	editor.getSession().setMode("ace/mode/javascript");
	editor.setReadOnly(true);

	editor.setTheme("ace/theme/monokai");
	editor.setOption("hScrollBarAlwaysVisible", false);
	editor.setOption("vScrollBarAlwaysVisible", false);
	editor.setShowPrintMargin(false);
	editor.setAnimatedScroll(true);
	editor.getSession().setUseWorker(false);
	editor.resize(true);
	$('#editor').show();

	$('#text_loading').hide();
</script>
<!-- script used to make large select inputs easier for the user -->
<?php publishHTMLInclude('vendor/chosen/chosen.jquery.min.js'); ?>
<script type="text/javascript">
	$('.chosen').chosen({
		width: '85%',
		inherit_select_classes: true,
		search_contains: true,
		no_results_text: "Oops, syntax not found!",
		disable_search_threshold: 10,
	});
</script>