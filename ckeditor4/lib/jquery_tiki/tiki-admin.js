(function ($) {
	$(document).on('change', '.preference :checkbox', function () {
		var childBlock = $(this).data('tiki-admin-child-block')
			, childMode = $(this).data('tiki-admin-child-mode')
			, checked = $(this).is(':checked')
			, disabled = $(this).prop('disabled')
			;

		if (childMode === 'invert') {
			checked = ! checked;
		}

		if (disabled || ! checked) {
			$(childBlock).hide('fast');
		} else {
			$(childBlock).show('fast');
		}
	});

	$(document).on('click', '.pref-reset-wrapper a', function () {
		var box = $(this).closest('span').find(':checkbox');
		box.click();
		var $i = $(this).find("img");
		if ($i.attr("src").indexOf("undo") > -1) {
			$i.attr({
				"src": $i.attr("src").replace("undo", "redo"),
				"title": "Restore current value",
				"alt": "Restore current value"
			});
		} else {
			$i.attr({
				"src": $i.attr("src").replace("redo", "undo"),
				"title": "Reset to default",
				"alt": "Reset to default"
			});
		}
		return false;
	});
	
	$(document).on('click', '.pref-reset', function() {
		var c = $(this).prop('checked');
		var $el = $(this).closest('.adminoptionbox').find('input:not(:hidden),select,textarea')
			.not('.system').attr( 'disabled', c )
			.css("opacity", c ? .6 : 1 );
		var defval = $(this).data('preference-default');

		if ($el.is(':checkbox')) {
			$(this).data('preference-default', $el.prop('checked') ? 'y' : 'n');
			$el.prop('checked', defval === "y");
		} else {
			$(this).data('preference-default', $el.val());
			$el.val(defval);
		}
		$el.change();
		if (jqueryTiki.chosen) {
			$el.trigger("liszt:updated");
		}
	});

	$(document).on('change', '.preference select', function () {
		var childBlock = $(this).data('tiki-admin-child-block')
			, selected = $(this).val()
			;

		$(childBlock).hide();

		if (selected && /^[\w-]+$/.test(selected)) {
			$(childBlock).filter('.' + selected).show();
		}
	});

	$(document).on('change', '.preference :radio', function () {
		var childBlock = $(this).data('tiki-admin-child-block')

		if ($(this).prop('checked')) {
			$(childBlock).show('fast');
			$(this).closest('.preference').find(':radio').not(this).change();
		} else {
			$(childBlock).hide();
		}
	});

	$(function () {
		$('.preference :checkbox, .preference select, .preference :radio').change();

		$('.pref-reset')
			.wrap('<span class="pref-reset-wrapper"/>')
			.closest('span')
			.append('<a class="icon" href="#"><img class="icon" src="img/icons/arrow_undo.png" alt="Reset to default"/></a>')
			;
	});
})(jQuery);
