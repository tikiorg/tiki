{if !empty($datachannel_feedbacks)}
{remarksbox type='feedback' title='{tr}Feedback{/tr}'}
	{foreach from=$datachannel_feedbacks item=feedback}
		{$feedback|escape}<br />
	{/foreach}
{/remarksbox}
{/if}
<form method="post" action=""{$form_class_attr}{$datachannel_form_onsubmit}>
	{foreach from=$datachannel_fields key=name item=label}
		{if $name ne "external"}
			<div>
				{$label|escape}: <input type="text" name="{$name|escape}"/>
			</div>
		{else}
			<input type="hidden" name="{$label.fieldname|escape}" value="" />
		{/if}
	{/foreach}
	<div class="submit_row">
		<input type="hidden" name="datachannel_execution" value="{$datachannel_execution|escape}"/>
		<input type="submit" value="{tr}{$button_label}{/tr}"/>
	</div>
</form>
