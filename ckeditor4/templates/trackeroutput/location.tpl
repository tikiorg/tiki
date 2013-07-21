{if $field.value}
	<form method="get" action="">
		{if $context.list_mode eq 'y'}
			<div class="map-container" style="width: {if !empty($field.options_array[1])}{$field.options_array[1]}{else}200{/if}px; height: {if !empty($field.options_array[2])}{$field.options_array[2]}{else}200{/if}px;" data-target-field="location"></div>
		{else}
			<div class="map-container" style="width: {if !empty($field.options_array[3])}{$field.options_array[3]}{else}500{/if}px; height: {if !empty($field.options_array[4])}{$field.options_array[4]}{else}400{/if}px;" data-target-field="location"></div>
		{/if}
		<input type="hidden" name="location" value="{$field.value|escape}" disabled="disabled">
	</form>
{/if}
