{* $Header: /cvsroot/tikiwiki/tiki/templates/comment-header.tpl,v 1.5 2007-07-14 15:40:20 nyloth Exp $ *}
<div class="postbody-title">

	{if $thread_style != 'commentStyle_headers' and $comment.threadId > 0
		and $forum_mode neq 'y' || ( $forum_mode eq 'y' and $forumId > 0 and $comments_parentId > 0 )
	}
	<div class="actions">
		{if	$forum_mode neq 'y' && (
				$tiki_p_edit_comments eq 'y'
				|| $comment.userName == $user
			)
			|| $forum_mode eq 'y' && (
				$tiki_p_admin_forum eq 'y'
				|| ( $comment.userName == $user && $tiki_p_forum_edit_own_posts eq 'y' )
			)
		}
		<a title="{tr}edit{/tr}"
			{if $first eq 'y'}
			class="admlink" href="tiki-view_forum.php?comments_offset={$smarty.request.topics_offset}{$thread_sort_mode_param}&amp;comments_threshold={$smarty.request.topics_threshold}{$comments_find_param}&amp;comments_threadId={$comment.threadId}&amp;openpost=1&amp;forumId={$forum_info.forumId}{$comments_per_page_param}"
			{else}
			class="link" href="{$comments_complete_father}comments_threadId={$comment.threadId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}&amp;comments_parentId={$comments_parentId}&amp;thread_style={$thread_style}&amp;edit_reply=1#form"
			{/if}
		>{html_image file='pics/icons/page_edit.png' border='0' alt='{tr}edit{/tr}' title='{tr}edit{/tr}'}</a>
		{/if}

		{if
			( $forum_mode neq 'y' and $tiki_p_remove_comments eq 'y' )
			|| ( $forum_mode eq 'y' and $tiki_p_admin_forum eq 'y' )
		}
		<a title="{tr}delete{/tr}"
			{if $first eq 'y'}
			class="admlink" href="tiki-view_forum.php?comments_offset={$smarty.request.topics_offset}{$thread_sort_mode_param}&amp;comments_threshold={$smarty.request.topics_threshold}{$comments_find_param}&amp;comments_remove=1&amp;comments_threadId={$comment.threadId}&amp;forumId={$forum_info.forumId}{$comments_per_page_param}"
			{else}
			class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_remove=1&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}&amp;comments_parentId={$comments_parentId}&amp;thread_style={$thread_style}"
			{/if}
		>{html_image file='pics/icons/cross.png' border='0' alt='{tr}delete{/tr}' title='{tr}delete{/tr}'}</a>
		{/if}

	  	{if $user and $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
		<a title="{tr}Save to notepad{/tr}" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forumId}{$comments_threshold_param}&amp;comments_offset={$comments_offset}{$thread_sort_mode_param}{$comments_per_page_param}&amp;savenotepad={$comment.threadId}">{html_image file='pics/icons/disk.png' border='0' alt='{tr}save{/tr}'}</a>
		{/if}
	
		{if $user and $feature_user_watches eq 'y'}
		{if $user_watching_topic eq 'n'}
		<a href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;forumId={$forumId}&amp;comments_parentId={$comments_parentId}&amp;watch_event=forum_post_thread&amp;watch_object={$comments_parentId}&amp;watch_action=add">{html_image file='pics/icons/eye.png' border='0' alt='{tr}monitor this topic{/tr}' title='{tr}monitor this topic{/tr}'}</a>
		{else}
		<a href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;forumId={$forumId}&amp;comments_parentId={$comments_parentId}&amp;watch_event=forum_post_thread&amp;watch_object={$comments_parentId}&amp;watch_action=remove">{html_image file='pics/icons/no_eye.png' border='0' alt='{tr}stop monitoring this topic{/tr}' title='{tr}stop monitoring this topic{/tr}'}</a>
		{/if}
		{/if}
	</div>
	{/if}

	{if $first neq 'y'}
	<div class="checkbox">
		{if $tiki_p_admin_forum eq 'y' and $forum_mode eq 'y' and $comment.threadId > 0}
		<input type="checkbox" name="forumthread[]" value="{$comment.threadId|escape}" {if $smarty.request.forumthread and in_array($comment.threadId,$smarty.request.forumthread)}checked="checked"{/if} />
		{/if}
	</div>
	{/if}

	{if $comment.title neq ''}
	<div class="title">
	{if $first eq 'y'}
		<h2>{$comment.title}</h2>
	{else}
		{if $comments_reply_threadId == $comment.threadId}
		{html_image file='pics/icons/flag_blue.png' border='0'}<span class="highlight">
		{/if}
		<a class="link" href="{$comments_complete_father}comments_parentId={$comment.threadId}&amp;comments_per_page=1&amp;thread_style={$thread_style}">{$comment.title}</a>
		{if $comments_reply_threadId == $comment.threadId}
		</span>
		{/if}
	{/if}

	</div>
	{/if}

	{if $thread_style eq 'commentStyle_headers'}
		{include file="comment-footer.tpl"  comment=$comments_coms[rep]}
	{/if}
</div>
