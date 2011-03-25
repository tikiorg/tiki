{if $prefs.feature_canonical_url eq 'y'}
	{if $mid eq 'tiki-show_page.tpl'}
		<link rel="canonical" href="{$base_url}{$page|sefurl}" />
	{elseif $mid eq 'tiki-view_tracker_item.tpl'}
		<link rel="canonical" href="{$base_url}{$itemId|sefurl:trackeritem}" />
	{elseif $mid eq 'tiki-view_forum_thread.tpl'}
		<link rel="canonical" href="{$base_url}tiki-view_forum_thread.php?comments_parentId={$comments_parentId}" />
	{elseif $mid eq 'tiki-view_blog_post.tpl'}
		<link rel="canonical" href="{$base_url}{$postId|sefurl:blogpost}" />
	{elseif $mid eq 'tiki-read_article.tpl'}
		<link rel="canonical" href="{$base_url}{$articleId|sefurl:article}" />
	{/if}
{/if}
