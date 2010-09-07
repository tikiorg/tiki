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
		
			this.command = new CKEDITOR.command( editor , {
				modes: { wysiwyg:1 },
				exec: function(editor, data) {	// odd? elem param disappears from doubleclick
					var sel = editor.getSelection();
					if (data === editor) {
						//data = sel.getCommonAncestor().$.parentNode;	// this will need more checking
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
					//var r = sel.getRanges();	// ckeditor 3.4 seems to select some whitespace before the plugin element
					//r[0].checkBoundaryOfElement(data.$);
					//r[0].shrink();
					//r[0].setStartAfter( r[0].startContainer );
					//r[0].setStartBefore( data );	// nope - none of these do it - FIXME
					popupPluginForm( editor.name, data.getAttribute("plugin"), 0, '', args, data.getAttribute("body"), '');
				},
				canUndo: false
			});

			editor.addCommand('tikiplugin', this.command);
//							editor.ui.addButton('TikiPlugin', {
//								label : 'Tiki Plugin',
//								command : 'tikiplugin'
//							});
			// CKEDITOR.dialog.add( 'tikiplugin', this.path + 'dialogs/tikiplugin.js' );

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
		},

		requires : [ 'fakeobjects' ]
	});
	
	if (typeof CKEDITOR.editor.prototype.reParse != 'function') {
		CKEDITOR.editor.prototype.reParse = function() {
	
//			toHtmlFormat: function ( editor, data ) {
			// deal with plugins here?
			var output = "";
//			var twplugin = this;
			var data = this.getData();
			
			ajaxLoadingShow( "cke_contents_" + this.name);
			jQuery.ajax({
				async: false,	// wait for this one
				url: CKEDITOR.config.ajaxAutoSaveTargetUrl,
				type: "POST",
				data: {
					script: this.config.autoSaveSelf,
					editor_id: this.name,
					data: encodeURIComponent(data),
					command: "toHtmlFormat"
				},
				// good callback
				success: function(data) {
					output = unescape(jQuery(data).find('data').text());
					//var fragment = CKEDITOR.htmlParser.fragment.fromHtml( output, false );	// fixForBody?
					//editor.dataProcessor.htmlFilter.onFragment(fragment);
					//twplugin.ckToHtml.call(twplugin, output);
				},
				// bad callback - no good info in the params :(
				error: function(req, status, error) {
					output = "ajax error";
				}
			});
			ajaxLoadingHide();
//			return output;
//		}
 

			this.setData(output);
		};
	}
})();
