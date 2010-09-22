// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id:

$(function () {
	$('#compareSheetsSubmit').click(function() {
		var sheetId = $('#sheetId').val();
		var sheetReadDates = '';
		
		$('input.compareSheet1:checked,input.compareSheet2:checked').each(function() {
			sheetReadDates += $(this).val() + '|';
		});
		
		if (sheetReadDates) {
			window.location = "tiki-history_sheets.php?sheetId=" + sheetId + "&readdate=" + sheetReadDates;
		}
		
		return false;
	});
	
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