/**
 * (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
 *
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * $Id$
 *
 * Handles list plugin GUI
 */


// event for plugin list
$(document)
	.off('plugin_list_ready')
	.on('plugin_list_ready', function (params) {

		var $textarea = params.modal.find("textarea[name=content]");
		jqueryTiki.plugins.list.setup($textarea);

	});

$(document).ready(function () {

	jqueryTiki.plugins = jqueryTiki.plugins || {};

	jqueryTiki.plugins.list = {
		/**
		 * Local data
		 */
		current: {},
		fields: {},
		plugins: {},
		objectType: null,
		$editor: null,

		/**
		 * Main GUI setup
		 *
		 * @param $textarea
		 */
		setup: function ($textarea) {

			var gui = this;

			gui.current = {};
			gui.fields = {};
			gui.plugins = {};
			gui.objectType = null;

			// display GUI interface here
			gui.$editor = $("<div>")
				.addClass('plugin-list-editor clearfix');

			var buildToolbar = function () {
				var $tb = $("<div>")
					.addClass("btn-toolbar")
					.append(
						$("<div>")
							.addClass("btn-group")
							.append(
								$("<a>")
									.addClass("btn btn-default btn-sm")
									.append(
										$("<span>").getIcon("list"),
										" ",
										tr("Source")
									)
									.click(function () {
										toggleGui();
									})
									.attr("title", tr("Toggle source mode"))
							),
						gui.buildDropDown(gui.arrayKeys(gui.plugins), tr("Add plugin"), function () {
							if ($(this).data("value")) {
								$ul.append(
									gui.addPlugin({
										name: $(this).data("value"),
										params: [],
										plugins: []
									})
								);
								return false;
							}
							$(".dropdown-menu", $tb).hide();
						})
					);

				$(".dropdown-toggle", $tb).removeClass("btn-link btn-xs").addClass("btn-default btn-sm");

				return $tb;
			};

			var $toolbar = buildToolbar();
			gui.$editor.append($toolbar);

			var $ul = $("<ul>")
				.addClass('plugin-list-gui clearfix')
				.appendTo(gui.$editor);

			var toggleGui = function () {
				if ($textarea.is(":visible")) {
					showGui();
				} else {

					gui.saveToTextarea();

					$textarea.show();
					$ul.hide();
					$(".gui-only", $toolbar)
						.prop("disabled", true)
						.css("opacity", 0.3);
				}
			};

			var showGui = function () {
				$textarea.tikiModal(tr("Loading..."));
				$.getJSON(
					$.service("plugin", "list_edit"),
					{
						body: $textarea.val()
					},
					function (data) {
						if (data) {
							$textarea.hide();
							$ul.empty().show();
							$(".gui-only", $toolbar)
								.prop("disabled", false)
								.css("opacity", 1);

							gui.current = data.current;
							gui.plugins = data.plugins;
							gui.fields = data.fields;
							gui.fields.formatted = [];

							for (var p in gui.current) {	// check for format names and add to fields
								if (gui.current.hasOwnProperty(p)) {

									if (gui.current[p].name === "format" && typeof gui.current[p].params.name !== "undefined") {
										gui.fields.formatted.push(gui.current[p].params.name);
									}
								}
							}

							for (p in gui.current) {
								if (gui.current.hasOwnProperty(p)) {

									$ul.append(
										gui.addPlugin(gui.current[p])
									);
								}
							}

							if (jqueryTiki.chosen) {
								gui.$editor.find("select").trigger("chosen:updated");
							}

							$toolbar.replaceWith(buildToolbar()).applyChosen();
							$textarea.tikiModal();
						}
					}
				);

			};

			$ul.nestedSortable({
				listType: "ul",
				maxLevels: 2,
				handle: "div",
				items: "li",
				disableNesting: "no-nesting"
			});

			$textarea
				.before(gui.$editor)
				.parents("form:first")
				.submit(function () {
					return gui.saveToTextarea();
				});

			showGui();
		},

		/**
		 * Convert the current state of the GUI into plugin markup in the content textarea
		 */
		saveToTextarea: function () {
			var $ul = this.$editor.find(".plugin-list-gui"), currentPlugins, markup = "";

			var findPlugins = function ($element) {
					var plugins = [];

					$element.find(".plugin").each(function () {
						var $plugin = $(this),
							pluginName = $plugin.data("name"),
							params = {};

						if ($plugin.is(".done")) {
							return plugins;
						}

						$plugin.find("> .params > div").each(function () {
							var $param = $(this),
								paramName = $param.find(".param-name > span").text();

							params[paramName] = $param.find("> select, > input").val();
						});

						$plugin.addClass("done");

						plugins.push({
							name: pluginName,
							params: params,
							body: "",
							plugins: findPlugins($plugin)
						});

					});

					return plugins;
				},
				getSyntax = function (plugin, indent) {
					indent = indent || "";
					var name = plugin.plugins.length ? plugin.name.toUpperCase() : plugin.name;

					var output = indent + "{" + name + (plugin.plugins.length ? "(" : "");

					for (var param in plugin.params) {
						if (plugin.params.hasOwnProperty(param)) {
							output += " " + param + "=\"" + plugin.params[param] + "\"";
						}
					}
					output += (plugin.plugins.length ? ")" : "") + "}\n";

					for (var i = 0; i < plugin.plugins.length; i++) {
						output += getSyntax(plugin.plugins[i], indent + "  ");
					}
					if (plugin.plugins.length) {
						output += "{" + name + "}\n"
					}

					return output;
				};


			currentPlugins = findPlugins($ul);

			for (var i = 0; i < currentPlugins.length; i++) {
				markup += getSyntax(currentPlugins[i]);
			}

			this.$editor.parent().find("textarea[name=content]").val(markup);

			return true;
		},

		/**
		 * Add the visual representation of a plugin
		 *
		 * @param plugin Object
		 * @param parentPlugin Object
		 * @return $li jQuery list item
		 */
		addPlugin: function (plugin, parentPlugin) {
			parentPlugin = parentPlugin || {};

			var gui = this,
				pluginName = plugin.name,
				paramsDef = gui.plugins[pluginName] ? gui.plugins[pluginName].params : null;

			if (!paramsDef) {
				for (pa in parentPlugin.params) {
					if (parentPlugin.params.hasOwnProperty(pa) && gui.plugins[parentPlugin.name].params[pa]) {
						if (typeof gui.plugins[parentPlugin.name].params[pa].options[parentPlugin.params[pa]].plugins[pluginName]) {
							paramsDef = gui.plugins[parentPlugin.name].params[pa].options[parentPlugin.params[pa]].plugins[pluginName].params;
							break;
						}
					}
				}
			}
			if (!paramsDef) {
				console.log("addPlugin error: " + pluginName + "->" + parentPlugin.name + " not found");
				return null;
			}

			var $li = $("<li>")
				.addClass("plugin inline-form")
				.data("name", pluginName)
				.append(
					$("<a>")
						.addClass("pull-right close small")
						.html("&times;")
						.click(function () {
							$(this).parents("li:first").remove();
						}),
					$("<div>")
						.addClass("name margin-bottom-sm")
						.text(pluginName)
						.append(
							gui.buildDropDown(gui.arrayKeys(paramsDef), tr("Add parameter"), function () {
								if ($(this).data("value")) {
									$li.find("> .params").append(
										gui.buildParam($li.data("name"), $(this).data("value"), "", parentPlugin)
									);
								}
							})
						)
				);

			if (pluginName !== "output" && pluginName !== "format") {
				$li.addClass("no-nesting");
			}

			var $paramsDivs = $("<div>")
				.addClass("params clearfix");

			for (var paramName in plugin.params) {
				if (plugin.params.hasOwnProperty(paramName)) {

					var value = plugin.params[paramName];

					$paramsDivs.append(this.buildParam(pluginName, paramName, value, parentPlugin, plugin.params));
				}
			}
			$li.append($paramsDivs);

			if (plugin.plugins.length) {
				var $ul = $("<ul>");
				for (var i in plugin.plugins) {
					if (plugin.plugins.hasOwnProperty(i)) {
						$ul.append(gui.addPlugin(plugin.plugins[i], plugin));
					}
				}
				$ul.appendTo($li);
			}
			return $li;
		},

		/**
		 * Create the div representing the parameter to attach to the plugin li element
		 *
		 * @param pluginName String
		 * @param paramName String
		 * @param value String
		 * @param parentPlugin Object
		 * @param otherParams Object
		 * @return {*|jQuery}
		 */
		buildParam: function (pluginName, paramName, value, parentPlugin, otherParams) {
			var paramDef, gui = this, $input, $moreParamsDropDown = "";

			value = value || "";
			parentPlugin = parentPlugin || {};
			otherParams = otherParams || {};

			if (this.plugins[pluginName] && typeof this.plugins[pluginName].params[paramName] !== "undefined") {
				// simple case first
				paramDef = this.plugins[pluginName].params[paramName];

			} else if (parentPlugin && this.plugins[parentPlugin.name]) {
				// nested output/column etc plugins
				if (!parentPlugin.params) {
					paramDef = this.plugins[parentPlugin.name].plugins[pluginName].params[paramName];
				} else {
					for (var pa in parentPlugin.params) {
						if (parentPlugin.params.hasOwnProperty(pa)) {
							if (typeof this.plugins[parentPlugin.name].params[pa].options[parentPlugin.params[pa]].plugins[pluginName].params[paramName] !== "undefined") {
								paramDef = this.plugins[parentPlugin.name].params[pa].options[parentPlugin.params[pa]].plugins[pluginName].params[paramName];
								break;
							}
						}
					}
				}
			} else if (otherParams) {	// for params dependent on others, like field for content filter
				for (var otherParam in otherParams) {
					if (otherParams.hasOwnProperty(otherParam)) {
						if (otherParam !== paramName &&
							typeof this.plugins[pluginName].params[otherParam] !== "undefined" &&
							typeof this.plugins[pluginName].params[otherParam].params !== "undefined") {

							paramDef = this.plugins[pluginName].params[otherParam].params[paramName];
							break;

						}
					}
				}
			} else {
				console.log("Warning: param " + paramName + " not found in plugin " + pluginName);
			}

			if (typeof paramDef !== "undefined") {

				if (paramDef.options) {			// select
					$input = gui.buildSelector(
						gui.arrayKeys(paramDef.options),
						value
					);
				} else {
					switch (paramDef.type) {
						case "object_type":
							gui.objectType = value;
							$input = gui.objectTypesSelector(value);
							break;

						case "field":
							if (value === "tracker_id") {
								gui.trackerId = otherParams.content;
							}
							$input = gui.fieldsSelector(value);
							break;

						case "number":
							$input = $("<input>")
								.attr("type", "number")
								.addClass("param-value form-control")
								.val(value);
							break;

						default:	// text
							$input = $("<input>")
								.addClass("param-value form-control")
								.val(value);

					}
				}

				var plugins = paramDef.options && paramDef.options[value] && paramDef.options[value] ? paramDef.options[value].plugins : paramDef.plugins;
				if (plugins) {


					var clickFunction = function () {
						var parentParams = {};
						parentParams[$input.parent().find( "> .param-name > span").text()] = $input.val();

						var $plug = gui.addPlugin({
									name: $(this).data("value"),
									params: {},
									plugins: []
								},
								{
									name: pluginName,
									params: parentParams,
									plugins: []
								})
						;
						var $ul = $input.parents(".plugin > ul");
						if (!$ul.length) {
							$ul = $("<ul>").appendTo($input.parents(".plugin"));
						}
						$ul.append(
							$plug
						);
					};

					$moreParamsDropDown = gui.buildDropDown(
						gui.arrayKeys(plugins),
						tr("Other Plugins"),
						clickFunction
					);

					$input.change(function () {
						$moreParamsDropDown.replaceWith(
							gui.buildDropDown(
								gui.arrayKeys(paramDef.options[$input.val()].plugins),
								tr("Other Plugins"),
								clickFunction
							)
						);
					});

				} else if (paramDef.params) {	// extra params

					$input.data("params", gui.arrayKeys(paramDef.params));
					$moreParamsDropDown = this.buildDropDown(
						gui.arrayKeys(paramDef.params),
						tr("Other Parameters"),
						function () {
							var otherParams = gui.plugins[pluginName].params;
							$input.parents(".params").append(
								gui.buildParam(pluginName, $(this).data("value"), "", {}, otherParams)
							);
						}
					);
				}


				return $("<div>")
					.addClass("col-sm-6 input-group input-group-sm pull-left")
					.css("paddingLeft", "1rem")
					.append(
						$("<div>")
							.addClass("param-name input-group-addon")
							.css({minWidth: "6rem", textAlign: "right"})
							.append(
								$moreParamsDropDown,
								$("<span>").text(paramName)
							),
						$input,
						$("<a>")
							.addClass("input-group-addon")
							.html("&times;")
							.click(function () {
								$(this).parents(".input-group").remove();
							})
					);
			}
		}
		,

		arrayKeys: function (object) {
			var list = [];

			for (var key in object) {
				if (object.hasOwnProperty(key)) {
					list.push(key);
				}
			}
			return list;
		}

		,

		objectTypesSelector: function (value) {

			return this.buildSelector(
				this.arrayKeys(this.fields.object_types),
				value
			);
		}
		,
		pluginsSelector: function (value) {

			return this.buildSelector(
				this.arrayKeys(this.plugins),
				value
			);
		}
		,
		paramsSelector: function (value) {

			return this.buildSelector(
				this.arrayKeys(this.plugins[value]),
				''
			);
		}
		,
		fieldsSelector: function (value) {
			var fields;
			if (this.objectType) {
				fields = this.fields.object_types[this.objectType];
			} else {
				for (var type in this.fields.object_types) {
					if (this.fields.object_types.hasOwnProperty(type)) {
						if ($.inArray(value, this.fields.object_types[type]) > -1) {
							this.objectType = type;
							fields = this.fields.object_types[type];
						}
					}
				}
				if (!fields) {
					fields = this.fields.global;
				}
			}
			if (this.fields.formatted) {
				fields = fields.concat(this.fields.formatted);
			}

			return this.buildSelector(fields, value);
		}
		,
		buildSelector: function (list, value) {
			var $select = $("<select>")
				.addClass("form-control");

			for (var item in list) {
				if (list.hasOwnProperty(item)) {
					$select.append(
						$("<option>")
							.val(list[item])
							.text(tr(list[item]))
							.prop("selected", list[item] === value)
					);
				}
			}

			return $select;
		}
		,
		buildDropDown: function (list, title, clickFunction, icon) {
			icon = icon || "plus";
			title = title || tr("Add");
			clickFunction = clickFunction || function () {
				};

			var $div = $("<div>")
					.addClass("btn-group")
					.append(
						$("<a>")
							.addClass("btn btn-link dropdown-toggle btn-xs")
							.data("toggle", "dropdown")
							.attr("title", title)
							.attr("href", "#")
							.append(
								$("<span>").getIcon(icon)
							)
					),
				$ul = $("<ul>")
					.addClass("dropdown-menu")
					.appendTo($div)
					.append(
						$("<li>")
							.addClass("dropdown-title")
							.text(title),
						$("<li>")
							.addClass("divider")
					);

			for (var item in list) {
				if (list.hasOwnProperty(item)) {
					$ul.append(
						$("<li>")
							.append(
								$("<a>")
									.data("value", list[item])
									.text(tr(list[item]))
							)
					);
				}
			}

			$(".dropdown-toggle", $div).dropdown();
			$div.find(".dropdown-menu a").click(function () {
				clickFunction.call(this);
				$(".dropdown-toggle", $div).parent().removeClass('open');
			});
			$(".dropdown-menu", $div).mouseleave(function () {
				$(".dropdown-toggle", $div).parent().removeClass('open');
			});

			return $div;
		}

	}
	;

})
;

