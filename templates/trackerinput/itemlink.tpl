{* $Id$ *}
<div class="item-link">
	{if $data.selectMultipleValues}
		<input type="hidden" name="{$field.ins_id}_old" value="{$field.value|escape}" />
	{/if}
	{if $data.displayFieldsListType === 'table'}
	  <div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th><input type="checkbox" name="selectall" value="" class="{$field.ins_id}-select-all"></th>
						{foreach item=label from=$data.list.fields}
							<th>{$label|escape}</th>
						{/foreach}
					</tr>
				</thead>
				<tbody>
					{foreach key=id item=fields from=$data.list.items}
						<tr>
							<td><input type="checkbox" class="{$field.ins_id}-checkbox" name="{$field.ins_id}[]" value="{$id|escape}" {if $data.preselection and $data.crossSelect neq 'y'}disabled="disabled"{/if} {if $data.preselection and !$field.value and $data.preselection eq $id or (($data.selectMultipleValues and is_array($field.value) and in_array($id, $field.value) or $field.value eq $id))}checked="checked"{/if} /></td>
							{foreach key=fieldId item=label from=$fields}
								<td id="il{$id|escape}-{$fieldId}">{$label|escape}</td>
							{/foreach}
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
		{jq}
			$(".{{$field.ins_id}}-select-all").removeClass('{{$field.ins_id}}-select-all')
			.on('click', function (e) {
				if( this.checked )
					$(this).closest('form').find(':checkbox:not(:checked)[name^={{$field.ins_id}}]').click();
				else
					$(this).closest('form').find(':checkbox:checked[name^={{$field.ins_id}}]').click();
			});
		{/jq}
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
					$(this).prev().trigger("change");
					$.closeModal();
				}
			});
		{/jq}
	{/if}
</div>
