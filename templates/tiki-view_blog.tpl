{* $Id$ *}
{if !isset($show_heading) or $show_heading neq "n"}
	{if strlen($heading) > 0 and $prefs.feature_blog_heading eq 'y'}
		{eval var=$heading}
	{else}
		{include file='blog_heading.tpl'}
	{/if}

	<div class="blogactions clearfix margin-bottom-md">
		<div class="btn-group">
			{if $tiki_p_blog_post eq "y"}
				{if ($user and $creator eq $user) or $tiki_p_blog_admin eq "y" or $public eq "y"}
					<a class="btn btn-default btn-small bloglink tips" href="tiki-blog_post.php?blogId={$blogId}" alt="{tr}Post{/tr}" title=":{tr}Post{/tr}">{icon name=post}</a>
				{/if}
			{/if}
			{if $prefs.feed_blog eq "y"}
				<a class="btn btn-default btn-small bloglink tips" href="tiki-blog_rss.php?blogId={$blogId}" alt="{tr}RSS feed{/tr}" title=":{tr}RSS feed{/tr}">{icon name=rss}</a>
			{/if}
			{if ($user and $creator eq $user) or $tiki_p_blog_admin eq "y"}
				<a class="btn btn-default btn-small bloglink tips" href="tiki-edit_blog.php?blogId={$blogId}" alt="{tr}Edit Blog{/tr}" title=":{tr}Edit Blog{/tr}">{icon name=edit}</a>
				{if $allow_comments eq 'y'}
					<a class='btn btn-default btn-small bloglink tips' href='tiki-list_comments.php?types_section=blogs&amp;blogId={$blogId}' alt="{tr}Comments{/tr}" title=":{tr}Comments{/tr}">{icon name=comments}</a>
				{/if}
			{/if}
			{if $user and $prefs.feature_user_watches eq 'y'}
				{if $user_watching_blog eq 'n'}
					<a href="tiki-view_blog.php?blogId={$blogId}&amp;watch_event=blog_post&amp;watch_object={$blogId}&amp;watch_action=add" class="btn btn-default btn-small tips" alt="{tr}Monitor this Blog{/tr}" title=":{tr}Monitor this Blog{/tr}">{icon name=watch}</a>
				{else}
					<a href="tiki-view_blog.php?blogId={$blogId}&amp;watch_event=blog_post&amp;watch_object={$blogId}&amp;watch_action=remove" class="btn btn-default btn-small tips" alt="{tr}Stop Monitoring this Blog{/tr}" title=":{tr}Stop Monitoring this Blog{/tr}">{icon name=stop_watching}</a>
				{/if}
			{/if}
			{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
				<a href="tiki-object_watches.php?objectId={$blogId|escape:"url"}&amp;watch_event=blog_post&amp;objectType=blog&amp;objectName={$title|escape:"url"}&amp;objectHref={'tiki-view_blog.php?blogId='|cat:$blogId|escape:"url"}" class="btn btn-default btn-small tips" title=":{tr}Group Monitor{/tr}">{icon name="watch-group"}</a>
			{/if}
			{if $user and $prefs.feature_user_watches eq 'y'}
				{if $category_watched eq 'y'}
					{tr}Watched by categories:{/tr}
					{section name=i loop=$watching_categories}
						<a href="tiki-browse_categories.php?parentId={$watching_categories[i].categId}" class="btn btn-default btn-small">{$watching_categories[i].name|escape}</a>&nbsp;
					{/section}
				{/if}
			{/if}
		</div>
	</div>
	{if $use_find eq 'y'}
		<div class="blogtools">
			{include file='find.tpl' find_show_num_rows='y'}
		</div>
	{/if}
{/if}

{if $excerpt eq 'y'}
	{assign "request_context" "excerpt"}
{else}
	{assign "request_context" "view_blog"}
{/if}

{foreach from=$listpages item=post_info}
	<article class="blogpost post panel panel-default{if !empty($container_class)} {$container_class}{/if}">
		{include file='blog_wrapper.tpl' blog_post_context=$request_context}
	</article>
{/foreach}

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}