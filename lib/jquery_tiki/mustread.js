(function ($) {
	"use strict";

	$(document).on('submit', 'form.add-members, form.add-users', ajaxSubmitEventHandler(function (data) {
		var form = this;

		$(':checked', this).closest('li').remove();

		form.reset();

		$('.alert-success .groupname', form).text(data.group);
		$('.alert-success .add-count', form).text(data.add);
		$('.alert-success .skip-count', form).text(data.skip);
		$('.alert-success', form)
			.clearError()
			.toggleClass('hidden', data.add + data.skip === 0);
	}));

	$(document).on('change', 'form.add-members select', function () {
		var target = $(this).data('copy-into');
		$(target).val($(this).val());
	});

	$(document).on('change', 'form.add-members .group-field', function (e) {
		var group = $(this).val(),
		    $selector = $('.add-users .user-selector');

		$selector.object_selector_multi('setfilter', 'groups', '"' + group + '"');
	});

	$(document).on('click', '.btn.add-mustread-item', $.clickModal({
		success: function (data) {
			$.closeModal({
				done: function () {
					$.openModal({
						remote: $.service('mustread', 'circulate', {id: data.itemId}),
						close: function () {
							var button = this,
								url = $.service('mustread', 'list', {id: data.itemId}) + '#contentmustread_detail-notification';

							document.location.href = url;
						}
					});
				}
			});
		}
	}));
})(jQuery);
