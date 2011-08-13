{if $field.options_array[0] eq 'd'}
	{if !empty($inForm) or !empty($context.inForm)}
		{* inside form set by trackerfilter - so use a clear date so we can find all by default *}
		{jscalendar date="" id=$field.ins_id fieldname=$field.ins_id showtime="n"}
	{elseif empty($field.value)}
		{jscalendar id=$field.ins_id fieldname=$field.ins_id showtime="n"}
	{else}
		{jscalendar date=$field.value id=$field.ins_id fieldname=$field.ins_id showtime="n"}
	{/if}
{else}
	{if !empty($inForm) or !empty($context.inForm)}
		{* inside form set by trackerfilter - so use a clear date so we can find all by default *}
		{jscalendar date="" id=$field.ins_id fieldname=$field.ins_id showtime="y"}
	{elseif empty($field.value)}
		{jscalendar id=$field.ins_id fieldname=$field.ins_id showtime="y"}
	{else}
		{jscalendar date=$field.value id=$field.ins_id fieldname=$field.ins_id showtime="y"}
	{/if}
{/if}
