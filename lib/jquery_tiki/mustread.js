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
})(jQuery);
