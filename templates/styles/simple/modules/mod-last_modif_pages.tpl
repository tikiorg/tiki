{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/simple/modules/mod-last_modif_pages.tpl,v 1.3 2007-10-04 22:17:50 nyloth Exp $ *}
{if $prefs.feature_wiki eq 'y'}
	{if $nonums eq 'y'}
		{eval var="{tr}Last `$module_rows` changes{/tr}" assign="tpl_module_title"}
	{else}
		{eval var="{tr}Last changes{/tr}" assign="tpl_module_title"}
	{/if}
	{tikimodule title=$tpl_module_title name="last_modif_pages" flip=$module_params.flip decorations=$module_params.decorations}
		{if $nonums != 'y'}
			<ol>
		{else}
			<ul>
		{/if}
		{section name=ix loop=$modLastModif}
				<li><a class="linkmodule" href="tiki-index.php?page={$modLastModif[ix].pageName|escape:"url"}" title="{$modLastModif[ix].lastModif|tiki_short_datetime}, {tr}by{/tr} {if $modLastModif[ix].user ne ''}{$modLastModif[ix].user}{else}{tr}Anonymous{/tr}{/if}{if (strlen($modLastModif[ix].pageName) > $maxlen) && ($maxlen > 0)}, {$modLastModif[ix].pageName}{/if}">{if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}{$modLastModif[ix].pageName|truncate:$maxlen:"...":true}{else}{$modLastModif[ix].pageName}{/if}</a></li>
		{/section}
		{if $nonums != 'y'}
			</ol>
		{else}
			</ul>
		{/if}
	{/tikimodule}
{/if}
