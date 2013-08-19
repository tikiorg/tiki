(function ($) {
	var 
		sortFriends = function () {
			$('.friend-list').each(function () {
				$(this).sortList();
			});
		},
		reload = function (control) {
			$(control).closest('.friend-container').parent().load($.service('social', 'list_friends'), sortFriends);
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
	$(document).on('click', '.request-list .add-friend, .request-list .approve-friend', function (e) {
		var control = this;
		e.preventDefault();
		$.post($(control).attr('href'), function () {
			reload(control);
		});

		return false;
	});
	$(document).on('click', ' .remove-friend', function (e) {
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

(function ($) {
	$(document).on('click', '.activity a.comment', function () {
		var control = this;
		$(this).serviceDialog({
			title: $(this).text(),
			load: function () {
				var container = this;
				$('.confirm-prompt', this).requireConfirm({
					success: function () {
						$(container).reload();
					}
				});
			}
		});

		return false;
	});
})(jQuery);
