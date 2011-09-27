<select name="{$field.ins_id}" {if $context.preselection}disabled="disabled"{/if}>
	{if $field.isMandatory ne 'y' || empty($field.value)}
		<option value=""></option>
	{/if}
	{foreach key=id item=label from=$field.list}
		<option value="{$id|escape}" {if $context.preselection and !$field.value and $context.preselection eq $id or $field.value eq $id}selected="selected"{/if}>
			{if $field.listdisplay[$id] eq ""}
				{$label|escape}
			{else}
				{$field.listdisplay[$id]|escape}
			{/if}
		</option>
	{/foreach}
</select>
