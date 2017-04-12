{if !empty($datachannel_feedbacks)}
{remarksbox type='note' title="{tr}Feedback{/tr}"}
	{foreach from=$datachannel_feedbacks item=feedback}
		{$feedback|escape}<br>
	{/foreach}
{/remarksbox}
{/if}
<form class="form-horizontal" method="post" action="#{$datachannel_execution}"{$form_class_attr}{$datachannel_form_onsubmit}>
	{foreach from=$datachannel_fields key=name item=label}
		{if $label eq "external"}
			<input type="hidden" name="{$name|escape}" value="">
		{elseif $datachannel_inputfields.$name eq "hidden"}
			<input type="hidden" name="{$name|escape}" value="{$label}">
		{else}
			<div class="form-group">
				<label class="col-md-3 control-label">{$label|escape}:</label>
				<div class="col-md-9">
					<input type="text" name="{$name|escape}" class="form-control">
				</div>
			</div>
		{/if}
	{/foreach}
	<div class="submit_row">
		<div class="form-group">
			<label class="div"></label>
			<input type="hidden" name="datachannel_execution" value="{$datachannel_execution|escape}">
			<div class="col-md-9 col-md-offset-3">
				<input type="submit" class="btn btn-primary btn-sm" value="{tr}{$button_label}{/tr}">
			</div>
		</div>
	</div>
</form>
