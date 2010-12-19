/**
 * File galleries - Templates management
 */

$(function() {
	$('#fgal_template').change(selectTemplate).trigger('change');
})

var selectTemplate = function() {
	var otherTabs = $('span.tabinactive');
	var otherParams = $('#description').parents('tr').nextAll('tr');

	if ($(this).val() != '') {
		// Select template, hide parameters
		otherTabs.hide();
		otherParams.hide();
	} else {
		// No template, show parameters
		otherTabs.show();
		otherParams.show();
	}
}