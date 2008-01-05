{* $Header$ *}

{if $prefs.feature_calendar eq 'y'}
{if !isset($tpl_module_title)}
	{if $nonums eq 'y'}
		{if $module_rows gt 1}
			{eval var="{tr}Upcoming `$module_rows` events{/tr}" assign="tpl_module_title"}
		{elseif $module_rows eq 1}
			{assign var="tpl_module_title" value="{tr}The Next Event{/tr}"}
		{else}
			{assign var="tpl_module_title" value="{tr}No Upcoming Events{/tr}"}
		{/if}
	{else}
		{eval var="{tr}Upcoming events{/tr}" assign="tpl_module_title"}
	{/if}
{/if}
{tikimodule title=$tpl_module_title name="upcoming_events" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
	{if isset($module_params.date_format)}
		{assign var=date_format value=$module_params.date_format}
	{else}
		{assign var=date_format value="`$prefs.short_date_format` `$prefs.short_time_format`"}
	{/if}
	<table border="0" cellpadding="{if isset($module_params.cellpadding)}{$module_params.cellpadding}{else}0{/if}" cellspacing="{if isset($module_params.cellspacing)}{$module_params.cellspacing}{else}0{/if}">
	{section name=ix loop=$modUpcomingEvents}
		{assign var=date_value value=$modUpcomingEvents[ix].start|tiki_date_format:$date_format}
			{if $smarty.section.ix.first}<tr>{else}</td></tr><tr>{/if}
			{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})&nbsp;</td>{/if}
			<td class="module vevent"><abbr class="dtstart" title="{$modUpcomingEvents[ix].start|isodate}">{$date_value}</abbr>
				{* the following hidden spans for hCalendar microformat support *}
				<span class="dtend" style="display:none;">{$modUpcomingEvents[ix].end|isodate}</span>
				<span class="description" style="display:none;">{$modUpcomingEvents[ix].description|escape}</span>
				<span class="category" style="display:none;">{$modUpcomingEvents[ix].category|escape}</span>
				<span class="location" style="display:none;">{$modUpcomingEvents[ix].location|escape}</span>
				<span class="url" style="display:none;">{$modUpcomingEvents[ix].url|escape}</span>
				<br />
				<a class="linkmodule summary" href="tiki-calendar_edit_item.php?viewcalitemId={$modUpcomingEvents[ix].calitemId}" title="{if $module_params.tooltip_infos neq 'n'}{$modUpcomingEvents[ix].lastModif|tiki_short_datetime}, {tr}by{/tr} {if $modUpcomingEvents[ix].user ne ''}{$modUpcomingEvents[ix].user|userlink|strip_tags|trim}{else}{tr}Anonymous{/tr}{/if}{else}{tr}click to view{/tr}{/if}">
					{if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
						{$modUpcomingEvents[ix].name|truncate:$maxlen:"...":true}
					{else}
						{$modUpcomingEvents[ix].name}
					{/if}
				</a>
		{assign var=old_date_value value=$date_value}
		{if $smarty.section.ix.last}
			</td>
		</tr>
		{/if}
	{/section}
	</table>
{/tikimodule}
{/if}
