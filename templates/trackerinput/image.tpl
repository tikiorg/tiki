<input type="file" name="{$field.ins_id}"{if isset($input_err)} value="{$field.value}"{/if} />
{if $field.value ne ''}
	<br />
	<img src="{$field.value}" alt="" width="{$field.options_array[2]}" height="{$field.options_array[3]}" />
	{if $field.isMandatory ne 'y'}
		<a href="{$smarty.server.PHP_SELF}?{query removeImage='y' fieldId=`$field.fieldId` itemId=`$item.itemId` trackerId=`$item.trackerId` fieldName=`$field.name`}">{icon _id='cross' alt="{tr}Remove Image{/tr}"}</a>
	{/if}
{/if}
