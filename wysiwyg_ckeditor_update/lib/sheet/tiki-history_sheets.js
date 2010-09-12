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

var instanceCount = 0;
function lockSheetTogether() {
	for (var i = 0; i < instanceCount; i++) {
		var jS = $.sheet.instance[i];
		
		if (jS) {
			jS.obj.paneAll().each(function() {
				$(this).scroll(function() {
					scrollLocker($(this), i);
				});
			});
			
			jS.obj.tabContainer().click(function(e) {
				tabLock(jQuery(e.target));
			});
		}
	}
	
	function scrollLocker(obj, instance) {
		var id = obj.attr('id');
		var ids = id.split('_');
		
		for (var i = 0; i < $.sheet.instance.length; i++) {
			$('#' + ids[0] + '_' + i + '_' + ids[2])
				.scrollLeft(obj.scrollLeft())
				.scrollTop(obj.scrollTop());
		}
	}
	
	function tabLock(button) {
		var id = button.attr('id');
		ids = id.split('_');
		
		for (var i = 0; i < $.sheet.instance.length; i++) {
			$($.sheet.instance).each(function() {
				this.setActiveSheet($('#' + this.id.tableControl + i), ids[2]);
			});
		}
	}
}