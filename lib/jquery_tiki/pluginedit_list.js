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

		/**
		 * Main GUI setup
		 *
		 * @param $textarea
		 * @param data
		 */
		setup: function ($textarea) {

			var gui = this,
				$textarea = $textarea;

			// display GUI interface here
			var $editor = $("<div>")
				.addClass('plugin-list-editor clearfix');

			var $toolbar = $("<div>")
				.addClass("textarea-toolbar nav-justified")
				.append(
					$("<a>")
						.addClass("toolbar btn btn-xs")
						.text("{source}")
						.click(function () {
							toggleGui();
						})
						.attr("title", tr("Toggle source mode")),
					$("<a>")
						.addClass("toolbar btn btn-xs gui-only")
						.html($("<span>").getIcon("plus"))
						.click(function () {
							newPlugin();
						})
						.attr("title", tr("Add new plugin"))
				);

			$editor.append($toolbar);

			var $ul = $("<ul>")
				.appendTo($editor);

			var newPlugin = function () {

			};

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
								$ul.find("select").trigger("chosen:updated");
							}
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

			$textarea.before($editor);

			showGui();
		},

		/**
		 * Add the visual representation of a plugin
		 *
		 * @param plugin Object
		 * @return $li jQuery list item
		 */
		addPlugin: function (plugin) {

			var pluginName = plugin.name,
				$li = $("<li>")
					.addClass("plugin")
					.data("name", pluginName)
					.append(
						$("<div>")
							.addClass("name margin-bottom-sm")
							.text(pluginName),
						" "
					);

			if (pluginName !== "output" && pluginName !== "format") {
				$li.addClass("no-nesting");
			}

			var $argsDivs = $("<div>")
				.addClass("args clearfix");

			for (var argName in plugin.args) {
				if (plugin.args.hasOwnProperty(argName)) {

					var $input;

					if (pluginName === "filter" && argName === "type") {
						this.objectType = plugin.args[argName];
						$input = this.objectTypesSelector(plugin.args[argName]);
					} else if (pluginName === "filter" && argName === "field") {
						if (plugin.args[argName] === "tracker_id") {
							this.trackerId = plugin.args.content;
						}
						$input = this.fieldsSelector(plugin.args[argName]);
					} else if ((pluginName === "display" && argName === "name") ||
						(pluginName === "column" && argName === "sort")) {

						$input = this.fieldsSelector(plugin.args[argName]);

						// TODO input types and other selects
						// } else if (this.plugins[pluginName] && this.plugins[pluginName][argName]) {
						// 	if (typeof this.plugins[pluginName][argName] === "object") {
						// 		$input = this.buildSelector(this.plugins[pluginName][argName], plugin.args[argName]);
						// 	} else if (typeof this.plugins[pluginName][argName] === "number") {
						// 		$input = $("<input>")
						// 			.attr("type", "numeric")
						// 			.addClass("arg-value form-control")
						// 			.val(plugin.args[argName]);
						// 	}
					} else {
						$input = $("<input>")
							.addClass("arg-value form-control")
							.val(plugin.args[argName]);
					}

					$argsDivs
						.append($("<div>")
							.addClass("col-sm-6 input-group input-group-sm pull-left")
							.css("paddingLeft", "1rem")
							.append(
								$("<span>")
									.addClass("arg-name input-group-addon")
									.css({minWidth: "8rem", textAlign: "right"})
									.text(argName),
								$input,
								$("<span>")
									.addClass("input-group-addon")
							)
						).appendTo($argsDivs);
				}
			}
			if ($argsDivs.children()) {
				$li.append($argsDivs);
			}
			if (plugin.plugins.length) {
				var $ul = $("<ul>");
				for (var i in plugin.plugins) {
					if (plugin.plugins.hasOwnProperty(i)) {
						$ul.append(this.addPlugin(plugin.plugins[i]));
					}
				}
				$ul.appendTo($li);
			}
			return $li;
		},
		objectTypesSelector: function (value) {
			var list = [];
			for (var type in  this.fields.object_types) {
				if (this.fields.object_types.hasOwnProperty(type)) {
					list.push(type);
				}
			}

			return this.buildSelector(list, value);
		},
		fieldsSelector: function (value) {
			var fields;
			if (this.objectType) {
				fields = this.fields.object_types[this.objectType];
			} else {
				fields = this.fields.globals;
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

