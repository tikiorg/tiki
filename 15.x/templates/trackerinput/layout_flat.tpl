{if $status}
	<div class="form-group">
		<label for="trackerinput_status" class="control-label">{tr}Status{/tr}</label>
		<div id="trackerinput_status">
			{include 'trackerinput/status.tpl' status_types=$status_types status=$status}
		</div>
	</div>
{/if}
{$jscal = 0}
{foreach from=$fields item=field}
	<div class="form-group">
		<label for="trackerinput_{$field.fieldId|escape}" class="control-label{if $field.type eq 'h'} h{$field.options_map.level}{/if}">
			{$field.name|tra|escape}
			{if $field.isMandatory eq 'y'}
				<span class="mandatory_star">*</span>
			{/if}
		</label>
		<div id="trackerinput_{$field.fieldId|escape}">
			{trackerinput field=$field}
			{if !empty($field.description) && $field.type ne 'S'}
				{if $field.descriptionIsParsed eq 'y'}
					<div class="description help-block">{wiki}{$field.description}{/wiki}</div>
				{else}
					<div class="description help-block">{$field.description|tra|escape}</div>
				{/if}
			{/if}
		</div>
	</div>
	{if $field.type == 'j'}{$jscal = 1}{/if}
{/foreach}
{jq}$('label').click(function() {$('input, select, textarea', '#'+$(this).attr('for')).focus();});{/jq}
{if $jscal}
	{js_insert_icon type="jscalendar"}
{/if}
