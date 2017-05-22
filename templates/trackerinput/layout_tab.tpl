{tabset name="tracker_section_output"|cat:$tracker_info.trackerId}
	{foreach $sections as $pos => $sect}
		{tab name=$sect.heading}
			{if ! $pos && $status}
				<div class="form-group">
					<label for="trackerinput_status" class="control-label">{tr}Status{/tr}</label>
					<div id="trackerinput_status">
						{include 'trackerinput/status.tpl' status_types=$status_types status=$status}
					</div>
				</div>
			{/if}
			{foreach from=$sect.fields item=field}
				<div class="form-group">
					<label for="trackerinput_{$field.fieldId|escape}" class="control-label">
						{$field.name|tra|escape}
						{if $field.isMandatory eq 'y'}
							<strong class='mandatory_star text-danger tips' title=":{tr}This field is mandatory{/tr}">*</strong>
						{/if}
					</label>
					<div id="trackerinput_{$field.fieldId|escape}">
						{trackerinput field=$field item=$item}
						{if !empty($field.description) && $field.type ne 'S'}
							{if $field.descriptionIsParsed eq 'y'}
								<div class="description help-block">{wiki}{$field.description}{/wiki}</div>
							{else}
								<div class="description help-block">{$field.description|tra|escape}</div>
							{/if}
						{/if}
					</div>
				</div>
			{/foreach}
		{/tab}
	{/foreach}
{/tabset}
{jq}$('label').click(function() {$('input, select, textarea', '#'+$(this).attr('for')).focus();});{/jq}
