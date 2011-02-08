{title help="trackers"}{tr}Tracker Item History{/tr}{/title}
<div class="navbar">
	 {button href="tiki-view_tracker_item.php?itemId=`$item_info.itemId`" _text="{tr}View Tracker Item{/tr}"}
</div>

<div class="clearfix">
	 <form method="post">
	 <label class="findtitle">{tr}Version{/tr}<input type="text" name="version" value="{if !empty($filter.version)}{$filter.version|escape}{/if}" /></label>
	 <label class="findtitle">{tr}Field ID{/tr}<input type="text" name="fieldId" value="{if !empty($fieldId)}{$fieldId|escape}{/if}" /></label>
	 <input type="submit" name="Filter" value="{tr}Filter{/tr}" />
	 </form>
</div>

<table class="normal">
<tr>
	<th>{tr}Version{/tr}</th>
	<th>{tr}Date{/tr}</th>
	<th>{tr}User{/tr}</th>
	<th>{tr}Field ID{/tr}</th>
	<th>{tr}Field{/tr}</th>
	<th>{tr}Old{/tr}</th>
	<th>{tr}New{/tr}</th>
	{if $prefs.feature_multilingual eq 'y'}<th>{tr}Language{/tr}</th>{/if}
</tr> 
{cycle values="odd,even" print=false}
{foreach from=$history item=hist}
	{assign var='fieldId' value=`$hist.fieldId`}
	{assign var='field_value' value=`$field_option[$fieldId]`}
	<tr class="{cycle}">
		<td class="id">{$hist.version|escape}</td>
		<td class="date">{$hist.lastModif|tiki_short_datetime}</td>
		<td class="username">{$hist.user|username}</td>
		<td class="text">
			{if $fieldId ne -1}{$fieldId}{/if}
		</td>
		<td class="text">
			{if $fieldId eq -1}_{tr}Status{/tr}_{else}{$field_option[$fieldId].name}{/if}
		</td>
		<td class="text">{assign var='field_value.value' value=`$hist.value`}{include file='tracker_item_field_value.tpl' field_value=`$field_value` list_mode=n item=$item_info history=y}</td>
		<td class="text">{assign var='field_value.value' value=`$hist.new`}{include file='tracker_item_field_value.tpl' field_value=`$field_value` list_mode=n item=$item_info history=y}</td>
		{if $prefs.feature_multilingual eq 'y'}
			<td class="text">{$hist.lang|escape}</td>
		{/if}
	</tr>
{/foreach}
</table>
{pagination_links cant=$cant offset=$offset step=$prefs.maxRecords}
{/pagination_links}
