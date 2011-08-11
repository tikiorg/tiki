<select name="{$field.ins_id}">
	{if $field.isMandatory ne 'y' || empty($field.value)}
		<option value=""></option>
	{/if}
	{foreach key=id item=label from=$field.list}
		<option value="{$id|escape}" {if $field.value eq $id}selected="selected"{/if}>
			{if $field.listdisplay[$id] eq ""}
				{$label|escape}
			{else}
				{$field.listdisplay[$id]|escape}
			{/if}
		</option>
	{/foreach}
</select>
