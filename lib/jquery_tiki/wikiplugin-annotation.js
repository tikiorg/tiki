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
			$editor = $container.find(".editor");

		$(".minimize", $editor).click(function () {
			endEdit(cid, false);
			return false;
		});

		$(".delete", $editor).click(function (event) {
			handleDelete(cid);
			event.preventDefault();
			return false;
		});

		// events for the container, click and mousemove

		$container.click(function (event) {


			if (selected[cid]) {
				if (event.target.id == cid)
					endEdit(cid, false);
				return;
			}

			if (!active) {

				active = {
					obj: null,
					link: null,
					x1: event.offsetX,
					x2: event.offsetX,
					y1: event.offsetY,
					y2: event.offsetY,
					value: 'New annotation',
					target: ''
				};

				initAnnotation(active, cid);
				positionize(active, cid);

			} else {
				active.y2 = event.offsetY;
				active.x2 = event.offsetX;

				positionize(active, cid);

				activateAnnotation(active, cid);
				beginEdit(event, active, cid);

				active = null;
				serializeAnnotations(annotations, cid);
			}

		}).mousemove(function (event) {

			if (active == null)
				return;

			active.x2 = event.offsetX;
			active.y2 = event.offsetY;

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
				o.obj = document.createElement('div');
				o.obj.style.borderStyle = 'solid';
				o.obj.style.borderWidth = '2px';
				o.obj.style.borderColor = 'red';
				o.obj.style.position = 'absolute';
				getc(cid).insertBefore(o.obj, getc(cid).firstChild);
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

				var $div = $("<div class='annotation-link' />");

				var $a = $("<a href='#'/>")	// link that goes below the image
					.text(o.value)
					.click(function (e) {
						beginEdit(e, o, cid);
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
					if (!active) beginEdit(e, o, cid);
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

				console.log(o.x1, o.x2, o.y1, o.y2);
			},

			highlight = function (id, cid) {
				var o = annotations[id];
				o.obj.style.borderColor = 'green';
			},

			unhighlight = function (id, cid) {
				var o = annotations[id];
				o.obj.style.borderColor = 'red';
			},

			beginEdit = function (event, o, cid) {
				var pos = $(o.obj).position();

				var left = pos.left;
				if (left + $editor.width() > window.innerWidth)
					left += window.innerWidth - left - $editor.width();

				var top = pos.top + $(o.obj).outerHeight();

				if (top + $editor.height() > window.innerHeight)
					top += window.innerHeight - top - $editor.height();

				$editor.css({
						top: top + "px",
						left: left + "px"
					})
					.show();

				$("input[name=label]", $editor).val(o.value).select().focus();
				$("input[name=link]", $editor).val(o.target);

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
