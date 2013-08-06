{if $rule.ruleId && $rule.ruleType neq 'advanced'}
	{remarksbox title="{tr}Operation not reversible{/tr}"}
		{tr}This action is currently of a basic type. Using the advanced editor will prevent the simple editor to be used.{/tr}
	{/remarksbox}
{/if}
<form class="simple" method="post" action="{service controller=managestream action=advanced}">
	<label>
		{tr}Event{/tr}
		<select name="event">
			{foreach from=$eventTypes item=eventName}
				<option value="{$eventName|escape}"{if $rule.eventType eq $eventName} selected{/if}>{$eventName|escape}</option>
			{/foreach}
		</select>
	</label>
	<label>
		{tr}Notes{/tr}
		<textarea name="notes">{$rule.notes|escape}</textarea>
	</label>
	<label>
		{tr}Rule{/tr}
		<textarea name="rule">{$rule.rule|escape}</textarea>
	</label>
	<div class="submit">
		<input type="hidden" name="ruleId" value="{$rule.ruleId|escape}"/>
		<input type="submit" value="{tr}Save{/tr}"/>
	</div>
</form>
