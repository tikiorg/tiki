{if $prefs.tracker_field_computed eq 'y'}
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
		{$field.value}
	{/if}
{elseif $tiki_p_admin eq 'y'}
	<form class="labelColumns" method="post" action="tiki-admin.php">
		{preference name=tracker_field_computed}
		<input type="submit" value="{tr}Enable{/tr}"/>
	</form>
{else}
	{tr}Administrator intervention required.{/tr}
{/if}
