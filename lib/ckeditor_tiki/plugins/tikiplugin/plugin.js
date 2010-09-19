/* (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
 * 
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * $Id$
 *
 * Tiki plugin integration plugin.
 * 
 * Based on ckeditor flash plugin
 */

(function() {


	CKEDITOR.plugins.add( 'tikiplugin', {
		init : function(editor) {
		
			this.ckToHtml = editor.dataProcessor.toHtml;		// reference to original ckeditor dataProcessor
			this.ckToData = editor.dataProcessor.toDataFormat;
			this.editor = editor;								// which expects these references too
			this.dataFilter = editor.dataProcessor.dataFilter;
			this.writer = new CKEDITOR.htmlWriter();
			
			this.command = new CKEDITOR.command( editor , {
				modes: { wysiwyg:1 },
				exec: function(editor, data) {	// odd? elem param disappears from doubleclick
					var sel = editor.getSelection();
					if (data === editor) {
						data = sel.getCommonAncestor().getParent();	// this will need more checking
					}
					var args = {};
					var str = $('<div/>').html(unescape(data.getAttribute("args"))).text();	// decode html entities
					var pairs = str.split("&");	//unescape().replace(/&amp;/gi, "&").split("&");
					for (var i = 0; i < pairs.length; i++) {
						if (pairs[i].indexOf("=") > -1) {
							args[pairs[i].substring(0, pairs[i].indexOf("="))] = pairs[i].substring((pairs[i].indexOf("=") + 1));
						}
					}
					sel.selectElement( data );
					popupPluginForm( editor.name, data.getAttribute("plugin"), 0, '', args, data.getAttribute("body"), '');
				},
				canUndo: false
			});

			editor.addCommand('tikiplugin', this.command);
//							editor.ui.addButton('TikiPlugin', {
//								label : 'Tiki Plugin',
//								command : 'tikiplugin'
//							});

			editor.addCss('.tiki_plugin'
							+ '{'
							+ 'display: inline-block;'
							+ 'background-color: #eee;'
							+ 'border: 1px solid #666;'
							+ '}');

			// If the "menu" plugin is loaded, register the menu items.
			if (editor.addMenuItems) {
                editor.addMenuGroup("tiki", 999);
				editor.addMenuItems( {
					tikiplugin : {
						label : 'Tiki Plugin',
						command : 'tikiplugin',
						group : 'tiki',
						icon : CKEDITOR.config._TikiRoot + 'pics/icons/plugin_edit.png'
					}
				});
			}
			
			editor.on('contentDom', function() {	// re-apply the doubleclick event when wysiwyg mode happens
				editor.on( 'doubleclick', function(evt) {
					var element = evt.data.element;

					while (element.getParent()) {
						element = element.getParent();
						if ((element.is('span') || element.is('div')) && element.getAttribute('plugin')) {
							break;
						}
					}
					
					if ((element.is('span') || element.is('div')) && element.getAttribute('plugin')) {
						evt.cancel();
						evt.stop();
						//evt.removeListener();
						evt.data.dialog = null;
						//evt.preventDefault();
						this.plugins.tikiplugin.command.exec(element);
					}
				});
			});

			// If the "contextmenu" plugin is loaded, register the listeners.
			if (editor.contextMenu) {
				editor.contextMenu.addListener(function(element, selection) {
					
					while (element.getParent()) {
						element = element.getParent();
						if ((element.is('span') || element.is('div')) && element.getAttribute('plugin')) {
							break;
						}
					}
					
					if (element && element.getAttribute('plugin')) {
						return {
							tikiplugin : CKEDITOR.TRISTATE_OFF
						};
					}
				});
			}
			if (true) {	// sure there should be a test here
				var asplugin = this;
				editor.dataProcessor.toDataFormat 	= function ( html, fixForBody ) { return asplugin.toHTMLSource( editor, html ); };
				editor.dataProcessor.toHtml			= function ( data, fixForBody ) { return asplugin.toHtmlFormat( editor, data ); };
			}
		},			// end of init()
		
		toHTMLSource: function( editor, html ) {
			// replace visual plugins with syntax
			var output = html.replace(/<(?:div|span)[^>]*syntax="([^"]*)"[\s\S]*?end tiki_plugin --><\/(?:div|span)>/mig, function() {
				if (arguments.length > 0) {
					return $("<span />").html(arguments[1]).text();	// decode html entities
				} else {
					alert("ckeditor: error parsing to html source");
					return "";
				}
			});
			return this.ckToData(output);
		},

		toHtmlFormat: function( editor, data ) {
			var output = "";
			var asplugin = this;
			ajaxLoadingShow( "cke_contents_" + this.editor.name);
			$("#ajaxLoading").show();		// FIXME safari/chrome refuse to show until ajax finished
			jQuery.ajax({
				async: false, // wait for this one
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
					ajaxLoadingHide();
					output = unescape(jQuery(data).find('data').text());
					return asplugin.ckToHtml.call(asplugin, output);
				},
				// bad callback - no good info in the params :(
				error: function(req, status, error) {
					ajaxLoadingHide();
					output = "ajax error";
				}
			});
			return output;
		},
		
		requires : [ '' ]	// TODO check req - autosave? (really)
	});
	
	if (typeof CKEDITOR.editor.prototype.reParse != 'function') {
		CKEDITOR.editor.prototype.reParse = function() {
	
			// send the whole source off to the server to get reparsed?
			var myoutput = "";
			var mydata = this.getData();
			
			ajaxLoadingShow( "cke_contents_" + this.name);
			jQuery.ajax({
				async: false,	// wait for this one
				url: CKEDITOR.config.ajaxAutoSaveTargetUrl,
				type: "POST",
				data: {
					script: this.config.autoSaveSelf,
					editor_id: this.name,
					data: encodeURIComponent(mydata),
					command: "toHtmlFormat"
				},
				// good callback
				success: function(data) {
					myoutput = unescape(jQuery(data).find('data').text());
				},
				// bad callback - no good info in the params :(
				error: function(req, status, error) {
					myoutput = "ajax error";
				}
			});
			ajaxLoadingHide();
			this.setData(myoutput);
		};
	}
})();
