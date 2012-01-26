
{if $prefs.feature_canonical_url eq 'y'}
	{if $page neq ''} <link rel="canonical" href="{$page|sefurl}" /> {/if}
	{if $itemId neq ''} <link rel="canonical" href="tiki-view_tracker_item.php?itemId={$itemId}" /> {/if}
	{if $comments_parentId neq ''} <link rel="canonical" href="tiki-view_forum_thread.php?comments_parentId={$comments_parentId}" /> {/if}
{/if}
