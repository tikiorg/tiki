// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$(document).ready(function () {

	var tocDirty = false,
		$options = $("#options");

	var setupAdminOptions = function () {

		var setDirty = function () {
				if ($(".save_menu.disabled").length) {
					$(".save_menu").removeClass("disabled").prop("disabled", false);
					tocDirty = true;
				}
			};

		$(".save_menu").addClass("disabled").prop("disabled", true);

		$options.find("li").each(function () {
			var parent = $(this).data("parent");
			if (parent) {
				$("#node_" + parent).find(".child-options:first").append(this);
			}
		});

		$options.nestedSortable({

			disableNesting: 'no-nest',
			forcePlaceholderSize: true,
			handle: 'div',
			helper: 'clone',
			items: 'li',
			maxLevels: 4,
			opacity: .6,
			tabSize: 20,
			tolerance: 'pointer',
			toleranceElement: '> div',
			placeholder: "ui-state-highlight",
			rootID: "root",
			// connectWith:"#page_list_container",

			stop: function (event, ui) {
				setDirty();
				$(this).removeClass("ui-state-active");
			},
			start: function (event, ui) {
				$(this).addClass("ui-state-active");
			}
		})
			.droppable({
				classes: {
					"ui-droppable-hover": "ui-state-active"
				},
				drop: function (event, ui) {
					// new option dropped?
					var $dropped = $(ui.draggable).clone();

					if ($dropped.hasClass("new")) {

						$dropped.find(".hidden").removeClass("hidden");
						$dropped.find(".field-label")
							.prop("readonly", false)
							.attr("placeholder", tr("Label"));

						$(ui.helper).hide();
						var element = document.elementFromPoint(event.clientX, event.clientY),
						$target = $(element).parents("li:first");

						if (! $target.length) {
							$target = $options.find("li:last");
						}
						$dropped
							.removeClass("new")
							.addClass("added")
							.show()
							.attr("id", "node_new_" + $(".added", $options).length);

						if ($target.length) {
							$target.after($dropped);
						} else {
							$options.append($dropped);
						}

						$dropped.find(".field-label").focus();
						setDirty();
					}
				}
			})
			.disableSelection();

		$("#node_new").draggable({
			connectToSortable:"#options",
			revert:"invalid",
			helper:"clone",
			start:function (event, ui) {
				$(ui.helper)
					.css({
						zIndex: 10000,
						width: "800px"
					})
					.find(".hidden").removeClass("hidden")
				;

				$options.mouseover(function () {
					$options.addClass("over");
				}).mouseout(function () {
					$options.removeClass("over");
				});
			},
			stop:function (event, ui) {
				$(ui.helper).css("z-index", "auto");

				$options.off("mouseover").off("mouseout");
			}
		}).disableSelection();

		$options.on("click", ".option-remove", function () {
			if (confirm(tr("Are you sure you want to remove this option?"))) {
				$(this).parents("li:first").remove();
				setDirty();
			}
			return false;
		});

		$options.find("input").change(function () {
			setDirty();
		});

		var $previewForm = $("form.preview"),
			$previewDiv = $(".preview-menu");

		$previewForm
			.off("submit")
			.submit(function () {
				$previewDiv.tikiModal(" ");
				var $this = $(this);
				$previewDiv.load(
					$this.attr("action"),
					$this.serialize(),
					function () {
						$previewDiv.tikiModal();
					}
				);
				return false;
			})
			.submit()
			.find("input,select").change(function () {
				$previewForm.submit();
			}).trigger("chosen:updated");

		$("#col1").tikiModal();


		$(".deploy_menu").click(function () {

			window.showModuleEditForm (null, {
				modName: "menu",
				modPos: $("#preview_position").val(),
				modOrd: 1,
				dropped: false,
				modId: 0,
				formVals: {
					assign_params: {
						id: $("input[name=menuId]").val(),
						type: $("#preview_type").val(),
						css: $("#preview_css:checked").length ? "y" : "n",
						bootstrap: $("#preview_bootstrap:checked").length ? "y" : "n"
					}
				}
			});



		/*	$.openModal({
				remote: "tiki-admin_modules.php?edit_module=true&assign_name=menu&preview=true&assign_params~id=" + $("input[name=menuId]").val() +
						"&assign_params~type=" + $("#preview_type").val() +
						"&assign_params~css=" + ($("#preview_css:checked").length ? "y" : "n") +
						"&assign_params~bootstrap=" + ($("#preview_bootstrap:checked").length ? "y" : "n")+
						"&assign_position=" + $("#preview_position").val(),
				open: function () {
					$("#module_params", this).tabs();
				}
			});*/

			return false;
		});
	};


	$(window).on("beforeunload", function () {
		if (tocDirty) {
			return tr("You have unsaved changes to your structure, are you sure you want to leave the page without saving?");
		}
	});

	$(".save_menu").click(function () {

		$options.tikiModal(tr("Saving..."));

		var option, options, $row, saveOptions = [], saveOption, position = 0,
			hasChildren = function (id, options) {
				for (var opt = 0;  opt < options.length; opt++) {
					if (options[opt].parent_id === id) {
						return true;
					}
				}
				return false;
			};

		options = $options.nestedSortable('toArray', {startDepthCount: 0, listType: "ol"});

		for (var i = 0; i < options.length; i++) {
			option = options[i];
			saveOption = {};
			if (option.item_id !== "root") {

				$row = $options.find("li#node_" + option.item_id);

				if (! $row.length) {
					$row = $options.find("li#node_new_" + option.item_id);
					saveOption.optionId = 0;
				} else {
					saveOption.optionId = option.item_id;
				}

				if ($row.find("input.samepos:checked").length === 0) {	// items with samepos checked are alternatives
					position++;
				}
				saveOption.position = position;

				saveOption.name = $row.find("input.field-label").val();
				saveOption.url = $row.find("input.field-url").val();

				switch (option.depth) {
					case 0:
					case 1:
						saveOption.type = 's';
						break;
					default:
						if (hasChildren(option.item_id, options)) {
							saveOption.type = option.depth - 1;
						} else if (saveOption.name && saveOption.name !== "---") {
							saveOption.type = 'o';
						} else {
							saveOption.type = '-';
						}
				}

				saveOptions.push(saveOption);
			}
		}

		$.post($.service("menu", "save"), {
			data: $.toJSON(saveOptions),
			menuId: $("input[name=menuId]").val(),
			ticket: $("input[name=ticket]").val(),
			daconfirm: $("input[name=daconfirm]").val()
		}, function (data) {

			$options.tikiModal();
			tocDirty = false;
			location.href = location.href;

		}, "json").always(function () {
			$options.tikiModal();
		});

		return false;
	});

	$("#col1").tikiModal(tr("Loading..."));

	setTimeout(function () {
		setupAdminOptions();
	}, 100);

});

