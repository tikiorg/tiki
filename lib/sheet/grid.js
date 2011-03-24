// override saveSheet on jQuery.sheet for tiki specific export
inlineMenu = $(
	($("#sheetTools").html() + "")
		.replace(/sheetInstance/g, "$.sheet.instance[" + $.sheet.I() + "]")
);

inlineMenu.find(".qt-picker").attr("instance", $.sheet.I());

$.sheet = $.extend({
	saveSheet: function( tikiSheet, fn ) {
		var jS = tikiSheet.getSheet();
		jS.evt.cellEditDone();
			
		var s = $.sheet.exportSheet(jS);
			
		s = "s=" + $.toJSON(s)	// convert to JSON
			.replace(/\+/g,"%2B")	// replace +s with 0x2B hex value
			.replace(/\&/g,"%26");	// and replace &s with 0x26
				
		var setDirty = this.setDirty;
		var url = "";
			
		if (tikiSheet.id) {
			url = "tiki-view_sheets.php?sheetId=" + tikiSheet.id;
		} else {
			url = "tiki-view_sheets.php?type=" + tikiSheet.type + "&file=" + tikiSheet.file;
		}
			
		$.ajax({
			url: url,
			type: "POST",
			data: s,
			//contentType: "application/json; charset=utf-8",
			dataType: "html",
			beforeSend: function() { window.showFeedback("Saving", 10000); }, 
			success: function(data) {
				jS.setDirty(false);
				if (fn) {
					if($.isFunction(fn)) {
						fn();
					}
				}
				window.showFeedback(data, 2000);
			}
		});
	},
	exportSheet: function(jS) {	// diverged from jQuery.sheet 1.1 / Tiki 6
		var sheetClone = jS.sheetDecorateRemove(true);
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
	
					document.data["r" + i]["c" + j] = {
						value: td.html(),
						formula: td.attr("formula"),
						stl: td.attr("style"),
						cl: td.attr("class")
					};
					
					var sp = td.attr("colspan");
					if (sp) {
						if (sp > 1) {
							document.data["r" + i]["c" + j].width = sp;
						}
					}
					
					sp = td.attr("rowspan");	// TODO in .sheet
					if (sp) {
						if (sp > 1) {
							document.data["r" + i]["c" + j].height = sp;
						}
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
	},
	tikiOptions:  {
			urlMenu:"lib/jquery_tiki/jquery.sheet/menu.html",
			urlGet: "",
			buildSheet: true,
			autoFiller: true,
			inlineMenu: inlineMenu,
			colMargin: 20, //beefed up colMargin because the default size was too small for font
			height: $(window).height() * 0.8
	},
	manageState: function(tikiSheet, toggleEdit, parse) {
		if (toggleEdit) {
			var url = "";
			if (tikiSheet.id) {
				url = "tiki-view_sheets.php?sheetId=" + tikiSheet.id + "&sheetonly=y&parse=" + parse;
			} else {
				url = "tiki-view_sheets.php?type=" + tikiSheet.type + "&file=" + tikiSheet.file + "&sheetonly=y&parse=" + parse;
			}
			$.get(url, function (o) {
				tikiSheet.getSheet().saveSheet = function(){};
				tikiSheet.getSheet().toggleState(o);
				$.sheet.manageState(tikiSheet);
			});
		} else {
			if (tikiSheet.getSheet().s.editable) {
				$("#jSheetControls").show();
				$("#saveState").show();
				$("#editState").hide();
			} else {
				$("#jSheetControls").hide();
				$("#saveState").hide();
				$("#editState").show();
			}
		}
	},
	dualFullScreenHelper: function(parent, reset) {
		var tikiSheets =  $('div.tiki_sheet');
		$($.sheet.instance).each(function(i) {
			var jS = this;
			var tikiSheet = tikiSheets.eq(i);
			if (!reset) {
				$('<div id="tiki_sheet_container_fullscreen" style="left: 0px; top: 0px; z-index: 99999; background-color: white;" parentId="' + parent.attr('id') + '" />')
					.css('position', 'absolute')
					.width($(window).width())
					.height($(window).height())
					.html(parent.children())
					.insertAfter(parent);
				
				jS.sizeOriginal = {
					height: tikiSheet.height(),
					width: tikiSheet.width()
				};
				jS.s.width = tikiSheet.parent().width();
				jS.s.height = $(window).height();
				
				tikiSheet.siblings().each(function() {
					jS.s.height -= $(this).height();
				});
				$('#tiki_sheet_container').siblings().each(function() {
					jS.s.height -= $(this).height();
				});
			} else {
				var container = $('#tiki_sheet_container_fullscreen');
				var parentId = container.attr('parentId');
				$('#' + parentId).html(container.children());
				container.remove();
				
				jS.s.width = jS.sizeOriginal.width;
				jS.s.height = jS.sizeOriginal.height;
			}
			
			this.sheetSyncSize();
		});
	},
	setValuesForCompareSheet: function(value1, set1, value2, set2) {
		value1 = (value1 ? ":eq(" + value1 + ")" : ":first");
		value2 = (value2 ? ":eq(" + value2 + ")" : ":last");
		
		$("input.compareSheet1").filter(value1).click();
		$("input.compareSheet2").filter(value2).click();
		
		this.compareSheetClick(set1, set2);
	},
	compareSheetClick: function(set1, set2) {
		var checked1, checked2;
		$(set1).each(function() {
			if ($(this).is(':checked')) {
				checked1 = $(this);
			}
		});
		$(set2).each(function() {
			if ($(this).is(':checked')) {
				checked2 = $(this);
			}
		});
		
		$(set1).removeAttr('disabled');
		$(set2).removeAttr('disabled');
		
		function disable(obj1, objIndex, obj2, after) {
			for (var i = (after ? obj1.index(objIndex) : 0); i < (after ? obj1.length : obj1.index(objIndex) + 1); i++) {
				obj2.eq(i).attr('disabled', 'true');
			}
		}
		
		disable(set1, checked1, set2, true);
		disable(set2, checked2, set1);
	},
	compareSheetsSubmitClick: function(o) {
		var sheetId = $('#sheetId').val();
		
		var sheetReadDates = 'idx_0=' + $('input.compareSheet1:checked').val() + '&idx_1=' + $('input.compareSheet2:checked').val() + '&';
		window.location = "tiki-history_sheets.php?sheetId=" + sheetId + "&" + sheetReadDates;
	
		return false;
	},
	link: {
		setupUI: function(tikiSheet) {
			tikiSheet
				.unbind("switchSpreadsheet")
				.bind("switchSpreadsheet", function(e, i) {
					var jS = tikiSheet.getSheet();
					
					if (i < 0) {
						var switchSheetMsg = $("div.switchSheet").first().clone();
						var msg;
						jS.switchSpreadsheet(i);
						return;
						//below to be added in 7.1
						switchSheetMsg.find("input.newSpreadsheet").click(function() {
							switchSheetMsg.dialog("close").remove();
							jS.switchSpreadsheet(i);
						});
						switchSheetMsg.find("input.addSpreadsheet").click(function() {
							switchSheetMsg.dialog("close").remove();
							msg = $("<div />").load("tiki-sheets.php #role_main .tabcontent:first table", function() {
								msg.dialog({
									width: tikiSheet.width(),
									height: tikiSheet.height(),
									modal: true
								});
								msg.find("a.sheetLink").each(function() {
									$(this).click(function() {
										msg.dialog("close").remove();
										$.sheet.link.make("spreadsheet", $(this).attr("sheetId"));
										return false;
									});
								});
							});
						});
						switchSheetMsg.find("input.addTracker").click(function() {
							switchSheetMsg.dialog("close").remove();
							msg = $("<div />").load("tiki-list_trackers.php #role_main table", function() {
								msg.dialog({
									width: tikiSheet.width(),
									height: tikiSheet.height(),
									modal: true
								});
								msg.find("a.trackerLink").each(function() {
									$(this).click(function() {
										msg.dialog("close").remove();
										$.sheet.link.make("tracker", $(this).attr("trackerId"));
										return false;
									});
								});
							});
						});
						switchSheetMsg.find("input.addFile").click(function() {
							switchSheetMsg.dialog("close").remove();
							msg = $("<div />").load("tiki-list_file_gallery.php #fgalform", function() {
								msg.dialog({
									width: tikiSheet.width(),
									height: tikiSheet.height(),
									modal: true
								});
								msg.find("a.fileLink").each(function() {
									$(this).click(function() {
										msg.dialog("close").remove();
										$.sheet.link.make("file", $(this).attr("fileId"));
										return false;
									});
								});
							});
						
						});
						
						switchSheetMsg
							.dialog({
								title: switchSheetMsg.attr("title"),
								modal: true,
								resizable: false
							});
					} else {
						jS.switchSpreadsheet(i);
					}
				});
		},
		make: function(type, id) {
			switch (type) {
				case "spreadsheet":
					break;
				case "file":
					break;
				case "tracker":
					break;
			}
			
			alert("User wants to link a " + type + " with id of " + id);
		}
	}
}, $.sheet);

window.toggleFullScreen = function(areaname) {
	tikiSheet.getSheet().toggleFullScreen();
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