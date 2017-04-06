{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="last_modif_events" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{modules_list list=$modLastEvents nonums=$nonums}
		{section name=ix loop=$modLastEvents}
			<li>
				<a class="linkmodule" href="tiki-calendar.php?todate={$modLastEvents[ix].start}" title="{$modLastEvents[ix].lastModif|tiki_short_datetime:'':'n'}, {tr}by{/tr} {if $modLastEvents[ix].user ne ''}{$modLastEvents[ix].user|username}{else}{tr}Anonymous{/tr}{/if}">
					{if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
						{$modLastEvents[ix].name|truncate:$maxlen:"...":true|escape}
					{else}
						{$modLastEvents[ix].name|escape}
					{/if}
				</a>
				{if $nodate neq 'y'}
					<div class="date">{$modLastEvents[ix].start|tiki_short_datetime}</div>
				{/if}
			</li>
		{/section}
	{/modules_list}
{/tikimodule}
