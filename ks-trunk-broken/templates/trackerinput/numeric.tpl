{*prepend*}
{if $field.options_array[2]}
	<span class="formunit">{$field.options_array[2]|escape}&nbsp;</span>
{/if}

<input type="number" class="numeric" name="{$field.ins_id|escape}" {if $field.options_array[1]}size="{$field.options_array[1]|escape}" maxlength="{$field.options_array[1]|escape}"{/if} value="{$field.value|escape}" id="{$field.ins_id}" />

{*append*}
{if $field.options_array[3]}
	<span class="formunit">&nbsp;{$field.options_array[3]|escape}</span>
{/if}

