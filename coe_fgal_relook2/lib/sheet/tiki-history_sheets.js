// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$:

function compareSheetsSubmitClick(o) {
	var sheetId = $('#sheetId').val();
	var sheetReadDates = '';
	
	$('input.compareSheet1:checked,input.compareSheet2:checked').each(function(i) {
		sheetReadDates += ('idx_' + i + '=') + $(this).val() + '&';
	});
	
	if (sheetReadDates) {
		window.location = "tiki-history_sheets.php?sheetId=" + sheetId + "&" + sheetReadDates;
	}
	
	return false;
}

function compareSheetClick() {
	var set1 = $("input.compareSheet1");
	var set2 = $("input.compareSheet2");
	var set = $(set1).add(set2);
	
	set.removeAttr('disabled');
	
	var state = "before";
	var before;
	var after;
	
	set.each(function(i) {
		
		if (set.eq(i).is(':checked')) {
			switch (state) {
				case "before": state = "middle"; break;
				case "middle": state = "after"; break;
			}
		}
		
		switch (state) {
			case "before": before = (before ? before.add(set.eq(i).filter('.compareSheet2')) : set2.eq(i));
				break;
			case "after": after = (after ? after.add(set.eq(i).filter('.compareSheet1')) : set1.eq(i));
				break;
		}
	});
	
	if (before)
		before.attr('disabled', 'true');
	if (after)
		after.attr('disabled', 'true');
}

function setValuesForCompareSheet(value1, value2) {
	value1 = (value1 ? ":eq(" + value1 + ")" : ":first");
	value2 = (value2 ? ":eq(" + value2 + ")" : ":last");
	
	$("input.compareSheet1").filter(value1).click();
	$("input.compareSheet2").filter(value2).click();
	
	compareSheetClick();
}

$(function () {
	$('#go_fullscreen').toggle(function() {
		$('<div id="tiki_sheet_container_fullscreen" />')
			.css('position', 'absolute')
			.width($(window).width())
			.height($(window).height())
			.css('left', '0px')
			.css('top', '0px')
			.html($('#tiki_sheet_container').children())
			.css('z-index', '999999')
			.prependTo($('body'));
		}, function() {
			$('#tiki_sheet_container')
				.children($('#tiki_sheet_container_fullscreen').children());
			$('#tiki_sheet_container_fullscreen').remove();
		});
});