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
			var newWindow = window.open("tiki-history_sheets.php?sheetId=" + sheetId + "&readdate=" + sheetReadDates, "_blank");
			newWindow.focus();
		}
		
		return false;
	});
	
	
});

function scrollLocker(obj, I) {
	$($.sheet.instance).each(function(i) {
		this.obj.pane()
			.scrollLeft(obj.scrollLeft())
			.scrollTop(obj.scrollTop());
	});
}

function tabLocker(I) {
	$($.sheet.instance).each(function(i) {
		this.setActiveSheet(I);
	});
}