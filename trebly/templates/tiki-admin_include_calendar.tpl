{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}To add/remove calendars, look for "Calendar" under "Admin" on the application menu, or{/tr} <a class="rbox-link" href="tiki-admin_calendars.php">{tr}Click Here{/tr}</a>.
{/remarksbox}

<form action="tiki-admin.php?page=calendar" method="post">
	<div class="input_submit_container clear" style="text-align: right;">
		<input type="submit" name="calprefs" value="{tr}Change settings{/tr}" />
	</div>

	<fieldset>
		<legend>{tr}General settings{/tr}{help url="Calendar+Admin"}</legend>
		<div class="adminoptionbox">
			<div class="adminoptionlabel">
				{tr}Default calendars to display:{/tr}
				{if $rawcals|@count ge '1'}
					<div class="adminoptionlabel">
						<input type="radio" id="feature_default_calendars1" name="feature_default_calendars" value="n" {if $prefs.feature_default_calendars neq 'y'}checked="checked"{/if} onclick="flip('default_calendars');" />
						<label for="feature_default_calendars1">{tr}All calendars{/tr}</label>
					</div>
					<div class="adminoptionlabel">
						<input type="radio" id="feature_default_calendars2" name="feature_default_calendars" value="y" {if $prefs.feature_default_calendars eq 'y'}checked="checked"{/if} onclick="flip('default_calendars');" />
						<label for="feature_default_calendars2">{tr}A subset of available calendars{/tr}</label>
					</div>
					<div class="adminoptionboxchild" id="default_calendars" style="display:{if $prefs.feature_default_calendars neq 'y'}none{else}block{/if};">
						{foreach item=k from=$rawcals}
							<div class="adminoption">
								<div class="adminoption">
									<input type="checkbox" name="default_calendars[]" id="{$k.calendarId}" value="{$k.calendarId}" {if in_array($k.calendarId,$prefs.site_default_calendars)}checked="checked"{/if} />
								</div>
								<div class="adminoptionlabel">
									<label for="{$k.calendarId}">{$k.name|escape}</label>
								</div>
							</div>
						{/foreach}
					</div>
				{else}
					{tr}None{/tr} {button href="tiki-admin_calendars.php?show=mod" _text="{tr}Create calendar{/tr}"}
				{/if}
			</div>
		</div>

		{preference name=calendar_view_mode}
		{preference name=calendar_list_begins_focus}
		{preference name=calendar_firstDayofWeek}
		{preference name=calendar_timespan}
		{preference name=feature_cal_manual_time}
		{preference name=calendar_addtogooglecal}

	<div class="adminoptionbox">
		<div class="adminoption">
			<input type="checkbox" id="feature_jscalendar" name="feature_jscalendar" {if $prefs.feature_jscalendar eq 'y'}checked="checked" {/if}onclick="flip('usejscalendar');" />
		</div>
		<div class="adminoptionlabel">
			<label for="feature_jscalendar">{tr}JS calendar{/tr}</label>
			{help url="Js+Calendar"}
		</div>
		</div>
		<div id="usejscalendar" style="display:{if $prefs.feature_jscalendar eq 'y'}none{else}block{/if}">
			{icon _id=information} {tr}Year selection is valid when the JS Calendar <strong>is not</strong> enabled{/tr}.
			{preference name=calendar_start_year}
			{preference name=calendar_end_year}
		</div>

		{preference name=calendar_sticky_popup}
		{preference name=feature_action_calendar}
		{preference name=calendar_view_tab}
		{preference type='multicheckbox' name='calendar_view_days'}
	</fieldset>
	<div class="input_submit_container clear" style="text-align: center;">
		<input type="submit" name="calprefs" value="{tr}Change settings{/tr}" />
	</div>
</form>

