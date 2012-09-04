(function ($) {
	$(document).on('click', '.workspace-ui .groups .key', function () {
		$(this).parent().find('ul').each(function () {
			if ($('.name', this).val() == '{group}') {
				$('.name', this).parent().hide();
			}
			$(this).toggle('slow');
		});
		return false;
	});

	$(document).on('click', '.workspace-ui .groups .add-group', function () {
		var name, item;

		if (name = prompt('Name')) {
			item = $(this).parent().parent().find('li:first').clone();

			$('.key', item).text(name);
			$('.name', item)
				.attr('name', 'groups~' + name + '~name')
				.val("{group} " + name)
				.parent().show();
			$('.managingGroup', item).attr('checked', false);
			$('.autojoin', item)
				.attr('name', 'groups~' + name + '~autojoin')
				.attr('checked', false);
			$('.permissions', item)
				.attr('name', 'groups~' + name + '~permissions')
				.val('');

			$(this).parent().before(item);
			$('.name', item).change();
		}
		return false;
	});

	$(document).on('change', '.workspace-ui .groups .name', function () {
		$(this).parent().parent().parent().find('.label').text($(this).val());
	});

	$(document).on('click', '.workspace-ui .permission-select', function () {
		var groups = {}, $items;

		$items = $(this).parent().find('.groups').children();
		$items.each(function () {
			var key = $('.key', this).text();
			var list = $('.permissions', this).val();

			if (key) {
				groups['permissions~' + key] = list;
			}
		});

		$(this).attr('href', $.service('workspace', 'select_permissions', groups));
		$(this).serviceDialog({
			modal: true,
			title: $(this).text(),
			success: function (data) {
				$items.each(function () {
					var key = $('.key', this).text();

					if (data.permissions[key]) {
						$('.permissions', this).val(data.permissions[key].join(','));
					}
				});
			}
		});
		return false;
	});
})(jQuery);
