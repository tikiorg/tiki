//$Id$

jQuery.fn.extend({
	flexibleSyntaxHighlighter: function(s) {
		s = jQuery.extend({
			mode: "tiki",
			lineNumbers: false,
			lineWrapping: true,
			readOnly: false,
			force: false,
			theme: window.codeMirrorTheme || 'default'
		}, s);

		jQuery(this).addClass('CodeMirrorPrepSize'); //we have to hide all the textareas or pre objects to know how much space we can take up
		
		jQuery(this).each(function() {
			var settings = jQuery.extend(true, {}, s);
			settings.parent = jQuery(this).parent();
			
			//removes the toggle button
			settings.parent.find('.cm-remove').remove();
			
			var o = jQuery(this);
			
			if (!settings.force && !o.is('pre')) {
				if (!o.data('codemirror') || o.data("nocodemirror")) {
					jQuery(this)
						.removeClass('CodeMirrorPrepSize')
						.show();
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
				settings.height = o.height();
				o.removeClass('CodeMirrorPrepSize');

				//prevent the bottom scrollbar from popping up if not enough height
				settings.height = (settings.height < 25 ? 25 : settings.height);
				settings.width = (settings.width < 50 ? 50 : settings.width);
				//-->End Width fix

				if (textarea.data('codeMirrorRelationshipFullscreen')) return false;
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

				if (!mode) {
					syntaxHighlighter.remove(textarea);
					syntaxHighlighter.ready(textarea, settings, "");
					settings.parent.modal();
				} else {
					o.addClass('CodeMirrorHide');
					var scrollTop = o.scrollTop(),
					textareaHeight = o.innerHeight();

					syntaxHighlighter.remove(textarea);

					var editor = syntaxHighlighter.ready(textarea, settings, mode),
					editorWindow = jQuery(editor.getWrapperElement()).find('.CodeMirror-scroll').andSelf(),
					scroller = jQuery(editor.getScrollerElement());
					scroller.scrollTop(scrollTop * (scroller.innerHeight() / textareaHeight))

					o.removeClass('CodeMirrorHide');

					settings.parent.modal();

					editorWindow.find('div:first')
						.css('width', 'auto')
						.css('height', 'auto');

					if (settings.readOnly) {
						editorWindow
							.css('height', 'auto')
							.css('overflow', 'hidden');
					}

					o.trigger('syntaxHighlighterLoaded');
				}
			}, true);
		});
		
		return this;
	}
});

//An effective way of interacting with a codemirror editor
var syntaxHighlighter = {
	ready: function(textarea, settings, mode) {
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
			mode: mode,
			lineWrapping: settings.lineWrapping,
			theme: settings.theme
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
			//design has a max-width set, and we really shouldn't override that if possible, so account for that when setting width
			var scroller = $(editor.getScrollerElement());
			var maxWidth = (scroller.parent().css('max-width') + '').replace('px', '') * 1;

			settings.width = (maxWidth < settings.width ? maxWidth : settings.width);

			scroller
				.width(settings.width)
				.children()
				.width(settings.width);
		}

		var parents = textarea.parents('form');
		if (!parents.data('codeMirrorActive')) {
			parents
				.submit(function() {
					if (textarea.data('codeMirrorRelationship')) { //always get value from textarea, but onlu update it if active
						textarea
							.val(editor.getValue())
							.change();
					}
				})
				.data('codeMirrorActive', true);
		}

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
						//Modes
						var options = tr('Modes:') + '<br />';
						options += "<select class='cm-mode'>";	
						options += "<option value=''>" + tr("Select a Mode") + "</option>";

						var modes = CodeMirror.listModes();
						for(i in modes) {
							options += '<option value="' + modes[i] + '">' + modes[i] + '</option>';
						}
						options += "</select><br />";

						//Modes
						options += tr('Theme:') + '<br />';
						options += '<select class="cm-theme">';
						options += '<option value="default">default</option>';
						options += '<option value="night">night</option>';
						options += '<option value="monokia">monokai</option>';
						options += '<option value="neat">neat</option>';
						options += '<option value="elegant">elegant</option>';
						options += '<option value="cobalt">cobalt</option>';
						options += '<option value="eclipse">eclipse</option>';
						options += '<option value="rubyblue">rubyblue</option>';
						options += '<option value="lesser-dark">lesser-dark</option>';
						options += '<option value="eq-dark">xq-dark</option>';
						options += '</select><br />';

						//Others
						options += tr('Options:') + '<br />';
						options += '<input class="opt" type="checkbox" value="lineNumbers" ' + (settings.lineNumbers ? 'checked="true"' : '') + '/>' + tr("Line Numbers") + '<br />';
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
											var newSettings = {
												mode: msg.find('.cm-mode').val(),
												theme: msg.find('.cm-theme').val()
											};
											
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
						
						msg.find(".cm-mode").val(mode);
						msg.find(".cm-theme").val(settings.theme);
					});
			
			var removeButton = jQuery(
				'<div class="button cm-remove" style="float: right;">' +
					'<a>' +
						settings.removeText +
					'</a>' +
				'</div>')
					.insertAfter(changeButton)
					.toggle(function(){
						if ($('.CodeMirror-fullscreen').length) syntaxHighlighter.fullscreen(textarea);

						syntaxHighlighter.remove(textarea);
						var scroller = $(editor.getScrollerElement()),
						scrollTop = scroller.scrollTop(),
						scrollerHeight = scroller.innerHeight();

						editor.toTextArea();

						textarea
							.removeClass('CodeMirrorPrepSize')
							.show()
							.removeData('codeMirrorRelationship')
							.scrollTop(scrollTop * (textarea.innerHeight() / scrollerHeight));
						changeButton.remove();
					}, function() {
						var scrollTop = $('html,body').scrollTop();
						textarea.flexibleSyntaxHighlighter(settings);
						$('html,body').scrollTop(scrollTop);
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
			.data('codeMirrorRelationship', i - 1)
			.addClass('codeMirror');
	},
	remove: function($input) {
		var relationship = parseInt($input.data('codeMirrorRelationship'));
		if (relationship) {
			window.codeMirrorEditor[relationship] = null;
			$input.removeData('codeMirrorRelationship');
		}
	},
	get: function($input) {
		var relationship = parseInt($input.data('codeMirrorRelationship'));
		
		if (window.codeMirrorEditor) {
			if (window.codeMirrorEditor[relationship]) {
				return window.codeMirrorEditor[relationship];
			}
		}
		return false;
	},
	fullscreen: function(textarea) {
		$('.CodeMirror-fullscreen').find('.CodeMirror').andSelf().css('height', '');
		
		//removes wiki command buttons (save, cancel, preview) from fullscreen view
		$('.CodeMirror-fullscreen .wikiaction').remove();
		
		textarea.parent().toggleClass('CodeMirror-fullscreen');
		$('body').toggleClass('noScroll');
		$('.tabs,.rbox-title').toggle();
		
		var isFullscreen = ($('.CodeMirror-fullscreen').length ? true : false);
		
		if (isFullscreen) {
			var win = $(window)
				.data('cm-resize', true),
			screen = $('.CodeMirror-fullscreen'),
			editorObj = screen.find('.CodeMirror'),
			toolbar = $('#editwiki_toolbar');

			win.resize(function() {
				if (win.data('cm-resize') && screen) {
					screen.css('height', win.height() + 'px');

					editorObj
						.css('height', ((win.height() - toolbar.height()) - 25) + 'px');
				}
			})
			.resize();
			
			//adds wiki command buttons (save, cancel, preview) from fullscreen view
			$('.wikiaction').clone().appendTo('.CodeMirror-fullscreen');
		} else {
			$(window).removeData('cm-resize');
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

			var modes = CodeMirror.listModes();
			for(i in modes) {
				$('<option />').text(modes[i]).attr('value', modes[i]).appendTo(colorsSelector);
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