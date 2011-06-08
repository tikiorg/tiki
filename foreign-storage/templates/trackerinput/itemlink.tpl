<select name="{$field.ins_id}" {if $field.http_request}onchange="selectValues('trackerIdList={$field.http_request[0]}&amp;fieldlist={$field.http_request[3]}&amp;filterfield={$field.http_request[1]}&amp;status={$field.http_request[4]}&amp;mandatory={$field.http_request[6]}&amp;filtervalue='+escape(this.value),'{$field.http_request[5]}')"{/if}>
	{if $field.isMandatory ne 'y' || empty($field.value)}
		<option value=""></option>
	{/if}
	{foreach key=id item=label from=$field.list}
		<option value="{$label|escape}" {if $field.value eq $label or $defaultvalues.$fid eq $label or $field.defaultvalue eq $label}selected="selected"{/if}>
			{if $field.listdisplay[$id] eq ""}{$label}{else}{$field.listdisplay[$id]}{/if}
		</option>
	{/foreach}
</select>
