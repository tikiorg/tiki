<form class="simple" method="post" action="{service controller=managestream action=record}">
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
	<div class="submit">
		<input type="hidden" name="ruleId" value="{$rule.ruleId|escape}"/>
		<input type="submit" value="{tr}Record Event{/tr}"/>
	</div>
</form>
