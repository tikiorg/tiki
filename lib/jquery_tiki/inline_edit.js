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

	obtainExecutor = function (container, delay) {
		var url = $.serviceUrl($(container).data('object-store-url'));

		if (executors[url]) {
			return executors[url];
		}

		return executors[url] = delayedExecutor(delay || 5000, function () {
			var parts = [];
			var containers = [];
			$('.editable-inline.modified :input, .editable-dialog.modified :input').each(function () {
				var container = $(this).closest('.editable-inline, .editable-dialog')[0];
				var ownership = $.serviceUrl($(container).data('object-store-url'));

				if (ownership === url) {
					parts.push($(this).serialize());

					if (-1 === containers.indexOf(container)) {
						containers.push(container);
					}
				}
			});

			$(containers).each(function () {
				$(this).tikiModal(tr("Saving..."));
			});

			$.post(url, parts.join('&'), 'json')
				.success(function () {
					$(containers).
						removeClass('modified').
						removeClass('unsaved').
						trigger('changed.inline.tiki').
						trigger('saved.tiki').
						filter(function () {
							// The post-save value application is only for cases where the field was initially fetched
							return $(this).data('field-fetch-url');
						}).
						each(function () {
							var $input = $("input:first[name!=mode_wysiwyg], select:first option:selected, textarea:first", this), newVal, newHtml;

							if ($input.length) {
								var html = $.trim(($(this).data("saved_html") || '').replace("&nbsp;", " "));
								var oldVal = $.trim($(this).data("saved_text"));
								if ($input.is(":checkbox")) {
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
								$(this).removeClass("loaded").html(newHtml).tikiModal();
							}
						});
				})
				.error(function () {
					$(containers).filter('.modified').
						addClass('unsaved').
						trigger('changed.inline.tiki');
					$.getJSON($.service('object', 'report_error'));
				})
				;
		});
	};

	$(document).on('click', '.editable-inline:not(.loaded)', function () {
		var container = this
			, url = $.serviceUrl($(this).data('field-fetch-url'))
			;

		$(container).
			addClass('loaded').
			data("saved_html", $(container).html()).
			data("saved_text", $(container).text());

		if ($(container).data('group')) {
			$('.editable-inline:not(.loaded)')
				.filter(function () {
					return $(this).data('group') == $(container).data('group');
				})
				.not(container)
				.click();
		}

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
		$(container).
			addClass('modified').
			trigger('changed.inline.tiki');

		executor();
	});

	$(document).on('click', '.editable-dialog:not(.loaded)', function () {
		var container = this,
			fields = {};
		
		$('.editable-dialog:not(.loaded)')
			.filter(function () {
				return $(this).data('group') == $(container).data('group');
			})
			.each(function (k) {
				fields['fields[' + k + '][label]'] = $(this).data('label');
				fields['fields[' + k + '][fetch]'] = $(this).data('field-fetch-url');
				fields['fields[' + k + '][store]'] = $(this).data('object-store-url');
			});

		$('#bootstrap-modal').modal('show');
		$('#bootstrap-modal .modal-content')
			.load(
				$.service('edit', 'inline_dialog', {
					modal: 1
				}), fields,
				function () {
					$('#bootstrap-modal').trigger('tiki.modal.redraw');
				}
			);
	});

	$(document).on('submit', '.inline-edit-dialog', function (e) {
		e.preventDefault();

		var reload = delayedExecutor(500, function () {
			document.location.reload();
		});

		$('#boostrap-modal').tikiModal('Loading...');
		$('.editable-dialog.loaded', this)
			.addClass('modified')
			.one('saved.tiki', function () {
				if ($('.editable-dialog.loaded.modified', this).size() === 0) {
					reload();
				}
			})
			.each(function () {
				var executor = obtainExecutor(this, 100);
				executor();
			});
	});

	$(function () {
		$('.inline-sort-handle')
			.next().children().bind('changed.inline.tiki', function () {
				$(this).parent().prev()
					.toggleClass('text-warning', $(this).hasClass('modified'))
					.toggleClass('text-danger', $(this).hasClass('unsaved'));
			})
			.closest('tbody, ul, ol')
			.each(function () {
				var $list = $('.inline-sort-handle', this);

				var first = $list.eq(0).data('current-value') || 1;
				var second = $list.eq(1).data('current-value') || 2;

				this.first = first;
				this.increment = (second - first) || 1;
			})
			.sortable({
				handle: '.inline-sort-handle',
				stop: function () {
					var first = this.first, increment = this.increment;

					$('.inline-sort-handle', this).next().find(':input:first').each(function (position) {
						var val = $(this).val(), target = first + position * increment;

						if (val != target) {
							$(this).val(target).change();
						}
					});
				}
			});
	});
})(jQuery);
