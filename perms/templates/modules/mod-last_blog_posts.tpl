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
				<a class="linkmodule" href="tiki-view_blog_post.php?postId={$modLastBlogPosts[ix].postId}" title="{$modLastBlogPosts[ix].created|tiki_short_datetime}, {tr}by{/tr} {if $modLastBlogPosts[ix].user ne ''}{$modLastBlogPosts[ix].user}{else}{tr}Anonymous{/tr}{/if}">
					{if $module_params.blogid eq ''}{$modLastBlogPosts[ix].blogTitle}: {/if}
					{$modLastBlogPosts[ix].title}
					{if $module_params.nodate neq 'y'}
						<small class="description">{$modLastBlogPosts[ix].created|tiki_short_date}</small>
					{/if}
				</a>
			</li>
		{/section}
		{if $module_params.nonums neq 'y'}</ol>{else}</ul>{/if} 
	{/tikimodule}
{/if}
