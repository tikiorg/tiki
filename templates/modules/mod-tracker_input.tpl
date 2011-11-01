{if $tracker_input.trackerId}
{tikimodule error=$module_params.error title=$tpl_module_title name="tracker_input" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<form class="mod-tracker-input simple" method="get" action="{service controller=tracker action=insert_item}">
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
			<input type="submit" value="{tr}Create{/tr}"/>
			{foreach from=$tracker_input.hiddenInput key=f item=v}
				<input id="{$f|escape}" type="hidden" name="forced~{$f|escape}" value="{$v|escape}"/>
			{/foreach}
		</div>
	</form>

	{jq}
	$('.mod-tracker-input').removeClass('mod-tracker-input').submit(function () {
		var form = this;
		$(this).serviceDialog({
			title: $(':submit', form).val(),
			data: $(form).serialize()
		});
		return false;
	}).each(function () {
		var form = this, location = '{{$tracker_input.location}}';

		if (location ) {
			setTimeout(function () {
				$(form).closest('.tab, #appframe, body').find('.map-container').setupMapSelection({
					field: $('#' + location)
				});
			}, 500);
		}
	});
	{/jq}
{/tikimodule}
{else}
{tr}Permission denied{/tr}
{/if}
