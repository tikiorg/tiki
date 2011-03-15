<input type="file" name="{$field_value.ins_id}"{if isset($input_err)} value="{$field_value.value}"{/if} />
{if $field_value.value ne ''}
	<br />
	<img src="{$field_value.value}" alt="" width="{$field_value.options_array[2]}" height="{$field_value.options_array[3]}" />
	{if $field_value.isMandatory ne 'y'}
		<a href="{$smarty.server.PHP_SELF}?{query removeImage='y' fieldId=`$field_value.fieldId` itemId=`$item.itemId` trackerId=`$item.trackerId` fieldName=`$field_value.name`}">{icon _id='cross' alt="{tr}Remove Image{/tr}"}</a>
	{/if}
{/if}
