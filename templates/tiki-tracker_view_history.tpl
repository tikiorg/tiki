{title help="trackers"}{tr}Tracker Item History{/tr}{/title}
<div class="t_navbar">
	{button _keepall='y' href="tiki-view_tracker_item.php" itemId=$item_info.itemId class="btn btn-default" _text="{tr}View Tracker Item{/tr}"}
</div>

{if $logging eq 0}
	{remarksbox title="{tr}Not logging{/tr}" type="warning"}
		{tr}Tracker changes are not being logged: Go to <a href="tiki-admin_actionlog.php?action_log_type=trackeritem&cookietab=2" >Action log admin</a> to enable{/tr}
	{/remarksbox}
{/if}

<div class="clearfix">
	<form method="post" class="form-inline">
		<div class="form-group">
			<label class="control-label">{tr}Version{/tr} <input type="text" name="version" value="{if !empty($filter.version)}{$filter.version|escape}{/if}"></label>
		</div>
		<div class="form-group">
			<label class="control-label">{tr}Field ID{/tr} <input type="text" name="fieldId" value="{if !empty($fieldId)}{$fieldId|escape}{/if}"></label>
		</div>
		<input type="submit" class="btn btn-default btn-sm" name="Filter" value="{tr}Filter{/tr}">
	</form>
</div>
<br/>

<div class="table-responsive">
	<table class="table">
		<tr>
			<th>{tr}Version{/tr}</th>
			<th>{tr}Date{/tr}</th>
			<th>{tr}User{/tr}</th>
			<th>{tr}Field ID{/tr}</th>
			<th>{tr}Field{/tr}</th>
			<th>{tr}Old{/tr}</th>
			<th>{tr}New{/tr}</th>
		</tr>

		{foreach from=$history item=hist}
			{if $hist.value neq $hist.new}
				{assign var='fieldId' value=$hist.fieldId}
				{assign var='field_value' value=$field_option[$fieldId]}
				<tr>
					<td class="id">{$hist.version|escape}</td>
					<td class="date">{$hist.lastModif|tiki_short_datetime}</td>
					<td class="username">{$hist.user|username}</td>
					<td class="text">
						{if $fieldId ne -1}{$fieldId}{/if}
					</td>
					<td class="text">
						{if $fieldId eq -1}_{tr}Status{/tr}_{else}{$field_option[$fieldId].name}{/if}
					</td>
					{if $field_value.fieldId}
						<td class="text">{$field_value.value=$hist.value}{trackeroutput field=$field_value list_mode=n item=$item_info history=y process=y}</td>
						<td class="text">{$field_value.value=$hist.new}{trackeroutput field=$field_value list_mode=n item=$item_info history=y process=y}</td>
					{else}
						<td class="text">{$hist.value|escape}</td>
						<td class="text">{$hist.new|escape}</td>
					{/if}
				</tr>
			{/if}
		{/foreach}
	</table>
</div>

{pagination_links cant=$cant offset=$offset step=$prefs.maxRecords}
{/pagination_links}
