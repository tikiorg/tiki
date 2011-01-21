// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$:

function compareSheetsSubmitClick(o) {
	var sheetId = $('#sheetId').val();
	
	var sheetReadDates = 'idx_0=' + $('input.compareSheet1:checked').val() + '&idx_1=' + $('input.compareSheet2:checked').val() + '&';
	window.location = "tiki-history_sheets.php?sheetId=" + sheetId + "&" + sheetReadDates;

	return false;
}

function compareSheetClick() {
	var set1 = $("input.compareSheet1");
	var set2 = $("input.compareSheet2");
	var checked1 = $("input.compareSheet1:checked");
	var checked2 = $("input.compareSheet2:checked");
	var set = set1.add(set2);
	
	set.removeAttr('disabled');
	
	function disable(obj1, objIndex, obj2, after) {
		for (var i = (after ? obj1.index(objIndex) + 1 : 0); i < (after ? obj1.length : obj1.index(objIndex)); i++) {
			obj2.eq(i).attr('disabled', 'true');
		}
	}
	
	disable(set1, checked1, set2, true);
	disable(set2, checked2, set1);
}

function setValuesForCompareSheet(value1, value2) {
	value1 = (value1 ? ":eq(" + value1 + ")" : ":first");
	value2 = (value2 ? ":eq(" + value2 + ")" : ":last");
	
	$("input.compareSheet1").filter(value1).click();
	$("input.compareSheet2").filter(value2).click();
	
	compareSheetClick();
}

function dualFullScreenHelper(reset) {
	var tikiSheets =  $('div.tiki_sheet');
	$($.sheet.instance).each(function(i) {
		var jS = this;
		var tikiSheet = tikiSheets.eq(i);
		if (!reset) {
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
			jS.s.width = jS.sizeOriginal.width;
			jS.s.height = jS.sizeOriginal.height;
		}
		
		this.sheetSyncSize();
	});
}
$(function () {
	$('#go_fullscreen').toggle(function() {
		var parent = $(this).parent().parent();
		
		$('<div id="tiki_sheet_container_fullscreen" style="left: 0px; top: 0px; z-index: 99999; background-color: white;" parentId="' + parent.attr('id') + '" />')
			.css('position', 'absolute')
			.width($(window).width())
			.height($(window).height())
			.html(parent.children())
			.prependTo($('body'));
		
		dualFullScreenHelper();
	}, function() {
		var parent = $(this).parent().parent();
		var parentId = parent.attr('parentId');
		$('#' + parentId)
			.html(parent.children());
		parent.remove();
		
		dualFullScreenHelper(true);
	});
});