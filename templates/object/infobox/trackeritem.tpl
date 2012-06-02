<table>
	{foreach from=$fields item=field}
		<tr>
			<th>{$field.name|escape}</th>
			<td>{trackeroutput field=$field item=$item process=y showlinks=n}</td>
		</tr>
	{/foreach}
</table>
{if $can_modify}
	<a class="service-dialog" href="{service controller=tracker action=update_item trackerId=$item.trackerId itemId=$item.itemId}" title="{tr}Edit{/tr}">{icon _id="pencil" alt="{tr}Edit{/tr}"}</a>
{/if}
{if $can_remove}
	<a class="service-dialog" href="{service controller=tracker action=remove_item trackerId=$item.trackerId itemId=$item.itemId}" title="{tr}Delete{/tr}">{icon _id="cross" alt="{tr}Delete{/tr}"}</a>
{/if}
