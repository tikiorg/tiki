<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function tiki_syntax_highlighter_flex() {
	global $headerlib, $prefs;
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {
		$headerlib->add_cssfile( 'lib/codemirror_tiki/docs.css' );
		$headerlib->add_jsfile( 'lib/codemirror/js/codemirror.js' );
		$headerlib->add_jsfile( 'lib/codemirror_tiki/codemirror_tiki.js' );
		$headerlib->add_js("
			$('textarea')
				.flexibleCodeMirror({
					changeText: '".tra("Change Highlighter")."'
				});
		");
		
	}
}

function tiki_syntax_highlighter_base()
{
	global $headerlib, $prefs;
	
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {
		$headerlib->add_cssfile( 'lib/codemirror_tiki/docs.css' );
		$headerlib->add_jsfile( 'lib/codemirror/js/codemirror.js' );		

		$headerlib->add_js("
			$(function() {				
				$('#editwiki_toolbar')
					.css('width', '100%')
					.nextAll()
					.css('width', '100%');
			});
		");
	}
}

function tiki_syntax_highlighter_code()
{
	global $headerlib, $prefs;
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {
		$headerlib->add_cssfile( 'lib/codemirror_tiki/docs.css' );
		$headerlib->add_jsfile( 'lib/codemirror/js/codemirror.js' );
		
		$headerlib->add_js("
			$(document)
				.bind('plugin_code_ready', function(args) {
					var code = args.container.find('textarea:first');
					
					code.flexibleCodeMirror({
						parse: ['xml', 'css', 'javascript', 'html'],
						lineNumbers: true,
						changeText: '".tra("Change Highlighter")."'
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
					var r = args.container.find('textarea:first');
				
					r.flexibleCodeMirror({
						parse: ['r'],
						lineNumbers: true,
						changeText: '".tra("Change Highlighter")."'
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
					var rr = args.container.find('textarea:first');

					rr.flexibleCodeMirror({
						parse: ['r'],
						lineNumbers: true,
						changeText: '".tra("Change Highlighter")."'
					});
				});
		");
	}
}
