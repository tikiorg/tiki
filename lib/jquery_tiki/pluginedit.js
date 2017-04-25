/**
 * (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
 *
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * $Id$
 *
 * Handles wiki plugin edit forms
 */


(function ($) {

	/* wikiplugin editor */
	window.popupPluginForm = function (area_id, type, index, pageName, pluginArgs, bodyContent, edit_icon, selectedMod) {

		var $textArea = $("#" + area_id);

		if ($textArea.length && $textArea[0].createTextRange) {	// save selection for IE
			storeTASelection(area_id);
		}

		var container = $('<div class="plugin"></div>');

		if (!index) {
			index = 0;
		}
		if (!pageName) {
			pageName = '';
		}
		var textarea = $textArea[0];
		var replaceText = false;

		if (!pluginArgs && !bodyContent) {
			pluginArgs = {};
			bodyContent = "";

			dialogSelectElement(area_id, '{' + type.toUpperCase(), '{' + type.toUpperCase() + '}');
			var sel = getTASelection(textarea);
			if (sel && sel.length > 0) {
				sel = sel.replace(/^\s\s*/, "").replace(/\s\s*$/g, "");	// trim
				//alert(sel.length);
				if (sel.length > 0 && sel.substring(0, 1) === '{') { // whole plugin selected
					var l = type.length;
					if (sel.substring(1, l + 1).toUpperCase() === type.toUpperCase()) { // same plugin
						var rx = new RegExp("{" + type + "[\\(]?([\\s\\S^\\)]*?)[\\)]?}([\\s\\S]*){" + type + "}", "mi"); // using \s\S matches all chars including lineends
						var m = sel.match(rx);
						if (!m) {
							rx = new RegExp("{" + type + "[\\(]?([\\s\\S^\\)]*?)[\\)]?}([\\s\\S]*)", "mi"); // no closing tag
							m = sel.match(rx);
						}
						if (m) {
							var paramStr = m[1];
							bodyContent = m[2];

							var pm = paramStr.match(/([^=]*)=\"([^\"]*)\"\s?/gi);
							if (pm) {
								for (var i = 0; i < pm.length; i++) {
									var ar = pm[i].split("=");
									if (ar.length) { // add cleaned vals to params object
										pluginArgs[ar[0].replace(/^[,\s\"\(\)]*/g, "")] = ar[1].replace(/^[,\s\"\(\)]*/g, "").replace(/[,\s\"\(\)]*$/g, "");
									}
								}
							}
						}
						replaceText = sel;
					} else {
						if (!confirm("You appear to have selected text for a different plugin, do you wish to continue?")) {
							return false;
						}
						bodyContent = sel;
						replaceText = true;
					}
				} else { // not (this) plugin
					if (type === 'mouseover') { // For MOUSEOVER, we want the selected text as label instead of body
						bodyContent = '';
						pluginArgs = {};
						pluginArgs['label'] = sel;
					} else {
						bodyContent = sel;
					}
					replaceText = true;
				}
			} else {	// no selection
				replaceText = false;
			}
		}

		var $modal = $('.modal.fade:not(.in)').first();

		var url = $.service("plugin", "edit", {
			area_id: area_id,
			type: type,
			index: index,
			page: pageName,
			pluginArgs: pluginArgs,
			bodyContent: bodyContent,
			edit_icon: !!edit_icon,
			selectedMod: selectedMod ? selectedMod : "",
			modal: 1
		});

		// Make the form appear
		$modal
			.modal({
				remote: url,
				show: false			// if it's the first time the show.bs.modal doesn't trigger sometimes
			})
			.one('loaded.bs.modal', function () {			// Bind remote loaded event
				// enables conditional display of inputs with a "parent" selector
				handlePluginFieldsHierarchy();
				// bind form button events and form validation
				handleFormSubmit(this, type, edit_icon, area_id, replaceText);
				// Trigger jQuery event 'plugin_#type#_ready' (see plugin_code_ready in codemirror_tiki.js for example)
				$document
					.trigger({
						type: 'plugin_' + type + '_ready',
						container: container,
						arguments: arguments,
						modal: $modal
					})
					.trigger({
						type: 'plugin_ready',
						container: container,
						arguments: arguments,
						modal: $modal
					});
			})
			// unset semaphore on object/page on cancel
			.one("hidden.bs.modal", function () {
				if ($("form", this).length && edit_icon) {
					$.getJSON($.service("semaphore", "unset"), {
						object_id: pageName
					});
				}
			})
			.modal("show")
			.find('.modal-dialog').addClass("modal-lg");


	};

	/*
	 * Hides all children fields in a wiki-plugin form and
	 * add javascript events to display them when the appropriate
	 * values are selected in the parent fields.
	 */
	function handlePluginFieldsHierarchy() {
		var $container = $('#plugin_params');

		var parents = {};

		$("[data-parent_name]", $container).each(function () {
			var parentName = $(this).data("parent_name"),
				parentValue = $(this).data("parent_value");
			if (parentName) {
				var $parent = $('[name$="params[' + parentName + ']"]', $container);

				var $row = $(this).parents(".form-group");
				$row.addClass('parent_' + parentName + '_' + parentValue);

				if ($parent.val() !== parentValue) {
					if (!$parent.val() && $("input, select", $row).val()) {
						$parent.val(parentValue);
					} else {
						$row.hide();
					}
				}

				if (!parents[parentName]) {
					parents[parentName] = {
						children: [],
						parentElement: $parent
					};
				}

				parents[parentName]['children'].push($(this).attr("id"));
			}
		});

		$.each(parents, function (parentName, parent) {
			parent.parentElement.change(function () {
				$.each(parent.children, function (index, id) {
					$container.find('#' + id).parents(".form-group").hide();
				});
				$container.find('.parent_' + parentName + '_' + this.value).show();
			})
				.change().trigger("chosen:updated");
		});
	}

	/**
	 * set up insert/replace button and submit handler in "textarea" edit mode
	 *
	 * @param container
	 * @param type
	 * @param edit_icon
	 * @param area_id
	 * @param replaceText
	 */
	function handleFormSubmit(container, type, edit_icon, area_id, replaceText) {

		var params = [], edit = !!edit_icon, bodyContent = "";

		var $form = $("form", container);

		$form.submit(function () {

			if (!process_submit(this)) {
				return false;
			}

			$("[name^=params]", $form).each(function () {

				var name = $(this).attr("name"),
					matches = name.match(/params\[(.*)\]/),
					val = $(this).val();

				if (!matches) {
					// it's not a parameter, skip
					if (name === "content") {
						bodyContent = $(this).val();
					}
					return;
				}

				if (val && !edit) {
					val = val.replace(/"/g, '\\"');	// escape double quotes
					params.push(matches[1] + '="' + val + '"');
				}
			});

			var blob, pluginContentTextarea = $("[name=content]", $form),
				pluginContentTextareaEditor = syntaxHighlighter.get(pluginContentTextarea),
				cont = (pluginContentTextareaEditor ? pluginContentTextareaEditor.getValue() : pluginContentTextarea.val());

			if (!edit) {
				if (bodyContent) {
					blob = '{' + type.toUpperCase() + '(' + params.join(' ') + ')}' + cont + '{' + type.toUpperCase() + '}';
				} else {
					blob = '{' + type.toLowerCase() + ' ' + params.join(' ') + '}';
				}

				insertAt(area_id, blob, false, false, replaceText);
				$(container).modal("hide");

				return false;
			}
		});
	}

	function dialogSelectElement(area_id, elementStart, elementEnd) {
		if (typeof CKEDITOR !== 'undefined' && typeof CKEDITOR.instances[area_id] !== 'undefined') {
			return;
		}	// TODO for ckeditor

		var $textarea = $('#' + area_id);
		var textareaEditor = syntaxHighlighter.get($textarea);
		var val = ( textareaEditor ? textareaEditor.getValue() : $textarea.val() );
		var pairs = [], pos = 0, s = 0, e = 0;

		while (s > -1 && e > -1) {	// positions of start/end markers
			s = val.indexOf(elementStart, e);
			if (s > -1) {
				e = val.indexOf(elementEnd, s + elementStart.length);
				if (e > -1) {
					e += elementEnd.length;
					pairs[pairs.length] = [s, e];
				}
			}
		}

		(textareaEditor ? textareaEditor : $textarea[0]).focus();

		var selection = ( textareaEditor ? syntaxHighlighter.selection(textareaEditor, true) : $textarea.selection() );

		s = selection.start;
		e = selection.end;
		var st = $textarea.attr('scrollTop');

		for (var i = 0; i < pairs.length; i++) {
			if (s >= pairs[i][0] && e <= pairs[i][1]) {
				setSelectionRange($textarea[0], pairs[i][0], pairs[i][1]);
				break;
			}
		}

	}

})(jQuery);
