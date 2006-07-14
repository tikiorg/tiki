{* $Header$ *}

{if $feature_calendar eq 'y'}
	{if $nonums eq 'y'}
		{eval var="{tr}Upcoming `$module_rows` events{/tr}" assign="tpl_module_title"}
	{else}
		{eval var="{tr}Upcoming events{/tr}" assign="tpl_module_title"}
	{/if}
	{tikimodule title=$tpl_module_title name="upcoming_events" flip=$module_params.flip decorations=$module_params.decorations}
   		<table  border="0" cellpadding="0" cellspacing="0">
		{section name=ix loop=$modUpcomingEvents}
		<tr>
			{if $nonums != 'y'}
				<td class="module" valign="top">{$smarty.section.ix.index_next})</td>
			{/if}
			<td class="module">&nbsp;{$modUpcomingEvents[ix].start|tiki_short_datetime}<br />
				<a class="linkmodule" href="tiki-calendar.php?editmode=details&calitemId={$modUpcomingEvents[ix].calitemId}" title="{$modUpcomingEvents[ix].lastModif|tiki_short_datetime}, {tr}by{/tr} {if $modUpcomingEvents[ix].user ne ''}{$modUpcomingEvents[ix].user}{else}{tr}Anonymous{/tr}{/if}">
					{if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
						{$modUpcomingEvents[ix].name|truncate:$maxlen:"...":true}
					{else}
						{$modUpcomingEvents[ix].name}
					{/if}
				</a>
			</td>
		</tr>
    {/section}
	</table>
	{/tikimodule}
{/if}
