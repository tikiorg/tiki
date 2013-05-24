<select name="{$field.ins_id}{if $data.selectMultipleValues}[]{/if}" {if $data.preselection}disabled="disabled"{/if} {if $data.selectMultipleValues}multiple="multiple"{/if}>
	{if $field.isMandatory ne 'y' || empty($field.value)}
		<option value=""></option>
	{/if}
	{foreach key=id item=label from=$data.list}
		<option value="{$id|escape}" {if $data.preselection and !$field.value and $data.preselection eq $id or (($data.selectMultipleValues and is_array($field.value) and in_array($id, $field.value) or $field.value eq $id))}selected="selected"{/if}>
			{$label|escape}
		</option>
	{/foreach}
</select>
