// override saveSheet on jQuery.sheet for tiki specific export
inlineMenu = $(
	($("#sheetTools").html() + "")
		.replace(/sheetInstance/g, "$.sheet.instance[" + $.sheet.I() + "]")
);

inlineMenu.find(".qt-picker").attr("instance", $.sheet.I());

var jST = $.sheet.tikiSheet;

$.sheet = $.extend({
	makeSmall: function() {
		var jS = jST.getSheet();
		if (jS.obj.fullScreen().is(':visible')) {
			jS.toggleFullScreen();
		}
	},
	saveSheet: function( fn ) {
		var jS = jST.getSheet();
		$.sheet.makeSmall();
		
		jS.evt.cellEditDone();
			
		var sheetData = $.sheet.exportSheet(jS);
				
		var url = "";
		if (jST.id) {
			url = "tiki-view_sheets.php?sheetId=" + jST.id;
		} else {
			url = "tiki-view_sheets.php?type=" + jST.type + "&file=" + jST.file;
		}
		
		jST.modal(tr("Saving"));
		
		$.post(url, {
			s: $.toJSON(sheetData)
		}, function(data) {
			jS.setDirty(false);
			if (fn) {
				if($.isFunction(fn)) {
					fn();
				}
			}
			jST.modal();
		});
	},
	deleteSheet: function() {
		var jS = jST.getSheet();
		var id = jS.obj.sheet().data('id');
		var type = jS.obj.sheet().data('type');
		
		if (type == "tracker") {
			$.post('tiki-view_sheets.php', {
				sheetId: jST.id,
				trackerId: id,
				relate: 'remove'
			}, function() {
				jS.deleteSheet();
			});
		} else if (type == "file") {
			$.post('tiki-view_sheets.php', {
				sheetId: jST.id,
				fileId: id,
				relate: 'remove'
			}, function() {
				jS.deleteSheet();
			});
		} else if (type == "sheet") {
			$.post('tiki-view_sheets.php', {
				sheetId: jST.id,
				childSheetId: id,
				relate: 'remove'
			}, function() {
				jS.deleteSheet();
			});
		} else {
			jS.deleteSheet();
		}
	},
	exportSheet: function() {
		var jS = jST.getSheet();
		var sheetClone = jS.sheetDecorateRemove(true);
		var documents = []; //documents
		
		$(sheetClone).each(function(i) {
			var table = $(this);
			var type = table.data('type');
			var id = table.data("id");
			
			if (type == "sheet" || !type) { //standard tiki sheet
				var document = {}; //document
				document.metadata = {};
				document.data = {};
				
				//This preserves the width for postback, very important for styles
				//<DO_NOT_REMOVE>
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
				
				var title = table.attr("title");
				
				document.metadata = {
					"columns": parseInt(colCount, 10), //length is 1 based, index is 0 based
					"rows": parseInt(rowCount, 10), //length is 1 based, index is 0 based
					"title": (title ? title : tr("Spreadsheet ") + i),
					"col_widths": {},
					"sheetId": id
				};

				table.find("colgroup").children().each(function(i) {
					document.metadata.col_widths["c" + i] = ($(this).attr("width") + "").replace("px", "");
				});
				
				documents.push(document); //append to documents
			}
		});
		return documents;
	},
	tikiOptions:  {
			urlMenu:"lib/jquery.sheet_tiki/menu.html",
			urlGet: "",
			buildSheet: true,
			autoFiller: true,
			inlineMenu: inlineMenu,
			colMargin: 20, //beefed up colMargin because the default size was too small for font
			height: $(window).height() * 0.8,
			minSize: {
				rows: 1,
				cols: 1
			}
	},
	manageState: function(toggleEdit, parse) {
		var jS = jST.getSheet();
		parse = (parse ? parse : '');
		var url = "";
		if (jST.id) {
			url = "tiki-view_sheets.php?sheetId=" + jST.id + "&sheetonly=y&parse=" + parse;
		} else {
			url = "tiki-view_sheets.php?type=" + jST.type + "&file=" + jST.file + "&sheetonly=y&parse=" + parse;
		}
		
		jS.saveSheet = function(){};
		
		jST.modal(tr("Updating"));
		
		var i = jS.i;
		jS.switchSpreadsheet(0);
		
		$.get(url, function (o) {
			if (!toggleEdit) {
				jS.s.editable = !jS.s.editable;
			}
			
			jS.toggleState(o);
			$.sheet.readyState();
			
			jS.switchSpreadsheet(i);
			
			jST.modal();
		});
	},
	readyState: function() {
		var jS = jST.getSheet();
		
		if (jS.s.editable) {
			$("#jSheetControls").show();
			$("#saveState").show();
			$("#editState").hide();
		} else {
			$("#jSheetControls").hide();
			$("#saveState").hide();
			$("#editState").show();
		}
	},
	dualFullScreenHelper: function(parent, reset) {
		var container = $('#' + parent.attr('id') + '_fullscreen');
		var sizeSet = false;
		$($.sheet.instance).each(function(i) {
			var jS = this;
			var tikiSheet = jST.eq(i);
			if (!reset) {
				jS.sizeOriginal = {
					height: tikiSheet.height(),
					width: tikiSheet.width()
				};
				
				if (!container.length) {
					container = $('<div style="left: 0px; top: 0px; z-index: 9999999;" data-parentid="" />')
						.attr('id', parent.attr('id') + '_fullscreen')
						.data('parentid', parent.attr('id'))
						.css('position', 'fixed')
						.addClass('ui-widget-content')
						.width($(window).width())
						.height($(window).height())
						.html(parent.children())
						.appendTo('body');
				}
				
				jS.s.width = $(window).width() / 2;
				jS.s.height = $(window).height();
				
				$('.sheet_sibling')
					.add('.navbar')
						.each(function() {
							jS.s.height -= $(this).height();
						});
			} else {
				if (parent.length) {
					$('#' + parent.data('parentid')).html(parent.children());
					parent.remove();
				}
				
				jS.s.width = jS.sizeOriginal.width;
				jS.s.height = jS.sizeOriginal.height;
			}
			
			jS.sheetSyncSize();
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
		setupUI: function() {
			jST
				.unbind("switchSpreadsheet")
				.bind("switchSpreadsheet", function(e, jS, i) {
					if (i < 0) {
						$.sheet.makeSmall();
						
						var switchSheetMsg = $("div.switchSheet").first().clone();
						var msg;
						
						switchSheetMsg.find("input.newSpreadsheet").click(function() {
							switchSheetMsg.dialog("close").remove();
							jS.switchSpreadsheet(i);
						});
						switchSheetMsg.find("input.addSpreadsheet").click(function() {
							switchSheetMsg.dialog("close").remove();
							$.modal(tr("Loading"));
							msg = $("<div />").load("tiki-sheets.php #role_main .tabcontent:first table", function() {
								$.modal();
								msg.dialog({
									width: jST.width(),
									height: jST.height(),
									modal: true
								});
								msg.find("a.sheetLink").each(function() {
									$(this).click(function() {
										msg.dialog("close").remove();
										$.sheet.link.make("spreadsheet", $(this).attr("sheetid"));
										return false;
									});
								});
							});
						});
						switchSheetMsg.find("input.addTracker").click(function() {
							switchSheetMsg.dialog("close").remove();
							$.modal(tr("Loading"));
							msg = $("<div />").load("tiki-list_trackers.php #role_main table", function() {
								$.modal();
								var trackerList = $('<table><tr><td>' + tr('Id') + '</td><td>' + tr('Name') + '</td></tr></table>');

								msg.find('td.id').each(function() {
									var trackerId = $.trim($(this).text());
									var trTrackerSelection = $('<tr><td>' + trackerId + '</td><td>' + $(this).next().text() + '</td></tr>')
										.click(function() {
											msg.dialog("close").remove();
											$.sheet.link.make("tracker", trackerId);
										})
										.css('cursor', 'pointer');


									trackerList.append(trTrackerSelection);
								});

								trackerList.dialog({
									width: jST.width(),
									height: jST.height(),
									modal: true,
									title: tr('Tracker')
								});
							});
						});
						switchSheetMsg.find("input.addFile").click(function() {
							switchSheetMsg.dialog("close").remove();
							$.modal(tr("Loading"));
							msg = $("<div />").load("tiki-list_file_gallery.php?find_fileType=csv #fgalform", function() {
								$.modal();
								msg.dialog({
									width: jST.width(),
									height: jST.height(),
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
						
						return false;
					} else {
						jS.switchSpreadsheet(i);
					}
				});
		},
		make: function(type, id) {
			jST.modal(tr('Updating'));
			$.sheet.saveSheet(function() {
				try {
					switch (type) {
						case "spreadsheet":
							$.get("tiki-view_sheets.php", {
								sheetId: jST.id,
								childSheetId: id,
								relate: "add"
							}, function() {
								$.sheet.manageState();
								jST.modal();
							});
							break;
						case "file":
							$.get("tiki-view_sheets.php", {
								sheetId: jST.id,
								fileId: id,
								relate: "add"
							}, function() {
								$.sheet.manageState();
								jST.modal();
							});
							break;
						case "tracker":
							$.get("tiki-view_sheets.php", {
								sheetId: jST.id,
								trackerId: id,
								relate: "add"
							}, function() {
								$.sheet.manageState();
								jST.modal();
							});
							break;
					}
				} catch(e) {}
			});
			return false;
		}
	}
}, $.sheet);