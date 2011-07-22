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
				load: function () {
					function split( val ) {
						return val.split( /,\s*/ );
					}
					function extractLast( term ) {
						return split( term ).pop();
					}

					$('.groupselector', this).tiki('autocomplete', 'groupname', {
						tiki_replace_term: function (term) {
							return extractLast(term);
						},
						focus: function() {
							return false;
						},
						select: function( event, ui ) {
							var terms = split( this.value );
							terms.pop();

							terms.push( ui.item.value );

							terms.push( "" );
							this.value = terms.join( ", " );
							return false;
						}
					});
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
					height: 600,
					close: function () {
						$(this).dialog('destroy');
					}
				});

				$dialog.tracker_load_service(options.data, $.extend(options, {origin: origin}));
			});

			return this;
		},
		tracker_load_service: function (data, options) {
			var $dialog = this;
			$dialog.load('tiki-ajax_services.php', data, function () {
				$dialog.find('form').submit(function (e) {
					var form = this;
					$.ajax('tiki-ajax_services.php', {
						type: 'POST',
						dataType: 'json',
						data: $(form).serialize(),
						success: function (data) {
							if (data.FORWARD) {
								$dialog.tracker_load_service(data.FORWARD, options);
							} else {
								$dialog.dialog('destroy');
								if (options.success) {
									options.success.apply(options.origin, [data]);
								}
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

				$('.confirm-prompt', this).click(function () {
					if (confirm ($(this).data('confirm'))) {
						$.ajax($(this).attr('href'), {
							type: 'POST',
							dataType: 'json',
							data: {
								'confirm': 1
							},
							success: function (data) {
								if (data.FORWARD) {
									$dialog.tracker_load_service(data.FORWARD, options);
								} else {
									$dialog.tracker_load_service(options.data, options);
								}
							},
							error: function (jqxhr) {
								$(form.name).showError(jqxhr);
							}
						});
					}
					return false;
				});
			});
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
						$row.append($('<td class="checkbox"/>').append($('<input type="checkbox" name="fields[]"/>').val(field.fieldId)));
						$row.append($('<td class="id"/>')
							.text(field.fieldId)
							.append($('<input type="hidden" name="field~' + field.fieldId + '~position"/>').val(field.position))
						);
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
							$row.append($('<td class="checkbox"/>').append(
								$('<input type="checkbox" name="field~' + field.fieldId + '~' + name + '" value="1"/>')
									.attr('checked', field[name] === 'y')
							));
						};

						addCheckbox('isTblVisible');
						addCheckbox('isMain');
						addCheckbox('isSearchable');
						addCheckbox('isPublic');
						addCheckbox('isMandatory');

						$row.append($('<td class="action"/>').append($('<a href="tiki-ajax_services.php?controller=tracker&amp;action=remove_fields&amp;trackerId=' + field.trackerId + '&amp;fields~0=' + field.fieldId + '"><img src="pics/icons/cross.png"/></a>')
							.click(function () {
								var link = this;
								if (confirm(tr('Removing the field will result in data loss. Are you sure?'))) {
									$.post($(this).attr('href'), {confirm: 1}, function () {
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
