{if $prefs.trackerfield_computed eq 'y'}
	{if isset($field.computedtype) and $field.computedtype eq 'duration'}
		{if $field.value}
			{if isset($field.operator) and $field.operator eq 'sum'}
				{$field.value|duration:false:'hour'}
			{else}
				{$field.value|duration:false}
			{/if}
		{else}
			&nbsp;
		{/if}
	{elseif isset($field.computedtype) and $field.computedtype eq 'f'}
		{if isset($field.value)}
			{if isset($context.list_mode) and $context.list_mode eq 'csv'}
				{$field.value|tiki_short_datetime:'':n}
			{else}
				{$field.value|tiki_short_datetime}
			{/if}
		{else}
			&nbsp;
		{/if}
	{else}
		{if empty($field.options_array[1]) and empty($field.options_array[2])}
			{if empty($field.options_array[3])}
				{$field.value|escape}
			{else}
				{$field.value|number_format|escape}
			{/if}
		{else}
			{$field.value|number_format:$field.options_array[1]:$field.options_array[2]:$field.options_array[3]|escape}
		{/if}
	{/if}
{elseif $tiki_p_admin eq 'y'}
	<form class="labelColumns" method="post" action="tiki-admin.php">
		{preference name=tracker_field_computed}
		<input type="submit" class="btn btn-default btn-sm" value="{tr}Enable{/tr}">
	</form>
{else}
	{tr}Administrator intervention required.{/tr}
{/if}
