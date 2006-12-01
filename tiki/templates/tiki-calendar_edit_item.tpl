<h1><a class="pagetitle" href="tiki-calendar_edit_item.php">{tr}Calendar Item{/tr}</a></h1>

<div class="page-bar">
&nbsp;
<a href="tiki-calendar.php" class="linkbut">Calendar</a>
<a href="tiki-admin.php?page=calendar" class="linkbut">Config Calendars</a>
<a href="tiki-admin_calendars.php?calendarId=" class="linkbut">Edit Calendar</a>
<a href="tiki-calendar_edit_item.php" class="linkbut">New item</a>
{if $id}

{/if}
</div>

<div class="wikitext">

<form action="{$myurl}" method="post" name="f">
<input type="hidden" name="save[user]" value="{$calitem.user}" />
{if $id}
<input type="hidden" name="save[calitemId]" value="{$id}" />
{/if}

<table class="normal">
<tr><td>
<table class="normal">
<tr><td colspan="2"><input type="submit" name="act" value="Save" />
{tr}in{/tr}
<select name="save[calendarId]" id="calid">
{foreach item=it from=$listcals}
<option value="{$it.calendarId}"{if $calendarId eq $it.calendarId} selected="selected"{/if}>{$it.name}</option>
{/foreach}
</select>
{tr}or{/tr} <input type="submit" name="act" value="Go to" onclick="document.location='{$myurl}?calendarId='+document.getElementById('calid').value;return false;" />
</td></tr>

<tr class="formcolor">
<td>{tr}Name{/tr}</td>
<td>
<input type="text" name="save[name]" value="{$calitem.name|escape}" size="32" style="width:90%;"/>
</td>
</tr>
<tr class="formcolor">
<td>{tr}From{/tr}</td><td>
<table cellpadding="0" cellspacing="0" border="0" style="border:0;">
<tr><td style="border:0;padding-top:2px;">
<a href="#" onclick="document.f.Time_Hour.selectedIndex=(document.f.Time_Hour.selectedIndex+1);"><img src="pics/icons/plus_small.png" height="8" width="11" border="0" align="left" /></a>
</td>
<td rowspan="2" style="border:0;padding-top:2px;">
{jscalendar id="start" date=$calitem.start fieldname="save[date_start]" align="Bc" showtime='n'}
</td>
<td style="border:0;padding-top:2px;">
<a href="#" onclick="document.f.start_Hour.selectedIndex=(document.f.start_Hour.selectedIndex+1);"><img src="pics/icons/plus_small.png" height="8" width="11" border="0" align="left" /></a>
</td>
<td rowspan="2" style="border:0;">
{html_select_time prefix="start_" display_seconds=false time=$calitem.start minute_interval=$calendar_timespan }
</td>
<td style="border:0;padding-top:2px;">
<a href="#" onclick="document.f.start_Minute.selectedIndex=(document.f.start_Minute.selectedIndex+1);"><img src="pics/icons/plus_small.png" height="8" width="11" border="0" align="left" /></a>
</td></tr>
<tr><td style="border:0;">
<a href="#" onclick="document.f.Time_Hour.selectedIndex=(document.f.Time_Hour.selectedIndex-1);"><img src="pics/icons/minus_small.png" height="8" width="11" border="0" align="left" /></a>
</td><td style="border:0;">
<a href="#" onclick="document.f.start_Hour.selectedIndex=(document.f.start_Hour.selectedIndex-1);"><img src="pics/icons/minus_small.png" height="8" width="11" border="0" align="left" /></a>
</td><td style="border:0;">
<a href="#" onclick="document.f.start_Minute.selectedIndex=(document.f.start_Minute.selectedIndex-1);"><img src="pics/icons/minus_small.png" height="8" width="11" border="0" align="left" /></a>
</td></tr>
</table>
</td>
</tr>
<tr class="formcolor">
<td>{tr}to{/tr}</td><td>
<table cellpadding="0" cellspacing="0" border="0" style="border:0;">
<tr><td style="border:0;padding-top:2px;">
<a href="#" onclick="document.f.Time_Hour.selectedIndex=(document.f.Time_Hour.selectedIndex+1);"><img src="pics/icons/plus_small.png" height="8" width="11" border="0" align="left" /></a>
</td>
<td rowspan="2" style="border:0;">
{jscalendar id="end" date=$calitem.end fieldname="save[date_end]" align="Bc" showtime='n'}
</td>
<td style="border:0;padding-top:2px;">
<a href="#" onclick="document.f.end_Hour.selectedIndex=(document.f.end_Hour.selectedIndex+1);"><img src="pics/icons/plus_small.png" height="8" width="11" border="0" align="left" /></a>
</td>
<td rowspan="2" style="border:0;">
{html_select_time prefix="end_" display_seconds=false time=$calitem.end minute_interval=$calendar_timespan }
</td>
<td style="border:0;padding-top:2px;">
<a href="#" onclick="document.f.end_Minute.selectedIndex=(document.f.end_Minute.selectedIndex+1);"><img src="pics/icons/plus_small.png" height="8" width="11" border="0" align="left" /></a>
</td></tr>
<tr><td style="border:0;">
<a href="#" onclick="document.f.Time_Hour.selectedIndex=(document.f.Time_Hour.selectedIndex-1);"><img src="pics/icons/minus_small.png" height="8" width="11" border="0" align="left" /></a>
</td><td style="border:0;">
<a href="#" onclick="document.f.end_Hour.selectedIndex=(document.f.end_Hour.selectedIndex-1);"><img src="pics/icons/minus_small.png" height="8" width="11" border="0" align="left" /></a>
</td><td style="border:0;">
<a href="#" onclick="document.f.end_Minute.selectedIndex=(document.f.end_Minute.selectedIndex-1);"><img src="pics/icons/minus_small.png" height="8" width="11" border="0" align="left" /></a>
</td></tr>
</table>
</td>
</tr>
<tr class="formcolor">
<td>{tr}Duration{/tr}</td>
<td>
<table cellpadding="0" cellspacing="0" border="0" style="border:0;">
<tr><td style="border:0;padding-top:2px;">
<a href="#" onclick="document.f.Time_Hour.selectedIndex=(document.f.Time_Hour.selectedIndex+1);"><img src="pics/icons/plus_small.png" height="8" width="11" border="0" align="left" /></a>
</td><td style="border:0;" rowspan="2">
{html_select_time display_seconds=false time=$calitem.duration|default:'01:00' minute_interval=$calendar_timespan}
</td><td style="border:0;padding-top:2px;">
<a href="#" onclick="document.f.Time_Minute.selectedIndex=(document.f.Time_Minute.selectedIndex+1);"><img src="pics/icons/plus_small.png" height="8" width="11" border="0" align="left" /></a>
</td></tr>
<tr><td style="border:0;">
<a href="#" onclick="document.f.Time_Hour.selectedIndex=(document.f.Time_Hour.selectedIndex-1);"><img src="pics/icons/minus_small.png" height="8" width="11" border="0" align="left" /></a>
</td><td style="border:0;">
<a href="#" onclick="document.f.Time_Minute.selectedIndex=(document.f.Time_Minute.selectedIndex-1);"><img src="pics/icons/minus_small.png" height="8" width="11" border="0" align="left" /></a>
</td></tr>
</table>
</td></tr>
<tr class="formcolor">
<td colspan="2">
<textarea cols="62" rows="8" name="save[description]" wrap="soft" style="width:98%;">{$calitem.description}</textarea>
</td>
</tr>
<tr><td colspan="2"><input type="submit" name="act" value="Save" />
{tr}in{/tr}
<select name="save[calendarId]" id="calid">
{foreach item=it from=$listcals}
<option value="{$it.calendarId}"{if $calendarId eq $it.calendarId} selected="selected"{/if}>{$it.name}</option>
{/foreach}
</select>
{tr}or{/tr} <input type="submit" name="act" value="Go to" onclick="document.location='tiki-calendar_edit_item.php?calendarId='+document.getElementById('calid').value;return false;" />
</td></tr>
</table>
</td>

<td>
<table class="normal">
<tr>
<td>{tr}Status{/tr}</td>
<td>
<div style="float:right;display:{if $calendar.custompriorities eq 'y'}block{else}none{/if};width:120px;padding:4px;border:1px solid #888;" id="calprio">
{tr}Priority{/tr}<br />
<select name="save[priority]" style="background-color:#{$listprioritycolors[$calitem.priority]};font-size:150%;width:90%;"
onchange="this.style.bacgroundColor='#'+this.selectedIndex.value;">
{foreach item=it from=$listpriorities}
<option value="{$it}" style="background-color:#{$listprioritycolors[$it]};"{if $calitem.priority eq $it} selected="selected"{/if}>{$it}</option>
{/foreach}
</select>
</div>
<span style="background-color:#cc6;padding:0 3px;{if $calitem.status eq 0}border:1px solid #000{/if};">
<input id="status0" type="radio" name="save[status]" value="0"{if $calitem.status eq 0} checked="checked"{/if} />
<label for="status0" style="width:120px;">{tr}Tentative{/tr}</label>
</span><br />
<span style="background-color:#6c6;padding:0 3px;{if $calitem.status eq 1}border:1px solid #000{/if};">
<input id="status1" type="radio" name="save[status]" value="1"{if $calitem.status eq 1} checked="checked"{/if} />
<label for="status1" style="width:120px;">{tr}Confirmed{/tr}</label>
</span><br />
<span style="background-color:#c66;padding:0 3px;{if $calitem.status eq 2}border:1px solid #000{/if};">
<input id="status2" type="radio" name="save[status]" value="2"{if $calitem.status eq 2} checked="checked"{/if} />
<label for="status2" style="width:120px;">{tr}Cancelled{/tr}</label>
</span><br />
</td></tr>
<tr class="formcolor" style="display:{if $calendar.customcategories eq 'y'}tablerow{else}none{/if};" id="calcat">
<td>{tr}Category{/tr}</td>
<td>
{if count($listcats)}
<select name="save[categoryId]">
<option value=""></option>
{foreach item=it from=$listcats}
<option value="{$it.categoryId}"{if $calitem.categoryId eq $it.categoryId} selected="selected"{/if}>{$it.name}</option>
{/foreach}
</select>
{tr}or new{/tr} {/if}
<input type="text" name="save[newcat]" value="" />
</td>
</tr>
<tr class="formcolor" style="display:{if $calendar.customlocations eq 'y'}tablerow{else}none{/if};" id="calloc">
<td>{tr}Location{/tr}</td>
<td>
{if count($listlocs)}
<select name="save[locationId]">
<option value=""></option>
{foreach item=it from=$listlocs}
<option value="{$it.locationId}"{if $calitem.locationId eq $it.locationId} selected="selected"{/if}>{$it.name}</option>
{/foreach}
</select>
{tr}or new{/tr} {/if}
<input type="text" name="save[newloc]" value="" />
</td>
</tr>
<tr class="formcolor">
<td>{tr}URL{/tr}</td>
<td>
<input type="text" name="save[url]" value="{$calitem.url}" size="32" style="width:90%;" />
</td>
</tr>
<tr class="formcolor" style="display:{if $calendar.customlanguages eq 'y'}tablerow{else}none{/if};" id="calcat">
<td>{tr}Language{/tr}</td>
<td>
<select name="save[lang]">
<option value=""></option>
{foreach item=it from=$listlanguages}
<option value="{$it.value}"{if $calitem.lang eq $it.value} selected="selected"{/if}>{$it.name}</option>
{/foreach}
</select>
</td>
</tr>

<tr><td colspan="2">&nbsp;</td></tr>

<tr class="formcolor" style="display:{if $calendar.customparticipants eq 'y'}tablerow{else}none{/if};" id="calorg">
<td>{tr}Organized by{/tr}</td>
<td>
<input type="text" name="save[organizers]" value="{$calitem.organizers}" style="width:90%;" />
</td>
</tr>
<tr class="formcolor" style="display:{if $calendar.customparticipants eq 'y'}tablerow{else}none{/if};" id="calpart">
<td>{tr}Participants{/tr}</td>
<td>
<input type="text" name="save[participants]" value="{$calitem.participants}" style="width:90%;" />
</td>
</tr>
</table>

</td></tr></table>

</form>

</div>

