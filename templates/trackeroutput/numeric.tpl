{if $field.value}
	{if $field.options_array[2]}
		<span class="formunit">{$field.options_array[2]|escape}</span>
	{/if}

	{if empty($field.options_array[4]) and empty($field.options_array[5])}
		{if empty($field.options_array[6])}
			{$field.value|escape}
		{else}
			{$field.value|number_format|escape}
		{/if}
	{else}
		{$field.value|number_format:$field.options_array[4]:$field.options_array[5]:$field.options_array[6]|escape}
	{/if}

	{if $field.options_array[3]}
		<span class="formunit">{$field.options_array[3]|escape}</span>
	{/if}
{/if}
