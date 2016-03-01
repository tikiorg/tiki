/* (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
 *
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 *
 * $Id$
 *
 * Include file for wikiplugin_annotation.php
 *
 */

(function ($) {

	$.fn.imageAnnotation = function (toCreate) {

		var $container = this,
			containerId = $container.attr("id"),
			$editor = $container.find(".editor"),
			$list = $container.parent().find(".list-box"),
			editable = $editor.length > 0 && toCreate.length === 0,	// default to not edit if there are existing annotations
			containerOffset = $container.offset(),
			dirty = false;

		$(".minimize", $editor).click(function () {
			endEdit(false);
			return false;
		});

		$(".delete", $editor).click(function (event) {
			handleDelete();
			event.preventDefault();
			return false;
		});

		$("#" + containerId + "-editable").change(function () {
			editable = $(this).prop("checked");
			return false;
		}).prop("checked", editable);

		// events for the container, click and mousemove

		$container.click(function (event) {

			if (editable) {

				if (selected[containerId]) {
					if (event.target.id == containerId)
						endEdit(false);
					return;
				}

				if (!active) {

					active = {
						obj: null,
						link: null,
						x1: event.pageX - containerOffset.left,
						x2: event.pageX - containerOffset.left,
						y1: event.pageY - containerOffset.top,
						y2: event.pageY - containerOffset.top,
						value: 'New annotation',
						target: ''
					};

					initAnnotation(active);
					positionize(active);

				} else {
					active.y2 = event.pageY - containerOffset.top;
					active.x2 = event.pageX - containerOffset.left;

					positionize(active);

					activateAnnotation(active);
					beginEdit(event, active);

					active = null;
					serializeAnnotations(annotations);
				}
			} else {

			}

		}).mousemove(function (event) {

			if (active == null)
				return;

			active.x2 = event.pageX - containerOffset.left;
			active.y2 = event.pageY - containerOffset.top;

			positionize(active);

		});

		// set up events on the popup form

		$("form", $container).submit(function () {

			endEdit(true);
			dirty = false;

			return false;

		}).find("input").keyup(function (event) {

			if (event.keyCode == event.DOM_VK_ESCAPE) {
				endEdit(false);
			}
		});

		// helper functions //
		//////////////////////

		var active = null;
		var selected = {};
		var annotations = {};
		var nextid = 0;

		var initAnnotation = function (o) {

				o.obj = $("<div />").addClass("annotation")[0];

				$container.prepend(o.obj);
			},

			activateAnnotation = function (o) {
				o.id = o.obj.id = "annotation-" + nextid++;
				annotations[o.id] = o;
				o.cid = containerId;

				var x1 = o.x1;
				var x2 = o.x2;
				var y1 = o.y1;
				var y2 = o.y2;

				o.x1 = Math.min(x1, x2);
				o.x2 = Math.max(x1, x2);
				o.y1 = Math.min(y1, y2);
				o.y2 = Math.max(y1, y2);

				if (o.target) {
					$(o.obj).attr("title", o.target);
				} else {
					$(o.obj).attr("title", "");
				}


				if ($list.length) {
					var $div = $("<div class='annotation-link' />");
					var $a = $("<a href='#'/>")	// link that goes below the image
						.text(o.value)
						.click(function (e) {
							if (editable) {
								beginEdit(e, o);
							} else {
								var offset = $(o.obj).offset();
								offset.left -= 20;
								offset.top -= 40;
								$('html, body').animate({
									scrollTop: offset.top,
									scrollLeft: offset.left
								});
							}
						})
						.mouseover(function (e) {
							highlight(o.id)
						})
						.mouseout(function (e) {
							if (!selected[containerId] || selected[containerId].obj.id != o.id) unhighlight(o.id)
						});

					$div.append($a).appendTo($list);
					o.link = $a[0];
				}

				o.obj.onmouseover = function (e) {
					highlight(o.id)
				};
				o.obj.onmouseout = function (e) {
					if (!selected[containerId] || selected[containerId].obj.id != o.id) unhighlight(o.id)
				};
				o.obj.onclick = function (e) {
					if (editable) {
						if (!active) beginEdit(e, o);
					} else {
						if (o.target) {
							location.href = o.target;
						}
					}
					return false;
				};
			},

			createAnnotation = function (o) {

				initAnnotation(o);
				activateAnnotation(o);
				positionize(o);
			},

			positionize = function (o) {
				o.obj.style.top = (Math.min(o.y1, o.y2)) + "px";
				o.obj.style.left = (Math.min(o.x1, o.x2)) + "px";
				o.obj.style.width = Math.abs(o.x1 - o.x2) + "px";
				o.obj.style.height = Math.abs(o.y1 - o.y2) + "px";
			},

			highlight = function (id) {
				var o = annotations[id];
				$(o.obj).addClass("selected");
			},

			unhighlight = function (id) {
				var o = annotations[id];
				$(o.obj).removeClass("selected");
			},

			beginEdit = function (event, o) {
				var $obj = $(o.obj),
					pos = $obj.position(),
					offset = $obj.offsetParent().offset();

				var left = pos.left,
					formLeft = offset.left + left + $editor.outerWidth() - window.scrollX;

				if (formLeft > window.innerWidth) {
					left += window.innerWidth - formLeft;
				}

				var top = pos.top + $obj.outerHeight(),
					formTop = offset.top + top + $editor.outerHeight() - window.scrollY;

				if (formTop > window.innerHeight) {
					top += window.innerHeight - formTop;
				}

				$("textarea[name=label]", $editor).val(o.value).select().focus();
				$("input[name=link]", $editor).val(o.target);

				$editor.css({
						top: top + "px",
						left: left + "px"
					})
					.show();

				selected[containerId] = o;
				highlight(o.id);
			},

			endEdit = function (store) {
				var o = selected[containerId];
				selected[containerId] = null;

				if (store) {
					if (o.value != $("textarea[name=label]", $editor).val() || o.target != $("input[name=link]", $editor).val()) {
						dirty = true;
					}

					o.value = $("textarea[name=label]", $editor).val();
					o.target = $("input[name=link]", $editor).val();
					if ($list.length) {
						o.link.innerHTML = o.value;
					}

					serializeAnnotations(annotations);
				}

				$editor.hide();

				unhighlight(o.id);

				return false;
			},

			handleDelete = function () {
				var o = selected[containerId];

				endEdit(false);

				o.obj.parentNode.removeChild(o.obj);
				if ($list.length) {
					o.link.parentNode.removeChild(o.link);
				}
				annotations[o.id] = null;
				selected[containerId] = null;

				serializeAnnotations(annotations);
			},

			serializeAnnotations = function (data) {
				var row, str = "";

				for (var k in data) {
					row = data[k];

					if (row == null || row.cid != containerId || !data.hasOwnProperty(k)) {
						continue;
					}

					str += "(" + (row.x1) + "," + (row.y1) + "),(" + (row.x2) + "," + (row.y2) + ") ";
					str += row.value + " [" + row.target + "]\n";
				}

				$("#" + containerId + "-content").val(str);
			};

		// finally initialise the annotations

		for (var k = 0; k < toCreate.length; ++k) {

			createAnnotation(toCreate[k]);

			serializeAnnotations(annotations);
		}

		$(window).on("beforeunload", function () {
			if (dirty) {
				return tr("you have unsaved changes to your annotations, are you sure you want to leave this page wihtout saving?");
			}
		});


	};


})(jQuery);
