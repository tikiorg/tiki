{* $Id$ *}
<div class="item-link">
	{if $data.selectMultipleValues}
		<input type="hidden" name="{$field.ins_id}_old" value="{$field.value|escape}" />
	{/if}
	{if $data.displayFieldsListType === 'table'}
		{wikiplugin _name=fancytable head='|'|implode:$data.list.fields sortable="type:reset" sortList="[1,0]" tsfilters="type:nofilter" tsfilteroptions="type:reset" tspaginate="max:5"}
		{foreach key=id item=fields from=$data.list.items}
			<input type="checkbox" class="{$field.ins_id}-checkbox" name="{$field.ins_id}[]" value="{$id|escape}" {if $data.preselection and $data.crossSelect neq 'y'}disabled="disabled"{/if} {if $data.preselection and !$field.value and $data.preselection eq $id or (($data.selectMultipleValues and is_array($field.value) and in_array($id, $field.value) or $field.value eq $id))}checked="checked"{/if} />|{'|'|implode:$fields}
		{/foreach}
		{/wikiplugin}
	{else}
		<select name="{$field.ins_id}{if $data.selectMultipleValues}[]{/if}" {if $data.preselection and $data.crossSelect neq 'y'}disabled="disabled"{/if} {if $data.selectMultipleValues}multiple="multiple"{/if} class="form-control">
			{if $field.isMandatory ne 'y' || empty($field.value)}
				<option value=""></option>
			{/if}
			{foreach key=id item=label from=$data.list}
				<option value="{$id|escape}" {if $data.preselection and !$field.value and $data.preselection eq $id or (($data.selectMultipleValues and is_array($field.value) and in_array($id, $field.value) or $field.value eq $id))}selected="selected"{/if}>
					{$label|escape}
				</option>
			{/foreach}
		</select>
	{/if}
	{if $field.options_map.addItems}
		<a class="btn btn-default insert-tracker-item" href="{service controller=tracker action=insert_item trackerId=$field.options_map.trackerId next=$data.next|escape}">{$field.options_map.addItems|escape}</a>
		{jq}
			$("select[name={{$field.ins_id}}]").next().clickModal({
				success: function (data) {
					$('<option>')
						.attr('value', data.itemId)
						.text(data.itemTitle)
						.appendTo($(this).prev());
					$(this).prev().val(data.itemId);
					$.closeModal();
				}
			});
		{/jq}
	{/if}
</div>
