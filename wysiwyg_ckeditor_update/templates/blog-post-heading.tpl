{title help='Blogs' url="tiki-view_blog.php?blogId=$blogId"}{$blog_data.title|escape}{/title}
<div class="breadcrumbs"><a class="link" href="tiki-list_blogs.php">{tr}Blogs{/tr}</a> {$prefs.site_crumb_seper} <a class="link" href="tiki-view_blog.php?blogId={$post_info.blogId}">{$blog_data.title|escape}</a> {$prefs.site_crumb_seper} {$post_info.title|escape}</div>
{* example code to add more info to the default blog heading if desired
 * remove the line above (starting curly bracket then asterisk) and the last line to enable
<div class="bloginfo">
{tr}Created by{/tr} {$post_info.user|userlink} {tr}on{/tr} {$post_info.created|tiki_short_datetime}<br />
{tr}Last post{/tr} {$post_info.lastModif|tiki_short_datetime}<br />
</div>
*}
