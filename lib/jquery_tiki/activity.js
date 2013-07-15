(function ($) {
	// Admin-only file

	$('#show-rules').click(function (e) {
		$(this).serviceDialog({
			title: $(this).text(),
			controller: 'managestream',
			action: 'list'
		});
		return false;
	});

	$(document).on('click', '.rule-edit, .rule-add', function () {
		$(this).serviceDialog({
			title: $(this).text(),
			controller: 'managestream',
			action: $(this).data('rule-type'),
			data: {
				id: $(this).data('rule-id')
			}
		});
		return false;
	});
})(jQuery);
