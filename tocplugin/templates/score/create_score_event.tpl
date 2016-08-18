{block name=content}
	{if $rowOnly neq 1}
	<tbody class="event-section" data-section="{$eventType}">
		<tr>
			<td colspan="2"><b>{$eventType}</b></td>
			<td class="text-right" colspan="3"><b>{tr}Reversal Event{/tr}</b>:
				<select class="reverse-event-select" name="events[{$eventType}][reversalEvent]" class="form-control">
					<option value="">{tr}None{/tr}</option>
					{foreach from=$eventTypes item=eventName}
						<option value="{$eventName|escape}">
							{$eventName|escape}
						</option>
					{/foreach}
				</select>
			</td>
		</tr>
	{/if}
	<tr class="condition-row">
		<td>
			<input type="text" placeholder="{tr}Unique Name{/tr}" size="30" data-name="label" name="events[{$eventType}][{$rowCount}][ruleId]" value="" />
		</td>
		<td>
			<input type="text" placeholder="{tr}Type (e.g. user){/tr}" size="20" name="events[{$eventType}][{$rowCount}][recipientType]" value="" />
		</td>
		<td>
			<input type="text" size="30" placeholder="{tr}Field value (e.g. user){/tr}" name="events[{$eventType}][{$rowCount}][recipient]" value="" />
		</td>
		<td>
			<input type="text" placeholder="{tr}Pt. value{/tr}" size="10" name="events[{$eventType}][{$rowCount}][score]" value="" />
		</td>
		<td class="text-right">
			<a class="delete-row" href="#">{icon name=delete title="Delete"}</a>
		</td>
	</tr>
	<tr class="advanced-row">
		<td class="text-right">{tr}Valid Triggering Object IDs{/tr}</td>
		<td>
			<input placeholder="Object Ids" type="text" size="20" name="events[{$eventType}][{$rowCount}][expiration]" value="" />
		</td>
		<td class="text-right">{tr}Min. Time Between Scoring{/tr}</td>
		<td>
			<input placeholder="In seconds" type="text" size="10" name="events[{$eventType}][{$rowCount}][validObjectIds]" value="" />
		</td>
	</tr>
	{* Not sure why this JQuery is needed again since it's in include_score.tpl, but it doesn't appear to wokr without it*}
	{jq}
		$('.delete-row').click(function(ev) {
		ev.preventDefault();
		var currentRow = $(this).closest('.condition-row');
		if ($(currentRow).siblings('.condition-row').length > 0) {
			$(currentRow).remove();
		} else {
			$(currentRow).closest('tbody').remove();
		}
	});

	{/jq}
	{if $rowOnly neq 1}
		</tbody>
	{/if}


{/block}