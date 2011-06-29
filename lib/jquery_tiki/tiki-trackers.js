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
		},
		tracker_load_fields: function (trackerId) {
			this.each(function () {
				var $container = $(this).empty();

				$.getJSON('tiki-ajax_services.php', {
					controller: 'tracker',
					action: 'list_fields',
					trackerId: trackerId
				}, function (data) {
					$.each(data.fields, function (k, field) {
						var $row = $('<tr/>');
						$row.append($('<td class="id"/>')
							.text(field.fieldId)
							.append($('<input type="hidden" name="field~' + field.fieldId + '~position"/>').val(k * 10)));
						$row.append($('<td/>').text(field.name));
						$row.append($('<td/>').text(data.types[field.type].name));

						var addCheckbox = function (name) {
							$row.append($('<td/>').append(
								$('<input type="checkbox" name="field~' + field.fieldId + '~' + name + '" value="1"/>')
									.attr('checked', field[name] === 'y')
							));
						};

						addCheckbox('isTblVisible');
						addCheckbox('isMain');
						addCheckbox('isSearchable');
						addCheckbox('isPublic');
						addCheckbox('isMandatory');

						$container.append($row);
					});
				});
			});

			return this;
		}
	});
}(jQuery));
