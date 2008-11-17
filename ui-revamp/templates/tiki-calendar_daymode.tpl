<table cellpadding="0" cellspacing="0" border="0" id="caltable">
<tr><th style="width:42px">{tr}Hours{/tr}</th><th>{tr}Events{/tr}</th></tr>
{cycle values="odd,even" print=false}
{section name=ar loop=$arows}
<tr><td width="42" class="{cycle advance=false}">{tr}All-Day{/tr}</td>
<td class="{cycle}">
{assign var=calendarId value=$arows[ar].result.calendarId}
{if ($prefs.calendar_view_tab eq "y" or $tiki_p_change_events eq "y") and $arows[ar].calname ne ""}<span  style="float:right;">
<a href="tiki-calendar_edit_item.php?viewcalitemId={$arows[ar].calitemId}"{if $prefs.feature_tabs ne "y"}#details{/if} title="{tr}Details{/tr}">{icon _id='magnifier' alt="{tr}Zoom{/tr}"}</a>
{if $arows[ar].modifiable eq "y"}
<a href="tiki-calendar_edit_item.php?calitemId={$arows[ar].calitemId}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
<a href="tiki-calendar_edit_item.php?calitemId={$arows[ar].calitemId}&amp;delete=1"  title="{tr}Remove{/tr}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>{/if}</span>
{/if}
<div {if $arows[ar].calname ne ""}class="Cal{$arows[ar].type} vevent" style="background-color:#{$infocals.$calendarId.custombgcolor};color:#{$infocals.$calendarId.customfgcolor};"{/if}>
{if $arows[ar].calname eq ""}{$arows[ar].type} : {/if}
{if $myurl eq "tiki-action_calendar.php"}
<a href="{$arows[ar].url}" class="url" title="{$arows[ar].web|escape}" class="linkmenu summary">{$arows[ar].name}</a>
{else}
<a href="tiki-calendar_edit_item.php?viewcalitemId={$arows[ar].calitemId}" class="linkmenu summary">{$arows[ar].name}</a>
{/if}
<span class="description">
{if $arows[ar].calname ne ""}{$arows[ar].parsedDescription}{else}{$arows[ar].description}{/if}
</span>
</div>
</td></tr>
{/section}



{foreach key=k item=h from=$hours}
<tr><td width="42" class="{cycle advance=false}">{$h}{tr}h{/tr}</td>
<td class="{cycle}">
{section name=hr loop=$hrows[$h]}
{assign var=calendarId value=$hrows[$h][hr].result.calendarId}
{if ($prefs.calendar_view_tab eq "y" or $tiki_p_change_events eq "y") and $hrows[$h][hr].calname ne ""}<span  style="float:right;">
<a href="tiki-calendar_edit_item.php?viewcalitemId={$hrows[$h][hr].calitemId}"{if $prefs.feature_tabs ne "y"}#details{/if} title="{tr}Details{/tr}">{icon _id='magnifier' alt="{tr}Zoom{/tr}"}</a>
{if $hrows[$h][hr].modifiable eq "y"}
<a href="tiki-calendar_edit_item.php?calitemId={$hrows[$h][hr].calitemId}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
<a href="tiki-calendar_edit_item.php?calitemId={$hrows[$h][hr].calitemId}&amp;delete=1"  title="{tr}Remove{/tr}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>{/if}</span>
{/if}
<div {if $hrows[$h][hr].calname ne ""}class="Cal{$hrows[$h][hr].type} vevent" style="background-color:#{$infocals.$calendarId.custombgcolor};color:#{$infocals.$calendarId.customfgcolor};"{/if}>
<abbr class="dtstart" title="{$hrows[$h][hr].startTimeStamp|isodate}">{$hours[$h]}:{$hrows[$h][hr].mins}</abbr> : {if $hrows[$h][hr].calname eq ""}{$hrows[$h][hr].type} : {/if}
{if $myurl eq "tiki-action_calendar.php"}
<a href="{$hrows[$h][hr].url}" class="url" title="{$hrows[$h][hr].web|escape}" class="linkmenu summary">{$hrows[$h][hr].name}</a>
{else}
<a href="tiki-calendar_edit_item.php?viewcalitemId={$hrows[$h][hr].calitemId}" class="linkmenu summary">{$hrows[$h][hr].name}</a>
{/if}
<span class="description">
{if $hrows[$h][hr].calname ne ""}{$hrows[$h][hr].parsedDescription}{else}{$hrows[$h][hr].description}{/if}
</span>
</div>
{/section}
</td></tr>
{/foreach}
</table>
