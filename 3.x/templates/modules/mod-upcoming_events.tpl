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
{tikimodule error=$module_params.error title=$tpl_module_title name="upcoming_events" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if isset($module_params.date_format)}
		{assign var=date_format value=$module_params.date_format}
	{else}
		{assign var=date_format value="`$prefs.short_date_format` `$prefs.short_time_format`"}
	{/if}
	<table border="0" cellpadding="{if isset($module_params.cellpadding)}{$module_params.cellpadding}{else}0{/if}" cellspacing="{if isset($module_params.cellspacing)}{$module_params.cellspacing}{else}0{/if}">
		{section name=ix loop=$modUpcomingEvents}
			{assign var=date_value value=$modUpcomingEvents[ix].start|tiki_date_format:$date_format}
			{assign var=calendarId value=$modUpcomingEvents[ix].calendarId}
			{if $smarty.section.ix.first}<tr>{else}</td></tr><tr>{/if}
			{if $nonums != 'y'}
				<td class="module" valign="top">{$smarty.section.ix.index_next})&nbsp;</td>
			{/if}
			<td class="module vevent"{if $module_params.showColor eq 'y' and $infocals.$calendarId.custombgcolor ne ''} style="background-color:#{$infocals.$calendarId.custombgcolor}{/if}">
				{if $modUpcomingEvents[ix].allday}
					<abbr class="dtstart" title="{$modUpcomingEvents[ix].start|isodate}">{$modUpcomingEvents[ix].start|tiki_short_date}</abbr>
				{elseif $module_params.date_format}
					<abbr class="dtstart" title="{$modUpcomingEvents[ix].start|isodate}">{$modUpcomingEvents[ix].start|tiki_date_format:$date_format}</abbr>
					{if $module_params.showEnd eq 'y'}
						-
						<abbr class="dtend" title="{$modUpcomingEvents[ix].start|isodate}">{$modUpcomingEvents[ix].end|tiki_date_format:$date_format}</abbr>
					{/if}
				{else}
					<abbr class="dtstart" title="{$modUpcomingEvents[ix].start|isodate}">{$modUpcomingEvents[ix].start|tiki_short_datetime}</abbr>	
					{if $module_params.showEnd eq 'y'}
						-
						{if $modUpcomingEvents[ix].start|tiki_short_date ne $modUpcomingEvents[ix].end|tiki_short_date}
							<abbr class="dtend" title="{$modUpcomingEvents[ix].end|isodate}">{$modUpcomingEvents[ix].end|tiki_short_datetime}</abbr>
						{else}
							<abbr class="dtend" title="{$modUpcomingEvents[ix].end|isodate}">{$modUpcomingEvents[ix].end|tiki_short_time}</abbr>
						{/if}
					{/if}
				{/if}	
				<br />
				<a class="linkmodule summary" href="tiki-calendar_edit_item.php?viewcalitemId={$modUpcomingEvents[ix].calitemId}" title="{if $module_params.tooltip_infos neq 'n'}{$modUpcomingEvents[ix].lastModif|tiki_short_datetime}, {tr}by{/tr} {if $modUpcomingEvents[ix].user ne ''}{$modUpcomingEvents[ix].user|username}{else}{tr}Anonymous{/tr}{/if}{else}{tr}click to view{/tr}{/if}">
					{if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
						{$modUpcomingEvents[ix].name|truncate:$maxlen:"...":true|escape}
					{else}
						{$modUpcomingEvents[ix].name|escape}
					{/if}
				</a>
				{if $module_params.showDescription eq 'y'}
					<div class="description">{$modUpcomingEvents[ix].description|escape}</div>
				{/if}
		{if $smarty.section.ix.last}
			</td>
			</tr>
		{/if}
	{/section}
	</table>
	{if $tiki_p_add_events eq 'y' }
		<p><a href="tiki-calendar_edit_item.php"><img src=pics/icons/add.png link="tiki-calendar_edit_item.php"> {tr}Add event{/tr}</a></p>
	{/if}
{/tikimodule}
{/if}
