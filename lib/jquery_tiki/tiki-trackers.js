// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
(function ($) {
	$.fn = $.extend($.fn, {
		/**
		 * options:
		 * 	trackerId: int
		 * 	success: function (data) {}
		 */
		tracker_add_field: function (options) {
			this.tracker_service_dialog({
				title: tr('Add Field'),
				data: {
					controller: 'tracker',
					action: 'add_field',
					trackerId: options.trackerId
				},
				success: options.success,
				load: function () {
					var dialog = this;
					$('select', dialog).change(function () {
						$('.description', dialog)
							.hide()
							.filter('.' + $(this).val())
							.show();
					}).change();
				}
			});
		},
		/**
		 * options:
		 * 	trackerId: int
		 * 	success: function (data) {}
		 */
		tracker_edit_field: function (options) {
			this.tracker_service_dialog({
				title: tr('Edit Field'),
				data: {
					controller: 'tracker',
					action: 'edit_field',
					trackerId: options.trackerId,
					fieldId: options.fieldId
				},
				success: options.success
			});

			return this;
		},
		tracker_service_dialog: function (options) {
			this.each(function () {
				var $dialog = $('<div/>'), origin = this;
				$(this).append($dialog);

				$dialog.dialog({
					title: options.title,
					minWidth: 500,
					height: 600
				});

				$dialog.load('tiki-ajax_services.php', options.data, function () {
					$dialog.find('form').submit(function (e) {
						var form = this;
						$.ajax('tiki-ajax_services.php', {
							type: 'POST',
							dataType: 'json',
							data: $(form).serialize(),
							success: function (data) {
								$dialog.dialog('destroy');
								if (options.success) {
									options.success.apply(origin, [data]);
								}
							},
							error: function (jqxhr) {
								$(form.name).showError(jqxhr);
							}
						});

						return false;
					});

					if (options.load) {
						options.load.apply($dialog[0], []);
					}
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
						$row.append($('<td class="id"/>').text(field.fieldId));
						$row.append($('<td/>').append($('<a/>')
							.text(field.name)
							.attr('href', 'tiki-ajax_services.php?controller=tracker&action=edit_field&trackerId=' + trackerId + '&fieldId=' + field.fieldId)
							.click(function () {
								$(this).tracker_edit_field({
									trackerId: trackerId,
									fieldId: field.fieldId,
									success: function () {
										$container.tracker_load_fields(trackerId);
									}
								});
								return false;
							})
						));
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

						$row.append($('<td/>').append($('<a href="tiki-ajax_services.php?controller=tracker&amp;action=remove_fields&amp;&amp;trackerId=' + field.trackerId + '&amp;fields~0=' + field.fieldId + '"><img src="pics/icons/cross.png"/></a>')
							.click(function () {
								var link = this;
								if (confirm(tr('Removing the field will result in data loss. Are you sure?'))) {
									$.post($(this).attr('href'), {confirm: true}, function () {
										$(link).closest('tr').remove();
									}, 'json');
								}

								return false;
							})
						));

						$container.append($row);
					});
				});
			});

			return this;
		}
	});
}(jQuery));
