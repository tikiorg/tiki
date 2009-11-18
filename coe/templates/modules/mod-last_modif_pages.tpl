{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="last_modif_pages" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
{section name=ix loop=$modLastModif}
<li>
	<a class="linkmodule" 
	{if $absurl == 'y'}
		href="{$base_url}tiki-index.php?page={$modLastModif[ix].pageName|escape:"url"}" 
	{else}
		href="{$modLastModif[ix].pageName|sefurl}"
	{/if}
	title="{$modLastModif[ix].lastModif|tiki_short_datetime}{if $prefs.wiki_authors_style ne 'lastmodif'}, {tr}by{/tr} {$modLastModif[ix].user|username}{/if}{if (strlen($modLastModif[ix].pageName) > $maxlen) && ($maxlen > 0)}, {$modLastModif[ix].pageName|escape}{/if}">
	{if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
		{$modLastModif[ix].pageName|escape|truncate:$maxlen:"...":true}
	{else}
		{$modLastModif[ix].pageName|escape}
	{/if}
	</a>
</li>
{/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
<a class="linkmodule" style="margin-left: 20px" href="{$url}">...{tr}more{/tr}</a>
{/tikimodule}
