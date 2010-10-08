//$Id$

$(document).ready(function() {
	$('form#edit_translations .edit_translations').each(function() {
		$(this).change(function() {
			$('form#edit_translations').submit();
		});
	});
});
