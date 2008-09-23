<div style="position:relative;">
  <div style="position:relative;height:36px;width:100%">
    <div id="topLeft" class="calHeading" style="position:absolute;top:0%;height:100%">
	  <strong>{$viewstart|tiki_date_format:"%Y"}</strong>
	</div>
{section name=dn loop=$daysnames}
    <div id="top_{$smarty.section.dn.index}" class="calHeading" style="position:absolute;top:0%;height:100%">
	  {$daysnames[dn]}<br />
	  <strong><a href="{$myurl}?todate={$viewWeekDays[dn]}" title="{tr}Change Focus{/tr}">{$viewWeekDays[dn]|tiki_date_format:"%B %e"}</a></strong>
    </div>
{/section}
  </div>
{foreach key=k item=h from=$hours}
  <div style="position:relative;height:24px;width:100%;">
    <div id="rowLeft_{$h}" class="calHours" style="position:absolute;top:0%;height:100%">{if ($h < 10)}0{/if}{$h}:00</div>
	{section name=weekday loop=$weekdays}
    <div id="row_{$h}_{$smarty.section.weekday.index}" class="calWeek" style="position:absolute;top:0%;height:100%;">
	</div>
	{/section}
  </div>
{/foreach}


{foreach key=k item=h from=$hours}
	{section name=weekday loop=$weekdays}
		{if $manyEvents[weekday].tooMany eq false}
			{section name=hr loop=$hrows[weekday][$h]}
				{assign var=event value=$hrows[weekday][$h][hr]}
				{assign var=calendarId value=$event.calendarId}
				{assign var=over value=$event.over}
	  <div id="event_{$smarty.section.weekday.index}_{$event.calitemId}" {if $event.calname ne ""}class="Cal{$event.type} vevent"{/if} style="overflow:visible;position:absolute;top:{$event.top}px;height:{$event.duree-1}px;background-color:#{$infocals.$calendarId.custombgcolor};border-color:#{$infocals.$calendarId.customfgcolor};color:#{$infocals.$calendarId.customfgcolor};opacity:0.7;filter:Alpha(opacity=70);text-align:center;overflow:hidden">
		  <span style="padding-top:4px;float:right">
			<a style="padding:0 3px;"
			{if $event.modifiable eq "y" || $event.visible eq 'y'}
			   href="tiki-calendar_edit_item.php?viewcalitemId={$event.calitemId}"
			{/if}
			{if $prefs.calendar_sticky_popup eq "y" and $event.calitemId}
			   {popup sticky=true fullhtml="1" text=$over|escape:"javascript"|escape:"html"}
			{else}
			   {popup fullhtml="1" text=$over|escape:"javascript"|escape:"html"}
			{/if}
		    ><img src="pics/icons/more_info.gif" alt="{tr}Details{/tr}" border="0"/></a>
		  </span>
	  	  <abbr class="dtstart" title="{$event.startTimeStamp|isodate}">{$event.name}</abbr>
	  </div>
	  <script type="text/javascript">
	  	var calWidth = document.getElementById('calscreen').offsetWidth - 30;
	  	var leftWidth = 9 * calWidth/100;
	  	var cellWidth = (calWidth - leftWidth) / 7;
	  	document.getElementById('event_{$smarty.section.weekday.index}_{$event.calitemId}').style.left=((({$event.left} / 100) + {$smarty.section.weekday.index}) * cellWidth + leftWidth + 3) + "px";
	  	document.getElementById('event_{$smarty.section.weekday.index}_{$event.calitemId}').style.width=(cellWidth/{$event.concurrences} - 3) + "px";
	  </script>
			{/section}
		{/if}
	{/section}
{/foreach}


{section name=weekday loop=$weekdays}
	{if $manyEvents[weekday].tooMany eq true}
		{assign var=overMany value=$manyEvents[weekday].overMany}
  <div id="many_{$smarty.section.weekday.index}" style="position:absolute;top:{$manyEvents[weekday].top}px;height:{$manyEvents[weekday].duree-3}px;border:2px dotted #000">
	<div style="position:absolute;top:50%;left:50%;margin-left:-40px;margin-top:-30px">
	  <a style="padding:0 3px;" href="{$myurl}?viewmode=day&todate={$viewWeekDays[weekday]}"
		{if $prefs.calendar_sticky_popup eq "y" and $hrows[weekday][$h][hr].calitemId}
		 {popup sticky=true fullhtml="1" text=$over|escape:"javascript"|escape:"html"}
		{else}
		 {popup fullhtml="1" text=$overMany|escape:"javascript"|escape:"html"}
		{/if}
	  ><img src="pics/icons/multiple_cal.png" alt="{tr}Details{/tr}" border="0"/></a>
	</div>
	<script type="text/javascript">
		var calWidth = document.getElementById('calscreen').offsetWidth - 30;
		var leftWidth = 9 * calWidth/100;
		var rightWidth = (calWidth - leftWidth) / 7;
		document.getElementById('many_{$smarty.section.weekday.index}').style.left=({$smarty.section.weekday.index} * rightWidth + leftWidth + 3) + "px";
		document.getElementById('many_{$smarty.section.weekday.index}').style.width=rightWidth - 3 + "px";
	</script>
  </div>
	{/if}
{/section}
</div>
<script type="text/javascript">
			var calWidth = document.getElementById('calscreen').offsetWidth - 30;
			var leftWidth = 9 * calWidth/100;
			var rightWidth = (calWidth - leftWidth) / 7;
			document.getElementById('topLeft').style.left="0px";
			document.getElementById('topLeft').style.width=leftWidth + "px";
{section name=dn loop=$daysnames}
			document.getElementById('top_{$smarty.section.dn.index}').style.left=({$smarty.section.dn.index} * rightWidth + leftWidth + 3) + "px";
			document.getElementById('top_{$smarty.section.dn.index}').style.width=rightWidth + "px";
{/section}
{foreach key=k item=h from=$hours}
			document.getElementById('rowLeft_{$h}').style.left="0px";
			document.getElementById('rowLeft_{$h}').style.width=leftWidth + "px";
{section name=weekday loop=$weekdays}
			document.getElementById('row_{$h}_{$smarty.section.weekday.index}').style.left=({$smarty.section.weekday.index} * rightWidth + leftWidth + 3) + "px";
			document.getElementById('row_{$h}_{$smarty.section.weekday.index}').style.width=rightWidth + "px";
{/section}
{/foreach}
</script>