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
			var newWindow = window.open("tiki-history_sheets.php?sheetId=" + sheetId + "&readdate=" + sheetReadDates, "_blank");
			newWindow.focus();
		}
		
		return false;
	});
});