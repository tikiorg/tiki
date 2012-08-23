{* $Id$ *}
{if $prefs.feature_canonical_url eq 'y' and isset($mid)}
	{if $mid eq 'tiki-show_page.tpl' or $mid eq 'tiki-index_p.tpl' or $mid eq 'tiki-show_page_raw.tpl'}
		<link rel="canonical" href="{$base_url_http}{$page|sefurl}" />
	{elseif $mid eq 'tiki-view_tracker_item.tpl'}
		<link rel="canonical" href="{$base_url_http}{$itemId|sefurl:trackeritem}" />
	{elseif $mid eq 'tiki-view_forum.tpl'}
		<link rel="canonical" href="{$base_url_http}{$forumId|sefurl:forum}" />
	{elseif $mid eq 'tiki-view_forum_thread.tpl'}
		<link rel="canonical" href="{$base_url_http}{$comments_parentId|sefurl:forumthread}" />
	{elseif $mid eq 'tiki-view_blog.tpl'}
		<link rel="canonical" href="{$base_url_http}{$blogId|sefurl:blog}" />
	{elseif $mid eq 'tiki-view_blog_post.tpl'}
		<link rel="canonical" href="{$base_url_http}{$postId|sefurl:blogpost}" />
	{elseif $mid eq 'tiki-read_article.tpl'}
		<link rel="canonical" href="{$base_url_http}{$articleId|sefurl:article}" />
	{elseif $mid eq 'tiki-browse_categories.tpl'}
		<link rel="canonical" href="{$base_url_http}{$parentId|sefurl:category}" />
	{/if}
{/if}
