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
			var containers = [];
			$('.editable-inline :input').each(function () {
				var container = $(this).closest('.editable-inline')[0];
				var ownership = $(container).data('object-store-url');

				if (ownership === url) {
					containers.push(container);
					parts.push($(this).serialize());
				}
			});

			$.post(url, parts.join('&'), 'json')
				.success(function () {
					$(containers).removeClass('modified').removeClass('unsaved');
				})
				.error(function () {
					$(containers).filter('.modified').addClass('unsaved');
				})
				;
		});
	};

	$(document).on('click', '.editable-inline:not(.disabled)', function () {
		var container = this
			, url = $(this).data('field-fetch-url')
			;

		$(container).addClass('disabled');

		$.get(url)
			.success(function (data) {
				$(container).html(data);
				$(container).on('change', ':input', function () {
					var executor = obtainExecutor(container);
					$(container).addClass('modified');

					executor();
				});
			})
			.error(function () {
				$(container).addClass('failure');
			})
			;
	});
})(jQuery);
