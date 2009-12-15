/*
	jQuery.sheet() Spreadsheet with Calculations Plugin
	Verison: 0.4
	
	Note:  There is a confusion bug with height that has been addressed
	Safari likes to use the attribute height,
	Firefox likes $().height;
	IE likes $().outerHeight();
*/
jQuery.fn.extend({
	sheet: function(settings) {
		settings = jQuery.extend({
			urlGet: "documentation.html table:first",
			urlSave: "save.html",
			title: '',
			editable: true,
			urlBaseCss: 'jquery.sheet.base.css',
			urlTheme: "theme/ui.theme.css",
			urlMenu: "menu.html",
			urlMenuJs: "plugins/jquery.clickmenu.pack.js",
			urlMenuCss: "plugins/clickmenu.css",
			urlScrollTo: "plugins/jquery.scrollTo-1.4.0-min.js",
			loading: 'Loading Spreadsheet...',
			newColumnWidth: '120px',
			ajaxSaveType: 'POST',
			buildSheet: false,//'10x30', this can be slow
			calcOff: false,
			log: false,
			lockFormulas: false,
			parent: this,
			colMargin: "25px",
			fnBefore: function() {},
			fnAfter: function() {},
			fnSave: function() { jS.saveSheet(); },
			fnOpen: function() {},
			fnClose: function() {}
		}, settings);
		
		settings.fnBefore();
		
		jS.getCss(settings.urlBaseCss);
		settings.width = jQuery(settings.parent).width();
		settings.height = jQuery(settings.parent).height();
		
		jQuery.fn.sheet.settings = jS.s = settings;
		
		var tempSheet = jQuery(jS.s.parent).html();
		jQuery(jS.s.parent).html(jS.s.loading);
		
		if (jS.s.log) {
			jQuery(jS.s.parent).after('<textarea id="' + jS.id.log + '" />');
		}
		
		var sheetObj = jQuery(
			'<div id="' + jS.id.ui + '">'+
				'<table class="tableControl">'+
					'<colgroup>'+
						'<col style="width: ' + jS.s.colMargin + ';" width="' + jS.s.colMargin + '"><col />'+
					'</colgroup>'+
					'<tbody>' +
						'<tr style="height: ' + jS.s.colMargin + ';">'+
							'<td><div id="' + jS.id.barCorner + '" onclick="jS.cellEditAbandon();" /></td>'+
							'<td style="height: ' + jS.s.colMargin + '; vertical-align: middle;"><div style="overflow: hidden;" id="' + jS.id.barTopParent + '" /></td>'+
						'</tr>'+
						'<tr style="position: relative;">' +
							'<td width="' + jS.s.colMargin + '" style="vertical-align: top; overflow: hidden; width: ' + jS.s.colMargin + ';">' +
								'<div style="overflow: hidden;" id="' + jS.id.barLeftParent + '" />' +
							'</td>' +
							'<td id="' + jS.id.paneParent + '" style="position: relative;">' +
								'<div id="' + jS.id.pane + '" />' +
							'</td>'+
						'</tr>' +
					'</tbody>'+
				'</table>'+
			'</div>'
		);
		
		//Make functions upper and lower case compatible
		for (var k in cE.fn) {
			var kLower = k.toLowerCase();
			if (kLower != k) {
				cE.fn[kLower] = cE.fn[k];
			}
		}
		
		function tuneForUse(obj, r) {
			jQuery(obj).find('table:first')
				.addClass(jS.cl.sheet)
				.attr('id', jS.id.sheet);
			jQuery(obj).find('.' + jS.cl.uiCell).removeClass(jS.cl.uiCell);
			jQuery(obj).find('td')
				.css('background-color', '')
				.css('color', '');
			if (r) {
				return obj;
			}
		}
		
		//We load a sheet from a url, ajaxx style
		if (jS.s.buildSheet == false) {
			jQuery(sheetObj).find('#' + jS.id.pane).load(jS.s.urlGet, function(){
				tuneForUse(this);
				jS.sheetInit(sheetObj);
				settings.fnAfter();
			});
		} else { //We know that the sheet is either one to build or already included in the code.
			var tunedSheet;
			
			if (jS.s.buildSheet.toLowerCase().match('x')) {//We now know that the sheet is (or should be) was contained in the parent
				tunedSheet = tuneForUse(jS.buildSheet(), true);
			} else { //We go ahead and build a sheet.
				tunedSheet = tuneForUse(tempSheet, true);
			}
			
			jQuery(sheetObj).find('#' + jS.id.pane).html(tunedSheet);
			jS.sheetInit(sheetObj);
			settings.fnAfter();
		}
	}
});

 var jS = jQuery.sheet = {
	version: '0.41',
	s: {},//s = settings object, used for shorthand, populated from jQuery.sheet
	obj: {//obj = object references
		parent: 		function() { return jQuery(jS.s.parent) },
		ui:				function() { return jQuery('#' + jS.id.ui) },
		sheet: 			function() { return jQuery('#' + jS.id.sheet) },
		bar:			function() { return jQuery('.' + jS.cl.bar) },
		barTop: 		function() { return jQuery('#' + jS.id.barTop) },
		barTopParent: 	function() { return jQuery('#' + jS.id.barTopParent) },
		barLeft: 		function() { return jQuery('#' + jS.id.barLeft) },
		barLeftParent: 	function() { return jQuery('#' + jS.id.barLeftParent) },
		barCorner:		function() { return jQuery('#' + jS.id.barCorner) },
		barSelected:	function() { return jQuery('.' + jS.cl.barSelected) },
		cell: 			function() { return jQuery('.' + jS.cl.cell) },
		controls:		function() { return jQuery('#' + jS.id.controls) },
		formula: 		function() { return jQuery('#' + jS.id.formula) },
		label: 			function() { return jQuery('#' + jS.id.label) },
		fx:				function() { return jQuery('#' + jS.id.fx) },
		pane: 			function() { return jQuery('#' + jS.id.pane) },
		paneParent:		function() { return jQuery('#' + jS.id.paneParent) },
		log: 			function() { return jQuery('#' + jS.id.log) },
		menu:			function() { return jQuery('#' + jS.id.menu) },
		title:			function() { return jQuery('#' + jS.id.title) },
		uiDefault:		function() { return jQuery('.' + jS.cl.uiDefault) },
		uiActive:		function() { return jQuery('.' + jS.cl.uiActive) },
		uiBase:			function() { return jQuery('.' + jS.cl.uiBase) },
		uiCell:			function() { return jQuery('.' + jS.cl.uiCell) },
		toggle:			function() { return jQuery('.' + jS.cl.toggle) },
		tableBody: 		function() { return document.getElementById(jS.id.sheet) },
		title: 	function() { return jQuery('#' + jS.id.title) }
	},
	id: {//id = id's references
		sheet: 			'jSheet',//This con probably be just about any value as long as it's not a duplicated id
		ui:				'jSheetUI',
		barTop: 		'jSheetBarTop',
		barTopParent: 	'jSheetBarTopParent',
		barLeft: 		'jSheetBarLeft',
		barLeftParent: 	'jSheetBarLeftParent',
		barCorner:		'jSheetBarCorner',
		controls:		'jSheetControls',
		formula: 		'jSheetControls_formula',
		label: 			'jSheetControls_loc',
		fx:				'jSheetControls_fx',
		pane: 			'jSheetEditPane',
		paneParent:		'jSheetEditPaneParent',
		log: 			'jSheetLog',
		menu:			'jSheetMenu',
		title:			'sheetTitle'
	},
	cl: {//cl = class references
		sheet: 			'jSheet',
		bar: 			'jSheetBar',
		cell: 			'jSheetCellActive',
		calcOff: 		'jSheetCalcOff',
		barSelected: 	'jSheetBarItemSelected',
		uiDefault:		'ui-state-default',
		uiActive:		'ui-state-active',
		uiBase:			'ui-widget-content',
		uiParent: 		'ui-widget ui-widget-content ui-corner-all',
		uiBar: 			'ui-widget-header ui-helper-clearfix',
		uiPane: 		'ui-widget-content',
		uiMenuUl: 		'ui-widget-header',
		uiMenuLi: 		'ui-widget-header',
		uiMenuHighlighted: 'ui-state-highlight',
		uiControl: 		'ui-widget-content',
		uiCell:			'themeRoller_activeCell',
		uiCellHighlighted: 'ui-state-highlight',
		toggle:			'cellStyleToggle'
	},
	ERROR: function() { return cE.ERROR },
	sheetInit: function(sheetObj) {
		jS.obj.parent().html(sheetObj);
		jS.sheetDecorate();
		jS.barAdjustor();
	},
	attr: {//I created this object so I could see, quickly, which attribute was most stable.
		width: function(obj) { return jQuery(obj).outerWidth(); },
		height: function(obj) { return jQuery(obj).outerHeight(); }
	},
	makeBarItemLeft: function() {
		jS.obj.barLeft().remove();
		
		var barLeft = jQuery('<table border="1px" id="' + jS.id.barLeft + '" width="100%" />');
		var tBody = jQuery('<tbody />');
		jS.obj.sheet().find('tr').each(function(i) {
			var newHeight = jS.attr.height(jQuery(this).find('td:first'));
			
			var tr = jQuery('<tr />').height(newHeight);
			var td = jQuery('<td>' + (i+1) + '</td>').height(newHeight).css('vertical-align', 'middle');
			
			if (jS.s.editable) {
				jS.getResizeControl.height(td);
			}
			
			tr.append(td);
			
			jQuery(tBody).append(tr);
		});
		barLeft.append(tBody);
		jS.obj.barLeftParent().append(barLeft);
		
		//Safari Fix
		jS.barHeightSync();
	},
	makeBarItemTop: function() {
		jS.obj.barTop().remove();
		
		var w = jS.attr.width(jS.obj.sheet());
		var barTop = jQuery('<table border="1px" id="' + jS.id.barTop + '" />').width(w);
		barTop.height(jS.s.colMargin);

		barTop.append(jS.obj.sheet().find('colgroup').clone());
		// Prepend a new row for column headers.
		var tr = jQuery("<tr class='" + jS.cl.bar + "' />");
		var td = '';
		
		jS.obj.sheet().find('tr:eq(0) td').each(function(i) {
			var v = cE.columnLabelString(i+1);
			//Creating the object without touching the DOM makes loading faster.
			td += "<td>" + v + "</td>";
		});
		
		jQuery(tr).append(td);
		
		jQuery(barTop).find('colgroup').after("<tbody />");
		jQuery(barTop).find('tbody').html(tr);

		// Prepend one colgroup/col element that covers the new row headers.
		if (jS.s.editable) {
			jS.getResizeControl.width(jQuery(barTop).find('td'));
		}
		
		jS.obj.barTopParent().append(barTop);

	},
	toggleHide: {//These are not ready for prime time
		row: function(i) {
			if (!i) {//If i is empty, lets get the current row
				i = jS.obj.cell().parent().attr('rowIndex');
			}
			if (i) {//Make sure that i equals something
				var o = jS.obj.barLeft().find('tr').eq(i);
				if (o.is(':visible')) {//This hides the current row
					o.hide();
					jS.obj.sheet().find('tr').eq(i).hide();
				} else {//This unhides
					//This unhides the currently selected row
					o.show();
					jS.obj.sheet().find('tr').eq(i).show();
				}
			} else {
				alert('No row selected.');
			}
		},
		rowAll: function() {
			jS.obj.sheet().find('tr').show();
			jS.obj.barLeft().find('tr').show();
		},
		column: function(i) {
			if (!i) {
				i = jS.obj.cell().attr('cellIndex');
			}
			if (i) {
				//We need to hide both the col and td of the same i
				var o = jS.obj.barTop().find('colgroup col').eq(i);
				if (o.is(':visible')) {
					jS.obj.sheet().find('tbody tr').each(function() {
						jQuery(this).find('td').eq(i).hide();
					});
					jS.obj.barTop().find('tbody tr td').eq(i);
					o.hide();
					jS.obj.sheet().find('colgroup col').eq(i).hide();
					jS.toggleHide.columnSizeManage();
				}
			} else {
				alert('Now column selected.');
			}
		},
		columnAll: function() {
		
		},
		columnSizeManage: function() {
			var w = jS.obj.barTop().width();
			var newW = 0;
			jS.obj.barTop().find('colgroup col').each(function() {
				var o = jQuery(this);
				if (o.is(':hidden')) {
					newW += parseInt(o.css('width').replace('px',''));
				}
			});
			jS.obj.barTop().width(w);
			jS.obj.sheet().width(w);
		}
	},
	getResizeControl: {
		height: function(obj) {
			jQuery(obj).mousedown(function(e) { 
				jS.barResizer(e, this, 'row');
				return false;
			})
			.dblclick(function() {
				var i = jQuery.trim(jQuery(this).text());
				i = parseInt(i) - 1;
				jS.cellSetActiveMultiRow(i);
			});
		},
		width: function(obj) {
			jQuery(obj).mousedown(function(e) {
				jS.barResizer(e, this, 'column');
				return false;
			})
			.dblclick(function() {
				var i = cE.columnLabelIndex(jQuery.trim(jQuery(this).text()));
				i = parseInt(i) - 1;
				jS.cellSetActiveMultiColumn(i);
			});
		}
	},
	makeControls: function(parent) {
		jS.obj.controls().remove();
		if (jS.s.editable) {
			// Register onclick for tableBody td elements.
			jS.obj.pane().find('td').mousedown(jS.cellOnMouseDown).click(jS.cellOnClick);

			var controls = jQuery('<div id="' + jS.id.controls + '" />');
			//Lets get the page title information			
			var sheetTitle = jS.sheetTitle(true);
			
			if (jS.s.urlMenu) {
				jQuery.getScript(jS.s.urlMenuJs, function() {
					jS.getCss(jS.s.urlMenuCss);
					var menuObj = jQuery('<div />').load(jS.s.urlMenu, function() {
						controls.prepend(menuObj.html());
						jS.obj.menu()
							.clickMenu()
							.append('&nbsp;&nbsp;&nbsp;<span id="' + jS.id.title + '">' + sheetTitle + '</span>')
							.find('.' + jS.cl.toggle)
								.click(function(e) {
									jS.cellStyleToggle(e);
								});

						jS.sheetSyncSize();
						
						jS.obj.menu()
					});
				});
			} else {
				controls.append('&nbsp;&nbsp;&nbsp;<span id="' + jS.id.title + '">' + sheetTitle + '</span>');
				jS.sheetSyncSize();
			}
			
			controls.append('<table style="width: 100%;">' +
							'<tr>' +
								'<td style="width: 35px; text-align: right;" id="' + jS.id.label + '"></td>' +
								'<td style="width: 10px;" id="' + jS.id.fx + '">fx</td>' + 
								'<td>' +
									'<textarea id="' + jS.id.formula + '"></textarea>' +
								'</td>' +
							'</tr>' +
						'</table>');
			
			
			
			//Get the scrollTo Pluggin
			if (jS.s.urlScrollTo) {
				jQuery.getScript(jS.s.urlScrollTo);
			}
			
			controls.keydown(function(e) {
				return jS.formulaKeyDown(e);
			});

			jQuery(parent).prepend(controls);
		}
	},
	sheetDecorate: function() {			
		// Set standard height for those with none
		jS.obj.sheet().find('[style!="height"] tr').innerHeight(jS.s.colMargin);
		jS.obj.sheet().find('[style!="height"] tr').outerHeight(jS.s.colMargin);
		
		jS.formatSheet();
		
		jS.makeBarItemLeft();
		jS.makeBarItemTop();
		
		jS.makeControls(jS.obj.ui());

		if (!jS.s.calcOff) {
			jS.calc(jS.obj.tableBody());
		}
	},
	formatSheet: function() {
		if (!jS.obj.parent().find('tbody').length > 0) {
			jS.obj.sheet().wrap('<tbody />');
		}
		
		if (!jS.obj.parent().find('colgroup').length > 0) {
			var colgroup = jQuery('<colgroup />');
			jS.obj.sheet().find('tr:first').find('td').each(function() {
				var w = jQuery(this).width();
				jQuery('<col />').width(w).attr('width', w).appendTo(colgroup);
			});
			colgroup.insertBefore(jS.obj.sheet().parent());
		}
	},
	getCss: function(url) {
		jQuery('head').append('<link rel="stylesheet" type="text/css" href="' + url + '"></link>')
	},
	themeRoller: {
		start: function() {
			jS.getCss(jS.s.urlTheme);		
			//Style sheet
			
			jS.obj.parent().addClass(jS.cl.uiParent);
			
			//Style bars
			jS.obj.barLeft().find('td').addClass(jS.cl.uiBar);
			jS.obj.barTop().find('td').addClass(jS.cl.uiBar);
			jS.obj.barCorner().addClass(jS.cl.uiBar);
			
			//The sheet is transparent, this gives it background
			jS.obj.pane().addClass(jS.cl.uiPane);
			
			//Style the menu
			jS.obj.menu()
				.css('font-size', '12px')
				.css('padding-top', '2px')
				.css('padding-left', '2px')
				.find('ul')
					.addClass(jS.cl.uiMenuUl)
					.andSelf()
				.find('li')
					.css('border', 'none')
					.addClass(jS.cl.uiMenuLi)
					.hover(function() {
						//Handle mouseover styling, or highlighting
						jQuery(this)
							.addClass(jS.cl.uiMenuHighlighted);
					},function() {
						//This cleans the menus so only what's mouseover shows as highlighted.
						jQuery(this).removeClass(jS.cl.uiMenuHighlighted);
					});
			
			jS.obj.sheet().addClass('ui-widget-content');
			
			jS.obj.barLeft().addClass(jS.cl.uiBar);
			jS.obj.barTop().addClass(jS.cl.uiBar);
			
			jS.obj.title().addClass(jS.cl.uiControl);
			jS.obj.fx().addClass(jS.cl.uiControl);
			jS.obj.label().addClass(jS.cl.uiControl);
			jS.obj.formula().addClass(jS.cl.uiControl);
		},
		cell: function(td) {
			jS.themeRoller.clearCell();
			if (td) {
				jQuery(td)
					.addClass(jS.cl.uiCellHighlighted)
					.addClass(jS.cl.uiCell);;
			}
		},
		clearCell: function() {
			jS.obj.uiActive().removeClass(jS.cl.uiActive);
			jS.obj.uiCell()
				.removeAttr('style')
				.removeClass(jS.cl.uiCellHighlighted)
				.removeClass(jS.cl.uiCell);
		},
		newBar: function(obj) {//This is for a tr
			jQuery(obj).addClass(jS.cl.uiBar);
		},
		barTop: function(i) {
			jS.obj.barTop().find('td').eq(i).addClass(jS.cl.uiActive);
		},
		barLeft: function(i) {
			jS.obj.barLeft().find('td').eq(i).addClass(jS.cl.uiActive);
		},
		barObj: function(obj) {
			jQuery(obj).addClass(jS.cl.uiActive);
		},
		clearBar: function() {
			jS.obj.barTopParent().find('.' + jS.cl.uiActive).removeClass(jS.cl.uiActive);
			jS.obj.barLeftParent().find('.' + jS.cl.uiActive).removeClass(jS.cl.uiActive);
		}
	},
	manageHtmlToText: function(v) {
		v = jQuery.trim(v);
		if (v.charAt(0) != "=") {
			v = v.replace(/&nbsp;/g, ' ')
				.replace(/&gt;/g, '>')
				.replace(/&lt;/g, '<')
				.replace(/\t/g, '')
				.replace(/\n/g, '')
				.replace(/<br>/g, '\r')
				.replace(/<BR>/g, '\n');

			jS.log("from html to text");
		}
		return v;
	},
	manageTextToHtml: function(v) {	
		v = jQuery.trim(v);
		if (v.charAt(0) != "=") {
			v = v.replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;')
				.replace(/ /g, '&nbsp;')
				.replace(/>/g, '&gt;')
				.replace(/</g, '&lt;')
				.replace(/\n/g, '<br>')
				.replace(/\r/g, '<br>');
			
			jS.log("from text to html");
		}
		return v;
	},
	sheetDecorateRemove: function(obj) {
		jQuery(obj).find('.' + jS.cl.cell).removeClass(jS.cl.cell);
		//IE Bug, match width with css width
		jQuery('col', obj).each(function(i) {
			var v = jQuery(this).css('width') + 'px';
			jQuery(obj).find('col').eq(i).attr('width', v);
		});
		
		jQuery(obj).find('td').css('height', '');
	},
	cellIsEdit: false,
	cellClick: function(verb, loc) {
		loc[0]--; loc[1]--;
		switch (verb) {
			case 'up': 		loc[0]--; break;
			case 'down': 	loc[0]++; break;
			case 'left': 	loc[1]--; break;
			case 'right': 	loc[1]++; break;
		}
		jS.obj.sheet().find('tr').eq(loc[0]).find('td:visible').eq(loc[1]).click();
	},
	cellOnMouseDown: function(evt) {
		jS.cellSetActiveMulti(evt);
		if (jQuery(evt.target).attr('cellIndex')) {//This is to detect if it is a textarea
			return false;
		}
	},
	cellOnClick: function(evt, o) {
		if (!o) {
			o = this;
		}
		switch (jS.s.lockFormulas) {
			case true:
				if (!jQuery(o).attr('formula')) {
					jS.cellOnClickManage(o);
					jS.followMe();
				}
				break;
			default:
				jS.cellOnClickManage(o);
				jS.followMe();
		}
	},
	cellOnClickManage: function(target) {
		switch (!jQuery(target).hasClass(jS.cl.cell)) {
			case true:
				var loc = jS.getTdLocation(target);
				if (loc) {
					jS.cellSetFocus(loc, jS.obj.label(), jS.obj.formula());
					jS.cellEdit(loc[0], loc[1], target);
				}
				jS.log('click: ' + loc);
				break;
			default:
				jS.cellIsEdit = true;
				jS.cellTextArea(target, false, true);
				jS.log('click, textarea over table activated');
		}
	},
	cellSetFocus: function(loc, labelLocation, editObject) {
		var v = editObject.val();
		switch (v.charAt(0)) {
			case '=':
				if ("=([:,*/+-".indexOf(v.charAt(v.length)) >= 0) {
					// Append cell location to currently edited formula based on mouse click.
					editObject.val(v);
					jQuery(editObject).focus();
					return false;
				}
				break;
		}
	},
	cellEdit: function(row, col, td) {
		// The row and col are 1-based.
		// This method points the controls to a new cell.
		if (!td[0]) {
			td = jS.getTd(jS.obj.tableBody(), row, col);
		}
		td = jQuery(td);
		jS.cellEditDone();
		jS.obj.label().html(cE.columnLabelString(col) + row);
		var v = td.attr('formula');
		if (!v || v.length <= 0) {
			v = jS.manageHtmlToText(td.html());
		}
		jS.obj.formula().val(v).focus().select();
		jS.cellSetActive(td);
	},
	cellSetActive: function(td) {
		var loc = jS.getTdLocation(td);
		jQuery(td).addClass(jS.cl.cell);
		jS.obj.barLeft().find('td').eq(loc[0]--).addClass(jS.cl.barSelected);
		jS.obj.barTop().find('td').eq(loc[1]--).addClass(jS.cl.barSelected);
		
		jS.themeRoller.cell(td);
		jS.themeRoller.barLeft(loc[0]--);
		jS.themeRoller.barTop(loc[1]--);
	},
	cellEditDone: function(bsheetClearActive) {
		switch (jS.cellIsEdit) {
			case true:
				// Any changes to the input controls are stored back into the table, with a recalc.
				var loc = cE.parseLocation(jS.obj.label().html());
				if (loc) {
					var td = jQuery(jS.getTd(jS.obj.tableBody(), loc[0], loc[1]));
					var recalc = false;
					
					//Lets ensure that the cell being edited is actually active
					if (td.hasClass(jS.cl.cell)) { 
						//This should return either a val from textbox or formula, but if fails it tries once more from formula.
						var v = jS.cellTextArea(td, true);

						//inputFormula.value;
						var noEditFormula = false;
						var noEditNumber = false;
						var noEditNull = false;
						var editedFormulaToFormula = false;
						var editedFormulaToReg = false;
						var editedRegToFormula = false;
						var editedRegToReg = false;
						var editedToNull = false;
						var editedNumberToNumber = false;
						var editedNullToNumber = false;
						
						var resize = false;
						
						var tdFormula = td.attr('formula');
						var tdPrevVal = td.attr('prevVal');
						var tdHeight = jS.attr.height(td);

						if (v) {
							if (v.charAt(0) == '=') { //This is now a formula
								if (v != tdFormula) { //Didn't have a formula before but now does
									editedFormulaToFormula = true;
									jS.log('edit, new formula, possibly had formula');
								} else if (tdFormula) { //Updated using inline edit
									noEditFormula = true;
									jS.log('no edit, has formula');
								} else {
									jS.log('no edit, has formula, unknown action');
								}
							} else if (tdFormula) { //Updated out of formula
								editedRegToFormula = true;
								jS.log('edit, new value, had formula');
							} else if (!isNaN(parseInt(v))) {
								if ((v != tdPrevVal && v != jS.obj.formula().val()) || (td.text() != v)) {
									editedNumberToNumber = true;
									jS.log('edit, from number to number, possibly in function');
								} else {
									noEditNumber = true;
									jS.log('no edit, is a number');
								}
							} else { //Didn't have a formula before of after edit
								editedRegToReg = true;
								jS.log('possible edit from textarea, has value');
							}
						} else { //No length value
							if (td.html().length > 0 && tdFormula) {
								editedFormulaToReg = true;
								jS.log('edit, null value from formula');
							} else if (td.html().length > 0 && tdFormula) {
								editedToNull = true;
								jS.log('edit, null value from formula');
							
							} else {
								noEditNull = true;
								jS.log('no edit, null value');
							}
						}
						
						td.removeAttr('prevVal');
						v = jS.manageTextToHtml(v);
						if (noEditFormula) {
							td.html(tdPrevVal);
						} else if (editedFormulaToFormula) {
							recalc = true;
							resize = true;
							td.attr('formula', v).html('');
						} else if (editedFormulaToReg) {
							recalc = true;
							resize = true;
							td.removeAttr('formula').html(v);
						} else if (editedRegToFormula) {
							recalc = true;
							resize = true;
							td.removeAttr('formula').html(v);
						} else if (editedRegToReg) {
							resize = true;
							td.html(v);
						} else if (noEditNumber) {
							td.html(v); 
						} else if (noEditNull) {
							td.html(v);
						} else if (editedNumberToNumber) {
							recalc = true;
							resize = true;
							td.html(v);
						} else if (editedToNull) {
							recalc = true;
							resize = true;
							td.removeAttr('formula').html('');
						}
						
						if (recalc && !jS.obj.sheet().hasClass(jS.cl.calcOff)) {
							jS.calc(jS.obj.tableBody());
						}
						
						if (bsheetClearActive != false) {
							// Treats null == true.
							jS.sheetClearActive();
						}
						
						if (resize) {
							td.parent().height(tdHeight);
							jS.obj.barLeft().find('tr').eq(loc[0] - 1).height(jS.attr.height(td.parent()));
						}
						
						jS.obj.formula().focus().select();
						jS.cellIsEdit = false;
					}
				}
				break;
			default:
				var loc = cE.parseLocation(jS.obj.label().html());
				try {
					if (!jQuery.browser.safari) {
						var tdHeight = jS.attr.height(jS.obj.cell().parent());
						jS.obj.cell().parent().height(tdHeight);
					} else {
						var h = jS.obj.barLeft().find('tr').eq(loc[0] - 1).attr('height');
						jS.obj.cell().parent().attr('height', h);
						//Fix for safari
						jS.barHeightSync();
					}
				} catch(e) {}
				jS.sheetClearActive();
		}
	},
	cellEditAbandon: function() {
		jS.themeRoller.clearCell();
		jS.themeRoller.clearBar();
		var v = jS.cellTextArea(jS.obj.cell(), true);
		if (v) {
			jS.obj.cell().html(jS.manageTextToHtml(v));
			jS.sheetClearActive();
			if (v.charAt(0) == '=') {
				jS.calc(jS.obj.tableBody());
			}
		}
		jS.obj.label().html('');
		return false;
	},
	formulaKeyDown: function(evt, isTextbox) {
		//Switch is much faster than if statements
		var loc = jS.getTdLocation(jS.obj.cell());
		function enter(evt) {
			var cell = jS.obj.cell();
			var vCell = jS.manageHtmlToText(cell.html());
			var vCellFormula = cell.attr('formula');
			var vFormula = jS.obj.formula().val();
			if ((vCell == vFormula || 
				vFormula == vCellFormula || 
				vFormula == vCell) && !evt.ctrlKey) {
				jS.cellClick('', loc);
			} else {
				jS.cellClick('down', loc);
			}
		}
		function tab(evt) {
			switch (evt.shiftKey) {
				case true: 					jS.cellClick('left', loc);
					break;
				case false: 				jS.cellClick('right', loc);
					break;
			}
		}
		function textBoxFn(evt) {
			switch (evt.ctrlKey) {
				case true:
					switch (evt.keyCode) {
						case key.ENTER: 	enter(evt);
							break;
					}
					break;
			}
			switch (evt.keyCode) {
				case key.TAB: 				tab(evt); return false;
					break;
			}
		}
		function regKeyHandle(evt) {
			switch (evt.ctrlKey) {
				case true: 					if (evt.keyCode == key.ENTER) { enter(evt); } else { jS.cellIsEdit = true; } return true; 
					break;
				default:
					switch (evt.keyCode) {
						case key.ESCAPE: 	jS.cellEditAbandon();
							break;
						case key.TAB: 		tab(evt); return false;
							break;
						case key.ENTER: 	enter(evt); return false;
							break;
						case key.LEFT: 		jS.cellClick('left', loc); return false;
							break;
						case key.UP: 		jS.cellClick('up', loc); return false;
							break;
						case key.RIGHT: 	jS.cellClick('right', loc); return false;
							break;
						case key.DOWN: 		jS.cellClick('down', loc); return false;
							break;
						default: 			jS.cellIsEdit = true;
					}
			}
		}
		var returnKey = true;
		switch (isTextbox) {
			case true:						returnKey = textBoxFn(evt);
				break;
			default:						returnKey = regKeyHandle(evt);
				break;
		}
		return returnKey;
	},
	cellStyleToggle: function(e) {
		var setClass = jQuery(e.target).attr('setclass');
		var removeClass = jQuery(e.target).attr('removeclass').split(',');
		
		jQuery(removeClass).each(function() {
			if (jS.obj.uiCell().hasClass(this)) {
				jS.obj.uiCell().removeClass(this);
			}
		});
		
		if (jS.obj.uiCell().hasClass(setClass)) {
			jS.obj.uiCell().removeClass(setClass);
		} else {
			jS.obj.uiCell().addClass(setClass);
		}
	
		return false;
	},
	context: {},
	calc: function(tableBody, fuel) {
		return cE.calc(new jS.tableCellProvider(tableBody.id), jS.context, fuel);
	},
	cellTextArea: function(cell, returnVal, makeEdit, setVal) {
		//Remove Textarea and transfer value.
		var v;
		cell = jQuery(cell);
		if (!makeEdit) {
			var textArea = jQuery(cell).find('textarea');
			var textAreaVal = textArea.val();
			if (textAreaVal || jS.obj.formula().attr('disabled')) {
				jS.log('Textarea value used');
				v = textAreaVal;
				textArea.remove();
				cell
					.css('text-align', '')
					.css('vertical-align', '');
			} else {
				jS.log('Formula value used');
				v = jS.obj.formula().val();
			}
			jS.obj.formula().removeAttr('disabled');
		} else {
			jS.obj.formula().attr('disabled', 'true');
			if (setVal) {
				v = setVal;
			} else {
				v = jS.obj.formula().val();
			}
			
			var textArea = jQuery('<textarea id="tempText" />');
			var w = cell.width();
			var h = cell.height();
			if (h < 75) {
				h = 75;
			} else {
				textArea.css('border', '0px');
			}
			//create text area.  Agian, strings are faster than DOM.
			textArea
				.removeAttr('disabled')
				.height(h)
				.width(w)
				.val(jQuery.trim(v))
				.css('position', 'absolute')
				.click(function(){
					return false;
				})
				.keydown(function(e) {
					return jS.formulaKeyDown(e, true);
				});
			
			//Se we can look at the past value after edit.
			if (cell.attr('formula')) {
				cell.attr('prevVal', cell.text()).removeAttr('formula');
			}
			//add it to cell
			cell.css('text-align', 'left')
				.css('vertical-align', 'top')
				.html(textArea);
			//focus textarea
			cell.find('textarea').focus();
		}
		if (returnVal) {
			return v;
		}
	},
	refreshLabelsColumns: function(){
		var w = 0;
		jS.obj.barTopParent().find('td').each(function(i) {
			jQuery(this).text(cE.columnLabelString(i+1));
			w += jQuery(this).width();
		});
		return w;
	},
	refreshLabelsRows: function(){
		jS.obj.barLeftParent().find('td').each(function(i) {
			jQuery(this).text((i + 1));
		});
	},
	addRowMulti: function(qty) {
		if (!qty) {
			qty = prompt('How many rows would you like to add?');
		}
		if (qty) {
			for (var i = 0; i <= qty; i++) {
				jS.addRow();
			}
		}
	},
	addColumnMulti: function(qty) {
		if (!qty) {
			qty = prompt('How many columns would you like to add?');
		}
		if (qty) {
			for (var i = 0; i <= qty; i++) {
				jS.addColumn();
			}
		}
	},
	addRow: function(atRow) {
		if (!atRow) {
			//if atRow has no value, lets just add it to the end.
			atRow = ':last';
		} else if (atRow == true) {//if atRow is boolean, then lets add it just after the currently selected row.
			var loc = jS.getTdLocation(jS.obj.cell());
			atRow = ':eq(' + (loc[0] - 1) + ')';
		} else {
			//If atRow is a number, lets add it at that row
			atRow = ':eq(' + (atRow - 1) + ')';
		}
		
		jS.cellEditAbandon();
		var currentRow = jS.obj.sheet().find('tr' + atRow);
		var newRow = currentRow.clone();
		jQuery('td', newRow)
			.html('')
			.attr('class', '')
			.attr('formula', '')
			.css('background-color', '')
			.removeAttr('function')
			.mousedown(jS.cellOnMouseDown)
			.click(jS.cellOnClick);
		newRow.insertAfter(currentRow);
		
		var currentLeftColumn = jS.obj.barLeft().find('tr' + atRow);
		var newLeftColumn = currentLeftColumn.clone();
		
		jS.themeRoller.newBar(newLeftColumn);
		
		newLeftColumn.find('td')
			.html(parseInt(currentLeftColumn.text()) + 1)
			.removeClass(jS.cl.uiActive)
			.addClass(jS.cl.uiDefault);
		
		jS.log('New row at: ' + (parseInt(currentLeftColumn.text()) + 1));
		
		jS.getResizeControl.height(jQuery(newLeftColumn).find('td'));
		
		newLeftColumn.insertAfter(currentLeftColumn);
		
		if (atRow) {//If atRow equals anything it means that we inserted at a point, because of this we need to update the labels
			jS.obj.barLeft().find('td').each(function(i) {
				jQuery(this).text((i + 1));
			});
		}
		//Fix for safari
		jS.barHeightSync();
	},
	addColumn: function(atColumn) {
		if (!atColumn) {
			//if atColumn has no value, lets just add it to the end.
			atColumn = ':last';
		} else if (atColumn == true) {
			//if atColumn is boolean, then lets add it just after the currently selected row.
			var loc = jS.getTdLocation(jS.obj.cell());
			atColumn = ':eq(' + (loc[1] - 1) + ')';
		} else {
			//If atColumn is a number, lets add it at that row
			atColumn = ':eq(' + (atColumn - 1) + ')';
		}

		jS.cellEditAbandon();
		var currentIndex = cE.columnLabelIndex(jS.obj.barTop().find('td' + atColumn).html());
		var newLabel = cE.columnLabelString(currentIndex + 1);
		jS.log('New Column: ' + currentIndex + ', ' + newLabel);
		//Add column heads first
		var col = jS.obj.barTop().find('col' + atColumn);
		col.clone()
			.insertAfter(col)
			.width(jS.s.newColumnWidth);
		
		var td = jS.obj.barTop().find('td' + atColumn);
		jS.getResizeControl.width(td.clone()
			.html(newLabel)
			.insertAfter(td));
		
		//Add column heads first
		jS.obj.sheet().find('col' + atColumn).clone()
			.insertAfter('#' + jS.id.sheet + ' col' + atColumn)
			.width(jS.s.newColumnWidth);
		
		//Add new spreadsheet column to top
		var newCell = '<td></td>';
		var j = 0;
		jS.obj.sheet().find('tr').each(function(i) {
			jQuery(this).find('td' + atColumn).after(newCell);
			j++;
		});
		jS.log('Sheet length: ' + j);
		jS.obj.pane().find('tr td')
			.unbind('click')
			.mousedown(jS.cellOnMouseDown)
			.click(jS.cellOnClick);
		
		if (atColumn) {//If atColumn equals anything it means that we inserted at a point, because of this we need to update the labels
			jS.obj.barTop().find('td').each(function(i) {
				jQuery(this).text(cE.columnLabelString(i + 1));
			});
		}
		//Fix for safari
		jS.barHeightSync();
	},
	deleteRow: function() {
		if (jS.obj.cell()[0]) {
			var v = confirm("Are you sure that you want to delete that row? Fomulas will not be updated.");
			if (v) {
				var loc = jS.getTdLocation(jS.obj.cell());
				loc = loc[0] - 1;
				jS.obj.barLeftParent().find('td').eq(loc).parent().remove();
				jS.obj.sheet().find('tr').eq(loc).remove();
				jS.obj.formula().val('');
				
				jS.refreshLabelsRows();
			}
		}
	},
	deleteColumn: function() {
		if (jS.obj.cell()[0]) {
			var v = confirm("Are you sure that you want to delete that column? Fomulas will not be updated.");
			if (v) {
				var loc = jS.getTdLocation(jS.obj.cell());
				loc = loc[1] - 1;

				jS.obj.barTop().find('td').eq(loc).remove();
				jS.obj.barTop().find('colgroup col').eq(loc).remove();
				jS.obj.sheet().find('colgroup col').eq(loc).remove();
				jS.obj.sheet().find('tr').each(function(i) {
						jQuery(this).find('td').eq(loc).remove();
				});
				
				jS.obj.formula().val('');
				
				var w = jS.refreshLabelsColumns();
				
				jS.obj.sheet().width(w);
				jS.obj.barTop().width(w);
			}
		}
	},
	sheetTitle: function(startup, newTitle) {
		var sheetTitle = '';
		if (startup) {
			var sheetTitles = new Array();
			sheetTitles[3] = jS.s.title;
			sheetTitles[2] = jS.obj.sheet().attr('sheettitle');
			sheetTitles[1] = jS.obj.sheet().find('td:contains("sheettitle")').text().split(':')[1];//sheetTitle:SheetTitle
			sheetTitles[0] = 'Untitled Spreadsheet';
			var i = sheetTitles.length;
			while (!sheetTitle) {
				sheetTitle = sheetTitles[i];
				i--;
			}
			return sheetTitle;
		} else {
			if (!newTitle) {
				newTitle = prompt("What would you like the sheet's title to be?", jS.obj.title().text());
			}
			switch (newTitle) {
				case '': newTitle = 'Untitled Spreadsheet'; break;
				case null: newTitle = jS.sheetTitle(true);
			}
			jS.obj.sheet().attr('sheettitle', newTitle);
			jS.obj.title().html(newTitle);
		}
	},
	viewSource: function(pretty) {
		var sheetClone = jS.obj.sheet().clone()[0];

		jS.sheetDecorateRemove(sheetClone);

		if (pretty) {
			var s = jS.HTMLtoPrettySource(sheetClone);
		} else {
			var s = jS.HTMLtoCompactSource(sheetClone);
		}
		var w = window.open();
		w.document.write("<html><body><xmp>" + s + "\n</xmp></body></html>");
		w.document.close();
		return false;
	},
	saveSheet: function() {
		var v = jS.obj.sheet().clone()[0];
		jS.sheetDecorateRemove(v);
		var s = jQuery('<div />').html(v).html();

		jQuery.ajax({
			url: jS.s.urlSave,
			type: jS.s.ajaxSaveType,
			data: 's=' + s,
			dataType: 'html',
			success: function(data) {
				alert('Success! - ' + data);
			}
		});
	},
	HTMLtoCompactSource: function(node) {
		var result = "";
		if (node.nodeType == 1) {
			// ELEMENT_NODE
			result += "<" + node.tagName;
			hasClass = false;
			
			var n = node.attributes.length;
			for (var i = 0, hasClass = false; i < n; i++) {
				var key = node.attributes[i].name;
				var val = node.getAttribute(key);
				if (val) {
					if (key == "contentEditable" && val == "inherit") {
						continue;
						// IE hack.
					}
					if (key == "class") {
						hasClass = true;
						jQuery(val).removeClass(jS.cl.cell);
					}
					
					if (typeof(val) == "string") {
						result += " " + key + '="' + val.replace(/"/g, "'") + '"';
					} else if (key == "style" && val.cssText) {
						result += ' style="' + val.cssText + '"';
					}
				}
			}

			if (node.tagName == "TABLE" && !hasClass) {
				// IE hack, where class doesn't appear in attributes.
				result += ' class="jSheet"';
			}
			if (node.tagName == "COL") {
				// IE hack, which doesn't like <COL..></COL>.
				result += '/>';
			} else {
				result += ">";
				var childResult = "";
				jQuery(node.childNodes).each(function() {
					childResult += jS.HTMLtoCompactSource(this);
				});
				result += childResult;
				result += "</" + node.tagName + ">";
			}

		} else if (node.nodeType == 3) {
			// TEXT_NODE
			result += node.data.replace(/^\s*(.*)\s*$/g, "$1");
		}
		return result;
	},
	HTMLtoPrettySource: function(node, prefix) {
		if (!prefix) {
			prefix = "";
		}
		var result = "";
		if (node.nodeType == 1) {
			// ELEMENT_NODE
			result += "\n" + prefix + "<" + node.tagName;
			var n = node.attributes.length;
			for (var i = 0; i < n; i++) {
				var key = node.attributes[i].name;
				var val = node.getAttribute(key);
				if (val) {
					if (key == "contentEditable" && val == "inherit") {
						continue; // IE hack.
					}
					if (typeof(val) == "string") {
						result += " " + key + '="' + val.replace(/"/g, "'") + '"';
					} else if (key == "style" && val.cssText) {
						result += ' style="' + val.cssText + '"';
					}
				}
			}
			if (node.childNodes.length <= 0) {
				result += "/>";
			} else {
				result += ">";
				var childResult = "";
				var n = node.childNodes.length;
				for (var i = 0; i < n; i++) {
					childResult += jS.HTMLtoPrettySource(node.childNodes[i], prefix + "  ");
				}
				result += childResult;
				if (childResult.indexOf('\n') >= 0) {
					result += "\n" + prefix;
				}
				result += "</" + node.tagName + ">";
			}
		} else if (node.nodeType == 3) {
			// TEXT_NODE
			result += node.data.replace(/^\s*(.*)\s*$/g, "$1");
		}
		return result;
	},
	barAdjustor: function() {
		var jSheetScroll = jS.obj.pane();
		/*
		jS.obj.barTop().animate({
			'left': '-' + jSheetScroll.scrollLeft()},
			50);
		jS.obj.barLeft().animate({
			'top': '-' + jSheetScroll.scrollTop()},
			50);
		*/
		jS.obj.barTop().css('left', '-' + jSheetScroll.scrollLeft());
		jS.obj.barLeft().css('top', '-' + jSheetScroll.scrollTop());
		window.setTimeout('jS.barAdjustor()', 5);
	},
	barHeightSync: function() {
		if (jQuery.browser.safari) {//Safari & Chrome have an odd issue with resize, this should fix it. 
			jS.obj.barLeft().find('tr').each(function(i) {
				var h = jS.attr.height(jQuery(this).find('td:first'));
				if (!h) {
					h = jQuery(this).find('td:first').attr('height');
				}
				jS.obj.sheet().find('tr').eq(i).find('td:first').attr('height', h).height(h);
				jS.obj.sheet().find('tr').eq(i).attr('height', h).height(h);
			});
		}
	},
	followMe: function() {
		if (jS.s.urlScrollTo) {
			jS.obj.pane().stop().scrollTo(jS.obj.cell(), {
				margin: true,
				axis: 'xy',
				duration: 800,
				offset:   -jS.s.height/3
			});
		}
	},
	count: {
		rows: function() {
			return parseInt(jQuery.trim(jS.obj.barLeft().find('tr:last').text()));
		},
		columns: function() {
			return parseInt(jS.columnLabelIndex(jQuery.trim(jS.obj.barTop().find('td:last').text())));
		}
	},
	openSheet: function(url, size) {
		function UI() {
			jS.makeBarItemTop();
			jS.makeBarItemLeft();
			jS.sheetTitle(true);
			
			if (jS.s.editable) {
				jS.obj.pane().find('td').mousedown(jS.cellOnMouseDown).click(jS.cellOnClick);
			}
			
			if (!jS.s.calcOff) {
				jS.calc(jS.obj.tableBody());
			}
			
			jS.themeRoller.start();
		}
		if (confirm("Are you sure you want to open a different sheet?  All unsaved changes will be lost.")) {
			if (!size) {
				jS.cellEditAbandon();
				jS.obj.pane().load(url, function() {
					UI();
				});
			} else {
				jS.obj.pane().html(jS.buildSheet(size));
				UI();
			}
		}
	},
	newSheet: function() {
		jS.openSheet('', prompt("What size would you like to make your spreadsheet? Example: '5x10' creates a sheet that is 5 columns by 10 rows."));
	},
	importRow: function(rowArray) {
		jS.addRow();

		var error = "";
		jS.obj.sheet().find('tr:last td').each(function(i) {
			jQuery(this).removeAttr('formula');
			try {
				//To test this, we need to first make sure it's a string, so converting is done by adding an empty character.
				if ((rowArray[i] + '').charAt(0) == "=") {
					jQuery(this).attr('formula', rowArray[i]);					
				} else {
					jQuery(this).html(rowArray[i]);
				}
			} catch(e) {
				//We want to make sure that is something bad happens, we let the user know
				error += e + ';\n';
			}
		});
		
		if (error) {//Show them the errors
			alert(error);
		}
		//Let's recalculate the sheet just in case
		jS.calc(jS.obj.sheet());
	},
	importColumn: function(columnArray) {
		jS.addColumn();

		var error = "";
		jS.obj.sheet().find('tr').each(function(i) {
			var o = jQuery(this).find('td:last');
			try {
				//To test this, we need to first make sure it's a string, so converting is done by adding an empty character.
				if ((columnArray[i] + '').charAt(0) == "=") {
					o.attr('formula', columnArray[i]);					
				} else {
					o.html(columnArray[i]);
				}
			} catch(e) {
				//We want to make sure that is something bad happens, we let the user know
				error += e + ';\n';
			}
		});
		
		if (error) {//Show them the errors
			alert(error);
		}
		//Let's recalculate the sheet just in case
		jS.calc(jS.obj.sheet());
	},
	buildSheet: function(size) {
		var sheetSize;
		if (!size) {
			sheetSize = jS.s.buildSheet.toLowerCase().split('x');
		} else {
			sheetSize = size.toLowerCase().split('x');
		}
		var columnsCount = parseInt(sheetSize[0]);
		var rowsCount = parseInt(sheetSize[1]);
		var columnWidth = jS.s.newColumnWidth;
		
		//Create elements before loop to make it faster.
		var newSheet = jQuery('<table border="1px" class="' + jS.cl.sheet + '" id="' + jS.id.sheet + '"><colgroup /><tbody><tr /></tbody></table>');
		var standardCol = '<col width="' + columnWidth + '" style="width: ' + columnWidth + '" />';
		var standardTd = '<td />';
		
		//Using -- is many times faster than ++
		for (var i = columnsCount; i >= 1; i--) {
			jQuery(newSheet).find('colgroup').prepend(standardCol);
			jQuery(newSheet).find('tbody tr')
				.prepend(standardTd)
				.height(jS.s.colMargin);
		}
		var row = jQuery('<div />').html(jQuery(newSheet).find('tbody tr')).html();
		for (var i = rowsCount; i >= 1; i--) {
			jQuery(newSheet).find('tbody').prepend(row);
		}
		jQuery(newSheet).width(jS.attr.width(jQuery(newSheet).find('tr:first')));
		return newSheet;
	},
	sheetSyncSize: function() {
		var h = jS.s.height;
		if (!h) {
			h = 400; //Height really needs to be set by the parent
		}
		var w = jS.s.width;
		
		if (jS.s.editable) {
			jQuery.each(jQuery.browser, function(i, val) {
				if((i=="mozilla" && val == true) || (i=="safari" && val == true)) {
					if (jS.s.urlMenu) {
						h = h - 89;
					} else {
						h = h - 87;
					}
					w = w - 30;
				} else if (i=="msie" && val == true) {
					if (jS.s.urlMenu) {
						h = h - 86;
					} else {
						h = h - 66;
					}
					
					w = w - 38;
					jS.obj.pane().height(h - 30);
				}
			});
			
		} else {
			jQuery.each(jQuery.browser, function(i, val) {
				if((i=="mozilla" && val == true) || (i=="safari" && val == true)) {
					h = h - 31;
					w = w - 30;
				} else if (i=="msie" && val == true) {
					h = h - 31;
					w = w - 38;
				}
			});
		}
		
		jS.obj.pane()
			.height(h)
			.width(w);
			
		jS.obj.barLeftParent()
			.height(h);
		
		jS.obj.barTopParent()
			.height(jS.s.colMargin)
			.width(w);
		jS.obj.parent().find('col:eq(1)').width(w);
		jS.obj.barCorner().height(jS.obj.barTopParent().height());
		jS.themeRoller.start();
	},
	barResizer: function(evt, target, type) {
		//Resize Column & Row & Prototype functions are private under class jSheet
		var columnResizer = {
			xyDimension: 0,
			getIndex: function(td) {
				return parseInt(cE.columnLabelIndex(jQuery.trim(jQuery(td).text()))) - 1;
			},
			get: function(obj, i) {
				return jQuery(obj).find('col').eq(i);
			},
			getSize: function(obj) {
				var v = jS.attr.width(obj);
				return v;
			},
			setSize: function(obj, v) {
				jQuery(obj).width(v);
			}
		};

		var rowResizer = {
			xyDimension: 1,
			getIndex: function(td) {
				return parseInt(jQuery.trim(jQuery(td).text())) - 1;
			},
			get: function(obj, i) {
				return jQuery(obj).find('tr').eq(i);
			},
			getSize: function(obj) {
				var v = jS.attr.height(obj);
				return v;
			},
			setSize: function(obj, v) {
				jQuery(obj).height(v);
			}
		};
		
		//Lets fix the resizers so that they are also compatible with safari...man this bug is irritating
		if (jQuery.browser.safari) {
			columnResizer.getSize = function(obj) {
				var v = jS.attr.width(obj);
				var vBackup = jQuery(obj).attr('width');
				return (v ? v : vBackup);
			};
			columnResizer.setSize = function(obj, v) {
				jQuery(obj)
					.css('width', v)
					.attr('width', v);
			};
			rowResizer.get = function(obj, i) {
				return jQuery(obj).find('tr').eq(i).find('td:first');
			};
			rowResizer.getSize = function(obj) {
				var v = jS.attr.height(obj);
				return v;
			};
			rowResizer.setSize = function(obj, v) {
				jQuery(obj)
					.css('height', v)
					.attr('height', v);
			};
		}
		
		var o;
		if (type == 'row') {
			o = rowResizer;
		} else {
			o = columnResizer;
		}
		var barResizer = {
			start: function(evt) {
				//Finish up last editted cell.
				jS.cellEditDone();
				jS.cellEditAbandon();
				
				jS.log('start resize');
				var srcTable = jQuery(target).parent().parent().parent();
				var tdPageXY = jS.cellFindXY(target)[o.xyDimension];
				var eventPOS = [evt.pageX, evt.pageY][o.xyDimension];
				var i = o.getIndex(target);
				var srcBarSize = o.getSize(o.get(srcTable, i));
				var edgeDelta = parseInt(eventPOS) - (parseInt(tdPageXY) + parseInt(srcBarSize));
				
				if (Math.abs(edgeDelta) <= 35 || jQuery.browser.safari) {//We do this because the registered height is lost after the first resize on Safari and Chrome
					//Add a little Themeroller
					jS.themeRoller.barObj(target);
					
					
					o.dragInfo = {
						startXY: [evt.pageX, evt.pageY],
						srcTable: srcTable,
						srcItem: o.get(srcTable, i),
						dstItem: o.get(jS.obj.sheet(), i),
						edgeDelta: edgeDelta,
						startSizes: o.getSize(o.get(srcTable, i)),
						i: i
					};
					jQuery(document)
						.mousemove(barResizer.drag)
						.mouseup(barResizer.stop);
					
					if (o.xyDimension) {
						jS.obj.barLeft().find('tr').eq(o.dragInfo.i).outerHeight(o.dragInfo.startSizes);
						jS.obj.barLeft().find('tr').eq(o.dragInfo.i).find('td').css('height', '').attr('height', '');
					}
				}
			},
			drag: function(evt) {
				var target = this;
				if (o.dragInfo.srcTable) {
					var newSize = o.dragInfo.startSizes;
					// Make a copy.
					var v = parseInt([evt.pageX, evt.pageY][o.xyDimension]) - parseInt(o.dragInfo.startXY[o.xyDimension]) + parseInt(o.dragInfo.startSizes);
					var sizeTotal = 0
					if (v > 0) {// A non-zero minimum size saves many headaches.
						newSize = Math.max(v - sizeTotal, 3);
					}
					
					sizeTotal += newSize;
					o.setSize(o.dragInfo.srcItem, newSize);
					
					if (!o.xyDimension) {
						o.setSize(o.dragInfo.srcTable, sizeTotal);
					}
					return false;
				}
			},
			stop: function(evt) {
				var target = this;
				if (o.dragInfo.srcTable) {
					var size = o.getSize(o.dragInfo.srcItem);
					var parentSize = o.getSize(o.dragInfo.srcTable);
					//Resize sheet column / row
					o.setSize(o.dragInfo.dstItem, size);
					
					//Sync sheet width with topbar width
					if (!o.xyDimension) {//columns
						o.setSize(jS.obj.sheet(), parentSize);
					}
					
					//If we've sized it too small, and it's a row,it will resize.
					if (o.xyDimension) {//rows
						o.setSize(o.dragInfo.srcItem, o.getSize(o.dragInfo.dstItem));
						//jS.barHeightSync();
						
						//This is a height fix for IE and Safari
						var h1 = jS.obj.barLeft().find('tr').eq(o.dragInfo.i).find('td').attr('height');
						var h2 = jS.obj.barLeft().find('tr').eq(o.dragInfo.i).find('td').css('height');
						h = (h1 ? h1 : h2);
						jS.obj.barLeft().find('tr').eq(o.dragInfo.i).outerHeight('height', h);
					}
				}
				o.dragInfo = {};
				jQuery(document)
					.unbind('mousemove')
					.unbind('mouseup');
				jS.log('stop resizing');
				//Remove themeRoller selection
				jS.themeRoller.clearBar();
			}
		}
		barResizer.start(evt);
	},
	cellFindXY: function(obj) {
		var curleft = curtop = 0;
		obj = jQuery(obj)[0];
		if (obj.offsetParent) {
			do {
				curleft += obj.offsetLeft;
				curtop += obj.offsetTop;
			} while (obj = obj.offsetParent);	
		}
		return [curleft,curtop];
	},
	cellFind: function(v) {
		if(!v) {
			v = prompt("What are you looking for in this spreadsheet?");
		}
		if (v) {//We just do a simple uppercase/lowercase search.
			var obj = jS.obj.sheet().find('td:contains("' + v + '")');
			
			if (obj.length < 1) {
				obj = jS.obj.sheet().find('td:contains("' + v.toLowerCase() + '")');
			}
			
			if (obj.length < 1) {
				obj = jS.obj.sheet().find('td:contains("' + v.toUpperCase() + '")');
			}
			
			obj = obj.eq(0);
			if (obj.length > 0) {
				obj.click();
			} else {
				alert('No results found.');
			}
		}
	},
	cellSetActiveMulti: function(evt) {
		var dragInfo = {
			startRow: evt.target.parentNode.rowIndex,
			startColumn: evt.target.cellIndex
		};//These are the events used to selected multiple rows.
		jS.obj.sheet()
			.mousemove(function(evt) {
				dragInfo.endRow = evt.target.parentNode.rowIndex;
				dragInfo.endColumn = evt.target.cellIndex;
				for (var i = dragInfo.startRow; i <= dragInfo.endRow; i++) {
					for (var j = dragInfo.startColumn; j <= dragInfo.endColumn; j++) {
						var o = jS.getTd(jS.obj.tableBody(), i + 1, j + 1);
						jQuery(o)
							.addClass(jS.cl.uiCell)
							.addClass(jS.cl.uiCellHighlighted);
					}
				}
				//This is used for debugging
				//jS.obj.formula().val(dragInfo.startRow + ',' + dragInfo.startColumn + ',' + dragInfo.endRow + ',' + dragInfo.endColumn);
				
				//There was some difficulty making the mouseover not select everything, this fixed it
				if (jQuery(evt.target).attr('id') == 'tempText') {
					return true;
				} else { 
					return false;
				}
			})
			.mouseup(function() {
				jS.obj.sheet()
					.unbind('mousemove')
					.unbind('mouseup');
			});
		return false;
	},
	cellSetActiveMultiColumn: function(i) {
		jS.obj.sheet().find('tr').each(function() {
			var o = jQuery(this).find('td').eq(i);
			jQuery(o)
				.addClass(jS.cl.uiCell)
				.addClass(jS.cl.uiCellHighlighted);
		});
		jS.themeRoller.barTop(i);
	},
	cellSetActiveMultiRow: function(i) {
		jS.obj.sheet().find('tr').eq(i).find('td')
			.addClass(jS.cl.uiCell)
			.addClass(jS.cl.uiCellHighlighted);
		jS.themeRoller.barLeft(i);
	},
	sheetClearActive: function() {
		jS.obj.formula().val('');
		jS.obj.cell().removeClass(jS.cl.cell);
		jS.obj.barSelected().removeClass(jS.cl.barSelected);
	},
	getIndexTr: function(row) {
		// The row is 1-based.
		return row - 1;
		// A indexTr is 0-based.
	},
	getIndexTd: function(col) {
		return col - 1;
		// A indexTd is 0-based.
	},
	getTd: function(tableBody, row, col, indexTr, indexTd) {
		// The row and col are 1-based.
		if (!indexTr) {
			// The indexTr and indexTd are 0-based.
			indexTr = jS.getIndexTr(row);
		}
		if (tableBody.rows) {
			var tr = tableBody.rows[indexTr];
			if (tr) {
				if (!indexTd) {
					indexTd = jS.getIndexTd(col);
				}
				return tr.cells[indexTd];
			}
		}
		return null;
	},
	getTdLocation: function(td) {
		var col = jQuery(td).attr('cellIndex') + 1;
		var row = jQuery(td).parent().attr('rowIndex') + 1;
		return [row--, col--];
		// The row and col are 1-based.
	},
	tableCellProvider: function(tableBodyId) {
		this.tableBodyId = tableBodyId;
		this.cells = {};
	},
	tableCell: function(tableBody, row, col) {
		this.tableBodyId = tableBody.id;
		this.row = row;
		this.col = col;
		this.indexTr = jS.getIndexTr(row);
		this.indexTd = jS.getIndexTd(col);
		this.value = jS.EMPTY_VALUE;
		
		//this.prototype = new cE.cell();
	},
	EMPTY_VALUE: {},
	log: function(msg) {
		switch (jS.s.log) {
			case true:
				jS.obj.log().prepend(msg + '; <br />\n');
				break;
		}
	}
}

jS.tableCellProvider.prototype = {
	getCell: function(row, col) {
		if (typeof(col) == "string") {
			col = cE.columnLabelIndex(col);
		}
		var key = row + "," + col;
		var cell = this.cells[key];
		if (!cell) {
			var tableBody = jS.obj.tableBody();
			if (tableBody) {
				var td = jS.getTd(tableBody, row, col);
				if (td) {
					cell = this.cells[key] = new jS.tableCell(tableBody, row, col);
				}
			}
		}
		return cell;
	},
	getNumberOfColumns: function(row) {
		var tableBody = jS.obj.tableBody();
		if (tableBody) {
			var tr = tableBody.rows[jS.getIndexTr(row)];
			if (tr) {
				return tr.cells.length;
			}
		}
		return 0;
	},
	toString: function() {
		result = "";
		jS.obj.sheet().find('tr').each(function() {
			result += this.innerHTML.replace(/\n/g, "") + "\n";
		});
		return result;
	}
};

jS.tableCell.prototype = {
	getTd: function() {
		return jS.getTd(jS.obj.tableBody(), this.row, this.col, this.indexTr, this.indexTd);
	},
	setValue: function(v, e) {
		this.error = e;
		this.value = v;
		this.getTd().innerHTML = (v ? v: "");

	},
	getValue: function() {
		var v = this.value;
		if (v === jS.EMPTY_VALUE && !this.getFormula()) {
			v = this.getTd().innerHTML;
			v = this.value = (v.length > 0 ? cE.parseFormulaStatic(v) : null);

		}
		return (v === jS.EMPTY_VALUE ? null: v);
	},
	getFormat: function() {
		return jQuery(this.getTd()).attr("format");
	},
	setFormat: function(v) {
		jQuery(this.getTd()).attr("format", v);
	},
	getFormulaFunc: function() {
		return this.formulaFunc;
	},
	setFormulaFunc: function(v) {
		this.formulaFunc = v;
	},
	getFormula: function() {
		return jQuery(this.getTd()).attr('formula');
	},
	setFormula: function(v) {
		if (v && v.length > 0) {
			jQuery(this.getTd()).attr('formula', v);
		} else {
			jQuery(this.getTd()).removeAttr('formula');
		}
	}
};

var key = {
	BACKSPACE: 8,
	CAPS_LOCK: 20,
	COMMA: 188,
	CONTROL: 17,
	DELETE: 46,
	DOWN: 40,
	END: 35,
	ENTER: 13,
	ESCAPE: 27,
	HOME: 36,
	INSERT: 45,
	LEFT: 37,
	NUMPAD_ADD: 107,
	NUMPAD_DECIMAL: 110,
	NUMPAD_DIVIDE: 111,
	NUMPAD_ENTER: 108,
	NUMPAD_MULTIPLY: 106,
	NUMPAD_SUBTRACT: 109,
	PAGE_DOWN: 34,
	PAGE_UP: 33,
	PERIOD: 190,
	RIGHT: 39,
	SHIFT: 16,
	SPACE: 32,
	TAB: 9,
	UP: 38
};

var cE = jQuery.calculationEngine = {
	TEST: {},
	ERROR: "#VALUE!",
	cFN: {//cFN = compiler functions, usually mathmatical
		SUM: 	function(x, y) { return x + y; },
		MAX: 	function(x, y) { return x > y ? x: y; },
		MIN: 	function(x, y) { return x < y ? x: y; },
		COUNT: 	function(x, y) { return (y != null) ? x + 1: x; },
		CLEAN: function(v) {
			if (typeof(v) == 'string') {
				v = v.replace(cE.RE_AMP, '&')
						.replace(cE.RE_NBSP, ' ')
						.replace(/\n/g,'')
						.replace(/\r/g,'');
			}
			return v;
		}
	},
	fn: {//fn = standard functions used in cells
		HTML: function(v) {
			return jQuery(v);
		},
		IMG: function(v) {
			return jQuery('<img src="' + v + '" style="border: ;"/>');
		},
		AVERAGE:	function(values) { 
			var arr = cE.foldPrepare(values, arguments);
			return cE.fn.SUM(arr) / cE.fn.COUNT(arr); 
		},
		AVG: 		function(values) { 
			return cE.fn.AVERAGE(values);
		},
		COUNT: 		function(values) { return cE.fold(cE.foldPrepare(values, arguments), cE.cFN.COUNT, 0); },
		SUM: 		function(values) { return cE.fold(cE.foldPrepare(values, arguments), cE.cFN.SUM, 0, true); },
		MAX: 		function(values) { return cE.fold(cE.foldPrepare(values, arguments), cE.cFN.MAX, Number.MIN_VALUE, true); },
		MIN: 		function(values) { return cE.fold(cE.foldPrepare(values, arguments), cE.cFN.MIN, Number.MAX_VALUE, true); },
		ABS	: 		function(v) { return Math.abs(cE.fn.N(v)); },
		CEILING: 	function(v) { return Math.ceil(cE.fn.N(v)); },
		FLOOR: 		function(v) { return Math.floor(cE.fn.N(v)); },
		INT: 		function(v) { return Math.floor(cE.fn.N(v)); },
		ROUND: 		function(v) { return Math.round(cE.fn.N(v)); },
		RAND: 		function(v) { return Math.random(); },
		RND: 		function(v) { return Math.random(); },
		TRUE: 		function() { return true; },
		FALSE: 		function() { return false; },
		NOW: 		function() { return new Date ( ); },
		TODAY: 		function() { return Date( Math.floor( new Date ( ) ) ); },
		DAYSFROM: 	function(year, month, day) { 
			return Math.floor( (new Date() - new Date (year, (month - 1), day)) / 86400000);
		},
		IF:			function(v, t, f){
			t = cE.cFN.CLEAN(t);
			f = cE.cFN.CLEAN(f);
			
			try { v = eval(v); } catch(e) {};
			try { t = eval(t); } catch(e) {};
			try { t = eval(t); } catch(e) {};

			if (v == 'true' || v == true || v > 0 || v == 'TRUE') {
				return t;
			} else {
				return f;
			}
		},
		FIXED: 		function(v, decimals, noCommas) { 
			if (decimals == null) {
				decimals = 2;
			}
			var x = Math.pow(10, decimals);
			var s = String(Math.round(cE.fn.N(v) * x) / x); 
			var p = s.indexOf('.');
			if (p < 0) {
				p = s.length;
				s += '.';
			}
			for (var i = s.length - p - 1; i < decimals; i++) {
				s += '0';
			}
			if (noCommas == true) {// Treats null as false.
				return s;
			}
			var arr	= s.replace('-', '').split('.');
			var result = [];
			var first  = true;
			while (arr[0].length > 0) { // LHS of decimal point.
				if (!first) {
					result.unshift(',');
				}
				result.unshift(arr[0].slice(-3));
				arr[0] = arr[0].slice(0, -3);
				first = false;
			}
			if (decimals > 0) {
				result.push('.');
				var first = true;
				while (arr[1].length > 0) { // RHS of decimal point.
					if (!first) {
						result.push(',');
					}
					result.push(arr[1].slice(0, 3));
					arr[1] = arr[1].slice(3);
					first = false;
				}
			}
			if (v < 0) {
				return '-' + result.join('');
			}
			return result.join('');
		},
		TRIM:		function(v) { 
			if (typeof(v) == 'string') {
				v = jQuery.trim(v);
			}
			return v;
		},
		HYPERLINK: function(link, name) {
			return jQuery('<a href="' + link + '" target="_new">' + name + '</a>');
		},
		DOLLAR: 	function(v, decimals, symbol) { 
			if (decimals == null) {
				decimals = 2;
			}
			if (symbol == null) {
				symbol = '$';
			}
			var r = cE.fn.FIXED(v, decimals, false);
			if (v >= 0) {
				return symbol + r; 
			}
			return '-' + symbol + r.slice(1);
		},
		VALUE: 		function(v) { return parseFloat(v); },
		N: 			function(v) { if (v == null) {return 0;}
						  if (v instanceof Date) {return v.getTime();}
						  if (typeof(v) == 'object') {v = v.toString();}
						  if (typeof(v) == 'string') {v = parseFloat(v.replace(cE.RE_N, ''));}
						  if (isNaN(v))		   {return 0;}
						  if (typeof(v) == 'number') {return v;}
						  if (v == true)			 {return 1;}
						  return 0; },
		PI: 		function() { return Math.PI; },
		POWER: 		function(x, y) {
			return Math.pow(x, y);
		}
	},
	calc: function(cellProvider, context, startFuel) {
		// Returns null if all done with a complete calc() run.
		// Else, returns a non-null continuation function if we ran out of fuel.  
		// The continuation function can then be later invoked with more fuel value.
		// The fuelStart is either null (which forces a complete calc() to the finish) 
		// or is an integer > 0 to slice up long calc() runs.  A fuelStart number
		// is roughly matches the number of cells to visit per calc() run.
		var calcState = { 
			cellProvider: cellProvider, 
			context	 : (context != null ? context: {}),
			row		 : 1, 
			col		 : 1, 
			done		: false,
			stack		: [],
			calcMore: function(moreFuel) {
				calcState.fuel = moreFuel;
				return cE.calcLoop(calcState);
			}
		};
		return calcState.calcMore(startFuel);
	},
	cell: function() {
		prototype: {// Cells don't know their coordinates, to make shifting easier.
			getError = function()	 { return this.error; },
			getValue = function()	 { return this.value; },
			setValue = function(v, e) { this.value = v; this.error = e; },
			getFormula	 = function()  { return this.formula; },	 // Like "=1+2+3" or "'hello" or "1234.5"
			setFormula	 = function(v) { this.formula = v; },
			getFormulaFunc = function()  { return this.formulaFunc; },
			setFormulaFunc = function(v) { this.formulaFunc = v; },
			toString = function() { return "Cell:[" + this.getFormula() + ": " + this.getValue() + ": " + this.getError() + "]"; }
		}
	}, // Prototype setup is later.
	columnLabelIndex: function(str) {
		// Converts A to 1, B to 2, Z to 26, AA to 27.
		var num = 0;
		for (var i = 0; i < str.length; i++) {
			var digit = str.charCodeAt(i) - 65 + 1;	   // 65 == 'A'.
			num = (num * 26) + digit;
		}
		return num;
	},
	parseLocation: function(locStr) { // With input of "A1", "B4", "F20",
		if (locStr != null &&								  // will return [1,1], [4,2], [20,6].
			locStr.length > 0 &&
			locStr != "&nbsp;") {
			for (var firstNum = 0; firstNum < locStr.length; firstNum++) {
				if (locStr.charCodeAt(firstNum) <= 57) {// 57 == '9'
					break;
				}
			}
			return [ parseInt(locStr.substring(firstNum)),
					 cE.columnLabelIndex(locStr.substring(0, firstNum)) ];
		}
		return null;
	},
	columnLabelString: function(index) {
		// The index is 1 based.  Convert 1 to A, 2 to B, 25 to Y, 26 to Z, 27 to AA, 28 to AB.
		// TODO: Got a bug when index > 676.  675==YZ.  676==YZ.  677== AAA, which skips ZA series.
		//	   In the spirit of billg, who needs more than 676 columns anyways?
		var b = (index - 1).toString(26).toUpperCase();   // Radix is 26.
		var c = [];
		for (var i = 0; i < b.length; i++) {
			var x = b.charCodeAt(i);
			if (i <= 0 && b.length > 1) {				   // Leftmost digit is special, where 1 is A.
				x = x - 1;
			}
			if (x <= 57) {								  // x <= '9'.
				c.push(String.fromCharCode(x - 48 + 65)); // x - '0' + 'A'.
			} else {
				c.push(String.fromCharCode(x + 10));
			}
		}
		return c.join("");
	},
	RE_N: /[\$,\s]/g,
	RE_REF_CELL: /\$?([a-zA-Z]+)\$?([0-9]+)/g,
	RE_REF_RANGE: /\$?([a-zA-Z]+)\$?([0-9]+):\$?([a-zA-Z]+)\$?([0-9]+)/g,
	parseFormula: function(formula, dependencies, calcState) { // Parse formula (without "=" prefix) like "123+SUM(A1:A6)/D5" into JavaScript expression string.
		var nrows = null;
		var ncols = null;
		if (calcState != null &&
			calcState.cellProvider != null) {
			nrows = calcState.cellProvider.nrows;
			ncols = calcState.cellProvider.ncols;
		}
		var arrayReferencesFixed = formula.replace(cE.RE_REF_RANGE, 
			function(ignored, startColStr, startRowStr, endColStr, endRowStr) {
				var res = [];
				var startCol = cE.columnLabelIndex(startColStr.toUpperCase());
				var startRow = parseInt(startRowStr);
				var endCol   = cE.columnLabelIndex(endColStr.toUpperCase());
				var endRow   = parseInt(endRowStr);
				if (ncols != null) {
					endCol = Math.min(endCol, ncols);
				}
				if (nrows != null) {
					endRow = Math.min(endRow, nrows);
				}
				for (var r = startRow; r <= endRow; r++) {
					for (var c = startCol; c <= endCol; c++) {
						res.push(cE.columnLabelString(c) + r);
					}
				}
				return "[" + res.join(",") + "]";
			}
		);
		var result = arrayReferencesFixed.replace(cE.RE_REF_CELL, 
			function(ignored, colStr, rowStr) {
				colStr = colStr.toUpperCase();
				if (dependencies != null) {
					dependencies[colStr + rowStr] = [parseInt(rowStr), cE.columnLabelIndex(colStr)]; 
				}
				return "(getCell((" + rowStr + "),\'" + colStr + "\').getValue())";
			}
		);
		return result;
	},	
	parseFormulaStatic: function(formula) { // Parse static formula value like "123.0" or "hello" or "'hello world" into JavaScript value.
		if (formula == null) {
			return null;
		}
		var formulaNum = formula.replace(cE.RE_N, '');
		var value = parseFloat(formulaNum);
		if (isNaN(value)) {
			value = parseInt(formulaNum);
		}
		if (isNaN(value)) {
			value = (formula.charAt(0) == "\'" ? formula.substring(1): formula);
		}
		return value;
	},
	calcLoop: function(calcState) {
		with (calcState) {
			if (done == true) {
				return null;
			}
			while (fuel == null || fuel > 0) {
				if (stack.length > 0) {
					var workFunc = stack.pop();
					if (workFunc != null) {
						workFunc(calcState);
					}
				} else if (cellProvider.formulaCells != null) {
					if (cellProvider.formulaCells.length > 0) {
						var loc = cellProvider.formulaCells.shift();
						cE.visitCell(calcState, loc[0], loc[1]);
					} else {
						done = true;
						return null;
					}					
				} else {
					if (cE.visitCell(calcState, row, col) == true) {
						done = true;
						return null;
					}

					if (col >= cellProvider.getNumberOfColumns(row)) {
						row = row + 1;
						col = 1;
					} else {
						col = col + 1; // Sweep through columns first.
					}
				}
				
				if (fuel != null) {
					fuel -= 1;
				}
			}
		}
		return calcState.calcMore;
	},
	visitCell: function(calcState, r, c) { // Returns true if done with all cells.
		with (calcState) {
			var cell = cellProvider.getCell(r, c);
			if (cell == null) {
				return true;
			}

			var value = cell.getValue();
			if (value == null) {
				var formula = cell.getFormula();
				if (formula != null) {
					var firstChar = formula.charAt(0);
					if (firstChar == '=') {
						var formulaFunc = cell.getFormulaFunc();
						if (formulaFunc == null ||
							formulaFunc.formula != formula) {
							formulaFunc = null;
							try {
								var dependencies = {};
								var body = cE.parseFormula(formula.substring(1), dependencies, calcState);
								formulaFunc = eval(
								"var Calc_spreadsheet_formula = " +
									"function(__CELL_PROVIDER, __CONTEXT, __STD_FUNCS) { " +
										"with (__CELL_PROVIDER) {" + 
											"with (__STD_FUNCS) { " +
												"with (__CONTEXT) { return (" + body + "); }" +
											"}" +
										"}" + 
									"}; " +
									"Calc_spreadsheet_formula"
								);
								formulaFunc.formula	  = formula;
								formulaFunc.dependencies = dependencies;
								cell.setFormulaFunc(formulaFunc);
							} catch (e) {
								//cell.setValue(cE.ERROR, e);
								cell.setValue(e,e);
							}
						}
						if (formulaFunc != null) {
							stack.push(cE.makeFormulaEval(r, c));

							// Push the cell's dependencies, first checking for any cycles. 
							var dependencies = formulaFunc.dependencies;
							for (var k in dependencies) {
								if (dependencies[k] instanceof Array &&
									cE.checkCycles(stack, dependencies[k][0], dependencies[k][1]) == true) {
									cell.setValue(cE.ERROR, "cycle detected");
									stack.pop();
									return false;
								}
							}
							for (var k in dependencies) {
								if (dependencies[k] instanceof Array) {
									stack.push(cE.makeCellVisit(dependencies[k][0], dependencies[k][1]));
								}
							}
						}
					} else {
						cell.setValue(cE.parseFormulaStatic(formula));
					}
				}
			}
		}
		return false;
	},
	makeCellVisit: function(row, col) {
		var func = function(calcState) { return cE.visitCell(calcState, row, col); };
		func.row = row;
		func.col = col;
		return func;
	},
	RE_AMP: /&/g,
	RE_LT: /</g,
	RE_GT: />/g,
	RE_NBSP: /&nbsp;/,
	makeFormulaEval: function(row, col) {
		var func = function(calcState) {
			var cell = calcState.cellProvider.getCell(row, col);
			if (cell != null) {
				var formulaFunc = cell.getFormulaFunc();
				if (formulaFunc != null) {
					try {
						var v = formulaFunc(calcState.cellProvider, calcState.context, cE.fn);
						if (typeof(v) == "string") {
							v = v
								.replace(cE.RE_AMP, '&amp;')
								.replace(cE.RE_LT, '&lt;')
								.replace(cE.RE_GT, '&gt;')
								.replace(cE.RE_NBSP, '&nbps;');
						} else if (typeof(v) == "object"){
							v = jQuery('<div />').html(v).html();
						}
						cell.setValue(v);
					} catch (e) {
						cell.setValue(cE.ERROR + ': ' + e, e);
					}
				}
			}
		}
		func.row = row;
		func.col = col;
		return func;
	},
	checkCycles: function(stack, row, col) {
		for (var i = 0; i < stack.length; i++) {
			var item = stack[i];
			if (item.row != null && item.col != null &&
				item.row == row  && item.col == col) {
				return true;
			}
		}
		return false;
	},
	foldPrepare: function(firstArg, theArguments) { // Computes the best array-like arguments for calling fold().
		if (firstArg != null &&
			firstArg instanceof Object &&
			firstArg["length"] != null) {
			return firstArg;
		}
		return theArguments;
	},
	fold: function(arr, funcOfTwoArgs, result, castToN) {
		for (var i = 0; i < arr.length; i++) {
			result = funcOfTwoArgs(result, (castToN == true ? cE.fn.N(arr[i]): arr[i]));
		}
		return result;
	}
};