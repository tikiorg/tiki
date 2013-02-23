{if $field.value}
	<form method="get" action="">
		{if $context.list_mode eq 'y'}
			<div class="map-container" style="width: 200px; height: 200px;" data-target-field="location"></div>
		{else}
			<div class="map-container" style="width: 500px; height: 400px;" data-target-field="location"></div>
		{/if}
		<input type="hidden" name="location" value="{$field.value|escape}" disabled="disabled"/>
	</form>
{/if}
