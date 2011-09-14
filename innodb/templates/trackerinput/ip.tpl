{if $field.options_array[0] eq 0 or $tiki_p_admin_trackers eq 'y'}
	<input type="text" name="{$field.ins_id}" value="{if $field.value}{$field.value|escape}{elseif $field.defaultvalue}{$field.defaultvalue|escape}{else}{$IP|escape}{/if}" />
{else}
	{if $field.options_array[0] eq 1 && empty($field.value)}
		<input type="hidden" name="authoripid" value="{$field.fieldId}" />
	{/if}
	{$IP|escape}
{/if}