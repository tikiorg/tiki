{* $Id$ *}
{*<div class="clearfix posttop">*}
	<div class="author_actions">
	{if $thread_style != 'commentStyle_headers' and $comment.threadId > 0 and $thread_is_locked neq 'y' and $comment.locked neq 'y'
		and ( $forum_mode neq 'y' || ( $forum_mode eq 'y' and $forumId > 0 and $comments_parentId > 0 and $thread_is_locked neq 'y' and $forum_is_locked neq 'y' ) )
	}
		<div class="actions">
		{if $forum_mode neq 'y' && $prefs.feature_comments_moderation eq 'y' && $tiki_p_admin_comments eq 'y' && $comment.approved eq 'n'}
			{self_link comments_approve='y' comments_threadId=$comment.threadId _icon='comment_approve'}{tr}Approve{/tr}{/self_link}
			{self_link comments_approve='n' comments_threadId=$comment.threadId _icon='comment_reject'}{tr}Reject{/tr}{/self_link}
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
		>{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
		{/if}
					
		{if $tiki_p_forums_report eq 'y' and $forum_mode eq 'y'}
			{self_link report=$comment.threadId _icon='delete' _alt='{tr}Report this post{/tr}' _title='{tr}Report this post{/tr}'}{/self_link}
		{/if}
					
	  	{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y' and $forumId}
			{self_link savenotepad=$comment.threadId _icon='disk' _alt='{tr}Save to notepad{/tr}' _title='{tr}Save to notepad{/tr}'}{/self_link}
		{/if}
	
		{if $user and $prefs.feature_user_watches eq 'y' and $display eq ''}
		{if $forum_mode eq 'y'}
		{if $user_watching_topic eq 'n'}
			{self_link watch_event='forum_post_thread' watch_object=$comments_parentId watch_action='add' _icon='eye' _alt='{tr}Monitor this Topic{/tr}' _title='{tr}Monitor this Topic{/tr}'}{/self_link}
		{else}
			{self_link watch_event='forum_post_thread' watch_object=$comments_parentId watch_action='remove' _icon='no_eye' _alt='{tr}Stop Monitoring this Topic{/tr}' _title='{tr}Stop Monitoring this Topic{/tr}'}{/self_link}
		{/if}
		{/if}
		<br />
		{if $category_watched eq 'y'}
			{tr}Watched by categories{/tr}:
			{section name=i loop=$watching_categories}
				<a href="tiki-browse_categories.php?parentId={$watching_categories[i].categId}" class="icon">{$watching_categories[i].name}</a>&nbsp;
			{/section}
		{/if}	
		{/if}
		</div>
	{/if}
		<div class="clearfix author">
	{if $thread_style != 'commentStyle_headers'}
		{if $forum_info.ui_avatar eq 'y' and $comment.userName|avatarize}
		<span class="avatar">{$comment.userName|avatarize}</span>
		{/if}
	{/if}

		<span class="author_info">

			<span class="author_post_info">
				{if $comment.userName}
					{tr}By{/tr} <span class="author_post_info_by">{$comment.userName|userlink}</span>
				{/if}
				{if $comment.commentDate > 0}
					{tr}on{/tr} <span class="author_post_info_on">{$comment.commentDate|tiki_short_datetime}</span>{*{if $comment.userName}, {/if}*}
				{/if}
				
			</span>
		{if $thread_style != 'commentStyle_headers'}
			{if $forum_info.ui_posts eq 'y' and $comment.user_posts}
			<span class="author_posts">
				{tr}posts:{/tr} {$comment.user_posts}
			</span>
			{/if}
			{if $forum_info.ui_level eq 'y' and $comment.user_level}
			<span class="author_stars">
				<img src="img/icons/{$comment.user_level}stars.gif" alt='{$comment.user_level} {tr}stars{/tr}' title='{tr}User Level{/tr}' />
			</span>
			{/if}

			{if $comment.userName}
			<span class="clearfix icons">
			<span class="actions">
			{if $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}   
				<a class="admlink" href="messu-compose.php?to={$comment.userName}&amp;subject={tr}Re:{/tr}%20{$comment.title|escape:"htmlall"}">{icon _id='user_go' alt="{tr}private message{/tr}"}</a>
			{/if}
			{if $forum_info.ui_email eq 'y' and strlen($comment.user_email) > 0 and $display eq ''}  
				<a href="mailto:{$comment.user_email|escape:'hex'}">{icon _id='email' alt="{tr}Send eMail to User{/tr}"}</a>
			{/if}
			</span>
			<span class="infos">
			{if $forum_info.ui_online eq 'y'}
				{if $comment.user_online eq 'y'}
				{icon _id='user_red' alt='{tr}user online{/tr}'}
				{elseif $comment.user_online eq 'n'}
			  	{icon _id='user_gray' alt='{tr}user offline{/tr}'}
				{/if}
			{/if}
			{if $forum_info.ui_flag eq 'y' and $comment.userName|countryflag}
				{$comment.userName|countryflag}
			{/if}
			</span>
			</span>
			{/if}
		{/if}
		</span>
		</div>
	</div>
	<div class="clearfix postbody-title">

	
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
		<h2>{$comment.title}</h2>
	{else}
		{if $comments_reply_threadId == $comment.threadId}
		{icon _id='flag_blue'}<span class="highlight">
		{/if}
		<a class="link" href="{$comments_complete_father}comments_parentId={$comment.threadId}&amp;comments_per_page=1&amp;thread_style={$thread_style}"><h3>{$comment.title}</h3></a>
		{if $comments_reply_threadId == $comment.threadId}
		</span>
		{/if}
	{/if}

	</div>
	{/if}

	{if $thread_style eq 'commentStyle_headers'}
		{include file="comment-footer.tpl"  comment=$comments_coms[rep]}
	{/if}
	{*<br class="clear" />*}
{*</div>*}
</div>
