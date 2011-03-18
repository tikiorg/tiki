{if $field.options_array[0] eq 'd'}
	{if empty($field.value) and empty($inForm)}
		{jscalendar id=$field.ins_id fieldname=$field.ins_id showtime="n"}
	{elseif !empty($inForm)}
		{* inside form set by tiki-export_tracker.tpl - so use a clear date so we can export all by default *}
		{jscalendar date="" id=$field.ins_id fieldname=$field.ins_id showtime="n"}
	{else}
		{jscalendar date=$field.value id=$field.ins_id fieldname=$field.ins_id showtime="n"}
	{/if}
{else}
	{if empty($field.value)}
		{jscalendar id=$field.ins_id fieldname=$field.ins_id showtime="y"}
	{else}
		{jscalendar date=$field.value id=$field.ins_id fieldname=$field.ins_id showtime="y"}
	{/if}
{/if}
