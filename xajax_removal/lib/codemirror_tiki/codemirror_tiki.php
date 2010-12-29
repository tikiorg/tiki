<?php

function tiki_syntax_highlighter_base() {
	global $headerlib, $prefs;
	
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {
		$headerlib->add_cssfile( 'lib/codemirror_tiki/docs.css' );
		$headerlib->add_jsfile( 'lib/codemirror/js/codemirror.js' );		

		$headerlib->add_js("
			var startCodeMirror = function() {
				var editwiki = $('#editwiki');
				//ensure that codemirror is running and CKEditor isn't, if so run
				if (window.CKEDITOR) return false;
				if (!editwiki[0]) return false;
				if (!CodeMirror) return false;
				
				var editor = CodeMirror.fromTextArea(editwiki[0], {
					height: '350px',
					path: 'lib/codemirror/js/',
					parserfile: ['../../codemirror_tiki/js/parsetikisyntax.js'],
					stylesheet: ['lib/codemirror_tiki/css/tikiwikisyntaxcolors.css'],
					onChange: function() {
						//Setup codemirror to send the text back to the textarea
						editwiki.val(editor.getCode());
					}
				});
				
				addCodeMirrorEditorRelation(editor, editwiki);
			};
			startCodeMirror();
		");
	}
}

function tiki_syntax_highlighter_code() {
	global $headerlib, $prefs;
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {
		$headerlib->add_cssfile( 'lib/codemirror_tiki/docs.css' );
		$headerlib->add_jsfile( 'lib/codemirror/js/codemirror.js' );
		
		$headerlib->add_js("
			$(document)
				.bind('plugin_code_ready', function(args) {
					var code = args.container.find('textarea:first').addClass('codeMirror');
					//ensure that codemirror is running and CKEditor isn't, if so run
					if (window.CKEDITOR) return false;
					if (!code[0]) return false;
					if (!CodeMirror) return false;

					var editor = CodeMirror.fromTextArea(code[0], {
						height: '350px',
						parserfile: ['parsexml.js', 'parsecss.js', 'tokenizejavascript.js', 'parsejavascript.js', 'parsehtmlmixed.js'],
						stylesheet: ['lib/codemirror/css/xmlcolors.css', 'lib/codemirror/css/jscolors.css', 'lib/codemirror/css/csscolors.css'],
						path: 'lib/codemirror/js/',
						onChange: function() {
							//Setup codemirror to send the text back to the textarea
							code.val(editor.getCode());
						}
					});
				});
		");
	}
}

function tiki_syntax_highlighter_r() {	
	global $headerlib, $prefs;
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {
		$headerlib->add_cssfile( 'lib/codemirror_tiki/docs.css' );
		$headerlib->add_jsfile( 'lib/codemirror/js/codemirror.js' );
	
		$headerlib->add_js("
			$(document)
				.bind('plugin_r_ready', function(args) {
					var r = args.container.find('textarea:first').addClass('codeMirror');
				
					//ensure that codemirror is running and CKEditor isn't, if so run
					if (window.CKEDITOR) return false;
					if (!r[0]) return false;
					if (!CodeMirror) return false;

					var editor = CodeMirror.fromTextArea(r[0], {
						height: '350px',
						parserfile: ['../../codemirror_tiki/js/parsersplus.js'],
						stylesheet: ['lib/codemirror_tiki/css/rspluscolors.css'],
						path: 'lib/codemirror/js/',
						onChange: function() {
							//Setup codemirror to send the text back to the textarea
							r.val(editor.getCode());
						},
						lineNumbers: true
					});
				});
		");
	}
}

function tiki_syntax_highlighter_rr() {
	global $headerlib, $prefs;
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {
		$headerlib->add_cssfile( 'lib/codemirror_tiki/docs.css' );
		$headerlib->add_jsfile( 'lib/codemirror/js/codemirror.js' );
	
		$headerlib->add_js("
			$(document)
				.bind('plugin_rr_ready', function(args) {
					var rr = args.container.find('textarea:first').addClass('codeMirror');
				
					//ensure that codemirror is running and CKEditor isn't, if so run
					if (window.CKEDITOR) return false;
					if (!rr[0]) return false;
					if (!CodeMirror) return false;

					var editor = CodeMirror.fromTextArea(rr[0], {
						height: '350px',
						parserfile: ['../../codemirror_tiki/js/parsersplus.js'],
						stylesheet: ['lib/codemirror_tiki/css/rspluscolors.css'],
						path: 'lib/codemirror/js/',
						onChange: function() {
							//Setup codemirror to send the text back to the textarea
							rr.val(editor.getCode());
						},
						lineNumbers: true
					});
				});
		");
	}
}
