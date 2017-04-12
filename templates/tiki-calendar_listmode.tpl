{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
{if $prefs.javascript_enabled !== 'y'}
	{$js = 'n'}
	{$libeg = '<li>'}
	{$liend = '</li>'}
{else}
	{$js = 'y'}
	{$libeg = ''}
	{$liend = ''}
{/if}

<table cellpadding="0" cellspacing="0" border="0" class="table normal table-striped table-hover">
	<tr>
		<th style="width:20%"><a href="{$myurl}?sort_mode={if $sort_mode eq 'start_desc'}start_asc{else}start_desc{/if}">{tr}Start{/tr}</a></th>
		<th style="width:20%"><a href="{$myurl}?sort_mode={if $sort_mode eq 'end_desc'}end_asc{else}end_desc{/if}">{tr}End{/tr}</a></th>
		<th><a href="{$myurl}?sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></th>
		<th></th>
	</tr>
	{if $listevents|@count eq 0}{norecords _colspan=4}{/if}

	{foreach from=$listevents item=event}
		{assign var=calendarId value=$event.calendarId}
		<tr class="{cycle}{if $event.start <= $smarty.now and $event.end >= $smarty.now} selected{/if} vevent">
			<td class="date">
				<abbr class="dtstart" title="{$event.start|tiki_short_date}">
					<a href="{$myurl}?todate={$event.start}" title="{tr}Change Focus{/tr}">{$event.start|tiki_short_date}</a>
				</abbr><br>
				{if $event.allday} {tr}All day{/tr} {else} {$event.start|tiki_short_time} {/if}
			</td>
			<td class="date">
				{if $event.start|tiki_short_date ne $event.end|tiki_short_date}<abbr class="dtend" title="{$event.end|tiki_short_date}"><a href="{$myurl}?todate={$event.end}" title="{tr}Change Focus{/tr}">{$event.end|tiki_short_date}</a></abbr> {/if}<br>
{if $event.start ne $event.end and $event.allday ne 1}{$event.end|tiki_short_time}{/if}
			</td>
			<td style="{if $infocals.$calendarId.custombgcolor ne ''}background-color:#{$infocals.$calendarId.custombgcolor};{/if}">
				<a class="link" href="tiki-calendar_edit_item.php?viewcalitemId={$event.calitemId}" title="{tr}View{/tr}">
				{if $infocals.$calendarId.customfgcolor ne ''}<span style="color:#{$infocals.$calendarId.customfgcolor};">{/if}
				<span class="summary">{$event.name|escape}</span></a><br>
				<span class="description" style="font-style:italic">{$event.parsed}</span>
				{if $event.web}
					<br><a href="{$event.web}" target="_other" class="calweb" title="{$event.web}"><img src="img/icons/external_link.gif" width="7" height="7" alt="&gt;"></a>
					{if $infocals.$calendarId.customfgcolor ne ''}</span>{/if}
				{/if}
			</td>
			<td class="action">
				{if $event.modifiable eq "y"}
					{capture name=calendar_actions}
						{strip}
							{$libeg}<a href="tiki-calendar_edit_item.php?calitemId={$event.calitemId}">
								{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
							</a>{$liend}
							{$libeg}<a href="tiki-calendar_edit_item.php?calitemId={$event.calitemId}&amp;delete=1">
								{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>{$liend}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.calendar_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.calendar_actions}</ul></li></ul>
					{/if}
				{/if}
			</td>
		</tr>
	{/foreach}
</table>
