//$Id: tiki-edit_languages.js 34965 2011-06-17 14:24:54Z sampaioprimo $

$(document).ready(function() {
	$('form#select_action .translation_action').each(function() {
		$(this).change(function() {
			$('form#select_action').submit();
		});
	});
});
