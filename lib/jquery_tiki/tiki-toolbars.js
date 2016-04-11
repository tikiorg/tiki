/* $Id$

 ******************************
 * Functions for dialog tools *
 ******************************/

// shared

window.dialogData = [];
var $dialogDiv;

function displayDialog( ignored, list, area_id ) {
	var obj = {
		width: 210,
		bgiframe: true,
		autoOpen: false,
		zIndex: 10000
	};

	if (! $dialogDiv) {
		$dialogDiv = $('<div/>');
	} else {
		try {
			$dialogDiv.empty();
			if ($dialogDiv.data("ui-dialog")) {
				$dialogDiv.dialog('destroy');
			}
		} catch( e ) {
			// IE throws errors destroying a non-existant dialog
		}
	}

	$.each (window.dialogData[list], function (i, item) {
		if (item.indexOf("<") === 0) {	// form element
			$dialogDiv.append(item);
		} else if (item.indexOf("{") === 0) {
			try {
				obj = $.extend(obj, eval("("+item+")"));
			} catch (e) {
				alert(e.name + ' - ' + e.message);
			}
		} else if (item.length > 0) {
			obj.title = item;
		}
	});

	// Selection will be unavailable after context menu shows up - in IE, lock it now.
	if ( typeof CKEDITOR !== "undefined" && CKEDITOR.env.ie ) {
		var editor = CKEDITOR.instances[area_id];
		var selection = editor.getSelection();
		if (selection) {selection.lock();}
	} else if ($("#" + area_id)[0] && $("#" + area_id)[0].createTextRange) {	// save selection for IE
		storeTASelection(area_id);
	}

	$dialogDiv.dialog(obj).dialog('open');

	return false;
}

window.pickerData = [];
var pickerDiv = {};

function displayPicker( closeTo, list, area_id, isSheet, styleType ) {
	$('div.toolbars-picker').remove();	// simple toggle
	var $closeTo = $(closeTo);

	if ($closeTo.hasClass('toolbars-picker-open')) {
		$('.toolbars-picker-open').removeClass('toolbars-picker-open');
		return false;
	}

	$closeTo.addClass('toolbars-picker-open');
	var textarea = $('#' +  area_id);

	var coord = $closeTo.offset();
	coord.bottom = coord.top + $closeTo.height();

	pickerDiv = $('<div class="toolbars-picker ' + list + '" />')
		.css('left', coord.left + 'px')
		.css('top', (coord.bottom + 8) + 'px')
		.appendTo('body');

	var prepareLink = function(ins, disp ) {
		disp = $(disp);

		var link = $( '<a href="#" />' ).append(disp);

		if (disp.attr('reset') && isSheet) {
			var bgColor = $('div.tiki_sheet:first').css(styleType);
			var color = $('div.tiki_sheet:first').css(styleType == 'color' ? 'background-color' : 'color');
			disp
				.css('background-color', bgColor)
				.css('color', color);

			link
				.addClass('toolbars-picker-reset');
		}

		if ( isSheet ) {
			link
				.click(function() {
					var I = $(closeTo).attr('instance');
					I = parseInt( I ? I : 0, 10 );

					if (disp.attr('reset')) {
						$.sheet.instance[I].cellChangeStyle(styleType, '');
					} else {
						$.sheet.instance[I].cellChangeStyle(styleType, disp.css('background-color'));
					}

					$closeTo.click();
					return false;
				});
		} else {
			link.click(function() {
				insertAt(area_id, ins);

				var textarea = $('#' + area_id);
				// quick fix for Firefox 3.5 losing selection on changes to popup
				if (typeof textarea.selectionStart != 'undefined') {
					var tempSelectionStart = textarea.selectionStart;
					var tempSelectionEnd = textarea.selectionEnd;
				}

				$closeTo.click();

				// quick fix for Firefox 3.5 losing selection on changes to popup
				if (typeof textarea.selectionStart != 'undefined' && textarea.selectionStart != tempSelectionStart) {
					textarea.selectionStart = tempSelectionStart;
				}
				if (typeof textarea.selectionEnd != 'undefined' && textarea.selectionEnd != tempSelectionEnd) {
					textarea.selectionEnd = tempSelectionEnd;
				}

				return false;
			});
		}
		return link;
	};
	var chr, $a;
	for( var i in window.pickerData[list] ) {
		chr = window.pickerData[list][i];
		if (list === "specialchar") {
			chr = $("<span>" + chr + "</span>");
		}
		$a = prepareLink( i, chr );
		if ($a.length) {
			pickerDiv.append($a);
		}
	}

	return false;
}



function dialogSharedClose( area_id, dialog ) {
	$(dialog).dialog("close");
}

// Internal Link

function dialogInternalLinkOpen( area_id ) {
	var initial = $("#" + area_id).data('initial'), options;
	if (initial) {
		options = { initial: initial };
	} else {
		options = {};
	}
	$("#tbWLinkPage").tiki("autocomplete", "pagename", options);
	dialogSelectElement( area_id, '((', '))' ) ;
	var s = getTASelection($('#' + area_id)[0]);
	var m = /\((.*)\(([^\|]*)\|?([^\|]*)\|?([^\|]*)\|?\)\)/g.exec(s);
	if (m && m.length > 4) {
		if ($("#tbWLinkRel")) {
			$("#tbWLinkRel").val(m[1]);
		}
		$("#tbWLinkPage").val(m[2]);
		if (m[4]) {
			if ($("#tbWLinkAnchor")) {
				$("#tbWLinkAnchor").val(m[3]);
			}
			$("#tbWLinkDesc").val(m[4]);
		} else {
			$("#tbWLinkDesc").val(m[3]);
		}
	} else {
		$("#tbWLinkDesc").val(s);
		if ($("#tbWLinkAnchor")) {
			$("#tbWLinkAnchor").val("");
		}
	}
}

function dialogInternalLinkInsert( area_id, dialog ) {
	if (!$("#tbWLinkPage").val()) {
		alert(tr("Please enter a page name"));
		return;
	}
	var s = "(";
	if ($("#tbWLinkRel") && $("#tbWLinkRel").val()) {
		s += $("#tbWLinkRel").val();
	}
	s += "(" + $("#tbWLinkPage").val();
	if ($("#tbWLinkAnchor") && $("#tbWLinkAnchor").val()) {
		s += "|" + ($("#tbWLinkAnchor").val().indexOf("#") !== 0 ? "#" : "") + $("#tbWLinkAnchor").val();
	}
	if ($("#tbWLinkDesc").val()) {
		s += "|" + $("#tbWLinkDesc").val();
	}
	s += "))";
	insertAt(area_id, s, false, false, true);

	dialogSharedClose( area_id, dialog );

}

// Object Link

function dialogObjectLinkOpen( area_id ) {
	dialogSelectElement( area_id, '((', '))' ) ;
	var m, s = getTASelection($("#" + area_id)[0]), title = "", url = "";
	m = /\((.*)\(([^\|]*)\|?([^\|]*)\|?([^\|]*)\|?\)\)/.exec(s);
	if (m) {
		if (m.length > 4 && (m[1] || m[4])) {
			alert(tr("Development notice: Semantic link types and anchors not fully supported by this tool, use the Wiki Link"));
		} else if (m.length > 3) {
			url = m[2];
			title = m[3];
		}
	} else {
		dialogSelectElement( area_id, "[", "]" ) ;
		s = getTASelection($('#' + area_id)[0]);
		m = /\[([^\|]*)\|?([^\]]*)]/.exec(s);
		if (m) {
			url = m[1];
			title = m[2];
		}
	}
	if (!title) {
		title = s;
	}
	$("#tbOLinkDesc").val(title);
	$("#tbOLinkObject").val(url);

	$("#tbOLinkObjectSelector").object_selector();

	$("#tbOLinkObjectType").change(function () {
		$("#tbOLinkObjectSelector").object_selector('setfilter', 'type', $(this).val());
	});

}

function dialogObjectLinkInsert( area_id, dialog ) {
	var url = $("#tbOLinkObjectSelector").val(),
		linkDesc = $("#tbOLinkDesc"),
		type = $("#tbOLinkObjectType").val();

	if (!url) {
		alert(tr("Please select an object"));
		return;
	}
	if (type === "wiki page") {
		url = url.replace(/^wiki page\:/, "");

		if (linkDesc.val()) {
			url = "((" + url + "|" + linkDesc.val() + "))";
		} else {
			url = "((" + url + "))";
		}
	} else {
		url = url;	// TODO: deal with other object types
	}

	insertAt(area_id, url, false, false, true);

	dialogSharedClose( area_id, dialog );

}

// External Link

function dialogExternalLinkOpen( area_id ) {
	$("#tbWLinkPage").tiki("autocomplete", "pagename");
	dialogSelectElement( area_id, '[', ']' ) ;
	var s = getTASelection($('#' + area_id)[0]);
	var m = /\[([^\|]*)\|?([^\|]*)\|?([^\|]*)\]/g.exec(s);
	if (m && m.length > 3) {
		$("#tbLinkURL").val(m[1]);
		$("#tbLinkDesc").val(m[2]);
		if (m[3]) {
			if ($("#tbLinkNoCache") && m[3] == "nocache") {
				$("#tbLinkNoCache").prop("checked", "checked");
			} else {
				$("#tbLinkRel").val(m[3]);
			}
		} else {
			$("#tbWLinkDesc").val(m[3]);
		}
	} else {
		if (s.match(/(http|https|ftp)([^ ]+)/ig) == s) { // v simple URL match
			$("#tbLinkURL").val(s);
		} else {
			$("#tbLinkDesc").val(s);
		}
	}
	if (!$("#tbLinkURL").val()) {
		$("#tbLinkURL").val("http://");
	}
}

function dialogExternalLinkInsert(area_id, dialog) {

	var s = "[" + $("#tbLinkURL").val();
	if ($("#tbLinkDesc").val()) {
		s += "|" + $("#tbLinkDesc").val();
	}
	if ($("#tbLinkRel").val()) {
		s += "|" + $("#tbLinkRel").val();
	}
	if ($("#tbLinkNoCache") && $("#tbLinkNoCache").prop("checked")) {
		s += "|nocache";
	}
	s += "]";
	insertAt(area_id, s, false, false, true);

	dialogSharedClose( area_id, dialog );

}

// Table

function dialogTableOpen(area_id, dialog) {

	dialogSelectElement( area_id, '||', '||' ) ;

	dialog = $(dialog);

	var s = getTASelection($('#' + area_id)[0]);
	var m = /\|\|([\s\S]*?)\|\|/mg.exec(s);
	var vals = [], rows = 3, cols = 3, c, r, i, j;
	if (m) {
		m = m[1];
		m = m.split("\n");
		rows = 0;
		cols = 1;
		for (i = 0; i < m.length; i++) {
			var a2 = m[i].split("|");
			var a = [];
			for (j = 0; j < a2.length; j++) { // links can have | chars in
				if (a2[j].indexOf("[") > -1 && a2[j].indexOf("[[") == -1 && a2[j].indexOf("]") == -1) { // external link
					a[a.length] = a2[j];
					j++;
					var k = true;
					while (j < a2.length && k) {
						a[a.length - 1] += "|" + a2[j];
						if (a2[j].indexOf("]") > -1) { // closed
							k = false;
						} else {
							j++;
						}
					}
				} else if (a2[j].search(/\(\S*\(/) > -1 && a2[j].indexOf("))") == -1) {
					a[a.length] = a2[j];
					j++;
					k = true;
					while (j < a2.length && k) {
						a[a.length - 1] += "|" + a2[j];
						if (a2[j].indexOf("))") > -1) { // closed
							k = false;
						} else {
							j++;
						}
					}
				} else {
					a[a.length] = a2[j];
				}
			}
			vals[vals.length] = a;
			if (a.length > cols) {
				cols = a.length;
			}
			if (a.length) {
				rows++;
			}
		}
	}
	for (r = 1; r <= rows; r++) {
		for (c = 1; c <= cols; c++) {
			var v = "";
			if (vals.length) {
				if (vals[r - 1] && vals[r - 1][c - 1]) {
					v = vals[r - 1][c - 1];
				} else {
					v = "   ";
				}
			} else {
				v = "   "; //row " + r + ",col " + c + "";
			}
			var el = $("<input type=\"text\" id=\"tbTableR" + r + "C" + c + "\" class=\"ui-widget-content ui-corner-all\" size=\"10\" style=\"width:" + (90 / cols) + "%\" />")
				.val($.trim(v))
				.appendTo(dialog);
		}
		if (r == 1) {
			el = $("<img src=\"img/icons/add.png\" />")
				.click(function() {
					dialog.data("cols", dialog.data("cols") + 1);
					for (r = 1; r <= dialog.data("rows"); r++) {
						v = "";
						var el = $("<input type=\"text\" id=\"tbTableR" + r + "C" + dialog.data("cols") + "\" class=\"ui-widget-content ui-corner-all\" size=\"10\" style=\"width:" + (90 / dialog.data("cols")) + "%\" />")
							.val(v);
						$("#tbTableR" + r + "C" + (dialog.data("cols") - 1)).after(el);
					}
					dialog.find("input").width(90 / dialog.data("cols") + "%");
				})
				.appendTo(dialog);
		}
		dialog.append("<br />");
	}
	el = $("<img src=\"img/icons/add.png\" />")
		.click(function() {
			dialog.data("rows", dialog.data("rows") + 1);
			for (c = 1; c <= dialog.data("cols"); c++) {
				v = "";
				var el = $("<input type=\"text\" id=\"tbTableR" + dialog.data("rows") + "C" + c + "\" class=\"ui-widget-content ui-corner-all\" size=\"10\" value=\"" + v + "\" style=\"width:" + (90 / dialog.data("cols")) + "%\" />")
					.insertBefore(this);
			}
			$(this).before("<br />");
			dialog.dialog("option", "height", (dialog.data("rows") + 1) * 1.2 * $("#tbTableR1C1").height() + 130);
		})
		.appendTo(dialog);

	dialog
			.data('rows', rows)
			.data('cols', cols)
			.dialog("option", "width", (cols + 1) * 120 + 50)
			.dialog("option", "position", "center");

	$("#tbTableR1C1").focus();
}

function dialogTableInsert(area_id, dialog) {
	var s = "||", rows, cols, c, r, rows2 = 1, cols2 = 1;
	dialog = $(dialog);

	rows = dialog.data('rows') || 3;
	cols = dialog.data('cols') || 3;
	for (r = 1; r <= rows; r++) {
		for (c = 1; c <= cols; c++) {
			if ($.trim($("#tbTableR" + r + "C" + c).val())) {
				if (r > rows2) {
					rows2 = r;
				}
				if (c > cols2) {
					cols2 = c;
				}
			}
		}
	}
	for (r = 1; r <= rows2; r++) {
		for (c = 1; c <= cols2; c++) {
			var tableData = $("#tbTableR" + r + "C" + c).val();
			s += tableData;
			if (c < cols2) {
				s += (tableData ? '|' : ' | ');
			}
		}
		if (r < rows2) {
			s += "\n";
		}
	}
	s += "||";
	insertAt(area_id, s, false, false, true);

	dialogSharedClose( area_id, dialog );
}

// Find

function dialogFindOpen(area_id) {

	var s = getTASelection($('#' + area_id)[0]);
	$("#tbFindSearch").val(s).focus();
}

function dialogFindFind( area_id ) {
	var ta = $('#' + area_id);
	var findInput = $("#tbFindSearch").removeClass("ui-state-error");

	var $textareaEditor = syntaxHighlighter.get(ta); //codemirror functionality
	if ($textareaEditor) {
		syntaxHighlighter.find($textareaEditor, findInput.val());
	}
	else { //standard functionality
		var s, opt, str, re, p = 0, m;
		s = findInput.val();
		opt = "";
		if ($("#tbFindCase").prop("checked")) {
			opt += "i";
		}
		str = ta.val();
		re = new RegExp(s, opt);
		p = getCaretPos(ta[0]);
		if (p && p < str.length) {
			m = re.exec(str.substring(p));
		}
		else {
			p = 0;
		}
		if (!m) {
			m = re.exec(str);
			p = 0;
		}
		if (m) {
			setSelectionRange(ta[0], m.index + p, m.index + s.length + p);
		}
		else {
			findInput.addClass("ui-state-error");
		}
	}
}

// Replace

function dialogReplaceOpen(area_id) {

	var s = getTASelection($('#' + area_id)[0]);
	$("#tbReplaceSearch").val(s).focus();

}

function dialogReplaceReplace( area_id ) {
	var findInput = $("#tbReplaceSearch").removeClass("ui-state-error");
	var s = findInput.val();
	var r = $("#tbReplaceReplace").val();
	var opt = "";
	if ($("#tbReplaceAll").prop("checked")) {
		opt += "g";
	}
	if ($("#tbReplaceCase").prop("checked")) {
		opt += "i";
	}
	var ta = $('#' + area_id);
	var str = ta.val();
	var re = new RegExp(s,opt);

	var textareaEditor = syntaxHighlighter.get(ta); //codemirror functionality
	if (textareaEditor) {
		syntaxHighlighter.replace(textareaEditor, s, r);
	}
	else { //standard functionality
		ta.val(str.replace(re, r));
	}

}

