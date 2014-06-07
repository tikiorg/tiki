(function ($) {
	"use strict";

	$(document).on('click', '.detail-link', function (e) {
		e.preventDefault();


		$($(this).data('target'))
			.tikiModal(tr('Loading..'))
			.load($(this).attr('href'), function () {
				$(this).tikiModal();
			});
	});

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
})(jQuery);
