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
			cid = $container.attr("id"),
			$editor = $container.find(".editor"),
			editable = $editor.length > 0,
			containerOffset = $container.offset();

		$(".minimize", $editor).click(function () {
			endEdit(cid, false);
			return false;
		});

		$(".delete", $editor).click(function (event) {
			handleDelete(cid);
			event.preventDefault();
			return false;
		});

		$("#" + cid + "-editable").change(function () {
			editable = $(this).prop("checked");
			return false;
		}).prop("checked", editable);

		// events for the container, click and mousemove

		$container.click(function (event) {

			if (editable) {

				if (selected[cid]) {
					if (event.target.id == cid)
						endEdit(cid, false);
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

					initAnnotation(active, cid);
					positionize(active, cid);

				} else {
					active.y2 = event.pageY - containerOffset.top;
					active.x2 = event.pageX - containerOffset.left;

					positionize(active, cid);

					activateAnnotation(active, cid);
					beginEdit(event, active, cid);

					active = null;
					serializeAnnotations(annotations, cid);
				}
			} else {

			}

		}).mousemove(function (event) {

			if (active == null)
				return;

			active.x2 = event.pageX - containerOffset.left;
			active.y2 = event.pageY - containerOffset.top;

			positionize(active, cid);

		});

		// set up events on the popup form

		$("form", $container).submit(function () {

			endEdit(cid, true);
			return false;

		}).find("input").keyup(function (event) {

			if (event.keyCode == event.DOM_VK_ESCAPE) {
				endEdit(cid, false);
			}
		});

		// helper functions //
		//////////////////////

		var active = null;
		var selected = {};
		var containers = {};
		var annotations = {};
		var nextid = 0;

		var getc = function (cid) {
				if (containers[cid] == null)
					containers[cid] = document.getElementById(cid);

				return containers[cid];
			},

			initAnnotation = function (o, cid) {

				o.obj = $("<div />").addClass("annotation")[0];

				$container.prepend(o.obj);
			},

			activateAnnotation = function (o, cid) {
				o.id = o.obj.id = "annotation-" + nextid++;
				annotations[o.id] = o;
				o.cid = cid;

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

				var $div = $("<div class='annotation-link' />");

				var $a = $("<a href='#'/>")	// link that goes below the image
					.text(o.value)
					.click(function (e) {
						if (editable) {
							beginEdit(e, o, cid);
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
						highlight(o.id, cid)
					})
					.mouseout(function (e) {
						if (!selected[cid] || selected[cid].obj.id != o.id) unhighlight(o.id, cid)
					});

				$div.append($a).appendTo($container.parent());

				o.obj.onmouseover = function (e) {
					highlight(o.id, cid)
				};
				o.obj.onmouseout = function (e) {
					if (!selected[cid] || selected[cid].obj.id != o.id) unhighlight(o.id, cid)
				};
				o.obj.onclick = function (e) {
					if (editable) {
						if (!active) beginEdit(e, o, cid);
					} else {
						if (o.target) {
							location.replace(o.target);
						}
					}
					return false;
				};

				o.link = $a[0];
			},

			createAnnotation = function (o, cid) {

				initAnnotation(o, cid);
				activateAnnotation(o, cid);
				positionize(o, cid);
			},

			positionize = function (o, cid) {
				o.obj.style.top = (Math.min(o.y1, o.y2)) + "px";
				o.obj.style.left = (Math.min(o.x1, o.x2)) + "px";
				o.obj.style.width = Math.abs(o.x1 - o.x2) + "px";
				o.obj.style.height = Math.abs(o.y1 - o.y2) + "px";

				//console.log(o.x1, o.x2, o.y1, o.y2);
			},

			highlight = function (id, cid) {
				var o = annotations[id];
				$(o.obj).addClass("selected");
			},

			unhighlight = function (id, cid) {
				var o = annotations[id];
				$(o.obj).removeClass("selected");
			},

			beginEdit = function (event, o, cid) {
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

				$("input[name=label]", $editor).val(o.value).select().focus();
				$("input[name=link]", $editor).val(o.target);

				$editor.css({
						top: top + "px",
						left: left + "px"
					})
					.show();

				selected[cid] = o;
				highlight(o.id, cid);
			},

			endEdit = function (cid, store) {
				var o = selected[cid];
				selected[cid] = null;

				if (store) {
					o.value = $("input[name=label]", $editor).val();
					o.target = $("input[name=link]", $editor).val();
					o.link.innerHTML = o.value;

					serializeAnnotations(annotations, cid);
				}

				$editor.hide();

				unhighlight(o.id, cid);

				return false;
			},

			handleDelete = function (cid) {
				var o = selected[cid];

				endEdit(cid, false);

				o.obj.parentNode.removeChild(o.obj);
				o.link.parentNode.removeChild(o.link);
				annotations[o.id] = null;
				selected[cid] = null;

				serializeAnnotations(annotations, cid);
			},

			serializeAnnotations = function (data, cid) {
				var row, str = "";

				for (var k in data) {
					row = data[k];

					if (row == null || row.cid != cid || !data.hasOwnProperty(k)) {
						continue;
					}

					str += "(" + (row.x1) + "," + (row.y1) + "),(" + (row.x2) + "," + (row.y2) + ") ";
					str += row.value + " [" + row.target + "]\n";
				}

				$("#" + cid + "-content").val(str);
			};

		// finally initialise the annotations

		for (var k = 0; k < toCreate.length; ++k) {

			createAnnotation(toCreate[k], cid);

			serializeAnnotations(annotations, cid);
		}



	};


})(jQuery);
