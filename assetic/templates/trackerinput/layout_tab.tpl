<div class="field-tabs">
	<ul>
		{foreach from=$sections key=k item=sect}
			<li><a href="#{$k|escape}">{$sect.heading|escape}</a></li>
		{/foreach}
	</ul>
	{foreach from=$sections key=k item=sect}
		<div id="{$k|escape}">
			{foreach from=$sect.fields item=field}
				<label>
					{$field.name|escape}
					{if $field.isMandatory eq 'y'}
						<span class="mandatory_star">*</span>
					{/if}
					{trackerinput field=$field}
					<div class="description">
						{$field.description|escape}
					</div>
				</label>
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
{/jq}
