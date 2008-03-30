{* $Id$ *}
{if $prefs.feature_forums eq 'y'}
	{if $nonums eq 'y'}
		{eval var="{tr}Last `$module_rows` forum posts{/tr}" assign="tpl_module_title"}
	{else}
		{eval var="{tr}Last forum posts{/tr}" assign="tpl_module_title"}
	{/if}
	{tikimodule title=$tpl_module_title name="forums_last_posts" flip=$module_params.flip decorations=$module_params.decorations}
		{if $nonums != 'y'}
			<ol>
		{else}
			<ul>
		{/if}
		{section name=ix loop=$modForumsLastPosts}
				<li><a class="linkmodule" href="{$modForumsLastPosts[ix].href}" title="{tr}Link{/tr}">{$modForumsLastPosts[ix].name}</a></li>
		{/section}
		{if $nonums != 'y'}
			</ol>
		{else}
			</ul>
		{/if}
	{/tikimodule}
{/if}
