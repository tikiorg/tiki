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
	.on('plugin_list_ready', function (args) {

		var $textarea = args.modal.find("textarea[name=content]");
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
										args: [],
										plugins: []
									})
								);
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

									if (gui.current[p].name === "format" && typeof gui.current[p].args.name !== "undefined") {
										gui.fields.formatted.push(gui.current[p].args.name);
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
							args = {};

						if ($plugin.is(".done")) {
							return plugins;
						}

						$plugin.find("> .args > div").each(function () {
							var $arg = $(this),
								argName = $arg.find(".arg-name").text();

							args[argName] = $arg.find("> select, > input").val();
						});

						$plugin.addClass("done");

						plugins.push({
							name: pluginName,
							args: args,
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

					for (var arg in plugin.args) {
						if (plugin.args.hasOwnProperty(arg)) {
							output += " " + arg + "=\"" + plugin.args[arg] + "\"";
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
				$li = $("<li>")
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
								gui.buildDropDown(gui.arrayKeys(gui.plugins[pluginName]), tr("Add argument"), function () {
									if ($(this).data("value")) {
										$li.find(".args").append(
											gui.buildArg($li.data("name"), $(this).data("value"), "", plugin)
										);
										$(".dropdown-menu", $li).hide();
									}
								})
							)
					);

			if (pluginName !== "output" && pluginName !== "format") {
				$li.addClass("no-nesting");
			}

			var $argsDivs = $("<div>")
				.addClass("args clearfix");

			for (var argName in plugin.args) {
				if (plugin.args.hasOwnProperty(argName)) {

					var value = plugin.args[argName];

					$argsDivs.append(this.buildArg(pluginName, argName, value, parentPlugin, plugin.args));
				}
			}
			$li.append($argsDivs);

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
		 * Create the div representing the argument to attach to the plugin li element
		 *
		 * @param pluginName String
		 * @param argName String
		 * @param value String
		 * @param parentPlugin Object
		 * @param otherArgs Object
		 * @return {*|jQuery}
		 */
		buildArg: function (pluginName, argName, value, parentPlugin, otherArgs) {
			var argDef, gui = this, $input;

			value = value || "";
			parentPlugin = parentPlugin || {};
			otherArgs = otherArgs || {};

			if (this.plugins[pluginName] && typeof this.plugins[pluginName][argName] !== "undefined") {
				// simple case first
				argDef = this.plugins[pluginName][argName];

			} else if (parentPlugin && this.plugins[parentPlugin.name]) {
				// nested output/column etc plugins
				if (!parentPlugin.args) {
					argDef = this.plugins[parentPlugin.name][pluginName][argName];
				} else {
					for (var pa in parentPlugin.args) {
						if (parentPlugin.args.hasOwnProperty(pa)) {
							if (typeof this.plugins[parentPlugin.name][pa][parentPlugin.args[pa]][pluginName][argName] !== "undefined") {
								argDef = this.plugins[parentPlugin.name][pa][parentPlugin.args[pa]][pluginName][argName];
								break;
							}
						}
					}
				}
			} else if (otherArgs) {	// for args dependent on others, like field for content filter
				for (var otherArg in otherArgs) {
					if (otherArgs.hasOwnProperty(otherArg)) {
						if (otherArg !== argName && typeof this.plugins[pluginName][otherArg] !== "undefined" &&
							this.plugins[pluginName][otherArg].length > 1 &&
							typeof this.plugins[pluginName][otherArg][1][argName] !== "undefined") {

							argDef = this.plugins[pluginName][otherArg][1][argName];

						}
					}
				}
			} else {
				console.log("Warning: arg " + argName + " not found in plugin " + pluginName);
			}

			if (typeof argDef !== "undefined") {


				if (typeof argDef === "object") {	// array or object

					if (argDef && typeof argDef.length === "undefined") {	// object
						$input = gui.buildSelector(
							gui.arrayKeys(argDef),
							value
						);
					} else {												// array
						var type;
						if (argDef.length && typeof argDef[0] === "number") {
							type = "number";
						} else {
							type = "text";
						}

						$input = $("<input>")
							.attr("type", type)
							.addClass("arg-value form-control")
							.val(value);

						if (argDef.length > 1) {
							$input.data("args", gui.arrayKeys(argDef.slice(1)))
						}
					}
				} else if (typeof argDef === "number") {
					$input = $("<input>")
						.attr("type", "number")
						.addClass("arg-value form-control")
						.val(value);
				} else if (argDef === "object_type") {
					gui.objectType = value;
					$input = gui.objectTypesSelector(value);
				} else if (argDef === "field") {
					if (value === "tracker_id") {
						gui.trackerId = plugin.args.content;
					}
					$input = gui.fieldsSelector(value);
				} else {
					$input = $("<input>")
						.addClass("arg-value form-control")
						.val(value);
				}

				return $("<div>")
					.addClass("col-sm-6 input-group input-group-sm pull-left")
					.css("paddingLeft", "1rem")
					.append(
						$("<span>")
							.addClass("arg-name input-group-addon")
							.css({minWidth: "6rem", textAlign: "right"})
							.text(argName),
						$input,
						$("<a>")
							.addClass("input-group-addon")
							.html("&times;")
							.click(function () {
								$(this).parents(".input-group").remove();
							})
					);
			}
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
		argsSelector: function (value) {

			return this.buildSelector(
				this.arrayKeys(this.plugins[value]),
				''
			);
		},
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
		},
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
		},
		buildDropDown: function (list, title, clickFunction, icon) {
			icon = icon || "plus";
			title = title || tr("Add");
			clickFunction = clickFunction || function () {
					$(".dropdown-menu", $div).hide();
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
			$div.find(".dropdown-menu a").click(clickFunction);
			$(".dropdown-menu", $div).mouseleave(function () {
				$(this).hide();
			});

			return $div;
		}

	};

});

