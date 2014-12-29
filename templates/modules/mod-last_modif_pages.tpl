{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="last_modif_pages" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{modules_list list=$modLastModif nonums=$nonums}
		{section name=ix loop=$modLastModif}
			<li>
				<a class="linkmodule"
					{if $absurl eq 'y'}
						href="{$base_url}tiki-index.php?page={$modLastModif[ix].pageName|escape:"url"}"
					{else}
						href="{$modLastModif[ix].pageName|sefurl}"
					{/if}
					title="{$modLastModif[ix].lastModif|tiki_short_datetime}{if $prefs.wiki_authors_style ne 'lastmodif'}, {tr}by{/tr} {$modLastModif[ix].user|username}{/if}{if (strlen($modLastModif[ix].pageName) > $maxlen) && ($maxlen > 0)}, {$modLastModif[ix].pageName|escape}{/if}"
				>

					{if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
						{if $namespaceoption eq 'n'}
							{$data=$prefs.namespace_separator|explode:$modLastModif[ix].pageName}
							{if empty($data['1'])}
								{$pagename=$data['0']}
							{else}
								{$pagename=$data['1']}
								{/if}
							{$pagename|escape|truncate:$maxlen:"...":true}
						{else}
							{$data=$prefs.namespace_separator|explode:$modLastModif[ix].pageName}
							{if sizeof($data) == 1}
								{$pagename=$modLastModif[ix].pageName|escape}
							{else}
								{$pagename=$modLastModif[ix]|escape}
							{/if}
							{$pagename|truncate:$maxlen:"...":true}
						{/if}
					{else}
						{$data=$prefs.namespace_separator|explode:$modLastModif[ix].pageName}
						{if $namespaceoption eq 'n'}
							{if empty($data['1'])}
								{$data['0']}
							{else}
								{$data['1']}
								{/if}
						{else}
							{$modLastModif[ix].pageName|escape}
						{/if}
					{/if}

				</a>
			</li>
		{/section}
	{/modules_list}
	<a class="linkmodule" style="margin-left: 20px" href="{$url}">...{tr}more{/tr}</a>
{/tikimodule}
