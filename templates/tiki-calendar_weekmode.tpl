<div style="position:relative;padding:0px">
<table border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;border-bottom:1px solid #ccc">
  <tr valign="middle" style="height:36px">
    <td id="topLeft" class="calHeading" width="9%"><strong>{$viewstart|tiki_date_format:"%Y"}</strong></td>
{section name=dn loop=$daysnames}
	{if in_array($smarty.section.dn.index,$viewdays)}
    <td id="top_{$smarty.section.dn.index}" class="calHeading{if $today eq $viewWeekDays[dn]}On{/if}" width="13%">
	  <a href="{$myurl}?viewmode=day&amp;todate={$viewWeekDays[dn]}" title="{tr}View this Day{/tr}">{$daysnames[dn]}</a><br />
{* test display_field_order and use %d/%m or %m/%d on each day 'cell' *}
	{if ($prefs.display_field_order eq 'DMY') || ($prefs.display_field_order eq 'DYM') || ($prefs.display_field_order eq 'YDM')}	
	  <strong><a href="{$myurl}?focus={$viewWeekDays[dn]}&amp;viewmode=week" title="{tr}Change Focus{/tr}">{$viewWeekDays[dn]|tiki_date_format:"%d/%m"}</a></strong>
	{else}<strong><a href="{$myurl}?focus={$viewWeekDays[dn]}&amp;viewmode=week" title="{tr}Change Focus{/tr}">{$viewWeekDays[dn]|tiki_date_format:"%m/%d"}</a></strong>
	{/if}	 
{* add additional check to NOT show add event icon if no calendar displayed *}	 
	  {if $tiki_p_add_events eq 'y' and count($listcals) > 0 and $displayedcals|@count > 0}<a href="tiki-calendar_edit_item.php?todate={$viewWeekDays[dn]}{if $displayedcals|@count eq 1}&amp;calendarId={$displayedcals[0]}{/if}">{icon _id='calendar_add' alt="{tr}Add Event{/tr}"}</a>{/if}
	</td>
	{/if}
{/section}
  </tr>
{foreach key=k item=h from=$hr_display}
  <tr valign="middle" style="height:24px">
	<td id="rowLeft_{$h[0]}" class="calHours">{$h[1]}</td>
	{section name=weekday loop=$weekdays}
		{if in_array($smarty.section.weekday.index,$viewdays)}
			<td id="row_{$h[0]}_{$smarty.section.weekday.index}" class="calWeek">&nbsp;</td>
		{/if}
	{/section}
  </tr>
{/foreach}
</table>
{foreach key=k item=h from=$hours name=hours}
	{section name=weekday loop=$weekdays}
		{if in_array($smarty.section.weekday.index,$viewdays)}
		{if $manyEvents[weekday].tooMany eq false}
			{section name=hr loop=$hrows[weekday][$h]}
				{assign var=event value=$hrows[weekday][$h][hr]}
				{assign var=calendarId value=$event.calendarId}
				{assign var=over value=$event.over|escape:"javascript"|escape:"html"}
		{if $event.calitemId neq ''}
	  <div id="event_{$smarty.section.weekday.index}_{$event.calitemId}" {if $event.calname ne ""}class="Cal{$event.type} vevent"{/if} style="overflow:visible;position:absolute;top:{$event.top}px;height:{$event.duree-1}px;left:{$event.left}%;width:{$event.width}%;background-color:#{$infocals.$calendarId.custombgcolor};border-color:#{$infocals.$calendarId.customfgcolor};opacity:{if $event.status eq '0'}0.6{else}0.8{/if};filter:Alpha(opacity={if $event.status eq '0'}60{else}80{/if});text-align:center;overflow:hidden;cursor:pointer"
		{if $prefs.calendar_sticky_popup eq "y"}
			{popup vauto=true hauto=true sticky=true fullhtml="1" trigger="onClick" text=$over}
		{else}
			{popup vauto=true hauto=true sticky=false fullhtml="1" text=$over}
		{/if}>
		  <span style="padding-top:4px;float:right">
			<a style="padding:0 3px;"
			{if $event.modifiable eq "y" || $event.visible eq 'y'}
				{if $prefs.calendar_sticky_popup eq "y"}
					href="#"
				{else}
					href="tiki-calendar_edit_item.php?viewcalitemId={$event.calitemId}"
				{/if}
			{/if}
		    ><img src="pics/icons/more_info.gif" alt="{tr}Details{/tr}" /></a>
		  </span>
	  	  <abbr class="dtstart" title="{if $event.result.allday eq '1'}{tr}All day{/tr}{else}{$event.startTimeStamp|isodate}{/if}" {if $event.status eq '2'}style="text-decoration:line-through"{/if}>{$event.name|escape}</abbr>
	  </div>
		{/if}
			{/section}
		{elseif $smarty.foreach.hours.first}
			{assign var=overMany value=$manyEvents[weekday].overMany|escape:"javascript"|escape:"html"}
	  <div id="many_{$smarty.section.weekday.index}" style="position:absolute;top:{$manyEvents[weekday].top}px;left:{$manyEvents[weekday].left}%;width:{$manyEvents[weekday].width}%;height:{$manyEvents[weekday].duree-1}px;border:2px dotted #000"
		{if $prefs.calendar_sticky_popup eq "y"}
			{popup vauto=true hauto=true sticky=true trigger="onClick" fullhtml="1" text=$overMany}
		{else}
			{popup vauto=true hauto=true sticky=false fullhtml="1" text=$overMany}
		{/if}>
		<div style="position:absolute;top:50%;left:50%;margin-left:-40px;margin-top:-30px">
		  <a style="padding:0 3px;" href="{$myurl}?viewmode=day&todate={$viewWeekDays[weekday]}"
		  ><img src="pics/icons/multiple_cal.png" alt="{tr}Details{/tr}" /></a>
		</div>
	  </div>
		{/if}
	{/if}
	{/section}
{/foreach}
</div>
