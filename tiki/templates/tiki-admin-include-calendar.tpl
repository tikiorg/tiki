<br />
<br />
<div class="highlight">{tr}Tip{/tr}: {tr}To add/remove calendars, look for "Calendar" under "Admin" on the application menu, or{/tr} <a class="link"
href="tiki-admin_calendars.php">{tr}click here{/tr}</a>.</div>
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

<tr>
<td colspan="2" class="button"><input type="submit" name="calprefs" value="{tr}Change settings{/tr}" /></td>
</tr>
</table>
</form>
</div>
</div>
