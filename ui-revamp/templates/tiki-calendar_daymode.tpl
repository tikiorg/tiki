<div style="position:relative">
  <table border="0" cellpading="0" cellspacing="0" style="width:100%">
	<tr valign="middle" style="height:36px">
	  <td id="month_title" style="text-align:center">{tr}Events{/tr}<br /><strong>{$focusdate|tiki_long_date}</strong></td>
	</tr>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;border:1px solid #ccc">
{foreach key=k item=h from=$hours}
  <tr valign="middle" style="height:24px">
    <td id="rowLeft_{$h}" class="calHours" style="width:10%">{if ($h < 10)}0{/if}{$h}:00</td>
    <td id="row_{$h}" class="calWeek" style="background:none">&nbsp;</td>
  </tr>
{/foreach}
</table>


{foreach key=k item=h from=$hours}
	{section name=hr loop=$hrows[$h]}
		{assign var=event value=$hrows[$h][hr]}
		{assign var=calendarId value=$event.calendarId}
		{assign var=over value=$event.over}
		<div id="event_{$event.calitemId}" {if $hrows[$h][hr].calname ne ""}class="Cal{$event.type} vevent"{/if} style="position:absolute;z-index:100;top:{$event.top}px;left:{$event.left}%;width:{$event.width}%;height:{$event.duree}px;background-color:#{$infocals.$calendarId.custombgcolor};border-color:#{$infocals.$calendarId.customfgcolor};color:#{$infocals.$calendarId.customfgcolor};opacity:0.7;filter:Alpha(opacity=70);text-align:center;overflow:hidden">
			<span style="padding-top:4px;padding-right:4px;float:right"><a style="padding:0 3px;"
			{if $event.modifiable eq "y" || $event.visible eq 'y'}
			    href="tiki-calendar_edit_item.php?viewcalitemId={$event.calitemId}"
			{/if}

			{if $prefs.calendar_sticky_popup eq "y" and $event.calitemId}
				{popup sticky=true fullhtml="1" text=$over|escape:"javascript"|escape:"html"}
			{else}
				{popup fullhtml="1" text=$over|escape:"javascript"|escape:"html"}
			{/if}
		><img src="pics/icons/more_info.gif" alt="{tr}Details{/tr}" border="0"/></a></span>
		{if $myurl eq "tiki-action_calendar.php"}
		<a href="{$event.url}" class="url" title="{$event.web|escape}" class="linkmenu summary" style="color:#{$infocals.$calendarId.customfgcolor}">{$event.name}</a>
		{else}
		<a href="tiki-calendar_edit_item.php?viewcalitemId={$event.calitemId}" class="linkmenu summary" style="color:#{$infocals.$calendarId.customfgcolor}">{$event.name}</a>
		{/if}
		</div>
	{/section}
{/foreach}

</div>
