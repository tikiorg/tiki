/* (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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


	var protectTikiPluginsRegexp = /<(?:div|span)[^>]*data-syntax="([^"]*)"[\s\S]*?end tiki_plugin -->\s*?<\/(?:div|span)>/mig;

	CKEDITOR.plugins.add( 'tikiplugin', {

		init : function(editor) {
		
			var asplugin = this;
			
			editor.config.protectedSource.push( protectTikiPluginsRegexp );
				
			this.command = new CKEDITOR.command( editor , {
				modes: { wysiwyg:1 },
				exec: function(editor, data) {	// odd? elem param disappears from doubleclick
					var sel = editor.getSelection(), r;
					// Selection will be unavailable after context menu shows up - in IE, lock it now.
					if ( CKEDITOR.env.ie ) {
						if (sel) { sel.lock(); }
					}
					if (data === editor) {
						r = sel.getRanges();
						data = r[0].startContainer;
					}
					if (!data.hasClass("tiki_plugin")) {
						var pluginTemp = $(".tiki_plugin:first", data.$);
						if (pluginTemp.length) {
							data = new CKEDITOR.dom.element(pluginTemp[0]);
						}
					}
					while (!data.hasClass("tiki_plugin") && data.getParent()) {	// try parents?
						data = data.getParent();
						if (data.hasClass("tiki_plugin")) {
							break;
						}
					}
					if (!data.hasClass("tiki_plugin")) {
						// problem here - wrong element sent in?
						debugger;		// intentionally left in to catch occasional IE edge cases
						return;
					}
					var args = {};
					var str = $('<div/>').html(data.data("args")).text();	// decode html entities
					var pairs = str.split("&");
					for (var i = 0; i < pairs.length; i++) {
						if (pairs[i].indexOf("=") > -1) {
							var val = pairs[i].substring((pairs[i].indexOf("=") + 1));
							if (val.match(/^".*"$/)) {								// strip off extra quotes
								val = val.substring(1, val.length - 1)
							}
							args[pairs[i].substring(0, pairs[i].indexOf("="))] = val;
						}
					}
					sel.selectElement( data );
					popupPluginForm( editor.name, data.data("plugin"), 0, '', args, data.data("body"), '');
				},
				canUndo: false
			});

			editor.addCommand('tikiplugin', this.command);

			// editor.addCss( for CkEditor 3.x
			CKEDITOR.addCss('.tiki_plugin'
							+ '{'
							+ 'display: inline-block;'
							+ 'background-color: #eee;'
							+ 'border: 1px solid #666;'
							+ '}'
							+ ' div.tiki_plugin { width: 100%; }'
						);

			// If the "menu" plugin is loaded, register the menu items.
			if (editor.addMenuItems) {
                editor.addMenuGroup("tiki", 999);
				editor.addMenuItems( {
					tikiplugin : {
						label : 'Tiki Plugin',
						command : 'tikiplugin',
						group : 'tiki',
						icon : CKEDITOR.config._TikiRoot + 'img/icons/plugin_edit.png'
					}
				});
				// remove the built in image menu item
				editor.removeMenuItem('image');
			}
			
			editor.on('contentDom', function() {	// re-apply the events etc when wysiwyg mode happens
				
				$(".tiki_plugin", editor.document.$).each(function() {
					var parentPlugin = new CKEDITOR.dom.element(this);
					$(this).find("*").addBack().each(function(){
						$(this).mousedown(function(){
							var sel = editor.getSelection();
							if (sel) {
								sel.selectElement(parentPlugin);
							}
							return false;
						}); //.attr("contenteditable", "false");
					});
				});
				
				editor.on( 'doubleclick', function(evt) {
					var element = evt.data.element;

					element = asplugin.getPluginParent( element );
					
					if (element) {
						evt.cancel();
						evt.stop();
						evt.data.dialog = null;
						//this.plugins.tikiplugin.command.exec(element);
                        this.execCommand('tikiplugin', element);
					}
				});
			});

			// If the "contextmenu" plugin is loaded, register the listeners.
			if (editor.contextMenu) {
				editor.contextMenu.addListener(function(element, selection) {
					
					element = asplugin.getPluginParent( element );
					
					if (element) {
						return {
							tikiplugin : CKEDITOR.TRISTATE_OFF
						};
					}
				});
			}
			if (jqueryTiki.autosave) {	// pref check
				// changed for ckeditor4 - to check?
				//if (typeof editor.plugins["tikiwiki"] === "undefined") {	// also defined in tikiwiki plugin
					this.ckToHtml = editor.dataProcessor.toHtml;		// reference to original ckeditor dataProcessor
					editor.dataProcessor.toHtml			= function ( data, fixForBody ) { return asplugin.toHtmlFormat( editor, data, fixForBody ); };
				//}
				this.ckToData = editor.dataProcessor.toDataFormat;
				editor.dataProcessor.toDataFormat 	= function ( html, fixForBody ) { return asplugin.toHTMLSource( editor, html, fixForBody ); };
			}
		},			// end of init()
		
		afterInit : function( editor ) {
			// Register a filter to displaying placeholders after mode change. (taken from pagebreak plugin)
	
			var asplugin = this;

			var dataProcessor = editor.dataProcessor,
				dataFilter = dataProcessor && dataProcessor.dataFilter;
	
			if ( dataFilter ) {
				dataFilter.addRules( {
					elements : {
						div : function( element ) {
							return asplugin.processTikiPlugin( element, 'div', editor);
						},
						span : function( element ) {
							return asplugin.processTikiPlugin( element, 'span', editor);
						}
					}
				});
			}
		},
	
		requires : [ 'fakeobjects' ],
		
		// variation on CKEDITOR.editor.createFakeParserElement set up by fakeobject plugin
		processTikiPlugin : function ( element, tag, editor ) {
			var name = element.attributes && element.attributes.plugin;
			
			if (name) {	// a tiki plugin
				var html;
			
				var writer = new CKEDITOR.htmlParser.basicWriter();
				element.writeHtml( writer );
				html = writer.getHtml();
				
				element.attributes._cke_realelement = tiki_encodeURIComponent( html );
				element.attributes._cke_real_node_type = tag;
				
				element.attributes._cke_real_element_type = tag;
				element.attributes.contenteditable = false;
				
				if (name === 'img') {
					element.attributes._cke_resizable = true;
				} else {
					element.attributes._cke_resizable = false;
				}
				
				// Set onclick for the contents
				for ( var i = 0 ; i < element.children.length ; i++ ) {
					if ( element.children[ i ].attributes ) {
						element.children[ i ].attributes.contenteditable = false;
					} else {
						// seems to make the whole editor readonly
						//element.children[ i ].attributes = { contenteditable: false};
					}
				}

				return element;
			}
			return null;
		},
		
		getPluginParent: function ( element ) {
			while (element && element.getParent()) {
				if (element.data('plugin')) {
					return element;
				}
				element = element.getParent();
			}
			return null;
		},

		toHTMLSource: function( editor, html, fixForBody ) {
			// de-protect ck_protected comments
			var output = html;

			// not working in ckeditor4 - to check
			//output = this.ckToData.call( editor.dataProcessor, output, fixForBody );

			if (typeof editor.plugins["tikiwiki"] === "undefined") {
				output = output.replace(/<pre class=["']tiki_plugin["']>([\s\S]*?)<\/pre>/ig, "$1");
			}
			output = output.replace(/<!--{cke_protected}{C}%3C!%2D%2D%20end%20tiki_plugin%20%2D%2D%3E-->/ig, "<!-- end tiki_plugin -->");
			// replace visual plugins with syntax
			output = output.replace( protectTikiPluginsRegexp, function() {
				if (arguments.length > 0) {
					var plugCode = $("<span />").html(arguments[1]).text();
					if (typeof editor.plugins["tikiwiki"] !== "undefined") {	// also defined in tikiwiki plugin
						plugCode = plugCode.replace(/\n/g, "<br />");
					}
					return plugCode;	// decode html entities
				} else {
					alert("ckeditor: error parsing to html source");
					return "";
				}
			});
			output = output.replace(/\s*<p>[\s]*<\/p>\s*/mgi, "");	// strip out empty <p> tags
			
			return output;
		},

		toHtmlFormat: function( editor, data, fixForBody ) {
			if (!jqueryTiki.autosave) {
				alert(tr("AJAX Autosave preference not enabled. Please go to admin/features/interface to enable it."));
			}

			if (typeof editor.pluginReparsing !== "undefined" && editor.pluginReparsing) {
				editor.pluginReparsing = false;
				return data;
			}

			var output = "";
			var asplugin = this;
			var orig_data = $("#editwiki").val();	// just in case
			var isHtml = $("#allowhtml:checked").length || $("#allowhtml[type=hidden]").length && $("#allowhtml[type=hidden]").val() ? 1 : 0;

			ajaxLoadingShow( "cke_contents_" + editor.name);
			$("#ajaxLoading").show();		// FIXME safari/chrome refuse to show until ajax finished
			jQuery.ajax({
				async: false, // wait for this one
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
					ajaxLoadingHide();
					try {
					    output = tiki_decodeURIComponent(jQuery(data).find('data').text());
					} catch (err) {
					    // Maybe it used escape to encode it? Try unescape
					    output = unescape(jQuery(data).find('data').text());
					}
					output = asplugin.ckToHtml.call(editor.dataProcessor, output, fixForBody);
					if (output.indexOf("</body>") === -1) {
						output += "</body>"; 	// workaround for cke 3.4 / tiki 6.0 FIXME
					}
				},
				// bad callback - no good info in the params :(
				error: function(req, status, error) {
					ajaxLoadingHide();
					output = orig_data;	//"ajax error";
					alert(tr("AJAX Error") + "\n" + tr("It may not be possible to edit this page reliably in WYSIWYG mode in this browser.") + "\n\n" + error.message);
				}
			});
			return output;
		},
		
		requires : [ '' ]	// TODO check req - autosave? (really)
	});
	
	if (typeof CKEDITOR.editor.prototype.reParse != 'function') {
		CKEDITOR.editor.prototype.pluginReparsing = false;

		CKEDITOR.editor.prototype.reParse = function() {
	
			// send the whole source off to the server to get reparsed?
			var myoutput = "";
			var mydata = this.getData();
			myoutput = this.dataProcessor.toHtml(mydata);

			this.pluginReparsing = true;
			this.setData(myoutput);
		};
	}
})();
