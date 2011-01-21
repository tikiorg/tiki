// override saveSheet on jQuery.sheet for tiki specific export
$.sheet.saveSheet = function( url, redirect, fn ) {
	$( $.sheet.instance ).each( function( i ){
		if (typeof redirect === "undefined") { redirect = false; }
		// not set to 0 by default in case AJAX has caused a spurious one to appear

		this.evt.cellEditDone();
		
		var s = $.sheet.exportSheet(this);
		
		s = "s=" + $.toJSON(s)	// convert to JSON
			.replace(/\+/g,"%2B")	// replace +s with 0x2B hex value
			.replace(/\&/g,"%26");	// and replace &s with 0x26
			
		var setDirty = this.setDirty;
		$.ajax({
			url: url,
			type: "POST",
			data: s,
			//contentType: "application/json; charset=utf-8",
			dataType: "html",
			beforeSend: function() { window.showFeedback("Saving", 10000); }, 
			success: function(data) {
				setDirty(false);
				if (fn) {
					if($.isFunction(fn)) {
						fn();
					}
				}
				window.showFeedback(data, 2000, redirect);
			}
		});
	});
};

$.sheet.exportSheet = function(sheetInstance) {	// diverged from jQuery.sheet 1.1 / Tiki 6
	var sheetClone = sheetInstance.sheetDecorateRemove(true);
	var documents = []; //documents
	
	$(sheetClone).each(function() {
		var document = {}; //document
		document.metadata = {};
		document.data = {};
		
		//This preserves the width for postback, very important for styles
		//<DO_NOT_REMOVE>
		var table = $(this);
		var trFirst = table.find("tr:first");
		table.find("col").each(function(i){
			//because css isnt always set correctly, we need to check the width attribute as well
			//we also sanitize width string here
			var w = parseInt((jQuery(this).css("width") + "").replace("px",""), 10);
			var w2 = parseInt((jQuery(this).attr("width") + "").replace("px",""), 10);
			
			w = (w > w2 ? w : w2);
			
			trFirst.find("td").eq(i)
				.css("width", w + "px")
				.attr("width", w);
		});
		//</DO_NOT_REMOVE>
		
		var trs = table.find("tr");
		var rowCount = trs.length;
		var colCount = 0;
		var col_widths = "";
		
		trs.each(function(i) {
			var tr = $(this);
			var tds = tr.find("td");
			colCount = tds.length;
			
			document.data["r" + i] = {};
			
			var h = tr.css("height");
			document.data["r" + i].height = (h ? h : tr.attr("height"));
			
			tds.each(function(j) {
				var td = jQuery(this);
				var colSpan = td.attr("colspan");
				colSpan = (colSpan > 1 ? colSpan : null);

				document.data["r" + i]["c" + j] = {
					value: td.html(),
					formula: td.attr("formula"),
					stl: td.attr("style"),
					colspan: colSpan,
					cl: td.attr("class")
				};
				
				var sp = td.attr("colSpan");
				if (sp > 1) {
					doc.data["r" + i]["c" + j].width = sp;
				}
				sp = td.attr("rowSpan");	// TODO in .sheet
				if (sp > 1) {
					doc.data["r" + i]["c" + j].height = sp;
				}
			});
		});
			
		var id = table.attr("rel");
		id = id ? id.match(/sheetId(\d+)/) : null;
		id = id && id.length > 0 ? id[1] : 0;

		document.metadata = {
			"columns": parseInt(colCount, 10), //length is 1 based, index is 0 based
			"rows": parseInt(rowCount, 10), //length is 1 based, index is 0 based
			"title": table.attr("title"),
			"col_widths": {},
			"sheetId": id
		};
		
		table.find("colgroup").children().each(function(i) {
			document.metadata.col_widths["c" + i] = ($(this).attr("width") + "").replace("px", "");
		});
		
		documents.push(document); //append to documents
	});
	return documents;
};

inlineMenu = $(
	($("#sheetTools").html() + "")
		.replace(/sheetInstance/g, "$.sheet.instance[" + $.sheet.I() + "]")
);

inlineMenu.find(".qt-picker").attr("instance", $.sheet.I());

$.sheet.tikiOptions =  {
		urlMenu:"lib/jquery_tiki/jquery.sheet/menu.html",
		urlGet: "",
		buildSheet: true,
		autoFiller: true,
		inlineMenu: inlineMenu,
		colMargin: 20, //beefed up colMargin because the default size was too small for font
		height: $(window).height() * 0.8
};
	
$.sheet.manageState = function(tikiSheet, toggleEdit, parse) {
	if (toggleEdit) {
		$.get("tiki-view_sheets.php?sheetId=" + tikiSheet.id + "&sheetonly=y&parse=" + parse, function (o) {
			tikiSheet.sheetInstance.saveSheet = function(){};
			tikiSheet.sheetInstance.toggleState(o);
			$.sheet.manageState(tikiSheet);
		});
	} else {
		if (tikiSheet.sheetInstance.s.editable) {
			$("#jSheetControls").show();
			$("#saveState").show();
			$("#editState").hide();
		} else {
			$("#jSheetControls").hide();
			$("#saveState").hide();
			$("#editState").show();
		}
	}
};

window.toggleFullScreen = function(areaname) {
	tikiSheet.sheetInstance.toggleFullScreen();
};

window.showFeedback = function(message, delay, redirect) {
	if (typeof delay == "undefined") { delay = 5000; }
	if (typeof redirect == "undefined") { redirect = false; }
	$fbsp = $("#feedback span");
	$fbsp.html(message).show();
	window.setTimeout( function () { $fbsp.fadeOut("slow", function () { $fbsp.html("&nbsp;"); }); }, delay);
	// if called from save button via saveSheet:success, then exit edit page mode
	if (redirect) {
		window.setTimeout( function () { $fbsp.html("Redirecting...").show(); }, 1000);
		window.setTimeout( function () { window.location.replace(window.location.href.replace("parse=edit", "parse=y")); }, 1500);
	}
};