{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_blog_posts.tpl,v 1.10 2004-01-20 17:40:34 mose Exp $ *}

{if $feature_blogs eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` blog posts{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last blog posts{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="last_blog_posts"}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastBlogPosts}
<tr>
{if $nonums != 'y'}<td class="module">{$smarty.section.ix.index_next})</td>{/if}
<td class="module">
<a class="linkmodule" href="tiki-view_blog.php?blogId={$modLastBlogPosts[ix].blogId}">
<b>{$modLastBlogPosts[ix].blogTitle}: </b>
{if $modLastBlogPostsTitle and $modLastBlogPosts[ix].title}
{$modLastBlogPosts[ix].title} : 
{/if}
{$modLastBlogPosts[ix].created|tiki_short_datetime}
</a>
</td></tr>
{/section}
</table>
{/tikimodule}
{/if}
