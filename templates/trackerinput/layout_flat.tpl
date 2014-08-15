{foreach from=$fields item=field}
	<label for="trackerinput_{$field.fieldId|escape}">
		{$field.name|tra|escape}
		{if $field.isMandatory eq 'y'}
			<span class="mandatory_star">*</span>
		{/if}
	</label>
	<div id="trackerinput_{$field.fieldId|escape}">
		{trackerinput field=$field}
		{if !empty($field.description) && $field.type ne 'S'}
			{if $field.descriptionIsParsed eq 'y'}
				<div class="description">{wiki}{$field.description}{/wiki}</div>
			{else}
				<div class="description">{$field.description|escape}</div>
			{/if}
		{/if}
	</div>
{/foreach}
{jq}$('label').click(function() {$('input, select, textarea', '#'+$(this).attr('for')).focus();});{/jq}
