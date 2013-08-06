<form class="simple" method="post" action="{service controller=managestream action=tracker_filter}">
	<label>
		{tr}Notes{/tr}
		<textarea name="notes">{$rule.notes|escape}</textarea>
	</label>
	<label>
		{tr}Source Event{/tr}
		<select name="sourceEvent">
			{foreach from=$eventTypes item=eventName}
				<option value="{$eventName|escape}"{if $rule.eventType eq $eventName} selected{/if}>{$eventName|escape}</option>
			{/foreach}
		</select>
	</label>
	<label>
		{tr}New Event{/tr}
		<input type="text" name="targetEvent" value="{$targetEvent|escape}"/>
	</label>
	<label>
		{tr}Tracker{/tr}
		<select name="tracker">
			{foreach from=$trackers.data item=tracker}
				<option value="{$tracker.trackerId|escape}"{if $tracker.trackerId eq $targetTracker} selected{/if}>{$tracker.name|escape}</option>
			{/foreach}
		</select>
	</label>
	<label>
		{tr}Values{/tr}
		<textarea name="parameters" rows="7">{$parameters|escape}</textarea>
	</label>
	<div class="submit">
		<input type="hidden" name="ruleId" value="{$rule.ruleId|escape}"/>
		<input type="submit" value="{tr}Save Rule{/tr}"/>
	</div>
</form>
