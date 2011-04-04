<input type="file" name="{$field.ins_id}"{if isset($input_err)} value="{$field.value}"{/if} />
{if $field.value ne ''}
	<br />
	{if $context.image_tag}{$context.image_tag}{/if}
	{if $field.isMandatory ne 'y'}
		<a href="{$smarty.server.PHP_SELF}?{query removeImage='y' fieldId=`$field.fieldId` itemId=`$item.itemId` trackerId=`$item.trackerId` fieldName=`$field.name`}" class="trkRemoveImage">{icon _id='cross' alt="{tr}Remove Image{/tr}"}</a>
		{jq}$(".trkRemoveImage").click(function(){return confirm("{tr}Are you sure you want to delete this image?{/tr}");});{/jq}
	{/if}
{/if}
