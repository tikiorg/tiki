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
					$(containers).
						removeClass('modified').
						removeClass('unsaved').
						each( function () {
							var $input = $("input:first, select:first option:selected", this), newVal, newHtml;
							if ($input.length) {
								var html = $.trim($(this).data("saved_html").replace("&nbsp;", " "));
								var oldVal = $.trim($(this).data("saved_text"));
								if ($input.is("input[type=checkbox]")) {
									if ($input.prop("checked")) {
										newVal = tr("Yes");
									} else {
										newVal = tr("No");
									}
								} else if ($input.is("input[type=hidden]")) { // js datetime picker
									newVal = $("#" + $input.attr("id") + "_dptxt").val();
								} else if ($input.is("option")) {
									newVal = $input.text();
								} else {
									newVal = $input.val();
								}
								if (html) {
									newHtml = html.replace(new RegExp(oldVal.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&")), $.trim(newVal));
								} else {
									newHtml = $.trim(newVal);
								}
								if (!newHtml) {
									newHtml = "&nbsp;";
								}
								$(this).removeClass("loaded").html(newHtml);
							}
						});
				})
				.error(function () {
					$(containers).filter('.modified').addClass('unsaved');
					$.getJSON($.service('object', 'report_error'));
				})
				;
		});
	};

	$(document).on('click', '.editable-inline:not(.loaded)', function () {
		var container = this
			, url = $(this).data('field-fetch-url')
			;

		$(container).
			addClass('loaded').
			data("saved_html", $(container).html()).
			data("saved_text", $(container).text());

		if (url) {
			$.get(url)
				.success(function (data) {
					var w = $(container).parent().width();	// td width
					$(container).html(data);
					$("input, select", container).each(function () {
						$(this).keydown(function (e) {
							if (e.which === 13) {
								$(this).blur();
								return false;
							} else if (e.which === 9) {
								$(this).blur();
								if (e.shiftKey) {
									$(this).parents("td:first").prev().find(".editable-inline:first").click();
								} else {
									$(this).parents("td:first").next().find(".editable-inline:first").click();
								}
								return false;
							} else {
								return true;
							}
						}).width(Math.min($(this).width(), w));
					});
					if (jqueryTiki.chosen) {
						var $select = $("select", container);
						if ($select.length) {
							$select.tiki("chosen");
						}
					}
				})
				.error(function () {
					$(container).addClass('failure');
				})
				;
		}
	});

	$(document).on('change', '.editable-inline.loaded :input', function () {
		var container, executor;
		
		container = $(this).closest('.editable-inline')[0];
		executor = obtainExecutor(container);
		$(container).addClass('modified');

		executor();
	});
})(jQuery);
