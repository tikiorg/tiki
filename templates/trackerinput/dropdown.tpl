<select name="{$field.ins_id}" {if $field.http_request}onchange="selectValues('trackerIdList={$field.http_request[0]}&amp;fieldlist={$field.http_request[3]}&amp;filterfield={$field.http_request[1]}&amp;status={$field.http_request[4]}&amp;mandatory={$field.http_request[6]}&amp;filtervalue='+escape(this.value),'{$field.http_request[5]}')"{/if}>
{assign var=otherValue value=$field.value}
	{if $field.isMandatory ne 'y' || empty($field.value)}
		<option value="">&nbsp;</option>
	{/if}
	{section name=jx loop=$field.options_array}
		<option value="{$field.options_array[jx]|escape}" {if !empty($item.itemId) && ($field.value eq $field.options_array[jx] or (isset($field.isset) && $field.isset == 'n' && $field.defaultvalue eq $field.options_array[jx]))}{assign var=otherValue value=''}selected="selected"{elseif (empty($item.itemId) || !isset($field.value)) && $field.defaultvalue eq $field.options_array[jx]}selected="selected"{/if}>
			{$field.options_array[jx]|tr_if}
		</option>
	{/section}
</select>
{if $field.type eq 'D'}
<br /><label for="other_{$field.ins_id}">{tr}Other:{/tr}</label> <input type="text" name="other_{$field.ins_id}" value="{$otherValue|escape}" id="other_{$field.ins_id}" />
{/if}