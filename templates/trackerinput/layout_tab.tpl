<div class="field-tabs">
	<ul>
		{foreach from=$sections key=k item=sect}
			<li><a href="#{$k|escape}">{$sect.heading|escape}</a></li>
		{/foreach}
	</ul>
	{foreach from=$sections key=k item=sect}
		<div id="{$k|escape}">
			{foreach from=$sect.fields item=field}
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
		</div>
	{/foreach}
</div>
{jq}
	$('.field-tabs')
		.tabs()
		.closest('.ui-dialog-content')
		.css('margin', '0px')
		.css('padding', '0px')
		.closest('.ui-dialog')
		.css('margin', '0px')
		.css('padding', '0px')
		;
	$('label').click(function() {
		$('input, select, textarea', '#'+$(this).attr('for')).focus();
	});
{/jq}
