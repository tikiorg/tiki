{* param status_types, $item.status, $tracker.newItemStatus, form_status *}
<select name="{$form_status}">
	{foreach key=st item=stdata from=$status_types}
		<option value="{$st}" 
			{if (empty($item) and $tracker.newItemStatus eq $st) or (!empty($item) and $item.status eq $st)} selected="selected"{/if}
			style="background-image:url('{$stdata.image}');background-repeat:no-repeat;padding-left:17px;">
			{$stdata.label}
		</option>
	{/foreach}
</select>