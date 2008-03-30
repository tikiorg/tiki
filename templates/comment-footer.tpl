{* $Id$ *}
<div class="postfooter">
	<div class="status">
	{if $prefs.feature_contribution eq 'y' and $prefs.feature_contribution_display_in_comment eq 'y'}
		<span class="contributions">
		{section name=ix loop=$comment.contributions}
			<span class="contribution">{$comment.contributions[ix].name|escape}</span>
		{/section}
		</span>
	{/if}
	{if $forum_mode eq 'y' and $forum_info.vote_threads eq 'y' or $forum_mode neq 'y'}
		<span class="score">
		<b>{tr}Score{/tr}</b>: {$comment.average|string_format:"%.2f"}
		{if $comment.userName ne $user and (
			   ( $forum_mode neq 'y' and $tiki_p_vote_comments eq 'y' )
			or ( $forum_mode eq 'y' and $forum_info.vote_threads eq 'y' and ( $tiki_p_forum_vote eq 'y' or $tiki_p_admin_forum eq 'y' ) )
		) }
		<b>{tr}Vote{/tr}</b>:

		{if $first eq 'y'}
		<a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}{$comments_threshold_param}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=1&amp;comments_offset={$comments_offset}{$thread_sort_mode_param}{$comments_per_page_param}&amp;comments_parentId={$comments_parentId}">1</a>
		<a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}{$comments_threshold_param}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=2&amp;comments_offset={$comments_offset}{$thread_sort_mode_param}{$comments_per_page_param}&amp;comments_parentId={$comments_parentId}">2</a>
		<a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}{$comments_threshold_param}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=3&amp;comments_offset={$comments_offset}{$thread_sort_mode_param}{$comments_per_page_param}&amp;comments_parentId={$comments_parentId}">3</a>
		<a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}{$comments_threshold_param}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=4&amp;comments_offset={$comments_offset}{$thread_sort_mode_param}{$comments_per_page_param}&amp;comments_parentId={$comments_parentId}">4</a>
		<a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}{$comments_threshold_param}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=5&amp;comments_offset={$comments_offset}{$thread_sort_mode_param}{$comments_per_page_param}&amp;comments_parentId={$comments_parentId}">5</a>
		{else}
		<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=1&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}&amp;comments_parentId={$comments_parentId}&amp;thread_style={$thread_style}">1</a>
		<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=2&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}&amp;comments_parentId={$comments_parentId}&amp;thread_style={$thread_style}">2</a>
		<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=3&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}&amp;comments_parentId={$comments_parentId}&amp;thread_style={$thread_style}">3</a>
		<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=4&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}&amp;comments_parentId={$comments_parentId}&amp;thread_style={$thread_style}">4</a>
		<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=5&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}&amp;comments_parentId={$comments_parentId}&amp;thread_style={$thread_style}">5</a>
		{/if}

		{/if}
		</span>
	{/if}
	
	{if $first eq 'y'}
                <span class="post_reads"><b>{tr}Reads{/tr}</b>: {$comment.hits}</span>
	{else}
		<span class="back_to_top"><a href="#tiki-top">{html_image file="img/icon_back_top.gif" border='0' alt="{tr}top of page{/tr}"}</a></span>
	{/if}

	</div>

	{if $thread_style != 'commentStyle_headers'}
	<div class="actions">
		{if ( $forum_mode neq 'y' and $tiki_p_post_comments == 'y' )
			or ( $forum_mode eq 'y' and $tiki_p_forum_post eq 'y' )
		}
			{if $forum_mode neq 'y'}
			<a class="linkbut" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_reply_threadId={$comment.threadId}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}&amp;comments_grandParentId={$comment.parentId}&amp;comments_parentId={$comment.threadId}&amp;thread_style={$thread_style}&amp;post_reply=1#form">{tr}Reply{/tr}</a>
			{else}
				{if $first eq 'y'}
				<a class="linkbut" href="#form">{tr}Reply{/tr}</a>
				{elseif $comments_grandParentId}
				<a class="linkbut" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_reply_threadId={$comment.threadId}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}&amp;comments_grandParentId={$comments_grandParentId}&amp;comments_parentId={$comments_grandParentId}&amp;thread_style={$thread_style}&amp;post_reply=1#form">{tr}Reply{/tr}</a>
				{elseif $forum_info.is_flat neq 'y'}
				<a class="linkbut" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_reply_threadId={$comment.threadId}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}&amp;comments_grandParentId={$comment.parentId}&amp;comments_parentId={$comment.parentId}&amp;thread_style={$thread_style}&amp;post_reply=1#form">{tr}Reply{/tr}</a>
				{/if}
			{/if}
		{/if}
	</div>
	{/if}

</div>
