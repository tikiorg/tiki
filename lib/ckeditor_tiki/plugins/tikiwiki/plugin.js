﻿/* (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
 * 
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * $Id$
 *
 * Main Tiki Wiki markup integration plugin.
 * 
 * Based on work done by the MediaWiki Team for FCKEditor, big thanx to them
 *
 * Initial clues from: http://mediawiki.fckeditor.net/index.php/Talk:Main_Page#CKEditor
 * (author Juan Valencia, thanks also)
 */
 
CKEDITOR.plugins.add('tikiwiki',{    
	init: function(editor)    {  
		var twplugin = this;
			
		editor.dataProcessor.toDataFormat 	= function ( html, fixForBody ) { return twplugin.toWikiFormat( editor, html ); };
		editor.dataProcessor.toHtml			= function ( data, fixForBody ) { return twplugin.toHtmlFormat( editor, data ); };
		
		// button stuff goes here?
	},
	
	toWikiFormat: function ( editor, html, fixForBody ) {
		// try ajax
		var output = "";
		//ajaxLoadingShow( "cke_contents_" + editor.name); eats keystrokes
		jQuery.ajax({
			async: false,	// wait for this one
			url: CKEDITOR.config.ajaxAutoSaveTargetUrl,
			type: "POST",
			data: {
				script: editor.config.autoSaveSelf,
				editor_id: editor.name,
				data: encodeURIComponent(html),
				command: "toWikiFormat"
			},
			// good callback
			success: function(data) {
				output = unescape(jQuery(data).find('data').text());
			},
			// bad callback - no good info in the params :(
			error: function(req, status, error) {
				output = "ajax error";
			}
		});
		//ajaxLoadingHide();
		return output;
	},
	
	toHtmlFormat: function ( editor, data ) {
		// deal with plugins here?
		var output = "";
		ajaxLoadingShow( "cke_contents_" + editor.name);
		jQuery.ajax({
			async: false,	// wait for this one
			url: CKEDITOR.config.ajaxAutoSaveTargetUrl,
			type: "POST",
			data: {
				script: editor.config.autoSaveSelf,
				editor_id: editor.name,
				data: encodeURIComponent(data),
				command: "toHtmlFormat"
			},
			// good callback
			success: function(data) {
				output = unescape(jQuery(data).find('data').text());
			},
			// bad callback - no good info in the params :(
			error: function(req, status, error) {
				output = "ajax error";
			}
		});
		ajaxLoadingHide();
		return output;
	}
 
});
 
