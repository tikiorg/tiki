{* $Id$ *}
<div class="table-responsive">
	<table class="caltable table">
		<tr>
			<td style="width: 1%:" class="heading weeks"></td>
			{section name=dn loop=$daysnames}
				{if in_array($smarty.section.dn.index,$viewdays)}
					<td id="top_{$smarty.section.dn.index}" class="heading" style="width:14%;">{$daysnames[dn]}</td>
				{/if}
			{/section}
		</tr>

		{section name=w loop=$cell}
			<tr id="row_{$smarty.section.w.index}" style="height:80px">
				<td class="heading weeks"><a href="{$myurl}?viewmode=week&amp;todate={$cell[w][0].day}" title="{tr}View this Week{/tr}">{$weekNumbers[w]}</a></td>
				{section name=d loop=$weekdays}
					{if in_array($smarty.section.d.index,$viewdays)}
						{if $cell[w][d].focus}
							{cycle values="odd,even" print=false advance=false}
						{else}
							{cycle values="text-muted" print=false advance=false}
						{/if}
						<td id="row_{$smarty.section.w.index}_{$smarty.section.d.index}" class="{if $cell[w][d].day eq $today}calhighlight calborder{/if} {cycle}" style="padding:0px">
							<table style="width:100%; border:none">
								<tr>
									<td class="focus {if $cell[w][d].day eq $today}calhighlight{/if}" style="width:50%;text-align:left">
										{* test display_field_order and use %d/%m or %m/%d on each day 'cell' *}
										{if ($prefs.display_field_order eq 'DMY') || ($prefs.display_field_order eq 'DYM') || ($prefs.display_field_order eq 'YDM')}
											<a href="{$myurl}?focus={$cell[w][d].day}" title="{tr}Change Focus{/tr}" style="font-size:11px">
												{$cell[w][d].day|tiki_date_format:"%d/%m"}
											</a>
										{else}
											<a href="{$myurl}?focus={$cell[w][d].day}" title="{tr}Change Focus{/tr}" style="font-size:11px">
												{$cell[w][d].day|tiki_date_format:"%m/%d"}
											</a>
										{/if}
									</td>
									{if $myurl neq "tiki-action_calendar.php"}
										<td class="focus {if $cell[w][d].day eq $today}calhighlight{/if}" style="width:50%;text-align:right;font-size:75%">
											{* add additional check to NOT show add event icon if no calendar displayed *}
											{if $tiki_p_add_events eq 'y' and count($listcals) > 0 and $displayedcals|@count > 0}
												<a href="tiki-calendar_edit_item.php?todate={$cell[w][d].day}{if $displayedcals|@count eq 1}&amp;calendarId={$displayedcals[0]}{/if}" title=":{tr}Add event{/tr}" class="addevent tips">
													{icon name='create'}
												</a>
											{/if}
											<a class="viewthisday tips" href="tiki-calendar.php?viewmode=day&amp;todate={$cell[w][d].day}{if $displayedcals|@count eq 1}&amp;calendarId={$displayedcals[0]}{/if}" title=":{tr}View this Day{/tr}">
												{icon name='calendar'}
											</a>
										</td>
									{/if}
								</tr>
							</table>
							{if $cell[w][d].focus}
								{section name=item loop=$cell[w][d].items}
									{if $smarty.section.item.first}
										<table style="width:100%;">
									{/if}
									{assign var=over value=$cell[w][d].items[item].over}
									{assign var=calendarId value=$cell[w][d].items[item].calendarId}
									<tr>
										{if is_array($cell[w][d].items[item])}
											<td class="Cal{$cell[w][d].items[item].type} calId{$cell[w][d].items[item].calendarId} viewcalitemId_{$cell[w][d].items[item].calitemId} tips" style="padding:0;height:14px;background-color:#{$infocals.$calendarId.custombgcolor};border-color:#{$infocals.$calendarId.customfgcolor};opacity:{if $cell[w][d].items[item].status eq '0'}0.8{else}1{/if};filter:Alpha(opacity={if $cell[w][d].items[item].status eq '0'}80{else}100{/if});text-align:left;border-width:1px {if $cell[w][d].items[item].endTimeStamp <= ($cell[w][d].day + 86400)}1{else}0{/if}px 1px {if $cell[w][d].items[item].startTimeStamp >= $cell[w][d].day}1{else}0{/if}px;cursor:pointer"
												{if $prefs.calendar_sticky_popup eq 'y'}
													{popup caption="{tr}Event{/tr}" vauto=true hauto=true sticky=true fullhtml="1" trigger="onClick" text=$over|escape:"javascript"|escape:"html"}
												{else}
													{popup caption="{tr}Event{/tr}" vauto=true hauto=true sticky=false fullhtml="1" text=$over|escape:"javascript"|escape:"html"}
												{/if}>

												{if $myurl eq "tiki-action_calendar.php" or ($cell[w][d].items[item].startTimeStamp >= $cell[w][d].day or $smarty.section.d.index eq '0' or $cell[w][d].firstDay or $infocals[$cell[w][d].items[item].calendarId].nameoneachday eq 'y')}
													<a style="padding:1px 3px;{if $infocals.$calendarId.customfgcolor}color:#{$infocals.$calendarId.customfgcolor}{/if}{if $cell[w][d].items[item].status eq '2'} text-decoration:line-through;{/if}"
														{if $myurl eq "tiki-action_calendar.php"}
															{if $cell[w][d].items[item].modifiable eq "y" || $cell[w][d].items[item].visible eq 'y'}href="{$cell[w][d].items[item].url}"{/if}
														{elseif $prefs.calendar_sticky_popup neq 'y'}
															{if $cell[w][d].items[item].modifiable eq "y" || $cell[w][d].items[item].visible eq 'y'}href="tiki-calendar_edit_item.php?viewcalitemId={$cell[w][d].items[item].calitemId}"{/if}
														{else}
																href="#"
														{/if}
													>{$cell[w][d].items[item].name|truncate:$trunc:".."|escape|default:"..."|unescape:"html"}</a>
													{if $cell[w][d].items[item].web}
														<a href="{$cell[w][d].items[item].web}" target="_other" class="calweb" title="{$cell[w][d].items[item].web}"><img src="img/icons/external_link.gif" width="7" height="7" alt="&gt;"></a>
													{/if}
													{if $cell[w][d].items[item].nl}
														<a href="tiki-newsletters.php?nlId={$cell[w][d].items[item].nl}&info=1" class="calweb" title="Subscribe"><img src="img/icons/external_link.gif" width="7" height="7" alt="&gt;"></a>
													{/if}
												{else}&nbsp;
												{/if}
											</td>
										{else}
											<td style="padding: 0; height: 14px; border: solid white 1px; width: 100%; font-size: 10px">&nbsp;</td>
										{/if}
									</tr>
									{if $smarty.section.item.last}
										</table>
									{/if}
								{/section}
							{/if}
						</td>
					{/if}
				{/section}
			</tr>
		{/section}
	</table>
</div>
