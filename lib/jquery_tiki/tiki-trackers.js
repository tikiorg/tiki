// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
(function ($) {
	$.fn = $.extend($.fn, {
		/**
		 * options:
		 * 	success: function (data) {}
		 */
		tracker_add_field: function (options) {
			this.each(function () {
				var $dialog = $('<div/>'), origin = this;
				$(this).append($dialog);

				$dialog.dialog({
					title: tr('Add Field'),
					minWidth: 500
				}).addClass('simple');

				$dialog.load('tiki-ajax_services.php', {
					controller: 'tracker',
					action: 'add_field',
					trackerId: options.trackerId
				}, function () {
					$dialog.find('form').submit(function (e) {
						var form = this;
						$.ajax('tiki-ajax_services.php', {
							dataType: 'json',
							data: $(form).serialize(),
							success: function (data) {
								if (data.fieldId) {
									$dialog.dialog('destroy');
									if (options.success) {
										options.success.apply(origin, [data]);
									}
								}
							},
							error: function (jqxhr) {
								$(form.name).showError(jqxhr);
							}
						});

						return false;
					});
				});
			});

			return this;
		}
	});
}(jQuery));
