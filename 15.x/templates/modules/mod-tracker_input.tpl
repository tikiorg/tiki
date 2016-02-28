{if $tracker_input.trackerId}
{tikimodule error=$module_params.error title=$tpl_module_title name="tracker_input" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<form class="mod-tracker-input simple" method="get" action="{service controller=tracker action=insert_item}" data-location="{$tracker_input.location|escape}" data-location-mode="{$tracker_input.locationMode|escape}" data-streetview="{$tracker_input.streetview|escape}" data-success="{$tracker_input.success|json_encode|escape}">
		{foreach from=$tracker_input.textInput key=token item=label}
			<label>
				{$label|escape}
				<input type="text" name="forced~{$token|escape}"/>
			</label>
		{/foreach}
		<div class="submit">
			<input type="hidden" name="trackerId" value="{$tracker_input.trackerId|escape}"/>
			<input type="hidden" name="controller" value="tracker"/>
			<input type="hidden" name="action" value="insert_item"/>
			<input type="submit" name="create" value="{$tracker_input.submit|escape}" class="btn btn-default btn-sm" />
			{foreach from=$tracker_input.hiddenInput key=f item=v}
				<input id="{$f|escape}" type="hidden" name="forced~{$f|escape}" value="{$v|escape}"/>
			{/foreach}
		</div>
	</form>

	{jq}
	function hasEmptyField(form, scope) {
		var hasEmpty = false;
		$(scope, form).each(function () {
			if ($(this).val() === '') {
				hasEmpty = true;
			}
		});

		return hasEmpty;
	}
	$('.mod-tracker-input').removeClass('mod-tracker-input').submit(function () {
		var form = this;
		if (hasEmptyField(form, ':input:not(:submit)')) {
			$(':submit', form).showError("{tr}Missing values{/tr}");
			return false;
		}

		$(this).serviceDialog({
			title: $(':submit', form).val(),
			data: $(form).serialize(),
			success: function (data) {
				$(form).trigger('insert', [ data ]);
				{{if $tracker_input.insertMode}}
					$(form).closest('.tab, #appframe, body').find('.map-container')[0].modeManager.switchTo("{{$tracker_input.insertMode|escape}}");
				{{/if}}
			},
			close: function () {
				$(form).trigger('cancel');
				{{if $tracker_input.insertMode}}
					$(form).closest('.tab, #appframe, body').find('.map-container')[0].modeManager.switchTo("{{$tracker_input.insertMode|escape}}");
				{{/if}}
			}
		});
		return false;
	}).each(function () {
		var form = this
			, location = $(this).data('location')
			, locationMode = $(this).data('location-mode')
			, streetview = $(this).data('streetview')
			, success = $(this).data('success')
			;

		if (success.operation === 'redirect') {
			$(form).bind('insert', function (e, data) {
				var url = success.argument;

				data.fields.itemId = data.itemId;
				data.fields.status = data.status;
				$.each(data.fields, function (k, v) {
					url = url.replace('@' + k + '@', encodeURIComponent(v));
				});

				document.location.href = url;
			});
		}

		if (location ) {
			var map = $(form).closest('.tab, #appframe, body').find('.map-container')[0];
			$(':submit', form).hide();
			$(map).one('initialized', function () {
				var control, button, modeManager, newMode;
				modeManager = map.modeManager;

				if (locationMode === 'marker') {
					control = $(map).setupMapSelection({
						field: $('#' + location),
						click: function () {
							$(form).submit();
						}
					});
					control.deactivate();

					modeManager.addMode({name: newMode = "{{$tpl_module_title}}", controls: [control, new OpenLayers.Control.NavToolbar()]});
				}

				button = $('<input type="submit" class="btn btn-default btn-sm" />')
					.val($(':submit', form).val())
					.button()
					.click(function () {
						if (newMode) {
							modeManager.switchTo(newMode);
							return false;
						}

						$('#' + location).val($(map).getMapCenter())
					})
					.appendTo(form);

				$(':text', form).keyup(function (e) {
					button.button(hasEmptyField(form, ':text') ? 'disable' : 'enable');

					if (e.which === 13) {
						button.click();
					}
				}).keyup();

				$(form).bind('insert', function () {
					$(map).removeMapSelection();
					$(map).trigger('changed');
				});
				$(form).bind('cancel', function () {
					$(map).removeMapSelection();
				});

				if (streetview) {
					map.streetview.addButton('{tr}Add Marker{/tr}', function (canvas) {
						var url = canvas.getImageUrl(), position = canvas.getPosition();
						$.ajax({
							type: 'POST',
							url: $.service('file', 'remote'),
							dataType: 'json',
							data: {
								galleryId: "{{$tracker_input.galleryId|escape}}",
								url: url,
								reference: 1
							},
							success: function (data) {
								var input = $('<input type="hidden" name="forced~' + streetview + '"/>')
									.val(data.fileId)
									.appendTo(form);
								$('#' + location).val(position);

								$(form).submit();
								input.remove();
							},
							complete: function () {
								$(canvas).dialog('close');
							}
						});
					});
				}
			});
		}
	});
	{/jq}
{/tikimodule}
{else}
{tr}Permission denied{/tr}
{/if}
