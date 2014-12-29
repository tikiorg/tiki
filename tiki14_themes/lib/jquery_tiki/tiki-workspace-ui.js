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

		if (name = prompt('Name', "")) {
			item = $(this).parent().parent().find('li:first').clone();

			$('.key', item).text(name);
			$('.name', item)
				.attr('name', 'groups~' + name + '~name')
				.val("{group} " + name)
				.parent().show();
			$('.managingGroup', item).prop('checked', false);
			$('.autojoin', item)
				.attr('name', 'groups~' + name + '~autojoin')
				.prop('checked', false);
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

	$(document).on('click', '.workspace-ui .pages .key', function () {
		$(this).parent().find('ul').each(function () {
			if ($('.name', this).val() == '{namespace}') {
				$('.name', this).parent().hide();
			}
			$(this).toggle('slow');
		});
		return false;
	});

	$(document).on('change', '.workspace-ui .pages .name', function () {
		if ($(this).val() !== '{namespace}' && $(this).val().length) {
			$(this).parent().parent().parent().find('.key').text($(this).val());
		}
	});

	$(document).on('click', '.workspace-ui .pages .add-page', function () {
		var name, item;

		if (name = prompt('Name', "")) {
			item = $(this).parent().parent().find('li:first').clone();

			$('.name', item)
				.attr('name', 'pages~' + name + '~name')
				.val(name)
				.parent().show();
			$('.namespace', item)
				.attr('name', 'pages~' + name + '~namespace')
				.val('{namespace}');
			$('.content', item)
				.attr('name', 'pages~' + name + '~content')
				.val('');

			$(this).parent().before(item);
			$('.name', item).change();
		}

		return false;
	});

	$(document).on('click', '.workspace-ui .pages .edit-content', function () {
		var field = $(this).parent().find('.content')[0];
		$(this).serviceDialog({
			modal: true,
			title: $(this).text(),
			data: {
				content: $(field).val()
			},
			success: function (data) {
				if (data.content) {
					$(field).val(data.content);
				} else if (data.page) {
					$(field).val('wikicontent:' + data.page);
				}
			}
		});
		return false;
	});

	$(document).on('change', '.workspace-ui-content-form :input', function () {
		$(this).closest('form').find(':input').not(this).not(':submit').val('');
	});

})(jQuery);
