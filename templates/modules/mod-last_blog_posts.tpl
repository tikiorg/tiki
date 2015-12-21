{* $Id$ *}
{tikimodule error=$module_params.error title=$tpl_module_title name="last_blog_posts" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modLastBlogPosts nonums=$nonums}
	{section name=ix loop=$modLastBlogPosts}
		<li>
			<a class="linkmodule" href="{$modLastBlogPosts[ix].postId|sefurl:blogpost}" title="{$modLastBlogPosts[ix].created|tiki_short_datetime}, {tr}by{/tr} {if $modLastBlogPosts[ix].user ne ''}{$modLastBlogPosts[ix].user|username}{else}{tr}Anonymous{/tr}{/if}">
				{if $blogid eq '-1'}{$modLastBlogPosts[ix].blogTitle|escape}: {/if}
					{$modLastBlogPosts[ix].title|escape}{if $modLastBlogPosts[ix].priv eq 'y'} ({tr}private{/tr}){/if}
			</a>
			{if $nodate neq 'y'}
				<div class="date">{$modLastBlogPosts[ix].created|tiki_short_datetime}</div>
			{/if}
		</li>
	{/section}
{/modules_list}
{if ($tiki_p_blog_post eq 'y' or $tiki_p_blog_admin eq 'y')}
	<a class="btn btn-link" href="tiki-blog_post.php?blogId={$blogid}">{icon name="add"} {tr}Add Post{/tr}</a>
{/if}
{/tikimodule}
