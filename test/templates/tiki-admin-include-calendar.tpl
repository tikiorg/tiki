{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To add/remove calendars, look for "Calendar" under "Admin" on the application menu, or{/tr} <a class="rbox-link" href="tiki-admin_calendars.php">{tr}Click Here{/tr}</a>.{/remarksbox}

<div class="cbox">
<div class="cbox-title">
  {tr}{$crumbs[$crumb]->description}{/tr}
  {help crumb=$crumbs[$crumb]}
</div>
<div class="cbox-data">
<form action="tiki-admin.php?page=calendar" method="post">
<table class="admin">
<tr class="form">
<td><label>{tr}Group calendar sticky popup{/tr}</label></td>
<td><input type="checkbox" name="calendar_sticky_popup" {if $prefs.calendar_sticky_popup eq 'y'}checked="checked"{/if}/></td>
</tr>
<tr class="form">
<td><label>{tr}Group calendar item view tab{/tr}</label></td>
<td><input type="checkbox" name="calendar_view_tab" {if $prefs.calendar_view_tab eq 'y'}checked="checked"{/if}/></td>
</tr>

<tr class="form">
<td><label>{tr}Default view mode{/tr}</label></td>
<td><select name="calendar_view_mode">
  <option value="day" {if $prefs.calendar_view_mode eq 'day'}selected="selected"{/if}>{tr}Day{/tr}</option>
  <option value="week" {if $prefs.calendar_view_mode eq 'week'}selected="selected"{/if}>{tr}Week{/tr}</option>
  <option value="month" {if $prefs.calendar_view_mode eq 'month'}selected="selected"{/if}>{tr}Month{/tr}</option>
  <option value="quarter" {if $prefs.calendar_view_mode eq 'quarter'}selected="selected"{/if}>{tr}Quarter{/tr}</option>
  <option value="semester" {if $prefs.calendar_view_mode eq 'semester'}selected="selected"{/if}>{tr}Semester{/tr}</option>
  <option value="year" {if $prefs.calendar_view_mode eq 'year'}selected="selected"{/if}>{tr}Year{/tr}</option>
</select></td>
</tr>

<tr class="form">
<td><label>{tr}Calendar manual selection of time/date{/tr}</label></td>
<td><input type="checkbox" name="feature_cal_manual_time" {if $prefs.feature_cal_manual_time eq 'y'}checked="checked"{/if}/></td>
</tr>

<tr class="form">
<td><label>{tr}JsCalendar{/tr}</label></td>
<td><input type="checkbox" name="feature_jscalendar" {if $prefs.feature_jscalendar eq 'y'}checked="checked"{/if}/></td>
</tr>

<tr class="form">
<td><label>{tr}First day of the week{/tr}</label></td>
<td>
<select name="calendar_firstDayofWeek">
<option value="6"{if $prefs.calendar_firstDayofWeek eq "6"} selected="selected"{/if}>{tr}Saturday{/tr}</option>
<option value="0"{if $prefs.calendar_firstDayofWeek eq "0"} selected="selected"{/if}>{tr}Sunday{/tr}</option>
<option value="1"{if $prefs.calendar_firstDayofWeek eq "1"} selected="selected"{/if}>{tr}Monday{/tr}</option>
<option value="user"{if $prefs.calendar_firstDayofWeek eq "user"} selected="selected"{/if}>{tr}Depends user language{/tr}</option>
</select>
</td>
</tr>

<tr class="form">
<td><label>{tr}Split hours in periods of{/tr}</label></td>
<td>
<select name="calendar_timespan">
<option value="1"{if $prefs.calendar_timespan eq "1"} selected="selected"{/if}>{tr}1 minute{/tr}</option>
<option value="5"{if $prefs.calendar_timespan eq "5"} selected="selected"{/if}>{tr}5 minutes{/tr}</option>
<option value="10"{if $prefs.calendar_timespan eq "10"} selected="selected"{/if}>{tr}10 minutes{/tr}</option>
<option value="15"{if $prefs.calendar_timespan eq "15"} selected="selected"{/if}>{tr}15 minutes{/tr}</option>
<option value="30"{if $prefs.calendar_timespan eq "30"} selected="selected"{/if}>{tr}30 minutes{/tr}</option>
</select>
</td>
</tr>

<tr class="form">
<td><label>{tr}First year in the dropdown, either year number, or relative to current year (+/- N){/tr}<br /><i>{tr}if no jscalendar{/tr}</i></label></td>
<td><input type="text" name="calendar_start_year" value="{$prefs.calendar_start_year}" /></td>
</tr>
<tr class="form">
<td><label>{tr}Last year in the dropdown, either year number, or relative to current year (+/- N){/tr}<br /><i>{tr}if no jscalendar{/tr}</i></label></td>
<td><input type="text" name="calendar_end_year" value="{$prefs.calendar_end_year}" /></td>
</tr>

<tr>
<td colspan="2" class="button"><input type="submit" name="calprefs" value="{tr}Change settings{/tr}" /></td>
</tr>
</table>
</form>
</div>
</div>
