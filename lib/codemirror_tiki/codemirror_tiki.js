//$Id$

jQuery.fn.extend({
	flexibleSyntaxHighlighter: function(settings) {
		settings = jQuery.extend({
			mode: "tiki",
			changeText: "Change Highlighter",
			buttonText: {
				update: "Update",
				cancel: "Cancel"
			},
			parent: jQuery(this).parent(),
			lineNumbers: false,
			textWrapping: true,
			readOnly: false,
			force: false,
			l: "lib/codemirror/mode/"
		}, settings);
		
		var l = settings.l;
		
		var modeLocs = {
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
			r: 			{js: "lib/codemirror_tiki/mode/r/r.js"},
			scheme: 	{js: l + "scheme/scheme.js"},
			smalltalk: 	{js: l + "smalltalk/smalltalk.js"},
			stex: 		{js: l + "stex/stex.js"},
			tiki:	{
							js: 'lib/codemirror_tiki/mode/tiki/tiki.js',
							css: 'lib/codemirror_tiki/mode/tiki/tiki.css'
						},
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
					.data('syntax', o.data('syntax'))
					.appendTo(settings.parent)
					.val(val);
				
				settings.width = o.width();
				settings.height = o.height();
				
				//prevent the bottom scrollbar from popping up if not enough height
				settings.height = (settings.height < 50 ? 50 : settings.height);
					
				o.hide();
			} else {
				textarea = o;
			}
			
			if (textarea.attr('codeMirrorRelationshipFullscreen')) return false;
			
			if (!textarea.length) return false;
			if (!window.CodeMirror) return false;
			
			var mode = textarea.data('syntax');
			
			if (!settings.mode) {
				settings.mode = mode;
			} else if (!mode) {
				mode = settings.mode;
			}
			settings.mode = mode;
			
			var modeDef = syntaxHighlighter.modes[mode];
			if (!modeDef) return;
			if (!modeDef.modeSpec) return;
			if (!modeDef.mime) return;
			var modeLoc = modeLocs[modeDef.modeSpec];
			if (!modeLoc) return;
			
			if (modeLoc.css) {
				jQuery('head').append('<link rel="stylesheet" class="tiki-codemirror-style" href="' + modeLoc.css + '" />');
			}
			
			if (modeLoc.js) {
				jQuery.getScript(modeLoc.js, function(e) {
					syntaxHighlighter.remove(textarea);
					syntaxHighlighter.ready(textarea, settings, modeDef, mode);
				});
			}
		});
	}
});

//An effective way of interacting with a codemirror editor
var syntaxHighlighter = {
	modes: {
		"c": {
			modeSpec: "clike",
			mime: "text/x-csrc"
		},
		"c++": {
			modeSpec: "clike",
			mime: "text/x-c++src"
		},
		"c#": {
			modeSpec: "clike",
			mime: "text/x-csharp"
		},
		"java": {
			modeSpec: "clike",
			mime: "text/x-java"
		},
		"css": {
			modeSpec: "css",
			mime: "text/css"
		},
		"diff": {
			modeSpec: "diff",
			mime: "text/x-diff"
		},
		"haskell": {
			modeSpec: "haskell",
			mime: "text/x-haskell"
		},
		"html mixed": {
			modeSpec: "htmlmixed",
			mime: "text/html"
		},
		"javascript": {
			modeSpec: "javascript",
			mime: "text/javascript"
		},
		"json": {
			modeSpec: "javascript",
			mime: "application/json"
		},
		"lua": {
			modeSpec: "lua",
			mime: "text/x-lua"
		},
		"php mixed": {
			modeSpec: "php",
			mime: "application/x-httpd-php"
		},
		"php plain": {
			modeSpec: "php",
			mime: "text/x-php"
		},
		"pl-sql": {
			modeSpec: "plsql",
			mime: "text/x-plsql"
		},
		"python": {
			modeSpec: "python",
			mime: "text/x-python"
		},
		"resturctured text": {
			modeSpec: "rst",
			mime: "text/x-rst"
		},
		"r": {
			modeSpec: "r",
			mime: "text/x-r"
		},
		"scheme": {
			modeSpec: "scheme",
			mime: "text/x-scheme"
		},
		"smalltalk": {
			modeSpec: "smalltalk",
			mime: "text/x-stsrc"
		},
		"stex": {
			modeSpec: "stex",
			mime: "text/x-stex"
		},
		"tiki": {
			modeSpec: "tiki",
			mime: "text/tiki"
		},
		"html": {
			modeSpec: "xml",
			mime: "text/html"
		},
		"xml": {
			modeSpec: "xml",
			mime: "application/xml"
		},
		"yaml": {
			modeSpec: "yaml",
			mime: "text/x-yaml"
		}
	},
	ready: function(textarea, settings, modeDef, currentMode) {
		var changeCount = 0;
		
		var editor = CodeMirror.fromTextArea(textarea[0], {
			stylesheet: 'default',
			onChange: function() {
				//Setup codemirror to send the text back to the textarea
				auto_save( textarea.attr('id') );
				
				changeCount++;
				if (changeCount > 50) {
					textarea.val(editor.getValue()).change();
					changeCount = 0;
				}
			},
			lineNumbers: settings.lineNumbers,
			textWrapping: settings.textWrapping,
			readOnly: settings.readOnly,
			mode: modeDef.mime
		});
		
		if (settings.readOnly) {
			$(editor.getWrapperElement())
				.addClass('codelisting')
				.css('padding', '0px');
		}
		
		if (settings.height) {
			$(editor.getScrollerElement())
				.height(settings.height)
				.children()
				.height(settings.height);
		}
		
		if (settings.width) {
			$(editor.getScrollerElement())
				.width(settings.width)
				.children()
				.width(settings.width);
		}
		
		textarea.parents('form').submit(function() {
			textarea.val(editor.getValue()).change();
		});
		
		if (!settings.readOnly) {
			syntaxHighlighter.add(editor, textarea);
			
			var changeButton = jQuery('<div class="button">' +
			'<a>' +
			settings.changeText +
			'</a>' +
			'</div>').insertAfter(textarea.next()).click(function(){
				var options = 'Modes: <br />';
				options += "<select class='cm-mode'>";	
				options += "<option value=''>Select a Mode</option>";
				for(mode in syntaxHighlighter.modes) {
					options += '<option value="' + mode + '">' + mode + '</option>';
				}
				options += "</select><br />";
				
				options += 'Options:<br />';
				options += '<input class="opt" type="checkbox" value="lineNumbers" ' + (settings.lineNumbers ? 'checked="true"' : '') + '/>Line Numbers<br />';
				options += '<input class="opt" type="checkbox" value="textWrapping" ' + (settings.textWrapping ? 'checked="true"' : '') + '/>Text Wrapping<br />';
				options += '<input class="opt" type="checkbox" value="dynamicHeight" ' + (settings.dynamicHeight ? 'checked="true"' : '') + '/>Dynamic Height<br />';
				
				var msg = jQuery('<div />')
					.html(options)
					.dialog({
						title: settings.changeText,
						modal: true,
						buttons: {
							"Update": function(){
								var newSettings = {};
								
								newSettings.mode = msg.find('.cm-mode').val();
								
								msg.find('.opt').each(function(){
									var o = jQuery(this);
									newSettings[o.val()] = o.is(':checked');
								});
								
								changeButton.remove();
								editor.toTextArea();
								
								textarea.data('syntax', newSettings.mode);
								
								textarea.flexibleSyntaxHighlighter(jQuery.extend(settings, newSettings));
								
								msg.dialog("destroy");
							},
							"Cancel": function(){
								msg.dialog("destroy");
							}
						}
					});
				
				msg.find(".cm-mode").val(currentMode);
			});
		}
	},
	sync: function(textarea) {
		var editor = this.get(textarea);
		if (editor) textarea.val(editor.getValue());
	},
	add: function(editor, $input, none, skipResize) {
		window.codeMirrorEditor = (window.codeMirrorEditor ? window.codeMirrorEditor : []);
		var i = window.codeMirrorEditor.push(editor);
		
		if ($.fn.resizable && !skipResize) {
			var codeWrapper = $(editor.getWrapperElement());
			
			codeWrapper
				.resizable({
					minWidth: codeWrapper.width(),
					minHeight: codeWrapper.height(),
					alsoResize: codeWrapper.find('div.CodeMirror-scroll')
				});
		}
		
		$input
			.attr('codeMirrorRelationship', i - 1)
			.addClass('codeMirror');
	},
	remove: function($input) {
		var relationship = parseInt($input.attr('codeMirrorRelationship'));
		if (relationship) {
			window.codeMirrorEditor[relationship] = null;
			$input.removeAttr('codeMirrorRelationship');
		}
	},
	get: function($input) {
		var relationship = parseInt($input.attr('codeMirrorRelationship'));
		
		if (window.codeMirrorEditor) {
			if (window.codeMirrorEditor[relationship]) {
				return window.codeMirrorEditor[relationship];
			}
		}
		return false;
	},
	fullscreen: function(textarea) {
		$('.CodeMirror-fullscreen, .CodeMirror-fullscreen .CodeMirror').css('height', '');
		textarea.parent().toggleClass('CodeMirror-fullscreen');
		$('body').toggleClass('noScroll');
		$('.tabs,.rbox-title').toggle();
		
		var isFullscreen = ($('.CodeMirror-fullscreen').length ? true : false);
		
		if (isFullscreen) {
			var win = $(window);
			win.resize(function() {
				$('.CodeMirror-fullscreen')
					.css('height', win.height() + 'px')
					.find('.CodeMirror')
					.css('height', ((win.height() - $('#editwiki_toolbar').height()) - 25) + 'px');
			})
			.resize();
		} else {
			$(window).unbind('resize');
		}
		return false;
	},
	find: function(textareaEditor, val) {
		if (!this.searchCursor[val]) {
			this.searchCursor[val] = textareaEditor.getSearchCursor(val);
		}
		if (this.searchCursor[val].findNext()) {
			textareaEditor.setSelection(this.searchCursor[val].from(), this.searchCursor[val].to());
		}
	},
	searchCursor: [],
	replace: function(textareaEditor, val, replaceVal) {
		if (!this.searchCursor[val]) {
			this.searchCursor[val] = textareaEditor.getSearchCursor(val);
		}
		
		while(this.searchCursor[val].findNext()) {
			this.searchCursor[val].replace(replaceVal);
		}
	},
	insertAt: function(textareaEditor, replaceString, perLine, blockLevel) {
		var toBeReplaced = /text|page|area_id/g;
		var handle = textareaEditor.getCursor(true);
		var selection = textareaEditor.getSelection();
	 	var cursor = textareaEditor.getCursor();
		
		var newString = '';
		
	 	if (perLine) { //for bullets
			if (textareaEditor.somethingSelected()) {//we kill all content because we already have the selection, and when we split it and re-insert, we get the lines again
				textareaEditor.replaceSelection('');
			} else {
				selection = textareaEditor.getLine(handle.line);
			}
			var lines = selection.split(/\n/g);
			jQuery(lines).each(function(i){
				newString += replaceString.replace(toBeReplaced, this + '') + (i == lines.length - 1 ? '' : '\n');
			});
			
			if (textareaEditor.getSelection()) {
				textareaEditor.replaceSelection(newString);
			} else {
				textareaEditor.setLine(handle.line, newString);
			}
		} else if (blockLevel && toBeReplaced) {
			selection = textareaEditor.getLine(handle.line);
			textareaEditor.setLine(handle.line, replaceString.replace(toBeReplaced, selection));
	 	} else if (replaceString) {
			var cursor = textareaEditor.getCursor();
			textareaEditor.replaceSelection(replaceString.replace(toBeReplaced, selection));
			cursor.ch += textareaEditor.getSelection().length;
			textareaEditor.setCursor(cursor);
	 	} else {
	 		textareaEditor.replaceRange(textareaEditor.lineCount() - 1, 'end', newString);
	 	}
	 	
		textareaEditor.focus();
		return;
	}
};