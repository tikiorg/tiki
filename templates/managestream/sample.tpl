<form class="simple" method="post" action="{service controller=managestream action=sample}">
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
	{remarksbox title="{tr}Obtain Data{/tr}"}
		{tr}The Event Sampler allow to record the data associated to the events to allow viewing what varibles are available. Visit back this page to see what the last triggered event contains.{/tr}
	{/remarksbox}
	{if $data}
		<pre>{$data|escape}</pre>
	{/if}
	<div class="submit">
		<input type="hidden" name="ruleId" value="{$rule.ruleId|escape}"/>
		<input type="submit" value="{tr}Record Event{/tr}"/>
	</div>
</form>
