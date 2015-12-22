{*$Id$*}
<div style="position:relative;padding:0px">
	<table class="calendarweek">
		<tr valign="middle" style="height:36px">
			<td id="topLeft" class="calHeading heading" width="9%">
				<strong>
					{$viewstart|tiki_date_format:"%Y"}
				</strong>
			</td>
			{section name=dn loop=$daysnames}
				{if in_array($smarty.section.dn.index,$viewdays)}
					<td id="top_{$smarty.section.dn.index}" class="{if $today eq $viewWeekDays[dn]}calheadhighlight{else}heading{/if} calHeading" width="13%">
						<a href="{$myurl}?viewmode=day&amp;todate={$viewWeekDays[dn]}" title="{tr}View this Day{/tr}">
							{$daysnames[dn]}
						</a><br>
						{* test display_field_order and use %d/%m or %m/%d on each day 'cell' *}
						{if ($prefs.display_field_order eq 'DMY') || ($prefs.display_field_order eq 'DYM')
							|| ($prefs.display_field_order eq 'YDM')}
							<strong>
								<a href="{$myurl}?focus={$viewWeekDays[dn]}&amp;viewmode=week" title="{tr}Change Focus{/tr}">
									{$viewWeekDays[dn]|tiki_date_format:"%d/%m"}
								</a>
							</strong>
						{else}
							<strong>
								<a href="{$myurl}?focus={$viewWeekDays[dn]}&amp;viewmode=week" title="{tr}Change Focus{/tr}">
									{$viewWeekDays[dn]|tiki_date_format:"%m/%d"}
								</a>
							</strong>
						{/if}
						{* add additional check to NOT show add event icon if no calendar displayed *}
						{if $tiki_p_add_events eq 'y' and count($listcals) > 0 and $displayedcals|@count > 0}
							<a class="tips" title=":{tr}Add Event{/tr}" href="tiki-calendar_edit_item.php?todate={$viewWeekDays[dn]}{if $displayedcals|@count eq 1}&amp;calendarId={$displayedcals[0]}{/if}">
								{icon name='add'}
							</a>
						{/if}
					</td>
				{/if}
			{/section}
		</tr>
		{foreach key=k item=h from=$hr_display}
			<tr valign="middle" style="height:24px">
				<td id="rowLeft_{$h[0]}" class="calHours">
					{$h[1]}
				</td>
				{section name=weekday loop=$weekdays}
					{if isset($smarty.section.weekday.index) and in_array($smarty.section.weekday.index,$viewdays)}
						<td id="row_{$h[0]}_{$smarty.section.weekday.index}" class="calWeek">&nbsp;

						</td>
					{/if}
				{/section}
			</tr>
		{/foreach}
	</table>
	{foreach key=k item=h from=$hours name=hours}
		{section name=weekday loop=$weekdays}
			{if isset($smarty.section.weekday.index) and in_array($smarty.section.weekday.index,$viewdays)}
				{if isset($manyEvents[weekday].tooMany) and $manyEvents[weekday].tooMany eq false and isset($hrows[weekday][$h])}
					{section name=hr loop=$hrows[weekday][$h]}
						{assign var=event value=$hrows[weekday][$h][hr]}
						{assign var=calendarId value=$event.calendarId}
						{assign var=over value=$event.over|escape:"javascript"|escape:"html"}
						{if !empty($event.calitemId)}
							<div id="event_{$h}_{$smarty.section.weekday.index}_{$event.calitemId}" {if $event.calname ne ""}class="Cal{$event.type} vevent tips"{/if} style="overflow:visible;position:absolute;top:{$event.top}px;height:{$event.duree-1}px;left:{$event.left}%;width:{$event.width}%;background-color:#{$infocals.$calendarId.custombgcolor};border-color:#{$infocals.$calendarId.customfgcolor};color:#{$infocals.$cellcalendarId.customfgcolor};opacity:{if $event.status eq '0'}0.8{else}1{/if};filter:alpha(opacity={if $event.status eq '0'}80{else}100{/if});text-align:center;overflow:hidden;cursor:pointer;{if $prefs.feature_jquery_ui eq 'y'}display:none;{/if}"
								title="{tr}Details{/tr}"
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
									   title="{tr}Details{/tr}">{icon name='info'}
									</a>
								</span>
								<abbr class="dtstart" title="{if $event.result.allday eq '1'}{tr}All day{/tr}{else}{$event.startTimeStamp|isodate}{/if}" style="{if $event.status eq '2'}text-decoration:line-through;{/if}{if isset($infocals.$cellcalendarId.customfgcolor)}color:#{$infocals.$cellcalendarId.customfgcolor};{/if}">
									{$event.name|escape}
								</abbr>
							</div>
							{jq}
								var id = '#event_{{$h}}_{{$smarty.section.weekday.index}}_{{$event.calitemId}}';
								var cell = '#row_{{$h}}_{{$smarty.section.weekday.index}}';
								var pos = $(cell).position();
								var eventwidth = ($(cell).width() + parseInt($(cell).css('padding-left'))
									+ parseInt($(cell).css('padding-right'))) / {{$event.concurrences}}
								var leftadd = {{$smarty.section.hr.index}} * eventwidth;
								var mins = parseInt({{$event.mins}});
								if (parseInt(mins) > 0) {
									var topadd = $(id).height() / (60 / mins);
								} else {
									var topadd = 0;
								}
								$(id).css('top', pos.top + topadd);
								$(id).css('left', pos.left + leftadd);
								$(id).css('width', eventwidth);
								$(id).css('display', 'inline');
							{/jq}
						{/if}
					{/section}
				{elseif $smarty.foreach.hours.first}
					{if isset($manyEvents[weekday].overMany)}
						{assign var=overMany value=$manyEvents[weekday].overMany|escape:"javascript"|escape:"html"}
						<div id="many_{$smarty.section.weekday.index}" style="position:absolute;{if isset($manyEvents[weekday].top)}top:{$manyEvents[weekday].top}px;{/if}{if isset($manyEvents[weekday].left)}left:{$manyEvents[weekday].left}%;{/if}{if isset($manyEvents[weekday].width)}width:{$manyEvents[weekday].width}%;{/if}{if isset($manyEvents[weekday].duree)}height:{$manyEvents[weekday].duree-1}px;{/if}border:2px dotted #000"
							{if $prefs.calendar_sticky_popup eq "y"}
								{popup vauto=true hauto=true sticky=true trigger="onClick" fullhtml="1" text=$overMany}
							{else}
								{popup vauto=true hauto=true sticky=false fullhtml="1" text=$overMany}
							{/if}
							title="{tr}Details{/tr}"
						>
							<div style="position:absolute;top:50%;left:50%;margin-left:-40px;margin-top:-30px">
								<a style="padding:0 3px;" href="{$myurl}?viewmode=day&todate={$viewWeekDays[weekday]}">
									<img src="img/icons/multiple_cal.png" alt="{tr}Details{/tr}">
								</a>
							</div>
						</div>
					{/if}
				{/if}
			{/if}
		{/section}
	{/foreach}
</div>
