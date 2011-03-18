{if $field.options[0] != 1 || $tiki_p_admin_trackers == 'y'}
	<input type="text" id="page_selector_{$field.fieldId}" name="{$field.ins_id}"
			{if $field.options_array[1] gt 0}size="{$field.options_array[1]}"{/if}
			value="{if $field.value}{$field.value|escape}{else}{$field.defaultvalue|escape}{/if}" />
	{if $field.isMandatory ne 'y'} {* since autocomplete allows blank entry it can't be used for mandatory selection. *}     
		{autocomplete element="#page_selector_`$field.fieldId`" type='pagename'}
	{/if}
{else}
	{$field.value|escape}
{/if}
