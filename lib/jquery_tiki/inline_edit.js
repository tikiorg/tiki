(function ($) {
	$(document).on('click', '.editable-inline:not(.disabled)', function () {
		var container = this, url = $(this).data('edit-url');
		$(container).addClass('disabled');

		$(this).load(url, function () {
			var executor = delayedExecutor(5000, function () {
				var parts = [];
				$(':input', container).each(function () {
					parts.push($(this).serialize());
				});

				$.post(url, parts.join('&'));
			});

			$(container).on('change', ':input', executor);
		});
	});
})(jQuery);
