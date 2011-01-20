<table cellpadding="0" cellspacing="0" border="0" class="normal" width="100%">
	<tr>
		<th style="width:20%"><a href="{$myurl}?sort_mode={if $sort_mode eq 'start_desc'}start_asc{else}start_desc{/if}">{tr}Start{/tr}</a></th>
		<th style="width:20%"><a href="{$myurl}?sort_mode={if $sort_mode eq 'end_desc'}end_asc{else}end_desc{/if}">{tr}End{/tr}</a></th>
		<th><a href="{$myurl}?sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></th>
		<th>{tr}Action{/tr}</th>
	</tr>
	{if $listevents|@count eq 0}{norecords _colspan=4}{/if}
	{cycle values="odd,even" print=false}
	{foreach from=$listevents item=event}
		{assign var=calendarId value=$event.calendarId}
		<tr class="{cycle}{if $event.start <= $smarty.now and $event.end >= $smarty.now} selected{/if} vevent">
			<td>
				<abbr class="dtstart" title="{$event.start|tiki_short_date}">
					<a href="{$myurl}?todate={$event.start}" title="{tr}Change Focus{/tr}">{$event.start|tiki_short_date}</a>
				</abbr><br />
				{if $event.allday} {tr}All day{/tr} {else} {$event.start|tiki_short_time} {/if}
			</td>
			<td>
				{if $event.start|tiki_short_date ne $event.end|tiki_short_date}<abbr class="dtend" title="{$event.end|tiki_short_date}"><a href="{$myurl}?todate={$event.end}" title="{tr}Change Focus{/tr}">{$event.end|tiki_short_date}</a></abbr> {/if}<br />
{if $event.start ne $event.end and $event.allday ne 1}{$event.end|tiki_short_time}{/if}
			</td>
			<td style="{if $infocals.$calendarId.custombgcolor ne ''}background-color:#{$infocals.$calendarId.custombgcolor};{/if}">
				<a class="link" href="tiki-calendar_edit_item.php?viewcalitemId={$event.calitemId}" title="{tr}View{/tr}">
				{if $infocals.$calendarId.customfgcolor ne ''}<span style="color:#{$infocals.$calendarId.customfgcolor};">{/if}
				<span class="summary">{$event.name|escape}</span></a><br />
				<span class="description" style="font-style:italic">{$event.parsed}</span>
				{if $event.web}
					<br /><a href="{$event.web}" target="_other" class="calweb" title="{$event.web}"><img src="img/icons/external_link.gif" width="7" height="7" alt="&gt;" /></a>
					{if $infocals.$calendarId.customfgcolor ne ''}</span>{/if}
				{/if}
			</td>
			<td>
				{if $event.modifiable eq "y"}
					<a class="link" href="tiki-calendar_edit_item.php?calitemId={$event.calitemId}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
					<a class="link" href="tiki-calendar_edit_item.php?calitemId={$event.calitemId}&amp;delete=1" title="{tr}Remove{/tr}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
				{/if}
			</td>
		</tr>
	{/foreach}
</table>
