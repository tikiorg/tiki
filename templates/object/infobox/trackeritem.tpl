<table>
	{foreach from=$fields item=field}
		<tr>
			<th>{$field.name|escape}</th>
			<td>{trackeroutput field=$field item=$item process=y showlinks=n}</td>
		</tr>
	{/foreach}
</table>
{if $can_modify}
	<a class="service-dialog" href="{service controller=tracker action=update_item trackerId=$item.trackerId itemId=$item.itemId}">{tr}Edit{/tr}</a>
{/if}
