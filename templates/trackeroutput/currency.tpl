{strip}
{if $field.value != ''}
	{if $field.options_array[2]}
		<span class="formunit">{$field.options_array[2]|escape}</span>
	{/if}
	{if empty($field.options_array[4])}
		{assign var=locale value='en_US'}
	{else}
		{assign var=locale value=$field.options_array[4]}
	{/if}
	{if empty($field.options_array[5])}
		{assign var=currency value='USD'}
	{else}
		{assign var=currency value=$field.options_array[5]}
	{/if}
	{if empty($field.options_array[6])}
		{assign var=part1a value='%(!#10n'}
		{assign var=part1b value='%(#10n'}
	{else}
		{assign var=part1a value='%(!#10'}
		{assign var=part1b value='%(#10'}
	{/if}
	{if (isset($context.reloff) and $context.reloff gt 0) and ($field.options_array[7] ne 1)}
		{assign var=format value=$part1a|cat:$field.options_array[6]}
		{$field.value|money_format:$locale:$currency:$format:0}
	{else}
		{assign var=format value=$part1b|cat:$field.options_array[6]}
		{$field.value|money_format:$locale:$currency:$format:1}
	{/if}
	{if $field.options_array[3]}
		<span class="formunit">{$field.options_array[3]|escape}</span>
	{/if}
{/if}
{/strip}

