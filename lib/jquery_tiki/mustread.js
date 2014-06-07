(function ($) {
	"use strict";

	$(document).on('submit', 'form.add-members', ajaxSubmitEventHandler(function (data) {
		var form = this;
		form.reset();
		$(':text', form).autocomplete('close');

		$('.alert-success .groupname', form).text(data.group);
		$('.alert-success .add-count', form).text(data.add);
		$('.alert-success .skip-count', form).text(data.skip);
		$('.alert-success', form)
			.clearError()
			.removeClass('hidden');
	}));

	$(document).on('click', '.btn.add-mustread-item', $.fn.clickModal.getHandler({
		success: function (data) {
			var button = this,
				url = $.service('mustread', 'list', {id: data.itemId});

			document.location.href = url;
		}
	}));
})(jQuery);
