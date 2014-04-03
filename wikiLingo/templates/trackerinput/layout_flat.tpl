{foreach from=$fields item=field}
	<label for="trackerinput_{$field.fieldId|escape}">
		{$field.name|escape}
		{if $field.isMandatory eq 'y'}
			<span class="mandatory_star">*</span>
		{/if}
	</label>
	<div id="trackerinput_{$field.fieldId|escape}">
		{trackerinput field=$field}
		<div class="description help-block">
			{$field.description|escape}
		</div>
	</div>
{/foreach}
{jq}$('label').click(function() {$('input, select, textarea', '#'+$(this).attr('for')).focus();});{/jq}
