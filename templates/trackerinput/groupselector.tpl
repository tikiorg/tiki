{if $field.options lt 1 or $tiki_p_admin_trackers eq 'y'}
	<select name="{$field.ins_id}">
		{if $field.isMandatory ne 'y'}
			<option value="">{tr}None{/tr}</option>
		{/if}
		{section name=ux loop=$groups}
			{if !isset($field.itemChoices) or $field.itemChoices|@count eq 0 or in_array($groups[ux], $field.itemChoices)}
				<option value="{$groups[ux]|escape}" {if $field.value eq $groups[ux]}selected="selected"{/if}>{$groups[ux]}</option>
			{/if}
		{/section}
	</select>
{elseif $field.options}
	{$field.value}
{/if}