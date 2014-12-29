{section name=i loop=$field.rating_options}
	<input name="{$field.ins_id}"{if $field.options_array[i] eq $item.my_rate} checked="checked"{/if} type="radio" value="{$field.options_array[i]|escape}" id="{$field.ins_id}{$smarty.section.i.index}"><label for="{$field.ins_id}{$smarty.section.i.index}">{$field.options_array[i]}</label>
{/section}
