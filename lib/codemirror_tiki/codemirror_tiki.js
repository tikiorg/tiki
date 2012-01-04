//$Id$

jQuery.fn.extend({
	flexibleSyntaxHighlighter: function(s) {
		s = jQuery.extend({
			mode: "tiki",
			lineNumbers: false,
			lineWrapping: true,
			readOnly: false,
			force: false,
			l: "lib/codemirror/mode/"
		}, s);
		
		var l = s.l;
		
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
		
		var scrollTop = $('html,body').scrollTop();
		
		jQuery(this).each(function() {
			jQuery(this).hide();
		});
		
		jQuery(this).each(function() {
			var settings = jQuery.extend(true, {}, s);
			settings.parent = jQuery(this).parent();
			
			//removes the toggle button
			settings.parent.find('.cm-remove').remove();
			
			var o = jQuery(this);
			
			if (!settings.force && !o.is('pre')) {
				if (!o.data('codemirror') || o.data("nocodemirror")) {
					jQuery(this).show();
					return;
				}
			}
			
			var textarea;
			settings.parent.visible(function() {
				settings.parent.modal(tr("Loading..."));
				
				if (!o.is(':input')) {
					var syntax = o.data('syntax');
					var lineNumbers = o.data('line-numbers');
					var wrap = o.data('wrap');

					textarea =  $('<textarea class="preCodeMirror"></textarea>')
						.val(o.text())
						.insertAfter(o);
					
					if (syntax) textarea.data('syntax', syntax);
					if (lineNumbers) textarea.data('line-numbers', lineNumbers);
					if (wrap) textarea.data('wrap', wrap);
					settings.readOnly = true;
				} else {
					textarea = o;
				}
				
				//-->Width fix
				settings.width = settings.parent.width();
				settings.height = o.show().height();
				
				o.hide();
	
				//prevent the bottom scrollbar from popping up if not enough height
				settings.height = (settings.height < 25 ? 25 : settings.height);
				settings.width = (settings.width < 50 ? 50 : settings.width);
				//-->End Width fix
				if (textarea.attr('codeMirrorRelationshipFullscreen')) return false;
				if (!textarea.length) return false;
				if (!window.CodeMirror) return false;
				
				function applyOverride(override, setting) {
					var attr = textarea.data(override);
					if (!settings[setting]) {
						settings[setting] = attr;
					} else if (!attr) {
						attr = settings[setting];
					}
					settings[setting] = attr;
					return settings[setting];
				}
				
				var mode = applyOverride('syntax', 'mode');
				var lineNumbers = applyOverride('line-numbers', 'lineNumbers');
				var wrap = applyOverride('wrap', 'lineWrapping');
				
				var modeDef = (syntaxHighlighter.modes[mode] ? syntaxHighlighter.modes[mode] : {modeSpec: '', mime: ''});
				if (!modeDef.mime) {
					syntaxHighlighter.remove(textarea);
					syntaxHighlighter.ready(textarea, settings, modeDef, "");
					settings.parent.modal();
				} else {
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
							var editor = syntaxHighlighter.ready(textarea, settings, modeDef, mode);
							settings.parent.modal();
							
							var editorWindow = jQuery(editor.getWrapperElement()).find('.CodeMirror-scroll').andSelf()
							/*	.height(settings.height)
								.width(settings.width);*/
							
							editorWindow.find('div:first')
								.css('width', 'auto')
								.css('height', 'auto');
							
							if (settings.readOnly) {
								editorWindow
									.css('height', 'auto')
									.css('overflow', 'visible');
							}
							
							
							$('html,body').scrollTop(scrollTop);
							
							o.trigger('syntaxHighlighterLoaded');
						});
					}
				}
			}, true);
		});
		
		return this;
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
			mime: "text/html",
			depend: ["xml", "javascript", "css"]
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
		
		settings = $.extend({
			changeText: tr("Change Highlighter"),
			removeText: tr("Toggle Highlighter")
		}, settings);
		
		var editor = CodeMirror.fromTextArea(textarea[0], {
			stylesheet: 'default',
			onChange: function() {
				//Setup codemirror to send the text back to the textarea
				if (window.auto_save) {
					auto_save( textarea.attr('id') );
				}
				
				changeCount++;
				if (changeCount > 50) {
					textarea.val(editor.getValue()).change();
					changeCount = 0;
				}
			},
			onBlur: function() {
				if (window.auto_save) {
					textarea.val(editor.getValue()).change();
					auto_save( textarea.attr('id') );
				}
			},
			lineNumbers: settings.lineNumbers,
			readOnly: settings.readOnly,
			mode: modeDef.mime,
			lineWrapping: settings.lineWrapping
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
			
			var changeButton = jQuery(
				'<div class="button">' +
					'<a>' +
						settings.changeText +
					'</a>' +
				'</div>')
					.insertAfter(textarea.next())
					.click(function(){
						var options = 'Modes: <br />';
						options += "<select class='cm-mode'>";	
						options += "<option value=''>" + tr("Select a Mode") + "</option>";
						for(mode in syntaxHighlighter.modes) {
							options += '<option value="' + mode + '">' + mode + '</option>';
						}
						options += "</select><br />";
						
						options += 'Options:<br />';
						options += '<input class="opt" type="checkbox" value="lineNumbers" ' + (settings.lineNumbers ? 'checked="true"' : '') + '/>' + tr("Line Numbers") + '<br />';
						options += '<input class="opt" type="checkbox" value="dynamicHeight" ' + (settings.dynamicHeight ? 'checked="true"' : '') + '/>' + tr("Dynamic Height") + '<br />';
						options += '<input class="opt" type="checkbox" value="lineWrapping" ' + (settings.lineWrapping ? 'checked="true"' : '') + '/>' + tr("Line Wrapping") + '<br />';
						
						var msg = jQuery('<div />')
							.html(options)
							.dialog({
								title: settings.changeText,
								modal: true,
								buttons: [
									{
										text: tr("Update"),
										click: function(){
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
										}
									},
									{
										text: tr("Cancel"),
										click: function(){
											msg.dialog("destroy");
										}
									}
								]
							});
						
						msg.find(".cm-mode").val(currentMode);
					});
			
			var removeButton = jQuery(
				'<div class="button cm-remove" style="float: right;">' +
					'<a>' +
						settings.removeText +
					'</a>' +
				'</div>')
					.insertAfter(changeButton)
					.toggle(function(){
						editor.toTextArea();
						textarea.removeAttr('codeMirrorRelationship');
						changeButton.remove();
					}, function() {
						textarea.flexibleSyntaxHighlighter(settings);
					});
		}
		
		return editor;
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
					alsoResize: codeWrapper.find('div.CodeMirror-scroll'),
					resize: function() {
						editor.refresh();
					}
				})
				.trigger("resizestop");
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
		
		//removes wiki command buttons (save, cancel, preview) from fullscreen view
		$('.CodeMirror-fullscreen .wikiaction').remove();
		
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
			
			//adds wiki command buttons (save, cancel, preview) from fullscreen view
			$('.wikiaction').clone().appendTo('.CodeMirror-fullscreen');
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
	insertAt: function(textareaEditor, replaceString, perLine, blockLevel, replaceSelection) {
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
		} else if (blockLevel) {
			selection = textareaEditor.getLine(handle.line);
			
			if (selection) {
				textareaEditor.setLine(handle.line, replaceString.replace(toBeReplaced, selection));
			} else {
				textareaEditor.setLine(handle.line, replaceString);
			}
			
	 	} else if (replaceString) {
			var cursor = textareaEditor.getCursor();
			
			if (replaceSelection) {
				textareaEditor.replaceSelection(replaceString);
			} else if (replaceString.match(toBeReplaced) && selection) {
				textareaEditor.replaceSelection(replaceString.replace(toBeReplaced, selection));
			} else {
				textareaEditor.replaceSelection(replaceString);
			}
			
			cursor.ch += textareaEditor.getSelection().length;
			textareaEditor.setCursor(cursor);
	 	} else {
	 		textareaEditor.replaceRange(textareaEditor.lineCount() - 1, 'end', newString);
	 	}
	 	
		textareaEditor.focus();
		return;
	}
};

$(function() {
	$('textarea')
			.flexibleSyntaxHighlighter();
			
	$('.codelisting')
		.flexibleSyntaxHighlighter({
			readOnly: true,
			mode: 'tiki',
			width: $(this).width() + 'px',
			height: $(this).parent().height() + 'px'
		})
		.hide();

	//for plugin code
	$(document)
		.unbind('plugin_code_ready')
		.bind('plugin_code_ready', function(args) {
			var colors = args.container.find('#param_colors input:first').hide();
			var colorsSelector = $('<select />')
				.insertAfter(colors)
				.change(function() {
					colors.val(colorsSelector.val());
				})
				.mousedown(function() {
					colorsSelector.change();
				})
				.mouseup(function() {
					colorsSelector.change();
				})
				.click(function() {
					colorsSelector.change();
				});
			
			for(mode in syntaxHighlighter.modes) {
				$('<option />').text(mode).attr('value', mode).appendTo(colorsSelector);
			}
			
			colorsSelector.val(colors.val());
			
			var code = args.container.find('textarea[name=\"content\"]');
			code.flexibleSyntaxHighlighter({
				mode: colorsSelector.val(),
				lineNumbers: true,
				force: true
			});
	});

	//for plugin html
	$(document)
		.unbind('plugin_html_ready')
		.bind('plugin_html_ready', function(args) {
			var code = args.container.find('textarea:first');
			
			code.flexibleSyntaxHighlighter({
				mode: 'xml',
				lineNumbers: true,
				force: true
			});
		});
		
	//for plugin r
	$(document)
		.unbind('plugin_r_ready')
		.bind('plugin_r_ready', function(args) {
			var r = args.container.find('textarea:first');
		
			r.flexibleSyntaxHighlighter({
				mode: 'r',
				lineNumbers: true,
				force: true
			});
		});

	//for plugin r
	$(document)
		.unbind('plugin_rr_ready')
		.bind('plugin_rr_ready', function(args) {
			var r = args.container.find('textarea:first');
		
			r.flexibleSyntaxHighlighter({
				mode: 'r',
				lineNumbers: true,
				force: true
			});
		});
});