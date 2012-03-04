// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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
			this.serviceDialog({
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
						$(this).closest('label').find('.description')
							.hide()
							.filter('.' + $(this).val())
							.show();
					}).change();
					$('form', dialog).each(function () {
						var form = this;
						$(form.name).keyup(function () {
							var val = $(this).val();
							val = val.replace(/[^\w]+/g, '_');
							val = val.replace(/_+([a-zA-Z])/g, function (parts) {
								return parts[1].toUpperCase();
							});
							val = val.replace(/^[A-Z]/, function (parts) {
								return parts[0].toLowerCase();
							});
							val = val.replace(/_+$/, '');

							$(form.permName).val(val);
						});

						$(form.submit_and_edit).click(function () {
							$(form.next).val('edit');
						});
					});
				}
			});
		},
		/**
		 * options:
		 * 	trackerId: int
		 * 	success: function (data) {}
		 */
		tracker_edit_field: function (options) {
			this.serviceDialog({
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
		tracker_load_fields: function (trackerId) {
			this.each(function () {
				var $container = $(this).empty();

				$.getJSON($.service('tracker', 'list_fields'), {
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
							.text(field.name == null?" ":field.name)
							.attr('href', $.service('tracker', 'edit_field', {trackerId: trackerId, fieldId: field.fieldId}))
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
						if (data.types[field.type]) {
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

							$row.append($('<td class="action"/>').append($('<a href="#"><img src="img/icons/cross.png"/></a>')
								.attr('href', $.service('tracker', 'remove_fields', {trackerId: trackerId, 'fields~0': field.fieldId}))
								.requireConfirm({
									message: tr('Removing the field will result in data loss. Are you sure?'),
									success: function (data) {
										$(this).closest('tr').remove();
									}
								})
							));
						}

						$container.append($row);
					});
				});
			});

			return this;
		}
	});
}(jQuery));
