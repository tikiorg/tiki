<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function tiki_syntax_highlighter_flex() {
	global $headerlib, $prefs;
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {

		$headerlib->add_jq_onready("
			$('textarea')
				.flexibleSyntaxHighlighter({
					changeText: '".tra("Change Highlighter")."'
				});
			
			$('.codelisting')
				.each(function() {
					$(this).flexibleSyntaxHighlighter({
						readOnly: true,
						mode: 'tikiwiki',
						width: $(this).width() + 'px',
						height: $(this).parent().height() + 'px'
					});
				})
				.hide();
		");
		
	}
}

function tiki_syntax_highlighter_base()
{
	global $headerlib, $prefs;

	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {
		$headerlib->add_cssfile( 'lib/codemirror/lib/codemirror.css' );
		$headerlib->add_cssfile( 'lib/codemirror/theme/default.css' );
		
		$headerlib->add_cssfile( 'lib/codemirror_tiki/docs.css' );
		
		$headerlib->add_jsfile( 'lib/codemirror/lib/codemirror.js' );
		$headerlib->add_jsfile( 'lib/codemirror_tiki/codemirror_tiki.js' );
	}
}

function tiki_syntax_highlighter_html()
{
	global $headerlib, $prefs;
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {		
		$headerlib->add_jq_onready("
			$(document)
				.bind('plugin_html_ready', function(args) {
					var code = args.container.find('textarea:first');
					
					code.flexibleSyntaxHighlighter({
						mode: 'php',
						lineNumbers: true,
						changeText: '".tra("Change Highlighter")."',
						force: true
					});
				});
		");
	}
}

function tiki_syntax_highlighter_code()
{
	global $headerlib, $prefs;
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {		
		$headerlib->add_jq_onready("
			$(document)
				.bind('plugin_code_ready', function(args) {
					var code = args.container.find('textarea:first');
					
					code.flexibleSyntaxHighlighter({
						mode: 'php',
						lineNumbers: true,
						changeText: '".tra("Change Highlighter")."',
						force: true
					});
				});
		");
	}
}

function tiki_syntax_highlighter_r() {	
	global $headerlib, $prefs;
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {
		$headerlib->add_jq_onready("
			$(document)
				.bind('plugin_r_ready', function(args) {
					var r = args.container.find('textarea:first');
				
					r.flexibleSyntaxHighlighter({
						mode: 'r',
						lineNumbers: true,
						changeText: '".tra("Change Highlighter")."',
						force: true
					});
				});
		");
	}
}

function tiki_syntax_highlighter_rr() {
	global $headerlib, $prefs;
	if ( $prefs['feature_syntax_highlighter'] == 'y' ) {	
		$headerlib->add_jq_onready("
			$(document)
				.bind('plugin_rr_ready', function(args) {
					var rr = args.container.find('textarea:first');

					rr.flexibleSyntaxHighlighter({
						mode: 'r',
						lineNumbers: true,
						changeText: '".tra("Change Highlighter")."',
						force: true
					});
				});
		");
	}
}
