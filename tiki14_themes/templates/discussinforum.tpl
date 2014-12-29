<div id="discuss-forum">
	<h1 >{tr}Page discussion{/tr}</h1>
	{capture assign=wiki_discussion_string}{include file='wiki-discussion.tpl'} [tiki-index.php?page={$page|escape:url}|{$page}]{/capture}
	{if isset($discuss_replies_cant) && $discuss_replies_cant eq 1 }
		{tr _0=$discuss_replies_cant}This page has been discussed once{/tr}
		{button _keepall='y' href="tiki-view_forum.php" forumId=$prefs.wiki_forum_id comments_postComment="post" comments_title=$page comments_data=$wiki_discussion_string comment_topictype="n" _text="{tr _0=$discuss_replies_cant}Access discussion{/tr}"}
	{else if isset($discuss_replies_cant) && $discuss_replies_cant gt 1 }
		{tr _0=$discuss_replies_cant}This page has been discussed %0 times{/tr}
		{button _keepall='y' href="tiki-view_forum.php" forumId=$prefs.wiki_forum_id comments_postComment="post" comments_title=$page comments_data=$wiki_discussion_string comment_topictype="n" _text="{tr _0=$discuss_replies_cant}Access discussion (%0 replies){/tr}"}
	{else}
		{tr}There are no discussions currently on this page{/tr}
		{button _keepall='y' href="tiki-view_forum.php" forumId=$prefs.wiki_forum_id comments_postComment="post" comments_title=$page comments_data=$wiki_discussion_string comment_topictype="n" _text="{tr}Start discussion{/tr}"}
	{/if}
</div>
