{* $Id$ *}
<div class="clearfix postbody-title">
	{if $prefs.feature_comments_locking neq 'y' or
		( $forum_mode neq 'y' and $comment.locked neq 'y' and $thread_is_locked neq 'y' )
		or ( $forum_mode eq 'y' and $comment.locked neq 'y' and $thread_is_locked neq 'y' )
	}
		{assign var='this_is_locked' value='n'}
	{else}
		{assign var='this_is_locked' value='y'}
	{/if}

	{if $thread_style != 'commentStyle_headers' and $this_is_locked eq 'n' and $comment.threadId > 0
		and ( $forum_mode neq 'y' || ( $forum_mode eq 'y' and $forumId > 0 and $comments_parentId > 0 ) )
	}
	<div class="actions">
		{if $forum_mode neq 'y' && $tiki_p_admin_comments eq 'y'}
			{if $prefs.feature_comments_moderation eq 'y' && $comment.approved eq 'n'}
				{self_link comments_approve='y' comments_threadId=$comment.threadId _icon='comment_approve'}{tr}Approve{/tr}{/self_link}
				{self_link comments_approve='n' comments_threadId=$comment.threadId _icon='comment_reject'}{tr}Reject{/tr}{/self_link}
			{/if}
			{if $prefs.comments_archive eq 'y'}
				{assign var='anchor' value=$comment.threadId}
				{if $comment.archived eq 'y'}
					{self_link comment_archive='n' comments_threadId=$comment.threadId _anchor="threadId$anchor" _icon='ofolder'}{tr}Unarchive{/tr}{/self_link}
				{else}
					{self_link comment_archive='y' comments_threadId=$comment.threadId _anchor="comments" _icon='folder'}{tr}Archive{/tr}{/self_link}
				{/if}
			{/if}
		{/if}
		{if	$forum_mode neq 'y' && (
				$tiki_p_edit_comments eq 'y'
				|| $comment.userName == $user
			)
			|| $forum_mode eq 'y' && (
				$tiki_p_admin_forum eq 'y'
				|| ( $comment.userName == $user && $tiki_p_forum_edit_own_posts eq 'y' )
			)
		}
		<a title="{tr}Edit{/tr}"
			{if $first eq 'y'}
			class="admlink" href="tiki-view_forum.php?comments_offset={$smarty.request.topics_offset}{$thread_sort_mode_param}&amp;comments_threshold={$smarty.request.topics_threshold}{$comments_find_param}&amp;comments_threadId={$comment.threadId}&amp;openpost=1&amp;forumId={$forum_info.forumId}{$comments_per_page_param}"
			{else}
			class="link" href="{$comments_complete_father}comments_threadId={$comment.threadId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}&amp;comments_parentId={$comments_parentId}&amp;thread_style={$thread_style}&amp;edit_reply=1#form"
			{/if}
		>{icon _id='page_edit'}</a>
		{/if}

		{if
			( $forum_mode neq 'y' and $tiki_p_remove_comments eq 'y' )
			|| ( $forum_mode eq 'y' and $tiki_p_admin_forum eq 'y' )
		}
		<a title="{tr}Delete{/tr}"
			{if $first eq 'y'}
			class="admlink" href="tiki-view_forum.php?comments_offset={$smarty.request.topics_offset}{$thread_sort_mode_param}&amp;comments_threshold={$smarty.request.topics_threshold}{$comments_find_param}&amp;comments_remove=1&amp;comments_threadId={$comment.threadId}&amp;forumId={$forum_info.forumId}{$comments_per_page_param}"
			{else}
			class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_remove=1&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}&amp;comments_parentId={$comments_parentId}&amp;thread_style={$thread_style}"
			{/if}
		>{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
		{/if}
					
		{if $tiki_p_forums_report eq 'y' and $forum_mode eq 'y'}
			{self_link report=$comment.threadId _icon='delete' _alt="{tr}Report this post{/tr}" _title="{tr}Report this post{/tr}"}{/self_link}
		{/if}
					
	  	{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y' and $forumId}
			{self_link savenotepad=$comment.threadId _icon='disk' _alt="{tr}Save to notepad{/tr}" _title="{tr}Save to notepad{/tr}"}{/self_link}
		{/if}
	
		{if $user and $prefs.feature_user_watches eq 'y' and $display eq ''}
		{if $forum_mode eq 'y' and $first eq 'y'}
		{if $user_watching_topic eq 'n'}
			{self_link watch_event='forum_post_thread' watch_object=$comments_parentId watch_action='add' _icon='eye' _alt="{tr}Monitor this Topic{/tr}" _title="{tr}Monitor this Topic{/tr}"}{/self_link}
		{else}
			{self_link watch_event='forum_post_thread' watch_object=$comments_parentId watch_action='remove' _icon='no_eye' _alt="{tr}Stop Monitoring this Topic{/tr}" _title="{tr}Stop Monitoring this Topic{/tr}"}{/self_link}
		{/if}
		{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
			<a href="tiki-object_watches.php?objectId={$comments_parentId|escape:"url"}&amp;watch_event=forum_post_thread&amp;objectType=forum&amp;objectName={$comment.title|escape:"url"}&amp;objectHref={'tiki-view_forum_thread.php?comments_parentId='|cat:$comments_parentId|cat:'&forumId='|cat:$forumId|escape:"url"}" class="icon">{icon _id='eye_group' alt="{tr}Group Monitor{/tr}"}</a>
		{/if}
		{/if}
		<br />
		{if $category_watched eq 'y'}
			{tr}Watched by categories:{/tr}
			{section name=i loop=$watching_categories}
				<a href="tiki-browse_categories.php?parentId={$watching_categories[i].categId}" class="icon">{$watching_categories[i].name|escape}</a>&nbsp;
			{/section}
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

	{if $comment.title neq '' && $comment.title neq 'Untitled' && $comment.title neq $page}
	<div class="title">
	{if $first eq 'y'}
		<h2>{$comment.title|escape}</h2>
	{elseif ( $forum_mode neq 'y' and $prefs.comments_notitle neq 'y' ) or ($forum_mode eq 'y' and $prefs.forum_reply_notitle neq 'y')}
		{if $comments_reply_threadId == $comment.threadId}
		{icon _id='flag_blue'}<span class="highlight">
		{/if}
		<a class="link" href="{$comments_complete_father}comments_parentId={$comment.threadId}&amp;comments_per_page=1&amp;thread_style={$thread_style}">{$comment.title|escape}</a>
		{if $comments_reply_threadId == $comment.threadId}
		</span>
		{/if}
	{/if}

	</div>
	{/if}

	{if $thread_style eq 'commentStyle_headers'}
		{include file='comment-footer.tpl'  comment=$comments_coms[rep]}
	{/if}
	<br class="clear" />
</div>
