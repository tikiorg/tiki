{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="last_actions" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modLastActions nonums=$nonums}
	{section name=ix loop=$modLastActions}
		<li>
			{capture name=label}{if $showuser eq 'y'}{$modLastActions[ix].user|username}: {/if}{$modLastActions[ix].action} {$modLastActions[ix].objectType} {$modLastActions[ix].object|escape}{if $showdate eq 'y'} {tr}at{/tr} {$modLastActions[ix].lastModif|tiki_short_datetime}{/if}{/capture}
			{if $modLastActions[ix].object ne ''}
				<a class="linkmodule" href="tiki-index.php?page={$modLastActions[ix].object|escape:"url"}" title="
				{if (strlen($smarty.capture.label) > $maxlen) && ($maxlen > 0)}
					{$modLastActions[ix].user|username}: {$modLastActions[ix].action} {$modLastActions[ix].objectType} {$modLastActions[ix].object|escape} {tr}at{/tr} {$modLastActions[ix].lastModif|tiki_short_datetime:'':'n'}
				{else}
					{if $showdate eq 'n'}{$modLastActions[ix].lastModif|tiki_short_datetime:'':'n'}{/if}{if $showuser eq 'n'}{if $showdate eq 'n'}, {tr}by{/tr} {/if}{$modLastActions[ix].user|username}{/if}
				{/if}
			">
			{/if}
			{if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
				{$smarty.capture.label|truncate:$maxlen:"...":true}
			{else}
				{$smarty.capture.label}
			{/if}
			{if $modLastActions[ix].object ne ''}</a>{/if}
		</li>
	{/section}
{/modules_list}
{/tikimodule}
