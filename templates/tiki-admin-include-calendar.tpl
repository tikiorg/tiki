<br />
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}To add/remove calendars, look for "Calendar" under "Admin" on the application menu, or{/tr} <a class="rbox-link" href="tiki-admin_calendars.php">{tr}click here{/tr}</a>.</div>
</div>
<br />

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
<td><input type="checkbox" name="calendar_sticky_popup" {if $calendar_sticky_popup eq 'y'}checked="checked"{/if}/></td>
</tr>
<tr class="form">
<td><label>{tr}Group calendar item view tab{/tr}</label></td>
<td><input type="checkbox" name="calendar_view_tab" {if $calendar_view_tab eq 'y'}checked="checked"{/if}/></td>
</tr>

<tr class="form">
<td><label>{tr}Default view mode{/tr}</label></td>
<td><select name="calendar_view_mode">
  <option value="day" {if $calendar_view_mode eq 'day'}selected="selected"{/if}>{tr}Day{/tr}</option>
  <option value="week" {if $calendar_view_mode eq 'week'}selected="selected"{/if}>{tr}Week{/tr}</option>
  <option value="month" {if $calendar_view_mode eq 'month'}selected="selected"{/if}>{tr}Month{/tr}</option>
  <option value="quarter" {if $calendar_view_mode eq 'quarter'}selected="selected"{/if}>{tr}Quarter{/tr}</option>
  <option value="semester" {if $calendar_view_mode eq 'semester'}selected="selected"{/if}>{tr}Semester{/tr}</option>
  <option value="year" {if $calendar_view_mode eq 'year'}selected="selected"{/if}>{tr}Year{/tr}</option>
</select></td>
</tr>

<tr class="form">
<td><label>{tr}Calendar manual selection of time/date{/tr}</label></td>
<td><input type="checkbox" name="feature_cal_manual_time" {if $feature_cal_manual_time eq 'y'}checked="checked"{/if}/></td>
</tr>

<tr class="form">
<td><label>{tr}JsCalendar{/tr}</label></td>
<td><input type="checkbox" name="feature_jscalendar" {if $feature_jscalendar eq 'y'}checked="checked"{/if}/></td>
</tr>

<tr class="form">
<td><label>{tr}First day of the week{/tr}</label></td>
<td>
<select name="calendar_firstDayofWeek">
<option value="6"{if $calendar_firstDayofWeek eq "6"} selected="selected"{/if}>{tr}Saturday{/tr}</option>
<option value="0"{if $calendar_firstDayofWeek eq "0"} selected="selected"{/if}>{tr}Sunday{/tr}</option>
<option value="1"{if $calendar_firstDayofWeek eq "1"} selected="selected"{/if}>{tr}Monday{/tr}</option>
<option value="user"{if $calendar_firstDayofWeek eq "user"} selected="selected"{/if}>{tr}Depends user language{/tr}</option>
</select>
</td>
</tr>

<tr class="form">
<td><label>{tr}Split hours in periods of{/tr}</label></td>
<td>
<select name="calendar_timespan">
<option value="1"{if $calendar_timespan eq "1"} selected="selected"{/if}>{tr}1 minute{/tr}</option>
<option value="5"{if $calendar_timespan eq "5"} selected="selected"{/if}>{tr}5 minutes{/tr}</option>
<option value="10"{if $calendar_timespan eq "10"} selected="selected"{/if}>{tr}10 minutes{/tr}</option>
<option value="15"{if $calendar_timespan eq "15"} selected="selected"{/if}>{tr}15 minutes{/tr}</option>
<option value="30"{if $calendar_timespan eq "30"} selected="selected"{/if}>{tr}30 minutes{/tr}</option>
</select>
</td>
</tr>

<tr>
<td colspan="2" class="button"><input type="submit" name="calprefs" value="{tr}Change settings{/tr}" /></td>
</tr>
</table>
</form>
</div>
</div>
