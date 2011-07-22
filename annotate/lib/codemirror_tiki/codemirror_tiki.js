//$Id$

jQuery.fn.extend({
	flexibleCodeMirror: function(settings) {
		settings = jQuery.extend({
			parse: ["tiki"],
			changeText: "Change Highlighter",
			languages: [
				"clike",
				"css",
				"diff",
				"haskell",
				"htmlmixed",
				"javascript",
				"lua",
				"php",
				"plsql",
				"python",
				"rst",
				"r",
				"scheme",
				"smalltalk",
				"stex",
				"tiki",
				"xml",
				"yaml"
			],
			buttonText: {
				update: "Update",
				cancel: "Cancel"
			},
			parent: jQuery(this).parent(),
			lineNumbers: false,
			textWrapping: true,
			readOnly: false,
			width: '100%',
			force: false
		}, settings);
		
		var l = "lib/codemirror/mode/";
		var parserLocs = {
			clike: 		{js: l + "clike/clike.js"},
			css: 		{js: l + "css/css.js"},
			diff: 		{
							js: l + "diff/diff.js",
							css: l + "diff/diff.css"
						},
			haskell: 	{js: l + "haskell/haskell.js"},
			htmlmixed: 	{js: l + "htmlmixed/htmlmixed.js"},
			javascript: {js: l + "javascript/javascript.js"},
			lua: 		{js: l + "lua/lua.js"},
			php: 		{js: l + "php/php.js"},
			plsql: 		{js: l + "plsql/plsql.js"},
			python: 	{js: l + "python/python.js"},
			rst: 		{
							js: l + "rst/rst.js",
							css: l + "rst/rst.css"
						},
			//r: 			{js: l + "clike/clike.js"},
			scheme: 	{js: l + "scheme/scheme.js"},
			smalltalk: 	{js: l + "smalltalk/smalltalk.js"},
			stex: 		{js: l + "stex/stex.js"},
			xml: 		{js: l + "xml/xml.js"},
			yaml:		{js: l + "yaml/yaml.js"}
		};
		
		jQuery(this).each(function() {
			var o = jQuery(this);
			
			if (!settings.force && !o.is('pre')) {
				if (!o.data('codemirror') || o.data("nocodemirror")) return false;
			}
			
			var textarea;
			
			if (!o.is(':input')) {
				var val = o.text();
				textarea =  $('<textarea></textarea>')
					.appendTo(settings.parent)
					.val(val);
			} else {
				textarea = o;
				settings.width = o.width() + "px";
				settings.height = o.height() + "px";
			}
			
			if (textarea.attr('codeMirrorRelationshipFullscreen')) return false;
			
			if (!textarea.length) return false;
			if (!window.CodeMirror) return false;
			
			var parserfiles = [];
			var stylesheet = [];
			
			var parse = textarea.data('syntax');
			if (parse) settings.parse = (parse + '').split(',');
			
			parse = settings.parse;
			
			if (!$.isArray(parse)) parse = [parse];
			
			jQuery('style.tiki-codemirror-style').remove();
			
			jQuery(parse).each(function(i) {
				if (parserLocs[this]) {
					if (parserLocs[this].css) {
						jQuery('head').append('<link rel="stylesheet" class="tiki-codemirror-style" href="' + parserLocs[this].css + '">');
					}
					if (parserLocs[this].js) {
						jQuery.addScript(parserLocs[this].js);
					}
				}
			});
			
			var editor = CodeMirror.fromTextArea(textarea[0], {
				width: settings.width,
				height: settings.height,
				stylesheet: stylesheet,
				onChange: function() {
					//Setup codemirror to send the text back to the textarea
					textarea.val(editor.getCode()).change();
				},
				lineNumbers: settings.lineNumbers,
				textWrapping: settings.textWrapping,
				readOnly: settings.readOnly
			});
			
			if (!settings.readOnly) {
				addCodeMirrorEditorRelation(editor, textarea);
				
				var changeButton = jQuery('<div class="button">' +
				'<a>' +
				settings.changeText +
				'</a>' +
				'</div>').insertAfter(textarea.next()).click(function(){
					var options = 'Languages:<br />';
					jQuery(settings.languages).each(function(){
						var lang = this + '';
						options += '<input class="lang" type="checkbox" value="' + lang + '" ' + (parse.indexOf(lang) > -1 ? 'checked="true"' : '') + '/>' + lang + '<br />';
					});
					
					options += 'Options:<br />';
					options += '<input class="opt" type="checkbox" value="lineNumbers" ' + (settings.lineNumbers ? 'checked="true"' : '') + '/>Line Numbers<br />';
					options += '<input class="opt" type="checkbox" value="textWrapping" ' + (settings.textWrapping ? 'checked="true"' : '') + '/>Text Wrapping<br />';
					options += '<input class="opt" type="checkbox" value="dynamicHeight" ' + (settings.dynamicHeight ? 'checked="true"' : '') + '/>Dynamic Height<br />';
					
					var msg = jQuery('<div />').html(options).dialog({
						title: settings.changeText,
						modal: true,
						buttons: {
							"Update": function(){
								var newSettings = {};
								var newParse = [];
								msg.find('.lang').each(function(){
									var o = jQuery(this);
									if (o.is(':checked')) {
										newParse.push(o.val());
									}
								});
								
								newSettings.parse = newParse;
								
								msg.find('.opt').each(function(){
									var o = jQuery(this);
									newSettings[o.val()] = o.is(':checked');
								});
								
								changeButton.remove();
								editor.toTextArea();
								
								
								textarea.flexibleCodeMirror(jQuery.extend(settings, newSettings));
								
								msg.dialog("destroy");
							},
							"Cancel": function(){
								msg.dialog("destroy");
							}
						}
					});
				});
			}
		});
	}
});
