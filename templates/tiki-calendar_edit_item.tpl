<h1><a class="pagetitle" href="tiki-calendar_edit_item.php">{tr}Calendar Item{/tr}</a></h1>

<div class="page-bar">
&nbsp;
{if $tiki_p_view_calendar eq 'y'}
<span class="button2"><a href="tiki-calendar.php" class="linkbut">{tr}Calendar{/tr}</a></span>
{/if}
{if $tiki_p_admin eq 'y'}
<span class="button2"><a href="tiki-admin.php?page=calendar" class="linkbut">{tr}Config Calendars{/tr}</a></span>
{/if}
{if $tiki_p_admin_calendar eq 'y'}
<span class="button2"><a href="tiki-admin_calendars.php?calendarId={$calendarId}" class="linkbut">{tr}Edit Calendar{/tr}</a></span>
{/if}
<br /><br />
&nbsp;
{if $id}
{if $edit}
<span class="button2"><a href="tiki-calendar_edit_item.php?viewcalitemId={$id}" class="linkbut">{tr}View event{/tr}</a></span>
{elseif $tiki_p_change_events eq 'y'}
<span class="button2"><a href="tiki-calendar_edit_item.php?calitemId={$id}" class="linkbut">{tr}Edit event{/tr}</a></span>
{/if}
{/if}
{if $tiki_p_add_events eq 'y'}
<span class="button2"><a href="tiki-calendar_edit_item.php" class="linkbut">{tr}New event{/tr}</a></span>
{/if}
{if $id}

{/if}
</div>

<div class="wikitext">

{if $edit}
<form action="{$myurl}" method="post" name="f">
<input type="hidden" name="save[user]" value="{$calitem.user}" />
{if $id}
<input type="hidden" name="save[calitemId]" value="{$id}" />
{/if}
{/if}

<table width="100%">
<tr><td>
<table class="normal">
{if $edit}
<tr><td colspan="2"><input type="submit" name="act" value="Save" />
{tr}in{/tr}
<span class="linkbut" style="background-color:#{$listcals.$calendarId.custombgcolor};color:#{$listcals.$calendarId.customfgcolor}">{$listcals.$calendarId.name}</span>
{tr}or{/tr} 
<input type="submit" name="act" value="{tr}Go to{/tr}" onclick="document.location='{$myurl}?calendarId='+document.getElementById('calid').value;return false;" />
<input type="submit" name="act" value="{tr}Duplicate to{/tr}" onclick="document.location='{$myurl}?calendarId='+document.getElementById('calid').value+'&amp;calitemId={$id}&amp;duplicate=1';return false;" />
<select name="save[calendarId]" id="calid">
{foreach item=it key=itid from=$listcals}
<option value="{$it.calendarId}"{if $calendarId eq $itid} selected="selected"{/if}>{$it.name}</option>
{/foreach}
</select>
</td></tr>
{else}
<tr class="formcolor"><td>
{tr}Calendar{/tr}</td>
<td>{$listcals.$calendarId.name}
</td></tr>
{/if}

<tr class="formcolor">
<td>{tr}Event Name{/tr}</td>
<td>
{if $edit}
<input type="text" name="save[name]" value="{$calitem.name|escape}" size="32" style="width:90%;"/>
{else}
{$calitem.name}
{/if}
</td>
</tr>
<tr >
<td>{tr}Start{/tr}</td><td>
{if $edit}
<table cellpadding="0" cellspacing="0" border="0" style="border:0;">
<tr><td style="border:0;padding-top:2px;">
<a href="#" onclick="document.f.Time_Hour.selectedIndex=(document.f.Time_Hour.selectedIndex+1);"><img src="pics/icons/plus_small.png" height="8" width="11" border="0" align="left" /></a>
</td>
<td rowspan="2" style="border:0;padding-top:2px;">
{if $feature_jscalendar eq 'y'}
{jscalendar id="start" date=$calitem.start fieldname="save[date_start]" align="Bc" showtime='n'}
{else}
{html_select_date prefix="start_date_" time=$calitem.start field_order=$display_field_order}
{/if}
</td>
<td style="border:0;padding-top:2px;">
<a href="#" onclick="document.f.start_Hour.selectedIndex=(document.f.start_Hour.selectedIndex+1);"><img src="pics/icons/plus_small.png" height="8" width="11" border="0" align="left" /></a>
</td>
<td rowspan="2" style="border:0;">
{html_select_time prefix="start_" display_seconds=false time=$calitem.start minute_interval=$calendar_timespan hour_minmax=$hour_minmax}
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
{else}
{$calitem.start|tiki_long_datetime}
{/if}
</td>
</tr>
<tr >
<td>{tr}End{/tr}</td><td>
{if $edit}
<input type="hidden" name="save[end_or_duration]" value="end" id="end_or_duration" />
<table cellpadding="0" cellspacing="0" border="0" style="border:0;display:block;" id="end_date"> 
<tr><td style="border:0;padding-top:2px;">
<a href="#" onclick="document.f.Time_Hour.selectedIndex=(document.f.Time_Hour.selectedIndex+1);"><img src="pics/icons/plus_small.png" height="8" width="11" border="0" align="left" /></a>
</td>
<td rowspan="2" style="border:0;">
{if $feature_jscalendar eq 'y'}
{jscalendar id="end" date=$calitem.end fieldname="save[date_end]" align="Bc" showtime='n'}
{else}
{html_select_date prefix="start_date_" time=$calitem.start field_order=$display_field_order}
{/if}
</td>
<td style="border:0;padding-top:2px;">
<a href="#" onclick="document.f.end_Hour.selectedIndex=(document.f.end_Hour.selectedIndex+1);"><img src="pics/icons/plus_small.png" height="8" width="11" border="0" align="left" /></a>
</td>
<td rowspan="2" style="border:0;">
{html_select_time prefix="end_" display_seconds=false time=$calitem.time_end minute_interval=$calendar_timespan hour_minmax=$hour_minmax}
</td>
<td style="border:0;padding-top:2px;">
<a href="#" onclick="document.f.end_Minute.selectedIndex=(document.f.end_Minute.selectedIndex+1);"><img src="pics/icons/plus_small.png" height="8" width="11" border="0" align="left" /></a>
</td>
<td rowspan="2" style="border:0;padding-top:2px;"><a href="#" onclick="document.getElementById('end_or_duration').value='duration';flip('end_duration');flip('end_date');return false;">{tr}Duration{/tr}</a>
</tr>
<tr><td style="border:0;">
<a href="#" onclick="document.f.Time_Hour.selectedIndex=(document.f.Time_Hour.selectedIndex-1);"><img src="pics/icons/minus_small.png" height="8" width="11" border="0" align="left" /></a>
</td><td style="border:0;">
<a href="#" onclick="document.f.end_Hour.selectedIndex=(document.f.end_Hour.selectedIndex-1);"><img src="pics/icons/minus_small.png" height="8" width="11" border="0" align="left" /></a>
</td><td style="border:0;">
<a href="#" onclick="document.f.end_Minute.selectedIndex=(document.f.end_Minute.selectedIndex-1);"><img src="pics/icons/minus_small.png" height="8" width="11" border="0" align="left" /></a>
</td>
</td>
</tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" style="border:0;display:none;" id="end_duration">
<tr><td style="border:0;padding-top:2px;">
<a href="#" onclick="document.f.duration_Hour.selectedIndex=(document.f.duration_Hour.selectedIndex+1);"><img src="pics/icons/plus_small.png" height="8" width="11" border="0" align="left" /></a>
</td><td style="border:0;" rowspan="2">
{html_select_time prefix="duration_" display_seconds=false time=$calitem.duration|default:'01:00' minute_interval=$calendar_timespan}
</td><td style="border:0;padding-top:2px;">
<a href="#" onclick="document.f.duration_Minute.selectedIndex=(document.f.duration_Minute.selectedIndex+1);"><img src="pics/icons/plus_small.png" height="8" width="11" border="0" align="left" /></a>
</td>
<td rowspan="2" style="border:0;padding-top:2px;"><a href="#" onclick="document.getElementById('end_or_duration').value='end';flip('end_date');flip('end_duration');return false;">{tr}Date and time of end{/tr}</a>
</tr>
<tr><td style="border:0;">
<a href="#" onclick="document.f.duration_Hour.selectedIndex=(document.f.duration_Hour.selectedIndex-1);"><img src="pics/icons/minus_small.png" height="8" width="11" border="0" align="left" /></a>
</td><td style="border:0;">
<a href="#" onclick="document.f.duration_Minute.selectedIndex=(document.f.duration_Minute.selectedIndex-1);"><img src="pics/icons/minus_small.png" height="8" width="11" border="0" align="left" /></a>
</td>
</tr>
</table>

{else}
{$calitem.end|tiki_long_datetime}
{/if}
</td>
</tr>
<tr class="formcolor">
<td colspan="2">
{if $edit}
<textarea cols="62" rows="8" name="save[description]" wrap="soft" style="width:98%;">{$calitem.description}</textarea>
{else}
{$calitem.description|default:"<i>No description</i>"}
{/if}
</td>
</tr>
</table>
</td>

<td>

<table class="normal">
<tr>
<td colspan="2">
<table cellpadding="0" cellspacing="0" border="0" style="border:0;" width="100%">
<tr><td>
<div class="statusbox{if $calitem.status eq 0} status0{/if}">
{if $edit}
<input id="status0" type="radio" name="save[status]" value="0"{if $calitem.status eq 0} checked="checked"{/if} />
<label for="status0">{tr}Tentative{/tr}</label>
{else}
{tr}Tentative{/tr}
{/if}
</div>
<div class="statusbox{if $calitem.status eq 1} status1{/if}">
{if $edit}
<input id="status1" type="radio" name="save[status]" value="1"{if $calitem.status eq 1} checked="checked"{/if} />
<label for="status1">{tr}Confirmed{/tr}</label>
{else}
{tr}Confirmed{/tr}
{/if}
</div>
<div class="statusbox{if $calitem.status eq 2} status2{/if}">
{if $edit}
<input id="status2" type="radio" name="save[status]" value="2"{if $calitem.status eq 2} checked="checked"{/if} />
<label for="status2">{tr}Cancelled{/tr}</label>
{else}
{tr}Cancelled{/tr}
{/if}
</div>
</td>

<td align="right" style="text-align:right;">
<div style="display:{if $calendar.custompriorities eq 'y'}block{else}none{/if};width:120px;padding:4px;border:1px solid #888;text-align:center;" id="calprio">
{tr}Priority{/tr}<br />
{if $edit}
<select name="save[priority]" style="background-color:#{$listprioritycolors[$calitem.priority]};font-size:150%;width:90%;"
onchange="this.style.bacgroundColor='#'+this.selectedIndex.value;">
{foreach item=it from=$listpriorities}
<option value="{$it}" style="background-color:#{$listprioritycolors[$it]};"{if $calitem.priority eq $it} selected="selected"{/if}>{$it}</option>
{/foreach}
</select>
{else}
<span style="background-color:#{$listprioritycolors[$calitem.priority]};font-size:150%;width:90%;padding:1px 4px">{$calitem.priority}</span>
{/if}
</div>

</td></tr></table>
</td></tr>
<tr class="formcolor" style="display:{if $calendar.customcategories eq 'y'}tablerow{else}none{/if};" id="calcat">
<td>{tr}Category{/tr}</td>
<td>
{if $edit}
{if count($listcats)}
<select name="save[categoryId]">
<option value=""></option>
{foreach item=it from=$listcats}
<option value="{$it.categoryId}"{if $calitem.categoryId eq $it.categoryId} selected="selected"{/if}>{$it.name}</option>
{/foreach}
</select>
{tr}or new{/tr} {/if}
<input type="text" name="save[newcat]" value="" />
{else}
{$calitem.categoryName}
{/if}
</td>
</tr>
<tr class="formcolor" style="display:{if $calendar.customlocations eq 'y'}tablerow{else}none{/if};" id="calloc">
<td>{tr}Location{/tr}</td>
<td>
{if $edit}
{if count($listlocs)}
<select name="save[locationId]">
<option value=""></option>
{foreach item=it from=$listlocs}
<option value="{$it.locationId}"{if $calitem.locationId eq $it.locationId} selected="selected"{/if}>{$it.name}</option>
{/foreach}
</select>
{tr}or new{/tr} {/if}
<input type="text" name="save[newloc]" value="" />
{else}
{$calitem.locationName}
{/if}
</td>
</tr>
<tr class="formcolor">
<td>{tr}URL{/tr}</td>
<td>
{if $edit}
<input type="text" name="save[url]" value="{$calitem.url}" size="32" style="width:90%;" />
{else}
<a href="{$calitem.url|escape:'url'}">{$calitem.url}</a>
{/if}
</td>
</tr>
<tr class="formcolor" style="display:{if $calendar.customlanguages eq 'y'}tablerow{else}none{/if};" id="calcat">
<td>{tr}Language{/tr}</td>
<td>
{if $edit}
<select name="save[lang]">
<option value=""></option>
{foreach item=it from=$listlanguages}
<option value="{$it.value}"{if $calitem.lang eq $it.value} selected="selected"{/if}>{$it.name}</option>
{/foreach}
</select>
{else}
{$calitem.lang}
{/if}
</td>
</tr>

<tr><td colspan="2">&nbsp;</td></tr>

<tr class="formcolor" style="display:{if $calendar.customparticipants eq 'y'}tablerow{else}none{/if};" id="calorg">
<td>{tr}Organized by{/tr}</td>
<td>
{if $edit}
<input type="text" name="save[organizers]" value="{$calitem.organizers}" style="width:90%;" />
{else}
{$calitem.organizers}
{/if}
</td>
</tr>
<tr class="formcolor" style="display:{if $calendar.customparticipants eq 'y'}tablerow{else}none{/if};" id="calpart">
<td>{tr}Participants{/tr}</td>
<td>
{if $edit}
<input type="text" name="save[participants]" value="{$calitem.participants}" style="width:90%;" />
{else}
{$calitem.participants}
{/if}
</td>
</tr>
</table>

</td></tr></table>

</form>

</div>

