<?php

function tiki_syntax_highlighter_base() {
	global $headerlib, $prefs;
	
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {
		$headerlib->add_cssfile( 'lib/codemirror_tiki/docs.css' );
		$headerlib->add_jsfile( 'lib/codemirror_tiki/js/codemirror.js' );
		
		$headerlib->add_js("
			var editwiki = $('#editwiki');
				
			//ensure that codemirror is running, if so run
			if (CodeMirror && editwiki[0]) {
				var editor = CodeMirror.fromTextArea(editwiki[0], {
					height: '350px',
					parserfile: ['parsetikisyntax.js'],
					stylesheet: ['lib/codemirror_tiki/css/tikiwikisyntaxcolors.css'],
					path: 'lib/codemirror_tiki/js/',
					onChange: function() {
						//Setup codemirror to send the text back to the textarea
						editwiki.val(editor.getCode());
					}
				});
				
				editwiki
					.change(function() {
						editor.setCode(editwiki.val());
					});
			}
		");
	}
}

function tiki_syntax_highlighter_code() {
	global $headerlib, $prefs;
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {
		$headerlib->add_cssfile( 'lib/codemirror_tiki/docs.css' );
		$headerlib->add_jsfile( 'lib/codemirror_tiki/js/codemirror.js' );
		
		$headerlib->add_js("
			$(document)
				.bind('plugin_code_ready', function(args) {
					var code = args.container.find('textarea:first');
					
					//ensure that codemirror is running, if so run
					if (CodeMirror && code[0]) {
						var editor = CodeMirror.fromTextArea(code[0], {
							height: '350px',
							parserfile: ['parsexml.js', 'parsecss.js', 'tokenizejavascript.js', 'parsejavascript.js', 'parsehtmlmixed.js'],
							stylesheet: ['lib/codemirror_tiki/css/xmlcolors.css', 'lib/codemirror_tiki/css/jscolors.css', 'lib/codemirror_tiki/css/csscolors.css'],
							path: 'lib/codemirror_tiki/js/',
							onChange: function() {
								//Setup codemirror to send the text back to the textarea
								code.val(editor.getCode());
							},
							lineNumbers: true
						});
					}
				});
		");
	}
}

function tiki_syntax_highlighter_r() {	
	global $headerlib, $prefs;
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {
		$headerlib->add_cssfile( 'lib/codemirror_tiki/docs.css' );
		$headerlib->add_jsfile( 'lib/codemirror_tiki/js/codemirror.js' );
	
		$headerlib->add_js("
			$(document)
				.bind('plugin_r_ready', function(args) {
					var r = args.container.find('textarea:first');
				
					//ensure that codemirror is running, if so run
					if (CodeMirror && r[0]) {
						var editor = CodeMirror.fromTextArea(r[0], {
							height: '350px',
							parserfile: ['parsersplus.js'],
							stylesheet: ['lib/codemirror_tiki/css/rspluscolors.css'],
							path: 'lib/codemirror_tiki/js/',
							onChange: function() {
								//Setup codemirror to send the text back to the textarea
								r.val(editor.getCode());
							},
							lineNumbers: true
						});
					}
				});
		");
	}
}

function tiki_syntax_highlighter_rr() {
	global $headerlib, $prefs;
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {
		$headerlib->add_cssfile( 'lib/codemirror_tiki/docs.css' );
		$headerlib->add_jsfile( 'lib/codemirror_tiki/js/codemirror.js' );
	
		$headerlib->add_js("
			$(document)
				.bind('plugin_rr_ready', function(args) {
					var rr = args.container.find('textarea:first')
						.attr('id', 'rr');
				
					//ensure that codemirror is running, if so run
					if (CodeMirror) {
						var editor = CodeMirror.fromTextArea('rr', {
							height: '350px',
							parserfile: ['parsersplus.js'],
							stylesheet: ['lib/codemirror_tiki/css/rspluscolors.css'],
							path: 'lib/codemirror_tiki/js/',
							onChange: function() {
								//Setup codemirror to send the text back to the textarea
								rr.val(editor.getCode());
							},
							lineNumbers: true
						});
					}
				});
		");
	}
}