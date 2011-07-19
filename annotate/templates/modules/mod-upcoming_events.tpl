{tikimodule error=$module_params.error title=$tpl_module_title name="upcoming_events" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if count($modUpcomingEvents)}
	{if isset($module_params.date_format)}
		{assign var=date_format value=$module_params.date_format}
	{else}
		{assign var=date_format value="`$prefs.short_date_format` `$prefs.short_time_format`"}
	{/if}
	<table border="0" cellpadding="{if isset($module_params.cellpadding)}{$module_params.cellpadding}{else}0{/if}" cellspacing="{if isset($module_params.cellspacing)}{$module_params.cellspacing}{else}0{/if}">
		{section name=ix loop=$modUpcomingEvents}
			{assign var=date_value value=$modUpcomingEvents[ix].start|tiki_date_format:$date_format}
			{assign var=calendarId value=$modUpcomingEvents[ix].calendarId}
			{if !$smarty.section.ix.first}</td></tr>{/if}<tr>
			{if $nonums != 'y'}
				<td class="module" valign="top">{$smarty.section.ix.index_next})&nbsp;</td>
			{/if}
			<td class="module vevent"{if $showColor eq 'y' and $infocals.$calendarId.custombgcolor ne ''} style="background-color:#{$infocals.$calendarId.custombgcolor}"{/if}>
				{if $modUpcomingEvents[ix].allday}
					<abbr class="dtstart" title="{$modUpcomingEvents[ix].start|isodate}">{$modUpcomingEvents[ix].start|tiki_short_date}</abbr>
				{else}
					<abbr class="dtstart" title="{$modUpcomingEvents[ix].start|isodate}">{$modUpcomingEvents[ix].start|tiki_date_format:$date_format}</abbr>	
					{if $showEnd eq 'y'}
						-
						<abbr class="dtend" title="{$modUpcomingEvents[ix].end|isodate}">{if $module_params.date_format}{$modUpcomingEvents[ix].end|tiki_date_format:$date_format}{elseif $modUpcomingEvents[ix].start|tiki_short_date ne $modUpcomingEvents[ix].end|tiki_short_date}{$modUpcomingEvents[ix].end|tiki_short_datetime}{else}{$modUpcomingEvents[ix].end|tiki_short_time}{/if}</abbr>
					{/if}
				{/if}
				<br />
				<a class="linkmodule summary" href="tiki-calendar_edit_item.php?viewcalitemId={$modUpcomingEvents[ix].calitemId}" title="{if $tooltip_infos neq 'n'}{$modUpcomingEvents[ix].lastModif|tiki_short_datetime}, {tr}by{/tr} {if $modUpcomingEvents[ix].user ne ''}{$modUpcomingEvents[ix].user|username}{else}{tr}Anonymous{/tr}{/if}{else}{tr}click to view{/tr}{/if}"{if $modUpcomingEvents[ix].status eq '2'} style="text-decoration: line-through;"{/if}>
					{if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
						{$modUpcomingEvents[ix].name|truncate:$maxlen:"...":true|escape}
					{else}
						{$modUpcomingEvents[ix].name|escape}
					{/if}
				</a>
				{if $showDescription eq 'y'}
					<div class="description">{$modUpcomingEvents[ix].parsed}</div>
				{/if}
		{if $smarty.section.ix.last}
			</td>
			</tr>
		{/if}
	{/section}
	</table>
{else}
      <em>{tr}No records to display{/tr}</em>
{/if}

{if $tiki_p_add_events eq 'y' && (empty($module_params.showaction) || $module_params.showaction ne 'n')}
	<p><a href="tiki-calendar_edit_item.php"><img src="pics/icons/add.png" alt="" /> {tr}Add Event{/tr}</a></p>
{/if}
{/tikimodule}
