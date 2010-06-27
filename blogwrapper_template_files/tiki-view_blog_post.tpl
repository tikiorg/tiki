{* $Id$ *}
{title help='Blogs' url="tiki-view_blog.php?blogId=$blogId"}{$blog_data.title|escape}{/title}
<a class="link" href="tiki-list_blogs.php">{tr}Blogs{/tr}</a> {$prefs.site_crumb_seper} <a class="link" href="tiki-view_blog.php?blogId={$post_info.blogId}">{$blog_data.title|escape}</a> {$prefs.site_crumb_seper} {$post_info.title|escape}

<div class="post">
	<div class="postbody">
		{include file='tiki-view_blog_post_actions.tpl'}{* actions split out, info moved below title for vacomm theme *}
		{include file='tiki-view_blog_post_postbody_title.tpl'}
		{include file='tiki-view_blog_post_author_info.tpl'}
		{include file='tiki-view_blog_post_postbody_content.tpl'}
	</div>
	{include file='tiki-view_blog_post_footer.tpl'}
</div>