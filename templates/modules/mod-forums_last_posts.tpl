{tikimodule error=$module_params.error title=$tpl_module_title name="forums_last_posts" flip=$module_params.flip decorations=$module_params.decorations notitle=$module_params.notitle}
{if $modForumsLastPosts[0] neq ''}
	{if $nonums != 'y'}<ol>{else}<ul>{/if}
	{section name=ix loop=$modForumsLastPosts}
		<li>
			<a class="linkmodule" href="{$modForumsLastPosts[ix].href}" title="{$modForumsLastPosts[ix].date|tiki_short_datetime}, {tr}by{/tr} {if $modForumsLastPosts[ix].user ne ''}{$modForumsLastPosts[ix].user}{else}{tr}Anonymous{/tr}{/if}">
				{$modForumsLastPosts[ix].name|escape}
			</a>
		</li>
	{/section}
	{if $nonums != 'y'}</ol>{else}</ul>{/if}
{else}
   <em>{tr}No records to display{/tr}</em>
{/if}
{/tikimodule}
