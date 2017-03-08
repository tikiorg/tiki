/**
 *
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
		var form = build_plugin_form(type, index, pageName, pluginArgs, bodyContent, selectedMod);

		//with PluginModule, if the user selects another module while the edit form is open
		//replace the form with a new one with fields to match the parameters for the module selected
		$(form).find('tr select[name="params[module]"]').change(function () {
			var npluginArgs = $.parseJSON($(form).find('input[name="args"][type="hidden"]').val());
			//this is the newly selected module
			var selectedMod = $(form).find('tr select[name="params[module]"]').val();
			$('div.plugin input[name="type"][value="' + type + '"]').parent().parent().remove();
			popupPluginForm(area_id, type, index, pageName, npluginArgs, bodyContent, edit_icon, selectedMod);
		});
		var $form = $(form).find('tr input[type=submit]').remove();

		container.append(form);


		var pfc = container.find('table tr').length;	// number of rows (plugin form contents)
		var t = container.find('textarea:visible').length;
		if (t) {
			pfc += t * 3;
		}
		if (pfc > 9) {
			pfc = 9;
		}
		if (pfc < 2) {
			pfc = 2;
		}
		pfc = pfc / 10;			// factor to scale dialog height

		var $closeBtn = $('<button>')
			.text(tr("Close"))
			.addClass('btn btn-default')
			.click(function () {
				$(this).parents(".modal").modal("hide");
			});

		var $submitBtn = $('<button>')
			.text(replaceText ? tr("Replace") : edit_icon ? tr("Submit") : tr("Insert"))
			.addClass('btn btn-primary')
			.click(function () {

				$(this).off("click").css("opacity", 0.3);

				var meta = tiki_plugins[type];
				var params = [];
				var edit = edit_icon;
				// whether empty required params exist or not
				var emptyRequiredParam = false;

				for (var i = 0; i < form.elements.length; i++) {
					var element = form.elements[i].name;

					var matches = element.match(/params\[(.*)\]/);

					if (matches === null) {
						// it's not a parameter, skip
						continue;
					}
					var param = matches[1];

					var val = form.elements[i].value;

					// check if fields that are required and visible are not empty
					if (meta.params[param]) {
						if (meta.params[param].required) {
							if (val === '' && $(form.elements[i]).is(':visible')) {
								$(form.elements[i]).css('border-color', 'red');
								if ($(form.elements[i]).next('.required_param').length === 0) {
									$(form.elements[i]).after('<div class="required_param" style="font-size: x-small; color: red;">(required)</div>');
								}
								emptyRequiredParam = true;
							}
							else {
								// remove required feedback if present
								$(form.elements[i]).css('border-color', '');
								$(form.elements[i]).next('.required_param').remove();
							}
						}
					}

					if (val !== '') {
						if (!edit) {
							val = val.replace(/"/g, '\\"');	// escape double quotes
						}
						params.push(param + '="' + val + '"');
					}
				}

				if (emptyRequiredParam) {
					return false;
				}

				var blob, pluginContentTextarea = $("[name=content]", form),
					pluginContentTextareaEditor = syntaxHighlighter.get(pluginContentTextarea);
				var cont = (pluginContentTextareaEditor ? pluginContentTextareaEditor.getValue() : pluginContentTextarea.val());

				if (meta.body) {
					blob = '{' + type.toUpperCase() + '(' + params.join(' ') + ')}' + cont + '{' + type.toUpperCase() + '}';
				} else {
					blob = '{' + type.toLowerCase() + ' ' + params.join(' ') + '}';
				}

				if (edit) {
					container.children('form').submit();
					// quick and dirty reload
					window.location = window.location.href;
				} else {
					insertAt(area_id, blob, false, false, replaceText);
				}
				$modal.modal("hide");

				return false;
			});

		var heading = container.find('h3').hide();

		var $modal = $('.modal.fade:not(.in)').first();

		$modal.find(".modal-dialog").empty()
			.append($("<div>").addClass("modal-content")
				.append($("<div>").addClass("modal-header").append($("<h4>").addClass("modal-title").text(heading.text())))
				.append($("<div>").addClass("modal-body").append(container))
				.append($("<div>").addClass("modal-footer").append($closeBtn, $submitBtn))
			);

		// Make the form appear
		$modal.modal({
			show: false			// if it;'s the first time the show.bs.modal doesn't trigger sometimes
		})
			.one('show.bs.modal', function () {			// Bind open event
				handlePluginFieldsHierarchy(type);
			})
			.modal("show");


		//This allows users to create plugin snippets for any plugin using the jQuery event 'plugin_#type#_ready' for document
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
	}

	/*
	 * Hides all children fields in a wiki-plugin form and
	 * add javascript events to display them when the appropriate
	 * values are selected in the parent fields.
	 */
	function handlePluginFieldsHierarchy(type) {
		var pluginParams = tiki_plugins[type]['params'];

		var parents = {};

		$.each(pluginParams, function (paramName, paramValues) {
			if (paramValues.parent) {
				var $parent = $('[name$="params[' + paramValues.parent.name + ']"]', '.wikiplugin_edit');

				var $row = $('.wikiplugin_edit').find('#param_' + paramName);
				$row.addClass('parent_' + paramValues.parent.name + '_' + paramValues.parent.value);

				if ($parent.val() !== paramValues.parent.value) {
					if (!$parent.val() && $("input, select", $row).val()) {
						$parent.val(paramValues.parent.value);
					} else {
						$row.hide();
					}
				}

				if (!parents[paramValues.parent.name]) {
					parents[paramValues.parent.name] = {};
					parents[paramValues.parent.name]['children'] = [];
					parents[paramValues.parent.name]['parentElement'] = $parent;
				}

				parents[paramValues.parent.name]['children'].push(paramName);
			}
		});

		$.each(parents, function (parentName, parent) {
			parent.parentElement.change(function () {
				$.each(parent.children, function () {
					$('.wikiplugin_edit #param_' + this).hide();
				});
				$('.wikiplugin_edit .parent_' + parentName + '_' + this.value).show();
			})
				.change().trigger("chosen:updated");
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


	function build_plugin_form(type, index, pageName, pluginArgs, bodyContent, selectedMod) {
		var form = document.createElement('form');
		form.method = 'post';
		form.action = 'tiki-wikiplugin_edit.php';
		form.className = 'wikiplugin_edit';

		var hiddenPage = document.createElement('input');
		hiddenPage.type = 'hidden';
		hiddenPage.name = 'page';
		hiddenPage.value = pageName;
		form.appendChild(hiddenPage);

		var hiddenType = document.createElement('input');
		hiddenType.type = 'hidden';
		hiddenType.name = 'type';
		hiddenType.value = type;
		form.appendChild(hiddenType);

		var hiddenIndex = document.createElement('input');
		hiddenIndex.type = 'hidden';
		hiddenIndex.name = 'index';
		hiddenIndex.value = index;
		form.appendChild(hiddenIndex);

		//
		var savedArgs = document.createElement('input');
		savedArgs.type = 'hidden';
		savedArgs.name = 'args';
		savedArgs.value = $.toJSON(pluginArgs);
		form.appendChild(savedArgs);

		//Convert to JSON and then back to an object to break
		//link between meta local variable and tiki_plugins[type] global variable.
		//Otherwise each change to meta.params using the extend below was being appended to the global
		//Probably a much easier way to do this
		var infostring = $.toJSON(tiki_plugins[type]);
		var meta = $.parseJSON(infostring);

		//For PluginModule, add selected module parameters to the plugin edit form
		if (type == 'module') {
			//isolate the module parameter object so it will be shown first in the form
			var onlymod = {"params": {"module": meta.params.module}};
			//user has not changed the module selection since opening the form
			if (typeof selectedMod == 'undefined') {
				//pick up the parameters of the saved module parameter
				if (typeof pluginArgs.module != 'undefined') {
					//this orders the module parameter first, module related parameters second, other PluginModule parameters besides module last
					meta.params = $.extend(onlymod.params, tiki_module_params[pluginArgs.module].params, meta.params);
					//Use the module description
					meta.params.module.description = tiki_module_params[pluginArgs.module].description;
					//otherwise pick up the parameters of the first module option since that will be selected automatically
				} else {
					meta.params = $.extend(onlymod.params, tiki_module_params[meta.params.module.options[0].value].params, meta.params);
					meta.params.module.description = tiki_module_params[meta.params.module.options[0].value].description;
				}
				//user has selected another module while the form was open - pick up parameters for the selected module
			} else if (tiki_module_params[selectedMod] != null) {
				meta.params = $.extend(onlymod.params, tiki_module_params[selectedMod].params, meta.params);
				meta.params.module.description = tiki_module_params[selectedMod].description;
			}
		}

		var header = document.createElement('h3');
		header.innerHTML = meta.name;
		form.appendChild(header);

		var desc = document.createElement('div');
		desc.innerHTML = meta.description;
		if (meta.documentation && jqueryTiki.helpurl) {
			desc.innerHTML += ' <a href="' + jqueryTiki.helpurl + meta.documentation + '" target="tikihelp" class="tikihelp" tabIndex="-1" title="' + tr('Help') + '">' +
				'<span class="icon icon-help fa fa-question-circle fa-fw"></span>' +
				'</a>';

		}
		form.appendChild(desc);

		var table = document.createElement('table'), param;
		table.className = 'table';
		table.id = 'plugin_params';
		form.appendChild(table);

		for (param in meta.params) {
			if (meta.params[param].advanced) {
				var br = document.createElement('br');
				form.appendChild(br);

				var span_advanced_button = document.createElement('span');
				span_advanced_button.className = 'button';
				form.appendChild(span_advanced_button);

				var advanced_button = document.createElement('a');
				advanced_button.innerHTML = tr('Advanced options');
				advanced_button.onclick = function () {
					flip('plugin_params_advanced');
				};
				span_advanced_button.appendChild(advanced_button);

				var table_advanced = document.createElement('table');
				table_advanced.className = 'normal';
				table_advanced.style.display = 'none';
				table_advanced.id = 'plugin_params_advanced';
				form.appendChild(table_advanced);

				break;
			}
		}

		var potentiallyExtraPluginArgs = pluginArgs, extraArg;

		var rowNumber = 0;
		var rowNumberAdvanced = 0;
		for (param in meta.params) {
			if (typeof(meta.params[param]) != 'object' || meta.params[param].name == 'array') {
				continue;
			}

			var row;
			if (meta.params[param].advanced && !meta.params[param].required && typeof pluginArgs[param] === "undefined") {
				row = table_advanced.insertRow(rowNumberAdvanced++);
			} else {
				row = table.insertRow(rowNumber++);
			}
			var value = pluginArgs.length < 1 ? '' : pluginArgs[param];// for param like sort
			//for use with PluginModule to identify saved module parameter value
			var nsavedArgs = $.parseJSON($(form).find('input[name="args"][type="hidden"]').val());
			//last two parameters (selectedMod and savedArgs are only needed for PluginModule
			build_plugin_form_row(row, param, meta.params[param].name, meta.params[param].required, value, meta.params[param].description, meta.params[param], selectedMod, nsavedArgs);

			delete potentiallyExtraPluginArgs[param];
		}

		for (extraArg in potentiallyExtraPluginArgs) {
			if (extraArg === '') {
				// TODO HACK: See bug 2499 http://dev.tiki.org/tiki-view_tracker_item.php?itemId=2499
				continue;
			}

			row = table.insertRow(rowNumber++);
			build_plugin_form_row(row, extraArg, extraArg, 'extra', pluginArgs[extraArg], extraArg);
		}

		var bodyRow = table.insertRow(rowNumber++);
		var bodyCell = bodyRow.insertCell(0);
		var bodyField = document.createElement('textarea');
		bodyField.rows = '12';
		bodyField.className = 'form-control';
		var bodyDesc = document.createElement('div');

		if (meta.body) {
			bodyDesc.innerHTML = meta.body;
		} else {
			bodyRow.style.display = 'none';
		}
		bodyField.name = 'content';
		bodyField.value = bodyContent;

		bodyRow.className = 'formcolor';

		bodyCell.appendChild(bodyDesc);
		bodyCell.appendChild(bodyField);
		bodyCell.colSpan = '2';

		var submitRow = table.insertRow(rowNumber++);
		var submitCell = submitRow.insertCell(0);
		var submit = document.createElement('input');

		submit.type = 'submit';
		submitCell.colSpan = 2;
		submitCell.appendChild(submit);
		submitCell.className = 'submit';

		return form;
	}

//last two parameters (selectedMod and savedArgs are only needed for PluginModule
	function build_plugin_form_row(row, name, label_name, requiredOrSpecial, value, description, paramDef, selectedMod, savedArgs) {

		var label = row.insertCell(0);
		var field = row.insertCell(1);
		row.className = 'form-group';
		row.id = 'param_' + name;

		label.innerHTML = label_name;
		label.className = 'col-sm-3';
		field.className = 'col-sm-9';
		switch (requiredOrSpecial) {
			case (true):  // required flag
				label.style.fontWeight = 'bold';
				break;
			case ('extra') :
				label.style.fontStyle = 'italic';
		}

		var input, icon;
		if (paramDef && paramDef.options) {
			input = document.createElement('select');
			input.name = 'params[' + name + ']';
			input.className = 'form-control';
			for (var o = 0; o < paramDef.options.length; o++) {
				var opt = document.createElement('option');
				opt.value = paramDef.options[o].value;
				var opt_text = document.createTextNode(paramDef.options[o].text);
				opt.appendChild(opt_text);
				//either not PluginModule or user has not changed module selection, so use saved value
				if (typeof selectedMod == 'undefined') {
					if (value && opt.value == value) {
						opt.selected = true;
					}
				} else {
					//user changed module selection in PluginModule
					if (selectedMod == opt.value) {
						opt.selected = true;
					} else if (savedArgs.module == opt.value) {
						//use later to display saved module parameter value
						var savedtext = opt.innerHTML;
						opt.style.fontWeight = 'bold';
						opt.innerHTML = opt.innerHTML + '  -- ' + tr('saved value');
					}
				}
				input.appendChild(opt);
			}
		} else {
			input = document.createElement('input');
			input.className = 'form-control';
			input.type = 'text';
			input.name = 'params[' + name + ']';
			if (value) {
				input.value = value.replace(/\\"/g, '"');	// unescape quotes
			}
		}

		field.appendChild(input);
		if (paramDef && paramDef.type == 'image') {
			icon = document.createElement('img');
			icon.src = 'img/icons/image.png';
			input.id = paramDef.area ? paramDef.area : 'fgal_picker';
			icon.onclick = function () {
				openFgalsWindowArea(paramDef.area ? paramDef.area : 'fgal_picker');
			};
			field.appendChild(icon);
		} else if (paramDef && paramDef.type == 'fileId') {
			var help = document.createElement('span');
			input.id = paramDef.area ? paramDef.area : 'fgal_picker';
			help.onclick = function () {
				openFgalsWindowArea(paramDef.area ? paramDef.area : 'fgal_picker');
			};
			help.innerHTML = " <a href='#'>" + tr('Pick a file.') + "</a>";
			field.appendChild(help);
		} else if (paramDef && paramDef.type == 'kaltura') {
			input.id = paramDef.area;
			var img = $("<img />")
				.attr("src", paramDef.icon)
				.addClass("icon")
				.css("cursor", "pointer")
				.attr("title", tr("Upload or record media"))
			;
			$(field).append(
				$('<a/>')
					.attr('href', $.service('kaltura', 'upload'))
					.append(img)
					.click(function () {
						$(this).serviceDialog({
							title: tr("Upload or record media"),
							width: 710,
							height: 450,
							hideButtons: true,
							success: function (data) {
								if (data.entries) {
									input.value = data.entries[0];
								}
							}
						});
						return false;
					})
			);
		}

		if (description) {
			var desc = document.createElement('div');
			desc.style.fontSize = 'x-small';
			desc.innerHTML = description;
			field.appendChild(desc);
		}
		if (paramDef && paramDef.accepted) {
			$(field).append(
				$("<div>")
					.css("fontSize", "x-small")
					.html("<strong>" + tr("Accepted:") + "</strong><br>" + paramDef.accepted)[0]
			);
		}
		//in PluginModule, show saved nodule parameter value if user has changed selection
		//since the form changes to match the newly selected module, it's useful to show the
		//saved module parameter so the user can go back to it
		if (typeof savedtext != 'undefined') {
			var saved = document.createElement('div');
			saved.style.fontSize = 'x-small';
			saved.style.fontStyle = 'italic';
			saved.style.fontWeight = 'bold';
			saved.innerHTML = tr('Saved value:') + ' ' + savedtext;
			field.appendChild(saved);
		}

		if (paramDef && paramDef.filter) {
			if (paramDef.filter == "pagename") {
				$(input).tiki("autocomplete", "pagename");
			} else if (paramDef.filter == "groupname") {
				$(input).tiki("autocomplete", "groupname", {multiple: true, multipleSeparator: "|"});
			} else if (paramDef.filter == "username") {
				$(input).tiki("autocomplete", "username", {multiple: true, multipleSeparator: "|"});
			} else if (paramDef.filter == "date") {
				$(input).tiki("datepicker");
			}
		}

	}

	function openFgalsWindowArea(area) {
		openFgalsWindow('tiki-list_file_gallery.php?filegals_manager=' + area + '&galleryId=' + jqueryTiki.home_file_gallery, true);	// reload
	}

})(jQuery);
