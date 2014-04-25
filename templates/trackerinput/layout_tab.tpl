{tabset name="tracker_section_output"}
	{foreach $sections as $sect}
		{tab name=$sect.heading}
			{foreach from=$sect.fields item=field}
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
		{/tab}
	{/foreach}
{/tabset}
{jq}$('label').click(function() {$('input, select, textarea', '#'+$(this).attr('for')).focus();});{/jq}
