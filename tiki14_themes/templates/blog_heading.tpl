{if $blog_data.use_title eq 'y'}
	{title}{$title}{/title}
{/if}
{if $blog_data.use_breadcrumbs eq 'y'}
	<div class="breadcrumb"><a class="link" href="tiki-list_blogs.php">{tr}Blogs{/tr}</a> {$prefs.site_crumb_seper} {$title|escape}</div>
{/if}
{if $blog_data.use_description eq 'y' && $description neq ""}
	<div class="description help-block">{$description|escape}</div>
{/if}


{* Below is example code if you wish to add more info to the default blog heading
 * remove the line above (starting curly bracket then asterisk) and the last line to enable
<div class="bloginfo">
{tr}Created by{/tr} {$creator|userlink} {$created|tiki_short_datetime:on}<br>
{tr}Last post{/tr} {$lastModif|tiki_short_datetime}<br>

({$posts} {tr}Posts{/tr} | {$hits} {tr}Visits{/tr} | {tr}Activity={/tr}{$activity|string_format:"%.2f"})
</div>
*}
