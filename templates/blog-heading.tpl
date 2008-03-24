<div class="blogtitle">{tr}Blog{/tr}: {$title}</div>
{if $description neq ""}
  <div class="blogdesc">{tr}Description:{/tr} {$description}</div>
{/if}
<div class="bloginfo">
{tr}Created by{/tr} {$creator|userlink}{tr} on {/tr}{$created|tiki_short_datetime}<br />
{tr}Last post{/tr} {$lastModif|tiki_short_datetime}<br />
<span style="float:right;">
		{if $tiki_p_blog_post eq "y"}
		{if ($user and $creator eq $user) or $tiki_p_blog_admin eq "y" or $public eq "y"}
		<a class="bloglink" href="tiki-blog_post.php?blogId={$blogId}">{icon _id='pencil_add' alt='{tr}Post{/tr}'}</a>
		{/if}
		{/if}
		{if $prefs.rss_blog eq "y"}
		<a class="bloglink" href="tiki-blog_rss.php?blogId={$blogId}">{icon _id='feed' alt='{tr}RSS feed{/tr}'}</a>
		{/if}
		{if ($user and $creator eq $user) or $tiki_p_blog_admin eq "y"}
		<a class="bloglink" href="tiki-edit_blog.php?blogId={$blogId}">{icon _id='page_edit' alt='{tr}Edit blog{/tr}'}</a>
		{/if}
		
		{if $user and $prefs.feature_user_watches eq 'y'}
		{if $user_watching_blog eq 'n'}
		<a href="tiki-view_blog.php?blogId={$blogId}&amp;watch_event=blog_post&amp;watch_object={$blogId}&amp;watch_action=add">{icon _id='eye' alt='{tr}monitor this blog{/tr}'}</a>
		{else}
		<a href="tiki-view_blog.php?blogId={$blogId}&amp;watch_event=blog_post&amp;watch_object={$blogId}&amp;watch_action=remove">{icon _id='no_eye' alt='{tr}Stop Monitoring this Blog{/tr}'}</a>
		{/if}
		{/if}
</span>
({$posts} {tr}Posts{/tr} | {$hits} {tr}Visits{/tr} | {tr}Activity={/tr}{$activity|string_format:"%.2f"})
</div>

<div class="bloginfo" align="right" >
{if $user and $prefs.feature_user_watches eq 'y'}
	{if $category_watched eq 'y'}
		{tr}Watched by categories{/tr}:
		{section name=i loop=$watching_categories}
			<a href="tiki-browse_categories?parentId={$watching_categories[i].categId}">{$watching_categories[i].name}</a>&nbsp;
		{/section}
	{/if}		
{/if}
</div>
