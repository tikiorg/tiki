{foreach from=$fields item=field}
	{if $status}
		<div class="form-group">
			<label for="trackerinput_status" class="control-label">{tr}Status{/tr}</label>
			<div id="trackerinput_status">
				{include 'trackerinput/status.tpl' status_types=$status_types status=$status}
			</div>
		</div>
	{/if}
	<div class="form-group">
		<label for="trackerinput_{$field.fieldId|escape}" class="control-label">
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
	</div>
{/foreach}
{jq}$('label').click(function() {$('input, select, textarea', '#'+$(this).attr('for')).focus();});{/jq}
