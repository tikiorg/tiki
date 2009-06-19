<h1>{$title|escape}</h1>
<table class="normal">
	{foreach from=$tracker.fields item=field}
		<tr class="formcolor">
			<td class="formlabel">
				{$field.name|escape}
			</td>
			<td class="formcontent">
				{include file='tracker_item_field_value.tpl' field_value=$field}
			</td>
		</tr>
	{/foreach}
</table>
