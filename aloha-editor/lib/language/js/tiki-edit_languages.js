//$Id$

$(document).ready(function() {
	$('form#select_action .translation_action').each(function() {
		$(this).change(function() {
			$('form#select_action').submit();
		});
	});
});
