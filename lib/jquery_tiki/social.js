(function ($) {
	var sortFriends = function () {
		$('.friend-list').sortList();
	};
	$(document).on('click', 'button.add-friend', function () {
		var control = this;
		$(this).serviceDialog({
			title: $(this).text(),
			controller: 'social',
			action: 'add_friend',
			success: function () {
				$(control).closest('.box-data, #role_main').load($.service('social', 'list_friends'), sortFriends);
			}
		});
	});

	$(sortFriends);
})(jQuery);
