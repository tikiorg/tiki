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
				return $("<div>")
					.addClass("btn-toolbar")
					.append(
						$("<div>")
							.addClass("btn-group")
							.append(
								$("<a>")
									.addClass("btn btn-default btn-sm")
									.text("Source")
									.click(function () {
										toggleGui();
									})
									.attr("title", tr("Toggle source mode")),
								gui.pluginsSelector()
									.prepend($("<option>")
										.text(tr("Add"))
										.prop("selected", true)
									)
									.change(function () {
										$ul.append(
											gui.addPlugin({
												name: $(this).val(),
												args: [],
												plugins: []
											})
										);

									})
							)
					);
			};

			var $toolbar = buildToolbar();
			gui.$editor.append($toolbar);

			var $ul = $("<ul>")
				.appendTo(gui.$editor);

			var toggleGui = function () {
				if ($textarea.is(":visible")) {
					showGui();
				} else {
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

							for (var p in gui.current) {
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

			$textarea.before(gui.$editor);

			showGui();
		},

		/**
		 * Add the visual representation of a plugin
		 *
		 * @param plugin Object
		 * @return $li jQuery list item
		 */
		addPlugin: function (plugin) {

			var gui = this;

			var pluginName = plugin.name;

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
							gui.argsSelector(pluginName)
								.css({width: "auto", fontWeight: "normal !important", marginLeft: "1rem"})
								.prepend($("<option>")
									.text(tr("Add"))
									.prop("selected", true)
								)
								.change(function () {
									$li.append(
										gui.addArg($li.data("name"), $(this).val(), "")
									);

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

					var $input;
					var value = plugin.args[argName];

					if (pluginName === "filter" && argName === "type") {
						gui.objectType = value;
						$input = gui.objectTypesSelector(value);
					} else if (pluginName === "filter" && argName === "field") {
						if (value === "tracker_id") {
							gui.trackerId = plugin.args.content;
						}
						$input = gui.fieldsSelector(value);
					} else if ((pluginName === "display" && argName === "name") ||
						(pluginName === "column" && argName === "sort")) {

						$input = gui.fieldsSelector(value);

					} else if (gui.plugins[pluginName] && gui.plugins[pluginName][argName]) {
						if (typeof gui.plugins[pluginName][argName] === "object") {

							var arg = gui.plugins[pluginName][argName];

							if (arg && typeof arg.length === "undefined") {
								$input = gui.buildSelector(
									gui.arrayKeys(arg),
									value
								);
							} else {
								var type;
								if (arg.length && typeof arg[0] === "number") {
									type = "numeric";
								} else {
									type = "text";
								}

								$input = $("<input>")
									.attr("type", type)
									.addClass("arg-value form-control")
									.val(value);

								if (arg.length > 1) {
									$input.data("args", gui.arrayKeys(arg.slice(1)))
								}
							}
						} else if (typeof gui.plugins[pluginName][argName] === "number") {
							$input = $("<input>")
								.attr("type", "numeric")
								.addClass("arg-value form-control")
								.val(value);
						}
					} else {
						$input = $("<input>")
							.addClass("arg-value form-control")
							.val(value);
					}

					$argDiv = $("<div>")
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

					$argsDivs.append($argDiv);
				}
			}
			if ($argsDivs.children().length) {
				$li.append($argsDivs);
			}
			if (plugin.plugins.length) {
				var $ul = $("<ul>");
				for (var i in plugin.plugins) {
					if (plugin.plugins.hasOwnProperty(i)) {
						$ul.append(gui.addPlugin(plugin.plugins[i]));
					}
				}
				$ul.appendTo($li);
			}
			return $li;
		},

		addArg: function (plugin, arg, value) {
			if (this.plugins[plugin][arg]) {
				return this.buildArg(this.plugins[plugin][arg], value);
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
		}

	};

});

