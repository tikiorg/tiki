{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_blog_posts.tpl,v 1.3 2003-08-07 20:56:53 zaufi Exp $ *}

{if $feature_blogs eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Last blog posts{/tr}" module_name="last_blog_posts"}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastBlogPosts}
<tr><td  width="5%" class="module" valign="top">{$smarty.section.ix.index_next})</td><td class="module"><a class="linkmodule" href="tiki-view_blog.php?blogId={$modLastBlogPosts[ix].blogId}">{$modLastBlogPosts[ix].blogTitle}:<br/>{$modLastBlogPosts[ix].created|tiki_short_datetime}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}