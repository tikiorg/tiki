{* $Id$ *}

{if $prefs.feature_blogs eq 'y'}
	{if !isset($tpl_module_title)}
		{if $module_params.nonums eq 'y'}
			{eval var="{tr}Last `$module_rows` blog posts{/tr}" assign="tpl_module_title"}
		{else}
			{eval var="{tr}Last blog posts{/tr}" assign="tpl_module_title"}
		{/if}
	{/if}
	{tikimodule error=$module_params.error title=$tpl_module_title name="last_blog_posts" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	
	{if $module_params.nonums neq 'y'}<ol>{else}<ul>{/if}
		{section name=ix loop=$modLastBlogPosts}
			<li>
				<a class="linkmodule" href="tiki-view_blog.php?blogId={$modLastBlogPosts[ix].blogId}" title="{$modLastBlogPosts[ix].created|tiki_short_datetime}, {tr}by{/tr} {if $modLastBlogPosts[ix].user ne ''}{$modLastBlogPosts[ix].user}{else}{tr}Anonymous{/tr}{/if}">
					<div>{if $module_params.blogid eq ''}<b>{$modLastBlogPosts[ix].blogTitle}</b>: {/if}{$modLastBlogPosts[ix].title}<br /></div>
					{if $module_params.nodate neq 'y'}
						<div style="font-weight:normal;font-style:italic">{$modLastBlogPosts[ix].created|tiki_short_datetime}</div>
					{/if}
				</a>
			</li>
		{/section}
		{if $module_params.nonums neq 'y'}</ol>{else}</ul>{/if} 
	{/tikimodule}
{/if}
