/* (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
		editor.dataProcessor.toDataFormat 	= function ( html, fixForBody ) {
			return twplugin.toWikiFormat( editor, oldToDataFormat.call( editor.dataProcessor, html, fixForBody ) ); };
		editor.dataProcessor.toHtml			= function ( data, fixForBody ) { return twplugin.toHtmlFormat( editor, data ); };

		// data in the clipboard is html, the input format is expected to be wiki
		editor.on('paste', function(evt) {evt.editor.insertHtml(twplugin.toWikiFormat( editor, evt.data.dataValue )); evt.stop(); }, editor.element.$);

		// If the "contextmenu" plugin is loaded, register a listener to disable the html table editor
		if (editor.contextMenu) {
			editor.contextMenu.addListener(function (element, selection) {

				if (element.is('table') || element.is('th') || element.is('td')) {
					for (var i = 0; i < editor.contextMenu.items.length; i++ ) {
						if (editor.contextMenu.items[i].command === "tableProperties") {
							editor.contextMenu.items[i].state = CKEDITOR.TRISTATE_DISABLED;
							break;
						}
					}
					return {};
				}
			});
		}
	},

	toWikiFormat: function ( editor, html, fixForBody ) {
		// try ajax
		var output = "";
		//ajaxLoadingShow( "cke_contents_" + editor.name); eats keystrokes
		jQuery.ajax({
			async: false,	// wait for this one
			url: $.service("edit", "towiki"),
			type: "POST",
			dataType: "json",
			data: {
				referer: editor.config.autoSaveSelf,
				editor_id: editor.name,
				data: html
			},
			// good callback
			success: function(data) {
				output = data.data;
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
		var isHtml = auto_save_allowHtml(editor.element.$.form);

		if (typeof editor.pluginReparsing !== "undefined" && editor.pluginReparsing) {
			editor.pluginReparsing = false;
			return data;
		}

		ajaxLoadingShow( "cke_contents_" + editor.name);
		jQuery.ajax({
			async: false,	// wait for this one
			url: $.service("edit", "tohtml"),
			type: "POST",
			dataType: "json",
			data: {
				referer: editor.config.autoSaveSelf,
				editor_id: editor.name,
				data: data,
				allowhtml: isHtml
			},
			// good callback
			success: function(data) {
				output = data.data;
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

