{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-upcoming_events.tpl,v 1.1.2.2 2005/02/23 15:18:33 michael_davey *}

{if $feature_calendar eq 'y'}
	{if $nonums eq 'y'}
		{eval var="{tr}Upcoming `$module_rows` events{/tr}" assign="tpl_module_title"}
	{else}
		{eval var="{tr}Upcoming events{/tr}" assign="tpl_module_title"}
	{/if}
	{tikimodule title=$tpl_module_title name="upcoming_events" flip=$module_params.flip decorations=$module_params.decorations}
	{if $nonums != 'y'}<ol>{else}<ul>{/if}
		{section name=ix loop=$modUpcomingEvents}
		<li>{$modUpcomingEvents[ix].start|tiki_short_datetime}<br/>
				<a class="linkmodule" href="tiki-calendar.php?editmode=details&calitemId={$modUpcomingEvents[ix].calitemId}" title="{$modUpcomingEvents[ix].lastModif|tiki_short_datetime}, {tr}by{/tr} {if $modUpcomingEvents[ix].user ne ''}{$modUpcomingEvents[ix].user}{else}{tr}Anonymous{/tr}{/if}">
					{if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
						{$modUpcomingEvents[ix].name|truncate:$maxlen:"...":true}
					{else}
						{$modUpcomingEvents[ix].name}
					{/if}
				</a>
			</li>
    {/section}
	{if $nonums != 'y'}</ol>{else}</ul>{/if}
	{/tikimodule}
{/if}
