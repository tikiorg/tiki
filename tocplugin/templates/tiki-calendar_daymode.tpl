<div style="position:relative">
	<table border="0" cellpadding="0" cellspacing="0" style="width:100%">
		<tr valign="middle" style="height:36px">
			<td id="month_title" {if $day eq $today}class="calheadhighlight"{/if} style="text-align:center; border:none; padding-top:4px"><strong>{$focusdate|tiki_long_date}</strong></td>
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
		{if isset($hrows[$h])}
			{section name=hr loop=$hrows[$h]}
				{assign var=event value=$hrows[$h][hr]}
				{assign var=calendarId value=$event.calendarId}
				{assign var=over value=$event.over}
				{if $event.calitemId neq ''}
					<div id="event_{$event.calitemId}" title="{tr}Details{/tr}" {if $hrows[$h][hr].calname ne ""}class="tips Cal{$event.type} vevent"{/if} style="position:absolute;z-index:100;top:{$event.top}px;left:{$event.left}%;width:{$event.width}%;height:{$event.duree}px;background-color:#{$infocals.$calendarId.custombgcolor};border-color:#{$infocals.$calendarId.customfgcolor};opacity:{if $event.status eq '0'}0.8{else}1{/if};filter:alpha(opacity={if $event.status eq '0'}80{else}100{/if});text-align:center;overflow:hidden;cursor:pointer"
						{if $prefs.calendar_sticky_popup eq "y"}
							{popup vauto=true hauto=true sticky=true trigger="onClick" fullhtml="1" text=$over|escape:"javascript"|escape:"html"}
						{else}
							{popup vauto=true hauto=true sticky=false fullhtml="1" text=$over|escape:"javascript"|escape:"html"}
						{/if}>
						<span style="padding-top:4px;padding-right:4px;float:right">
							<a style="padding:0 3px;"
									{if $event.modifiable eq "y" || $event.visible eq 'y'}
										{if $prefs.calendar_sticky_popup eq "y"}
											href="#"
										{else}
											href="tiki-calendar_edit_item.php?viewcalitemId={$event.calitemId}"
										{/if}
									{/if}
							   title="{tr}Details{/tr}">{icon name='info'}
							</a>
						</span>
					{if $myurl eq "tiki-action_calendar.php"}
						<a href="{$event.url}" class="url" title="{$event.web|escape}" class="linkmenu summary" style="color:#{$infocals.$calendarId.customfgcolor};{if $event.status eq '2'}text-decoration:line-through{/if}">{$event.name}</a>
					{else}
						<a href="tiki-calendar_edit_item.php?viewcalitemId={$event.calitemId}" class="linkmenu summary" style="color:#{$infocals.$calendarId.customfgcolor}">{$event.name}</a>
					{/if}
					</div>
				{/if}
			{/section}
		{/if}
	{/foreach}

</div>
