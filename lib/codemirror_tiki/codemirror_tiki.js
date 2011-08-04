//$Id$

jQuery.fn.extend({
	flexibleSyntaxHighlighter: function(settings) {
		settings = jQuery.extend({
			mode: "tiki",
			changeText: "Change Highlighter",
			modes: [
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
			var mode = textarea.data('syntax');
			if (!settings.mode) {
				if (mode.match(',')) mode = mode.split(',');
				settings.mode = mode;
			}
			
			if (!jQuery.isArray(settings.mode)) {
				settings.mode = [settings.mode];
			}
			
			jQuery('style.tiki-codemirror-style').remove();
			
			var modeStack = 0;
			jQuery(settings.mode).each(function() {
				mode = (this + '');
				if (modeLocs[mode]) {
					modeStack++;
					if (modeLocs[mode].css) {
						jQuery('head').append('<link rel="stylesheet" class="tiki-codemirror-style" href="' + modeLocs[mode].css + '" />');
					}
					if (modeLocs[mode].js) {
						jQuery.getScript(modeLocs[mode].js, function(o) {
							modeStack--;
							if (modeStack < 1) {
								syntaxHighlighter.ready(textarea, settings);
							}
						});
					}
				}
			});
		});
	}
});

//An effective way of interacting with a codemirror editor
var syntaxHighlighter = {
	ready: function(textarea, settings) {
		var editor = CodeMirror.fromTextArea(textarea[0], {
			stylesheet: 'default',
			onChange: function() {
				//Setup codemirror to send the text back to the textarea
				textarea.val(editor.getValue()).change();
			},
			lineNumbers: settings.lineNumbers,
			textWrapping: settings.textWrapping,
			readOnly: settings.readOnly,
			mode: (settings.mode == 'tiki' ? 'text/tiki' : '')
		});
		
		if (!settings.readOnly) {
			syntaxHighlighter.add(editor, textarea);
			
			var changeButton = jQuery('<div class="button">' +
			'<a>' +
			settings.changeText +
			'</a>' +
			'</div>').insertAfter(textarea.next()).click(function(){
				var options = 'Modes:<br />';
				jQuery(settings.modes).each(function(i){
					var mode = (this + '');
					options += '<input class="cm-mode" type="checkbox" value="' + mode + '" ' + (settings.mode.indexOf(mode) > -1 ? 'checked="true"' : '') + '/>' + mode + '<br />';
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
							var newMode = [];
							
							msg.find('.cm-mode:checked').each(function() {
									newMode.push(jQuery(this).val());
							});
							
							newSettings.mode = newMode;
							
							msg.find('.opt').each(function(){
								var o = jQuery(this);
								newSettings[o.val()] = o.is(':checked');
							});
							
							changeButton.remove();
							editor.toTextArea();
							
							
							textarea.flexibleSyntaxHighlighter(jQuery.extend(settings, newSettings));
							
							msg.dialog("destroy");
						},
						"Cancel": function(){
							msg.dialog("destroy");
						}
					}
				});
			});
		}
	},
	add: function(editor, $input, none, skipResize) {
		window.codeMirrorEditor = (window.codeMirrorEditor ? window.codeMirrorEditor : []);
		var i = window.codeMirrorEditor.push(editor);
		
		if ($.fn.resizable && !skipResize) {
			var codeWrapper = $('div.CodeMirror');
			
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
		window.codeMirrorEditor[relationship] = null;
		$input.removeAttr('codeMirrorRelationship');
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