{if $tracker_input.trackerId}
{tikimodule error=$module_params.error title=$tpl_module_title name="tracker_input" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<form class="mod-tracker-input simple" method="get" action="{service controller=tracker action=insert_item}" data-location="{$tracker_input.location|escape}" data-streetview="{$tracker_input.streetview|escape}">
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
			<input type="submit" name="create" value="{tr}Create{/tr}"/>
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
			success: function () {
				$(form).trigger('insert');
			},
			close: function () {
				$(form).trigger('cancel');
			}
		});
		return false;
	}).each(function () {
		var form = this
			, location = $(this).data('location')
			, streetview = $(this).data('streetview');

		if (location ) {
			var map = $(form).closest('.tab, #appframe, body').find('.map-container')[0];
			$(':submit', form).hide();
			$(map).one('initialized', function () {
				var control, button, modeManager, newMode;
				modeManager = map.modeManager;
				control = $(map).setupMapSelection({
					field: $('#' + location),
					click: function () {
						$(form).submit();
					}
				});
				control.deactivate();

				modeManager.addMode({name: newMode = "{{$tpl_module_title}}", controls: [control]});

				button = $('<input type="submit"/>')
					.val('{tr}Add Marker{/tr}')
					.button()
					.click(function () {
						modeManager.switchTo(newMode);
						return false;
					})
					.appendTo(form);

				$(':text', form).keyup(function (e) {
					button.button(hasEmptyField(form, ':text') ? 'disable' : 'enable');

					if (e.which === 13) {
						button.click();
					}
				}).keyup();

				$(form).bind('insert', function () {
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
								url: url
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
