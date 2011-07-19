{if $field.options_array[1] ne '' and $list_mode eq 'y'}
	{$field.value|truncate:$field.options_array[1]:"...":true|escape|nl2br}
{else}
	{$field.value|escape|nl2br}
{/if}
