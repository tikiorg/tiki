<table cellpadding="0" cellspacing="0" border="0" id="caltable">
<tr><td width="42" class="heading">{tr}Hours{/tr}</td><td class="heading">{tr}Events{/tr}</td></tr>
{cycle values="odd,even" print=false}
{foreach key=k item=h from=$hours}
<tr><td width="42" class="{cycle advance=false}">{$h}{tr}h{/tr}</td>
<td class="{cycle}">
{section name=hr loop=$hrows[$h]}
{if ($prefs.calendar_view_tab eq "y" or $tiki_p_change_events eq "y") and $hrows[$h][hr].calname ne ""}<span  style="float:right;">
<a href="tiki-calendar_edit_item.php?viewcalitemId={$hrows[$h][hr].calitemId}"{if $prefs.feature_tabs ne "y"}#details{/if} title="{tr}Details{/tr}">
<img src="img/icons/zoom.gif" border="0" width="16" height="16" alt="{tr}Zoom{/tr}" /></a>&nbsp;
{if $hrows[$h][hr].modifiable eq "y"}
<a href="tiki-calendar_edit_item.php?calitemId={$hrows[$h][hr].calitemId}" title="{tr}Edit{/tr}">
{icon _id='page_edit'}</a>
<a href="tiki-calendar_edit_item.php?calitemId={$hrows[$h][hr].calitemId}&amp;delete=1"  title="{tr}Remove{/tr}">
{icon _id='cross' alt="{tr}Remove{/tr}"}</a>{/if}</span>
{/if}
<div {if $hrows[$h][hr].calname ne ""}class="Cal{$hrows[$h][hr].type} vevent"{/if}>
{$hours[$h]}:{$hrows[$h][hr].mins} : {if $hrows[$h][hr].calname eq ""}{$hrows[$h][hr].type} : {/if}
{if $myurl eq "tiki-action_calendar.php"}
<a href="{$hrows[$h][hr].url}" class="linkmenu summary">{$hrows[$h][hr].name}</a>
{else}
<a href="tiki-calendar_edit_item.php?viewcalitemId={$hrows[$h][hr].calitemId}" class="linkmenu summary">{$hrows[$h][hr].name}</a>
{/if}
{* these hidden spans are for microformat hCalendar support *}
<span class="dtstart" style="display:none;">{$hrows[$h][hr].startTimeStamp|isodate}</span>
<span class="dtend" style="display:none;">{$hrows[$h][hr].endTimeStamp|isodate}</span>
<span class="url" style="display:none;">{$hrows[$h][hr].web|escape}</span>
<span class="location" style="display:none;">{$hrows[$h][hr].location|escape}</span>
<span class="category" style="display:none;">{$hrows[$h][hr].category|escape}</span>
<span class="description">
{if $hrows[$h][hr].calname ne ""}{$hrows[$h][hr].parsedDescription}{else}{$hrows[$h][hr].description}{/if}
</span>
</div>
{/section}
</td></tr>
{/foreach}
</table>
