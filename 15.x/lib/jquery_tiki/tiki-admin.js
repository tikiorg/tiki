(function ($) {
	$(document).on('change', '.preference :checkbox:not(.pref-reset)', function () {
		var childBlock = $(this).data('tiki-admin-child-block')
			, childMode = $(this).data('tiki-admin-child-mode')
			, checked = $(this).is(':checked')
			, disabled = $(this).prop('disabled')
			, $depedencies = $(this).parents(".adminoption").find(".pref_dependency")
			;

		if (childMode === 'invert') {
			checked = ! checked;
		}

		if (disabled && checked) {
			$(childBlock).show('fast');
			$depedencies.show('fast');
		} else if (disabled || ! checked) {
			$(childBlock).hide('fast');
			$depedencies.hide('fast');
		} else {
			$(childBlock).show('fast');
			$depedencies.show('fast');
		}
	});

	$(document).on('click', '.pref-reset-wrapper a', function () {
		var box = $(this).closest('span').find(':checkbox');
		box.click();
		$(this).closest('span').children( ".pref-reset-undo, .pref-reset-redo" ).toggle();
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
			$el.trigger("chosen:updated");
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
		var childBlock = $(this).data('tiki-admin-child-block');

		if ($(this).prop('checked')) {
			$(childBlock).show('fast');
			$(this).closest('.preference').find(':radio').not(this).change();
		} else {
			$(childBlock).hide();
		}
	});

	$(function () {
		$('.preference :checkbox, .preference select, .preference :radio').change();
	});

	$(function () {
		// highlight the admin icon (anchors)
		var $anchors = $(".adminanchors li a, .admbox"),
			bgcol = $anchors.is(".admbox") ? $anchors.css("background-color") : $anchors.parent().css("background-color");

		$("input[name=lm_criteria]").keyup( function () {
			var criterias = this.value.toLowerCase().split( /\s+/ ), word, text;
			$anchors.each( function() {
				var $parent = $(this).is(".admbox") ? $(this) : $(this).parent();
				if (criterias && criterias[0]) {
					text = $(this).attr("alt").toLowerCase();
					for( i = 0; criterias.length > i; ++i ) {
						word = criterias[i];
						if ( word.length > 0 && text.indexOf( word ) == -1 ) {
							$parent.css("background", "");
							return;
						}
					}
					$parent.css("background", "radial-gradient(white, " + bgcol + ")");
				} else {
					$parent.css("background", "");
				}
			});
		});
	});

})(jQuery);
