(function ($) {
	/**
	 * One executor is created for each object containing inline fields.
	 * When the field is modified, the executor is triggered.
	 *
	 * As a delayed executor, a period of grace is given for the field to
	 * be corrected or other fields to be modified. Each modification resets 
	 * the counter.
	 *
	 * When modifications stop happening, the entire object is stored in a
	 * single AJAX request.
	 */
	var executors = {}, obtainExecutor;

	obtainExecutor = function (container) {
		var url = $(container).data('object-store-url');

		if (executors[url]) {
			return executors[url];
		}

		return executors[url] = delayedExecutor(5000, function () {
			var parts = [];
			$('.editable-inline :input').each(function () {
				var ownership = $(this).closest('.editable-inline').data('object-store-url');

				if (ownership === url) {
					parts.push($(this).serialize());
				}
			});

			$.post(url, parts.join('&'), function () {
			}, 'json');
		});
	};

	$(document).on('click', '.editable-inline:not(.disabled)', function () {
		var container = this
			, url = $(this).data('field-fetch-url')
			;

		$(container).addClass('disabled');

		$(this).load(url, function () {
			var executor = obtainExecutor(container);

			$(container).on('change', ':input', executor);
		});
	});
})(jQuery);
