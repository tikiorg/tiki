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

	var numberRegex = /^\d+(?:\.\d+)?$/;

	function cssifyLength(length) {
		if (numberRegex.test(length))
			return length + 'px';
		return length;
	}

	function createFakeElement(editor, realElement) {
		var fakeElement = editor.createFakeParserElement(realElement, 'cke_tikiplugin', 'span', true);
		var fakeStyle = fakeElement.attributes.style || '';

		var width = realElement.attributes.width, height = realElement.attributes.height;

		if (typeof width != 'undefined')
			fakeStyle = fakeElement.attributes.style = fakeStyle + 'width:'
					+ cssifyLength(width) + ';';

		if (typeof height != 'undefined')
			fakeStyle = fakeElement.attributes.style = fakeStyle + 'height:'
					+ cssifyLength(height) + ';';

		return fakeElement;
	}

	CKEDITOR.plugins.add( 'tikiplugin', {
		init : function(editor) {
		
			this.command = new CKEDITOR.command( editor , {
				modes: { wysiwyg:1 },
				exec: function(editor, data) {	// odd? elem param disappears from doubleclick
					if (data === editor) {
						var sel = editor.getSelection();
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
					editor.getSelection().selectElement( data );
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

			editor.addCss('.cke_tiki_plugin'
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

		afterInit : function(editor) {
			var dataProcessor = editor.dataProcessor;
			var dataFilter = dataProcessor && dataProcessor.dataFilter;
			if (dataFilter) {
				dataFilter.addRules( {
					elements : {
						'span' : function( element ) {
							if (element.attributes && element.attributes.plugin) {
								//element.attributes.contenteditable = "false";
								for (var c = 0; c < element.children.length; c++) {
									if (!element.children[c].attributes) {
										element.children[c].attributes = {};
									}
									element.children[c].attributes.contenteditable = "false";
								}
								return element;
							} else {
								return null;
							}
						}
					}
				}, 1);
			}
		},

		requires : [ 'fakeobjects' ]
	});
})();
