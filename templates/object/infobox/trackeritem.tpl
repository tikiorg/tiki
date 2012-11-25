{if $mode eq 'divs'}
	{foreach from=$fields item=field}
		<h6 class="field_{$field.fieldId}">{$field.name|escape}</h6>
		<div class="field_{$field.fieldId}">{trackeroutput field=$field item=$item process=y showlinks=n}</div>
	{/foreach}
{else}
	<table>
		{foreach from=$fields item=field}
			<tr class="field_{$field.fieldId}">
				<th>{$field.name|escape}</th>
				<td>{trackeroutput field=$field item=$item process=y showlinks=n}</td>
			</tr>
		{/foreach}
	</table>
{/if}
{if $can_modify}
	<a class="service-dialog" href="{service controller=tracker action=update_item trackerId=$item.trackerId itemId=$item.itemId}" title="{tr}Edit{/tr}">{icon _id="pencil" alt="{tr}Edit{/tr}"}</a>
{/if}
{if $can_remove}
	<a class="service-dialog" href="{service controller=tracker action=remove_item trackerId=$item.trackerId itemId=$item.itemId}" title="{tr}Delete{/tr}">{icon _id="cross" alt="{tr}Delete{/tr}"}</a>
{/if}
