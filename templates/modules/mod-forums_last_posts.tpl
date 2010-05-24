{tikimodule error=$module_params.error title=$tpl_module_title name="forums_last_posts" flip=$module_params.flip decorations=$module_params.decorations notitle=$module_params.notitle}
{modules_list list=$modForumsLastPosts nonums=$nonums}
	{section name=ix loop=$modForumsLastPosts}
		<li>
			<a class="linkmodule" href="{$modForumsLastPosts[ix].href}" title="{$modForumsLastPosts[ix].date|tiki_short_datetime}, {tr}by{/tr} {if $modForumsLastPosts[ix].user ne ''}{$modForumsLastPosts[ix].user}{else}{tr}Anonymous{/tr}{/if}{if !empty($modForumsLastPosts[ix].title) ne ''} - {$modForumsLastPosts[ix].title}{/if}">
				{$modForumsLastPosts[ix].name|escape}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
