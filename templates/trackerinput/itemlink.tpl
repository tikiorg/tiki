<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue='+escape(this.value),'{$field_value.http_request[5]}')"{/if}>
	{if $field_value.isMandatory ne 'y' || empty($field_value.value)}
		<option value=""></option>
	{/if}
	{foreach key=id item=label from=$field_value.list}
		<option value="{$label|escape}" {if $field_value.value eq $label or $defaultvalues.$fid eq $label or $field_value.defaultvalue eq $label}selected="selected"{/if}>
			{if $field_value.listdisplay[$id] eq ""}{$label}{else}{$field_value.listdisplay[$id]}{/if}
		</option>
	{/foreach}
</select>
