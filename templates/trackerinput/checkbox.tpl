<input type="checkbox" class="checkbox" name="{$field.ins_id}"{if $field.value eq 'y' or $field.value eq 'on' or strtolower($field.value) eq 'yes' or $field.defaultvalue eq 'y'} checked="checked"{/if}>
{if $field.value eq 'y' or $field.value eq 'on' or strtolower($field.value) eq 'yes' or $field.defaultvalue eq 'y'}
	<input type="hidden" name="{$field.ins_id}_old" value="1">
{/if}
