{tikimodule error=$module_params.error title=$tpl_module_title name="forums_last_posts" flip=$module_params.flip decorations=$module_params.decorations notitle=$module_params.notitle}
{modules_list list=$modForumsLastPosts nonums=$nonums}
	{section name=ix loop=$modForumsLastPosts}
		<li>
			<a class="linkmodule" href="{$modForumsLastPosts[ix].href}" title="{if $date eq 'n'}{$modForumsLastPosts[ix].date|tiki_short_datetime}{/if}{if $author eq 'n'}{if $date eq 'n'}, {tr}by{/tr}{else}{tr}By{/tr} {/if}{if $modForumsLastPosts[ix].user ne ''}{$modForumsLastPosts[ix].user}{else}{tr}Anonymous{/tr}{/if}{/if}{if !empty($modForumsLastPosts[ix].title) ne ''}{if $date eq 'n' or $author eq 'n'} - {/if}{$modForumsLastPosts[ix].title}{/if}">
			{$modForumsLastPosts[ix].name|escape}</a>{if $author eq 'y'} {tr}by{/tr} {$modForumsLastPosts[ix].user|username}{/if}{if $date eq 'y'}, {$modForumsLastPosts[ix].date|tiki_short_datetime}{/if}
		</li>
	{/section}
{/modules_list}
{/tikimodule}
