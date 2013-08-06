(function ($) {
	var 
		sortFriends = function () {
			$('.friend-list').sortList();
		},
		reload = function (control) {
			$(control).closest('.box-data, #role_main').load($.service('social', 'list_friends'), sortFriends);
		};

	$(document).on('click', 'button.add-friend', function () {
		var control = this;
		$(this).serviceDialog({
			title: $(this).text(),
			controller: 'social',
			action: 'add_friend',
			success: function () {
				reload(control);
			}
		});
	});
	$(document).on('click', '.confirm-prompt', function (e) {
		var control = this;
		e.preventDefault();

		$(this).doConfirm({
			success: function () {
				reload(control);
			}
		});

		return false;
	});

	$(sortFriends);
})(jQuery);
