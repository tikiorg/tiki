/**
 * TikiSheet Client-side grid manipulation for viewing history.
 * By Robert Plummer
 * 2010
 */

$(function () {
	$('#compareSheetsSubmit').click(function() {
		var sheetId = $('#sheetId').val();
		var sheetReadDates = '';
		
		$('input.compareSheet:checked').each(function() {
			sheetReadDates += $(this).val() + '|';
		});
		
		if (sheetReadDates) {
			var newWindow = window.open("tiki-view_sheets.php?sheetId=" + sheetId + "&readdate=" + sheetReadDates, "_blank");
			newWindow.focus();
		}
		
		return false;
	});	
});