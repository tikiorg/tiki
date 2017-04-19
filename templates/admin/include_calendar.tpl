<form class="form-horizontal" action="tiki-admin.php?page=calendar" method="post">
	{ticket}
	<div class="t_navbar margin-bottom-md clearfix">
		<a role="link" class="btn btn-link tips" href="tiki-admin_calendars.php" title=":{tr}Calendars listing{/tr}">
			{icon name="list"} {tr}Calendars{/tr}
		</a>
		{include file='admin/include_apply_top.tpl'}
	</div>
	<fieldset>
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_calendar visible="always"}
	</fieldset>
	<fieldset>
		<legend>{tr}Plugins{/tr}</legend>
		{preference name=wikiplugin_calendar}
		{preference name=wikiplugin_events}
		{preference name=wikiplugin_mcalendar}
		{preference name=wikiplugin_addtogooglecal}
	</fieldset>
	<fieldset>
		<legend>{tr}General settings{/tr}{help url="Calendar+Admin"}</legend>
		{preference name=feature_default_calendars}
		<div class="adminoptionboxchild" id="feature_default_calendars_childcontainer">
			{preference name=default_calendars}
		</div>
		{preference name=calendar_view_mode}
		{preference name=calendar_list_begins_focus}
		{preference name=calendar_firstDayofWeek}
		{preference name=calendar_timespan}
		{preference name=feature_cal_manual_time}
		{preference name=calendar_export}
		{preference name=calendar_export_item}
		{preference name=calendar_addtogooglecal}
		{preference name=calendar_fullcalendar}
		{preference name=feature_jscalendar mode=invert}
		<div class="adminoptionboxchild" id="feature_jscalendar_childcontainer">
			{preference name=calendar_start_year}
			{preference name=calendar_end_year}
		</div>
		{preference name=calendar_sticky_popup}
		{preference name=feature_action_calendar}
		{preference name=calendar_view_tab}
		{preference name=calendar_view_days}
		{preference name=calendar_description_is_html}
		{preference name=calendar_watch_editor}
	</fieldset>
	{include file='admin/include_apply_bottom.tpl'}
</form>
