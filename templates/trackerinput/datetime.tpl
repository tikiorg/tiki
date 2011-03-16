{* ----- Start year --- *}
{if isset($field_value.options_array[1]) and $field_value.options_array[1] ne ''}
	{assign var=start value=$field_value.options_array[1]}
{elseif isset($prefs.calendar_start_year)}
	{assign var=start value=$prefs.calendar_start_year}
{else}
	{assign var=start value=-4}
{/if}
{if $field_value.year > 0 and $field_value.year < $start}
		{assign var=start value=$field_value.year}
{/if}

{* ----- End year --- *}
{if isset($field_value.options_array[2]) and $field_value.options_array[2] ne ''}
	{assign var=end value=$field_value.options_array[2]}
{elseif isset($prefs.calendar_end_year)}
	{assign var=end value=$prefs.calendar_end_year}
{else}
	{assign var=end value=+4}
{/if}
{if $field_value.year > $end}
	{assign var=end value=$field_value.year}
{/if}
{if $field_value.value eq '' or ($field_value.options_array[3] eq 'blank' and empty($item.itemId))}
	{assign var=time value="--"}
{else}
	{assign var=time value=$field_value.value}
{/if}
{if $field_value.options_array[0] ne 't'}
	{if ($field_value.isMandatory ne 'y' and (isset($field_value.options_array[3]) and ($field_value.options_array[3] eq 'blank' or $field_value.options_array[3] eq 'empty'))) or (isset($inExportForm) and $inExportForm eq 'y')}
		{html_select_date prefix=$field_value.ins_id time=$time start_year=$start end_year=$end field_order=$prefs.display_field_order all_empty=" "}
	{else}
		{html_select_date prefix=$field_value.ins_id time=$time start_year=$start end_year=$end field_order=$prefs.display_field_order}
	{/if}
{/if}
{if $field_value.options_array[0] eq 'dt'}
	{tr}at{/tr} 
{/if}
{if $field_value.options_array[0] ne 'd'}
	{if $field_value.isMandatory ne 'y' and (isset($field_value.options_array[3]) and ($field_value.options_array[3] eq 'blank' or $field_value.options_array[3] eq 'empty'))or (isset($inExportForm) and $inExportForm eq 'y')}
		{html_select_time prefix=$field_value.ins_id time=$time display_seconds=false all_empty=" " use_24_hours=$use_24hr_clock}
	{else}
		{html_select_time prefix=$field_value.ins_id time=$time display_seconds=false use_24_hours=$use_24hr_clock}
	{/if}
{/if}
