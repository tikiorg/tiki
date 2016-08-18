{* ----- Start year --- *}
{if isset($field.options_array[1]) and $field.options_array[1] ne ''}
	{assign var=start value=$field.options_array[1]}
{elseif isset($prefs.display_start_year)}
	{assign var=start value=$prefs.display_start_year}
{else}
	{assign var=start value='-4'}
{/if}
{if $field.year > 0 and $field.year < $start}
		{assign var=start value=$field.year}
{/if}

{* ----- End year --- *}
{if isset($field.options_array[2]) and $field.options_array[2] ne ''}
	{assign var=end value=$field.options_array[2]}
{elseif isset($prefs.display_end_year)}
	{assign var=end value=$prefs.display_end_year}
{else}
	{assign var=end value='+4'}
{/if}
{if $field.year > $end}
	{assign var=end value=$field.year}
{/if}
{if $field.value eq ''}
	{assign var=time value="--"}
{else}
	{assign var=time value=$field.value}
{/if}
{if $field.options_array[0] ne 't'}
	{if ((isset($field.options_array[3]) and ($field.options_array[3] eq 'blank' or $field.options_array[3] eq 'empty'))) or (isset($inExportForm) and $inExportForm eq 'y')}
		{html_select_date prefix=$field.ins_id time=$time start_year=$start end_year=$end field_order=$prefs.display_field_order all_empty=" "}
	{else}
		{html_select_date prefix=$field.ins_id time=$time start_year=$start end_year=$end field_order=$prefs.display_field_order}
	{/if}
{/if}
{if $field.options_array[0] eq 'dt'}
	{tr}at{/tr}
{/if}
{if $field.options_array[0] ne 'd'}
	{if (isset($field.options_array[3]) and ($field.options_array[3] eq 'blank' or $field.options_array[3] eq 'empty'))or (isset($inExportForm) and $inExportForm eq 'y')}
		{html_select_time prefix=$field.ins_id time=$time display_seconds=false all_empty=" " use_24_hours=$use_24hr_clock}
	{else}
		{html_select_time prefix=$field.ins_id time=$time display_seconds=false use_24_hours=$use_24hr_clock}
	{/if}
{/if}
