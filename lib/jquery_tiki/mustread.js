(function ($) {
	"use strict";

	$(document).on('submit', 'form.add-members, form.add-users', ajaxSubmitEventHandler(function (data) {
		var form = this;

		$(':checked', this).closest('li').remove();

		form.reset();
		$(':text', form).autocomplete('close');

		$('.alert-success .groupname', form).text(data.group);
		$('.alert-success .add-count', form).text(data.add);
		$('.alert-success .skip-count', form).text(data.skip);
		$('.alert-success', form)
			.clearError()
			.toggleClass('hidden', data.add + data.skip === 0);
	}));

	$(document).on('click', 'form.add-members .select-members', function (e) {
		e.preventDefault();

		var $list = $('.add-users .user-list');

		$list.find('li').removeClass('preserved');
		$list.find(':checked').closest('li').addClass('preserved');
		
		$list.find('li:not(.empty, .preserved)')
			.remove();

		if (this.request) {
			this.request.abort();
		}

		this.request = $.ajax({
			url: $.service('mustread', 'list_members'),
			dataType: 'json',
			data: {
				'group': $(this).closest('form').find(':text').val(),
				'current[]': $('.add-users :checked').map(function () {
					return $(this).val();
				}).toArray()
			},
			success: function (data) {
				$.each(data.resultset.result, function (k, entry) {
					$('<label>')
						.append('<input type="checkbox" name="user[]">')
						.append(' ')
						.append(entry.title)
						.wrap('<li class="checkbox">')
						.find(':checkbox').val(entry.object_id)
						.closest('li')
						.appendTo($list);
				});

				$list.find('.empty').toggle($list.find('li:not(.empty)').size() === 0);
			}
		});
	});

	$(document).on('click', '.btn.add-mustread-item', $.clickModal({
		success: function (data) {
			var button = this,
				url = $.service('mustread', 'list', {id: data.itemId});

			document.location.href = url;
		}
	}));
})(jQuery);
