{button _class="add_tracker_button" _text="{tr}Add Tracker{/tr}" _htmlelement="role_main" _template="tiki-tracker_export_join_designer.tpl" _auto_args="*" _title="{tr}Add Tracker To Tracker Report{/tr}"|cat:$uWarning}
{button _class="view_button" _text="{tr}View{/tr}" _htmlelement="role_main" _template="tiki-tracker_export_join_designer.tpl" _auto_args="*" _title="{tr}View Tracker Report{/tr}"|cat:$uWarning}
<br>
<br>
<div id="trackerElements" style="display:none;">
	<select class="trackerList">
		<option value="">{tr}Pick tracker to join{/tr}</option>
		{foreach from=$trackers item=tracker}
			<option value="{$tracker.trackerId}">{$tracker.name}</option>
		{/foreach}
	</select>

	<select class="trackerFieldList">
		<option value="">{tr}Pick fields to join on{/tr}</option>
		{foreach from=$trackerFields item=field}
			<option class="tracker_option_{$field.trackerId}" value="{$field.fieldId}">{$field.trackerName} - {$field.fieldName}</option>
		{/foreach}
	</select>

	<div class="trackerFieldCheckboxList">
		{foreach from=$trackerFields item=field}
			<input type="checkbox" checked="true" class="tracker_checkbox_{$field.trackerId} tracker_checkbox" value="{$field.fieldId}" name="{$field.trackerName} - {$field.fieldName}">
		{/foreach}
	</div>

	<select class="trackerJoinType">
		<option value="">{tr}Join inner{/tr}</option>
		<option value="outer">{tr}Join outer{/tr}</option>
	</select>

	<div class="trackerStatusType">
		<h5>Tracker Status</h5>
		<input type="checkbox" checked="true" class="tracker_status_type" value="o" name="Tracker - Open"> {tr}Tracker - Open{/tr}<br>
		<input type="checkbox" checked="true" class="tracker_status_type" value="p" name="Tracker - Pending"> {tr}Tracker - Pending{/tr}<br>
		<input type="checkbox" checked="true" class="tracker_status_type" value="c" name="Tracker - Closed"> {tr}Tracker - Closed{/tr}
	</div>
</div>
<div id="reportDesigner">

</div>
<br>
<br>
{button _class="add_tracker_button" _text="{tr}Add Tracker{/tr}" _htmlelement="role_main" _template="tiki-tracker_export_join_designer.tpl" _auto_args="*" _title="{tr}Add Tracker To Tracker Report{/tr}"|cat:$uWarning}
{button _class="view_button" _text="{tr}View{/tr}" _htmlelement="role_main" _template="tiki-tracker_export_join_designer.tpl" _auto_args="*" _title="{tr}View Tracker Report{/tr}"|cat:$uWarning}
