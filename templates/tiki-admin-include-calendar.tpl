<!-- START of {$smarty.template} -->{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To add/remove calendars, look for "Calendar" under "Admin" on the application menu, or{/tr} <a class="rbox-link" href="tiki-admin_calendars.php">{tr}Click Here{/tr}</a>.{/remarksbox}

<form action="tiki-admin.php?page=calendar" method="post">
<div class="cbox">
<table class="admin"><tr><td>
<div align="center" style="padding:1em;"><input type="submit" name="calprefs" value="{tr}Change settings{/tr}" /></div>
<fieldset><legend>{tr}General settings{/tr}{if $prefs.feature_help eq 'y'} {help url="Calendar+Admin"}{/if}</legend>
<div class="adminoptionbox">
	<div class="adminoptionlabel">{tr}Default calendars to display{/tr}:
	{if $rawcals|@count ge '1'}
<div class="adminoptionboxchild">
	{foreach item=k from=$rawcals}
	<div class="adminoption">
		<div class="adminoption"><input type="checkbox" name="default_calendars[]" id="{$k.calendarId}" value="{$k.calendarId}" {if in_array($k.calendarId,$prefs.site_default_calendars)}checked="checked"{/if} /></div>
		<div class="adminoptionlabel"><label for="{$k.calendarId}">{$k.name}</div>
	</div>
{/foreach}
</div>
	{else}{tr}None{/tr} <a href="tiki-admin_calendars.php" class="button" title="{tr}Create calendar{/tr}.">{tr}Create calendar{/tr}.</a>
	{/if}
</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="calendar_view_mode">{tr}Default view mode{/tr}:</label> 
	<select name="calendar_view_mode" id="calendar_view_mode">
  <option value="day" {if $prefs.calendar_view_mode eq 'day'}selected="selected"{/if}>{tr}Day{/tr}</option>
  <option value="week" {if $prefs.calendar_view_mode eq 'week'}selected="selected"{/if}>{tr}Week{/tr}</option>
  <option value="month" {if $prefs.calendar_view_mode eq 'month'}selected="selected"{/if}>{tr}Month{/tr}</option>
  <option value="quarter" {if $prefs.calendar_view_mode eq 'quarter'}selected="selected"{/if}>{tr}Quarter{/tr}</option>
  <option value="semester" {if $prefs.calendar_view_mode eq 'semester'}selected="selected"{/if}>{tr}Semester{/tr}</option>
  <option value="year" {if $prefs.calendar_view_mode eq 'year'}selected="selected"{/if}>{tr}Year{/tr}</option>
	</select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="calendar_firstDayofWeek">{tr}First day of the week{/tr}: </label>
	<select name="calendar_firstDayofWeek" id="calendar_firstDayofWeek">
<option value="6"{if $prefs.calendar_firstDayofWeek eq "6"} selected="selected"{/if}>{tr}Saturday{/tr}</option>
<option value="0"{if $prefs.calendar_firstDayofWeek eq "0"} selected="selected"{/if}>{tr}Sunday{/tr}</option>
<option value="1"{if $prefs.calendar_firstDayofWeek eq "1"} selected="selected"{/if}>{tr}Monday{/tr}</option>
<option value="user"{if $prefs.calendar_firstDayofWeek eq "user"} selected="selected"{/if}>{tr}Depends user language{/tr}</option>
	</select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="calendar_timespan">{tr}Split hours in periods of{/tr}: </label>
	<select name="calendar_timespan" id="calendar_timespan">
<option value="1"{if $prefs.calendar_timespan eq "1"} selected="selected"{/if}>{tr}1 minute{/tr}</option>
<option value="5"{if $prefs.calendar_timespan eq "5"} selected="selected"{/if}>{tr}5 minutes{/tr}</option>
<option value="10"{if $prefs.calendar_timespan eq "10"} selected="selected"{/if}>{tr}10 minutes{/tr}</option>
<option value="15"{if $prefs.calendar_timespan eq "15"} selected="selected"{/if}>{tr}15 minutes{/tr}</option>
<option value="30"{if $prefs.calendar_timespan eq "30"} selected="selected"{/if}>{tr}30 minutes{/tr}</option>
</select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_cal_manual_time" name="feature_cal_manual_time" {if $prefs.feature_cal_manual_time eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_cal_manual_time">{tr}Manual selection of time/date{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_jscalendar" name="feature_jscalendar" {if $prefs.feature_jscalendar eq 'y'}checked="checked" {/if}onclick="flip('usejscalendar');" /></div>
	<div class="adminoptionlabel"><label for="feature_jscalendar">{tr}JS calendar{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Js+Calendar"}{/if}</div>
</div>
<div id="usejscalendar" style="display:{if $prefs.feature_jscalendar eq 'y'}none{else}block{/if}">
{icon _id=information} {tr}Year selection is valid when the JS Calendar <strong>is not</strong> enabled{/tr}.
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="calendar_start_year">{tr}First year in the dropdown{/tr}</label>: <input type="text" name="calendar_start_year" id="calendar_start_year" value="{$prefs.calendar_start_year}" />
	<br />
	<em>{tr}Enter a year or use +/- N to specify a year relative to the current
	year{/tr}.</em></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="calendar_end_year">{tr}Last year in the dropdown{/tr}</label>: <input type="text" id="calendar_end_year" name="calendar_end_year" value="{$prefs.calendar_end_year}" />
	<br />
	<em>{tr}Enter a year or use +/- N to specify a year relative to the current
	year{/tr}.</em></div>
</div>
</div>
</fieldset>


<fieldset><legend>{tr}Group calendars{/tr}</legend>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="calendar_sticky_popup" name="calendar_sticky_popup" {if $prefs.calendar_sticky_popup eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="calendar_sticky_popup">{tr}Sticky popup{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="calendar_view_tab" name="calendar_view_tab" {if $prefs.calendar_view_tab eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="calendar_view_tab">{tr}Item view tab{/tr}</label></div>
</div>
</fieldset>

<div align="center" style="padding:1em;"><input type="submit" name="calprefs" value="{tr}Change settings{/tr}" /></div>
</td></tr></table>
</div>
</form>
<!-- END of {$smarty.template} -->
