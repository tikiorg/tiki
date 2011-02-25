
{if $prefs.feature_canonical_url eq 'y'}
	{if $page neq ''} <link rel="canonical" href="{$base_url}{$page|sefurl}" /> {/if}
	{if $itemId neq ''} <link rel="canonical" href="{$base_url}tiki-view_tracker_item.php?itemId={$itemId}" /> {/if}
	{if $comments_parentId neq ''} <link rel="canonical" href="{$base_url}tiki-view_forum_thread.php?comments_parentId={$comments_parentId}" /> {/if}
	{if $mid eq 'tiki-view_blog_post.tpl'}<link rel="canonical" href="{$base_url}{$postId|sefurl:blogpost}" />{/if}
{/if}
