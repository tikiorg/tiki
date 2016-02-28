<input type="file" name="{$field.ins_id}"{if isset($input_err)} value="{$field.value}"{/if}>
{if $field.value ne ''}
	<br>
	{$data.image_tag}
	{if $field.isMandatory ne 'y'}
		<a href="{$smarty.server.PHP_SELF}?{query removeImage='y' fieldId=$field.fieldId itemId=$item.itemId trackerId=$item.trackerId fieldName=$field.name}" class="trkRemoveImage tips" title="{tr}Remove image{/tr}">{icon name='delete'}</a>
		{jq}$(".trkRemoveImage").click(function(){return confirm("{tr}Are you sure you want to delete this image?{/tr}");});{/jq}
	{/if}
{/if}
