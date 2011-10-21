<select name="{$field.ins_id}" {if $context.preselection}disabled="disabled"{/if}>
	{if $field.isMandatory ne 'y' || empty($field.value)}
		<option value=""></option>
	{/if}
	{foreach key=id item=label from=$field.list}
		<option value="{$label|escape}" {if $context.preselection and !$field.value and $context.preselection eq $label or $field.value eq $label}selected="selected"{/if}>
			{if $field.listdisplay[$id] eq ""}
				{$label|escape}
			{else}
				{$field.listdisplay[$id]|escape}
			{/if}
		</option>
	{/foreach}
</select>
