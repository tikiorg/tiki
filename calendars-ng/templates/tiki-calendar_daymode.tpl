<div style="position:relative">
  <div style="position:relative;height:36px">
    <div id="topLeft" class="calHeadingLeft" style="position:absolute;top:0%;height:100%">&nbsp;</div>
    <div id="top" class="calHeading" style="position:absolute;top:0%;height:100%">
		{tr}Events{/tr}<br /><strong>{$focusdate|tiki_long_date}</strong>
	</div>
  </div>
{foreach key=k item=h from=$hours}
  <div style="position:relative;height:24px">
    <div id="rowLeft_{$h}" class="calHours" style="position:absolute;top:0%;height:100%">{if ($h < 10)}0{/if}{$h}:00</div>
    <div id="row_{$h}" class="calWeek" style="position:absolute;top:0%;height:100%;background:none">
    </div>
  </div>
{/foreach}


{foreach key=k item=h from=$hours}
	{section name=hr loop=$hrows[$h]}
		{assign var=event value=$hrows[$h][hr]}
		{assign var=calendarId value=$event.calendarId}
		{assign var=over value=$event.over}
		<div id="event_{$event.calitemId}" {if $hrows[$h][hr].calname ne ""}class="Cal{$event.type} vevent"{/if} style="position:absolute;z-index:100;top:{$event.top}px;height:{$event.duree}px;background-color:#{$infocals.$calendarId.custombgcolor};border-color:#{$infocals.$calendarId.customfgcolor};color:#{$infocals.$calendarId.customfgcolor};opacity:0.7;filter:Alpha(opacity=70);text-align:center;overflow:hidden">
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
	    <script type="text/javascript">
		  var calWidth = document.getElementById('calscreen').offsetWidth;
		  var leftWidth = 5 * calWidth/100;
		  var cellWidth = 19 * leftWidth;
	   	  document.getElementById('event_{$event.calitemId}').style.left=(leftWidth + ({$event.left} * cellWidth / 100)) + "px";
	   	  document.getElementById('event_{$event.calitemId}').style.width=(cellWidth/{$event.concurrences} - 3) + "px"; + "px";
	    </script>
	{/section}
{/foreach}

</div>
<script type="text/javascript">
			var calWidth = document.getElementById('calscreen').offsetWidth;
			var leftWidth = 5 * calWidth/100;
			var rightWidth = 19 * leftWidth;
			document.getElementById('topLeft').style.left="0px";
			document.getElementById('topLeft').style.width=leftWidth + "px";
			document.getElementById('top').style.left=leftWidth + "px";
			document.getElementById('top').style.width=rightWidth + "px";
{foreach key=k item=h from=$hours}
			document.getElementById('rowLeft_{$h}').style.left="0px";
			document.getElementById('rowLeft_{$h}').style.width=leftWidth + "px";
			document.getElementById('row_{$h}').style.left=leftWidth + "px";
			document.getElementById('row_{$h}').style.width=rightWidth + "px";
{/foreach}
</script>