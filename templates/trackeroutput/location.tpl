{if $field.value}
	<form method="get" action="">
		<div class="map-container" style="width: 250px; height: 250px;" data-target-field="location"></div>
		<input type="hidden" name="location" value="{$field.value|escape}" disabled="disabled"/>
	</form>
{/if}
