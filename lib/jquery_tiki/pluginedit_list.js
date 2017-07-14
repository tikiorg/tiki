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
			gui.trackers = {};
			gui.objectType = null;
			gui.sortableOptions = {
				listType: "ul",
				maxLevels: 2,
				handle: "div:first",
				items: "li",
				disableNesting: "no-nesting"
			};

			// display GUI interface here
			gui.$editor = $("<div>")
				.addClass('plugin-list-editor clearfix');

			var buildMainToolbar = function () {
				var $tb = $("<div>")
					.addClass("btn-toolbar")
					.append(
						gui.buildToolBar(gui.plugins, "", function () {
							if ($(this).data("plugin")) {
								var params = [];
								if ($(this).data("value")) {
									params[$(this).data("value")] = "";
								}
								$ul.append(
									gui.addPlugin({
										name: $(this).data("plugin"),
										params: params,
										plugins: []
									})
								).nestedSortable(gui.sortableOptions);

								return false;
							}
							$(".dropdown-menu", $tb).hide();
						}),
						$("<div>")
							.addClass("btn-group")
							.append(
								$("<a>")
									.addClass("btn btn-default btn-sm btn-source")
									.append(
										$("<span>").getIcon("list"),
										" ",
										tr("Source")
									)
									.click(function () {
										toggleGui();
									})
									.attr("title", tr("Toggle source mode"))
							)
					);

				$(".dropdown-toggle", $tb).removeClass("btn-link btn-xs").addClass("btn-default btn-sm");

				return $tb;
			};

			var $toolbar = buildMainToolbar();
			gui.$editor.append($toolbar);

			var $ul = $("<ul>")
				.addClass('plugin-list-gui clearfix')
				.appendTo(gui.$editor);

			var toggleGui = function () {
				var $btn = gui.$editor.find(".btn-source");
				if ($textarea.is(":visible")) {
					showGui();

					$btn.empty().append($("<span>").getIcon("list"), tr("Source"));

					$(".dropdown-toggle", gui.$editor)
						.removeClass("disabled")
						.css("opacity", 1);
				} else {

					gui.saveToTextarea();

					$textarea.show();
					$ul.hide();

					$btn.empty().append($("<span>").getIcon("mouse-pointer"), tr("GUI"));

					$(".dropdown-toggle", gui.$editor)
						.addClass("disabled")
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
						try {
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
								gui.trackers = data.trackers;

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

								$toolbar.replaceWith(buildMainToolbar()).applyChosen();
								$textarea.tikiModal();
							}
						}
						catch (e) {
							console.log(e);
							$ul.empty().hide();
							$toolbar.hide();
							$textarea
								.tikiModal()
								.show()
								.showError(tr("List plugin syntax is currently not compatible with the GUI, so source editing only is available."));
						}
					}
				);

			};

			$ul.nestedSortable(gui.sortableOptions);

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

			var findPlugins = function ($element, parentPluginName) {
					parentPluginName = parentPluginName || "";

					var plugins = [];

					$element.find(".plugin").each(function () {
						var body,
							$plugin = $(this),
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

						if (pluginName === "wiki text") {
							body = $plugin.find("textarea").val();
						} else {
							body = "";
						}

						plugins.push({
							name: pluginName,
							params: params,
							body: body,
							plugins: findPlugins($plugin, pluginName),
							parent: parentPluginName
						});

					});

					return plugins;
				},
				getSyntax = function (plugin, indent, noLineFeeds) {
					indent = indent && !noLineFeeds ? indent : "";
					noLineFeeds = noLineFeeds || false;
					var name = plugin.plugins.length ? plugin.name.toUpperCase() : plugin.name;
					var output;

					if (name === "wiki text") {
						output = plugin.body;
					} else {
						output = indent + "{" + name + (plugin.plugins.length ? "(" : "");

						for (var param in plugin.params) {
							if (plugin.params.hasOwnProperty(param)) {
								output += " " + param + "=\"" + plugin.params[param] + "\"";
							}
						}
						output += (plugin.plugins.length ? ")" : "") + "}";
						if (!noLineFeeds) {
							output += "\n";
						}

						for (var i = 0; i < plugin.plugins.length; i++) {
							output += getSyntax(plugin.plugins[i], indent + "  ", name === "OUTPUT" && !plugin.params.template);
						}
						if (plugin.plugins.length) {
							output += "{" + name + "}\n"
						}
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
				for (var pa in parentPlugin.params) {
					if (parentPlugin.params.hasOwnProperty(pa) && gui.plugins[parentPlugin.name].params[pa].options) {
						var options = gui.plugins[parentPlugin.name].params[pa].options;
						if (options[parentPlugin.params[pa]] && options[parentPlugin.params[pa]].plugins && typeof options[parentPlugin.params[pa]].plugins[pluginName] !== "undefined") {
							paramsDef = options[parentPlugin.params[pa]].plugins[pluginName].params;
							break;
						}
					}
				}
			}
			if (!paramsDef) {
				if (typeof plugin === 'string') {			// from current plugins
					pluginName = tr("wiki text");
					plugin = plugin.replace(/^\s*[\r\n]/, "");	// strip off initial blank line

				} else if (pluginName === "wiki text") {	// from the toolbar
					plugin = "";
				} else {
					console.log("addPlugin error: " + pluginName + "->" + parentPlugin.name + " not found");
					return null;
				}
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
				);

			if (pluginName !== "output" && pluginName !== "format") {
				$li.addClass("no-nesting");
			} else {
				$li.append(
					$("<div>").addClass("btn-toolbar").append(
						gui.buildToolBar(gui.plugins, pluginName, function () {
							if ($(this).data("plugin")) {
								var params = [];
								if ($(this).data("value")) {
									params[$(this).data("value")] = "";
								}
								$ul.append(
									gui.addPlugin({
										name: $(this).data("plugin"),
										params: params,
										plugins: []
									})
								).nestedSortable(gui.sortableOptions);

								return false;
							}
							$(".dropdown-menu", $tb).hide();
						})
					)
				);
			}

			if (pluginName === "wiki text") {
				$li.append(
					$("<div>")
						.addClass("params clearfix")
						.append(
							$("<textarea>")
								.addClass("form-control")
								.val(typeof plugin === "string" ? plugin : "")
						)
				);
				return $li;
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

			if (plugin.plugins) {
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

			if (paramName === "empty") {	// dummy parameter for output etc
				return;
			}

			value = value || "";
			parentPlugin = parentPlugin || {};
			otherParams = otherParams || {};

			if (this.plugins[pluginName] && typeof this.plugins[pluginName].params[paramName] !== "undefined") {
				// simple case first, e.g. filter.content
				paramDef = this.plugins[pluginName].params[paramName];
			}
			if (!paramDef && parentPlugin && this.plugins[parentPlugin.name]) {
				// nested output/column etc plugins e.g. format.display.name
				if (!parentPlugin.params) {
					paramDef = this.plugins[parentPlugin.name].plugins[pluginName].params[paramName];
				} else {
					for (var pa in parentPlugin.params) {
						if (parentPlugin.params.hasOwnProperty(pa) && this.plugins[parentPlugin.name].params[pa].options) {
							if (typeof this.plugins[parentPlugin.name].params[pa].options[parentPlugin.params[pa]].plugins[pluginName].params[paramName] !== "undefined") {
								paramDef = this.plugins[parentPlugin.name].params[pa].options[parentPlugin.params[pa]].plugins[pluginName].params[paramName];
								break;
							}
						}
					}
				}
			}
			if (!paramDef && otherParams) {
				// for params dependent on others, like filter.lat with filter.distance or display.singleList with display.categorylist
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
			}

			if (!paramDef) {
				console.log("Warning: param " + paramName + " not found in plugin " + pluginName);
				paramDef = {};
			}

			if (paramDef.options) {			// select
				var list = gui.arrayKeys(paramDef.options);
				if (pluginName === "output" && paramName === "template" && value &&
					($.inArray(value, list) === -1 || value === "input")) {

					$input = $("<input>")
						.addClass("param-value form-control")
						.val(value === "input" ? "" : value);
				} else {
					$input = gui.buildSelector(
						list,
						value
					);
				}
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

			var clickFunction = function () {
				var parentParams = {};
				parentParams[$input.parent().find("> .param-name > span").text()] = $input.val();

				var $plug = gui.addPlugin({
						name: $(this).data("plugin"),
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

			$input.change(function () {	// add param specific toolbar
				if ($input.is("select") && paramDef.options && paramDef.options[$input.val()].plugins) {
					$input.parents(".plugin").find(".btn-toolbar").empty().append(
						gui.buildToolBar(paramDef.options[$input.val()].plugins, pluginName, function () {
							if ($(this).data("plugin")) {
								var params = [];
								if ($(this).data("value")) {
									params[$(this).data("value")] = "";
								}
								var parentPlugin = gui.plugins[pluginName];
								parentPlugin["name"] = pluginName;

								$(this).parents(".plugin").find("> ul").append(
									gui.addPlugin({
										name: $(this).data("plugin"),
										params: params,
										plugins: []
									}, parentPlugin)
								).nestedSortable(gui.sortableOptions);

								return false;
							}
							$(".dropdown-menu", $tb).hide();
						})
					);
				}
			});

			if (paramDef.params) {	// extra params

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

		},

		arrayKeys: function (object) {
			var list = [];

			for (var key in object) {
				if (object.hasOwnProperty(key)) {
					list.push(key);
				}
			}
			return list;
		},

		objectTypesSelector: function (value) {

			return this.buildSelector(
				this.arrayKeys(this.fields.object_types),
				value
			);
		},
		pluginsSelector: function (value) {

			return this.buildSelector(
				this.arrayKeys(this.plugins),
				value
			);
		},
		paramsSelector: function (value) {

			return this.buildSelector(
				this.arrayKeys(this.plugins[value]),
				''
			);
		},
		fieldsSelector: function (value) {
			var fields;
			if (this.objectType && this.fields.object_types.hasOwnProperty(this.objectType)) {
				fields = this.fields.object_types[this.objectType];
				if (this.trackerId && this.trackers[this.trackerId]) {
					var generalFields = [], myTrackerFields = [], otherTrackerFields = [];
					for (var i = 0; i < fields.length; i++) {
						if (fields[i].indexOf("tracker_field_") === 0) {
							if ($.inArray(fields[i], this.trackers[this.trackerId]) > -1) {
								myTrackerFields.push(fields[i]);
							} else {
								otherTrackerFields.push(fields[i]);
							}
						} else {
							generalFields.push(fields[i]);
						}
					}
					fields = generalFields;
					fields.push("--");
					fields = fields.concat(myTrackerFields);
					fields.push("--");
					fields = fields.concat(otherTrackerFields);
				}
			} else {
				for (var type in this.fields.object_types) {
					if (this.fields.object_types.hasOwnProperty(type)) {
						if ($.inArray(value, this.fields.object_types[type]) > -1) {
							this.objectType = type;
							fields = this.fields.object_types[type];
						}
					}
				}
			}

			if (fields) {
				fields.push("--");	// separator before globals if object specific fields are found
			} else {
				fields = [];
			}

			// always add globals
			for (i = 0; i < this.fields.global.length; i++) {
				if ($.inArray(this.fields.global[i], fields) < 0) {
					fields.push(this.fields.global[i]);
				}
			}

			if (this.fields.formatted) {
				fields = fields.concat(this.fields.formatted);
			}

			return this.buildSelector(fields, value);
		},
		buildSelector: function (list, value) {
			var $select = $("<select>")
				.addClass("form-control");

			for (var item in list) {
				if (list.hasOwnProperty(item)) {
					if (list[item] !== "--") {
						$select.append(
							$("<option>")
								.val(list[item])
								.text(tr(list[item]))
								.prop("selected", list[item] === value)
						);
					} else {
						$select.append("<option disabled>──────────</option>");
					}
				}
			}

			return $select;
		},
		buildDropDown: function (list, title, clickFunction, icon, plugin) {
			if (!list.length) {
				return "";
			}

			icon = icon || "plus";
			title = title || tr("Add");
			clickFunction = clickFunction || function () {};
			plugin = plugin || "";

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
									.data("plugin", plugin)
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
		},
		buildToolBar: function (plugins, parent, clickFunction) {
			if (!plugins) {
				return "";
			}

			parent = parent || "";
			clickFunction = clickFunction || function () {};

			var $div = $("<div>").addClass("btn-group");

			for (var plugin in plugins) {
				if (plugins.hasOwnProperty(plugin) && plugins[plugin].icon &&
					((! parent && ! plugins[plugin].parents) || (parent && $.inArray(parent, plugins[plugin].parents) > -1))
				) {
					$div.append(
						this.buildDropDown(this.arrayKeys(plugins[plugin].params), tr("Add") + " " + plugin, clickFunction, plugins[plugin].icon, plugin)
					);
				}
			}

			// $div.find(".btn").click(function () {
			// 	clickFunction.call(this);
			// });
			// function () {
			// 	if ($(this).data("value")) {
			// 		$li.find("> .params").append(
			// 			gui.buildParam($li.data("name"), $(this).data("value"), "", parentPlugin, {}, $li.find(".dropdown-toggle"))
			// 		);
			// 	}
			// }

			return $div;
		}

	};

});

