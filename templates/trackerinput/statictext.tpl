{if $field.options_array[1] ne '' and $list_mode eq 'y'}
	{if $field.options_array[0] eq 1}
		{wiki}{$field.description|truncate:$field.options_array[1]:"...":true}{/wiki}
	{else}
		{$field.description|truncate:$field.options_array[1]:"...":true|escape|nl2br}
	{/if}
{else}
	{if $field.options_array[0] eq 1}
		{wiki}{$field.description}{/wiki}
	{else}
		{$field.description|escape|nl2br}
	{/if}
{/if}
