<div style="position:relative">
  <table border="0" cellpadding="0" cellspacing="0" style="width:100%">
	<tr valign="middle" style="height:36px">
	  <td id="month_title" {if $day eq $today}class="calfocuson"{/if} style="text-align:center; border:none; padding-top:4px"><strong>{$focusdate|tiki_long_date}</strong></td>
	</tr>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;border:1px solid #ccc">
{foreach key=k item=h from=$hr_display}
  <tr valign="middle" style="height:24px">
    <td id="rowLeft_{$h[0]}" class="calHours" style="width:10%">{$h[1]}</td>
    <td id="row_{$h[0]}" class="calWeek" style="background:none">&nbsp;</td>
  </tr>
{/foreach}
</table>


{foreach key=k item=h from=$hours}
	{section name=hr loop=$hrows[$h]}
		{assign var=event value=$hrows[$h][hr]}
		{assign var=calendarId value=$event.calendarId}
		{assign var=over value=$event.over}
		{if $event.calitemId neq ''}
		<div id="event_{$event.calitemId}" {if $hrows[$h][hr].calname ne ""}class="Cal{$event.type} vevent"{/if} style="position:absolute;z-index:100;top:{$event.top}px;left:{$event.left}%;width:{$event.width}%;height:{$event.duree}px;background-color:#{$infocals.$calendarId.custombgcolor};border-color:#{$infocals.$calendarId.customfgcolor};opacity:{if $event.status eq '0'}0.6{else}0.8{/if};filter:Alpha(opacity={if $event.status eq '0'}60{else}80{/if});text-align:center;overflow:hidden;cursor:pointer"
			{if $prefs.calendar_sticky_popup eq "y"}
				{popup vauto=true hauto=true sticky=true trigger="onClick" fullhtml="1" text=$over|escape:"javascript"|escape:"html"}
			{else}
				{popup vauto=true hauto=true sticky=false fullhtml="1" text=$over|escape:"javascript"|escape:"html"}
			{/if}>
			<span style="padding-top:4px;padding-right:4px;float:right"><a style="padding:0 3px;"
			{if $event.modifiable eq "y" || $event.visible eq 'y'}
				{if $prefs.calendar_sticky_popup eq "y"}
					href="#"
				{else}
					href="tiki-calendar_edit_item.php?viewcalitemId={$event.calitemId}"
				{/if}
			{/if}

		><img src="pics/icons/more_info.gif" alt="{tr}Details{/tr}" /></a></span>
		{if $myurl eq "tiki-action_calendar.php"}
		<a href="{$event.url}" class="url" title="{$event.web|escape}" class="linkmenu summary" style="color:#{$infocals.$calendarId.customfgcolor};{if $event.status eq '2'}text-decoration:line-through{/if}">{$event.name}</a>
		{else}
		<a href="tiki-calendar_edit_item.php?viewcalitemId={$event.calitemId}" class="linkmenu summary" style="color:#{$infocals.$calendarId.customfgcolor}">{$event.name}</a>
		{/if}
		</div>
		{/if}
	{/section}
{/foreach}

</div>
