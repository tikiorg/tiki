{if $field.options_array[0] eq 0 or $tiki_p_admin_trackers eq 'y'}
	<input type="text" name="{$field.ins_id}" value="{if $field.value}{$field.value|escape}{else}{$IP|escape}{/if}" class="form-control">
{else}
	{$IP|escape}
{/if}
