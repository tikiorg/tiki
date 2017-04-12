{if !empty($datachannel_feedbacks)}
{remarksbox type='note' title="{tr}Feedback{/tr}"}
	{foreach from=$datachannel_feedbacks item=feedback}
		{$feedback|escape}<br>
	{/foreach}
{/remarksbox}
{/if}
<form method="post" action="#{$datachannel_execution}"{$form_class_attr}{$datachannel_form_onsubmit}>
	{foreach from=$datachannel_fields key=name item=label}
		{if $label eq "external"}
			<input type="hidden" name="{$name|escape}" value="">
		{elseif $datachannel_inputfields.$name eq "hidden"}
			<input type="hidden" name="{$name|escape}" value="{$label}">
		{else}
			<div class="form-group">
				<label>{$label|escape}:</label>
				<div>
					<input type="text" name="{$name|escape}" class="form-control">
				</div>
			</div>
		{/if}
	{/foreach}
	<div class="submit_row">
		<input type="hidden" name="datachannel_execution" value="{$datachannel_execution|escape}">
		<input type="submit" class="btn btn-default btn-sm" value="{tr}{$button_label}{/tr}">
	</div>
</form>
