{if $status}
	<div class="form-group">
		<label for="trackerinput_status" class="control-label">{tr}Status{/tr}</label>
		<div id="trackerinput_status">
			{include 'trackerinput/status.tpl' status_types=$status_types status=$status}
		</div>
	</div>
{/if}
{foreach from=$fields item=field}
	<div class="form-group">
		<label for="trackerinput_{$field.fieldId|escape}" class="control-label">
			{$field.name|tra|escape}
			{if $field.isMandatory eq 'y'}
				<span class="mandatory_star">*</span>
			{/if}
		</label>
		<div id="trackerinput_{$field.fieldId|escape}">
			{trackerinput field=$field}
			<div class="description help-block">
				{$field.description|tra|escape}
			</div>
		</div>
	</div>
{/foreach}
{jq}$('label').click(function() {$('input, select, textarea', '#'+$(this).attr('for')).focus();});{/jq}
