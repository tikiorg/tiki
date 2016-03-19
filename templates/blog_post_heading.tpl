{* $Id$ *}
{if $blog_data.use_title_in_post eq 'y'}
	{capture name="blog_actions"}{include file='blog_actions.tpl'}{/capture}
	{title url={$blogId|sefurl:blog} actions="{$smarty.capture.blog_actions}"}{$blog_data.title}{/title}
{/if}
{if $blog_data.use_breadcrumbs eq 'y'}
	<div class="breadcrumb"><a class="link" href="tiki-list_blogs.php">{tr}Blogs{/tr}</a> {$prefs.site_crumb_seper} <a class="link" href="tiki-view_blog.php?blogId={$post_info.blogId}">{$blog_data.title|escape}</a> {$prefs.site_crumb_seper} {$post_info.title|escape}</div>
{/if}

{* below is example code. In case you desire to add more info to the default blog heading
 * remove the line above (starting curly bracket then asterisk) and the last line to enable
<div class="bloginfo">
{tr}Created by{/tr} {$post_info.user|userlink} {$post_info.created|tiki_short_datetime:on}<br>
{tr}Last post{/tr} {$post_info.lastModif|tiki_short_datetime}<br>
</div>
*}
