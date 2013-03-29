/* (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
 * 
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * $Id$
 *
 * Main Tiki Wiki markup integration plugin.
 * 
 * Based on work done by the MediaWiki Team for FCKEditor, big thanx to them
 * Initial clues from: http://mediawiki.fckeditor.net/index.php/Talk:Main_Page#CKEditor
 * (author Juan Valencia, thanks also)
 */

CKEDITOR.plugins.add('tikiwiki',{
	ckToHtml: null,
	editor: null,
	dataFilter: null,

	init: function(editor)    {
		var twplugin = this;
		this.ckToHtml = editor.dataProcessor.toHtml;		// reference to original ckeditor dataProcessor
		this.editor = editor;								// which expects these references too
		this.dataFilter = editor.dataProcessor.dataFilter;

		var oldToDataFormat = editor.dataProcessor.toDataFormat ;
		editor.dataProcessor.toDataFormat 	= function ( html, fixForBody ) { return twplugin.toWikiFormat( editor, oldToDataFormat( html, fixForBody ) ); };
		editor.dataProcessor.toHtml			= function ( data, fixForBody ) { return twplugin.toHtmlFormat( editor, data ); };

		// data in the clipboard is html, the input format is expected to be wiki
		editor.on('paste', function(evt) {evt.editor.insertHtml(twplugin.toWikiFormat( editor, evt.data.html )); evt.stop(); }, editor.element.$);

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
				referer: editor.config.autoSaveSelf,
				editor_id: editor.name,
				data: tiki_encodeURIComponent(html),
				command: "toWikiFormat"
			},
			// good callback
			success: function(data) {
				try {
					output = tiki_decodeURIComponent(jQuery(data).find('data').text());
				} catch (err) {
					output = unescape(jQuery(data).find('data').text()); // Reintroduced....(decodeURI and decodeURIComponent would break the Wiki Syntax)
				}
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
		var twplugin = this;
		var isHtml = $("#allowhtml:checked").length || $("#allowhtml[type=hidden]").val();

		if (typeof editor.pluginReparsing !== "undefined" && editor.pluginReparsing) {
			editor.pluginReparsing = false;
			return data;
		}

		ajaxLoadingShow( "cke_contents_" + editor.name);
		jQuery.ajax({
			async: false,	// wait for this one
			url: CKEDITOR.config.ajaxAutoSaveTargetUrl,
			type: "POST",
			data: {
				referer: editor.config.autoSaveSelf,
				editor_id: editor.name,
				data: tiki_encodeURIComponent(data),
				command: "toHtmlFormat",
				allowhtml: isHtml
			},
			// good callback
			success: function(data) {
				try {
					output = tiki_decodeURIComponent(jQuery(data).find('data').text());
				} catch(e) {}

				if (!output) {
					output = unescape(jQuery(data).find('data').text())
				}
				//var fragment = CKEDITOR.htmlParser.fragment.fromHtml( output, false );	// fixForBody?
				//editor.dataProcessor.htmlFilter.onFragment(fragment);
				output = twplugin.ckToHtml.call(twplugin, output);
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

