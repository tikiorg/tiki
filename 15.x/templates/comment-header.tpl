{* $Id$ *}
<header class="panel-heading clearfix postbody-title media-overflow-visible"> {*the panel-heading class will cut off dropdowns so need media-overflow-visible class *}
	{if $prefs.feature_comments_locking neq 'y' or
		( $comment.locked neq 'y' and $thread_is_locked neq 'y' )}
		{assign var='this_is_locked' value='n'}
	{else}
		{assign var='this_is_locked' value='y'}
	{/if}

	{if $thread_style != 'commentStyle_headers' and $this_is_locked eq 'n' and isset($comment.threadId) and $comment.threadId > 0}
		{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
		{if $prefs.javascript_enabled !== 'y'}
			{$js = 'n'}
			{$libeg = '<li>'}
			{$liend = '</li>'}
		{else}
			{$js = 'y'}
			{$libeg = ''}
			{$liend = ''}
		{/if}

		<div class="actions pull-right btn-group">
			{capture name=comment_actions}
				{strip}
					{if $comment.threadId eq $comments_parentId}
						{* Only on the main forum topic *}
						{$libeg}<div>
							{monitor_link type="forum post" object=$comments_parentId class='tips' linktext="{tr}Notification{/tr}"}
						</div>{$liend}
					{/if}
					{if $tiki_p_admin_forum eq 'y'
					|| ( $comment.userName == $user && $tiki_p_forum_edit_own_posts eq 'y' )}
						{$libeg}<a {if $first eq 'y'} href="tiki-view_forum.php?comments_offset={$smarty.request.topics_offset}{if isset($thread_sort_mode_param)}{$thread_sort_mode_param}{/if}&amp;comments_threshold={$smarty.request.topics_threshold}{if isset($comments_find_param)}{$comments_find_param}{/if}&amp;comments_threadId={$comment.threadId}&amp;openpost=1&amp;forumId={$forum_info.forumId}{if isset($comments_per_page_param)}{$comments_per_page_param}{/if}"
							{else}
								href="{$comments_complete_father}comments_threadId={$comment.threadId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}&amp;comments_parentId={$comments_parentId}&amp;thread_style={$thread_style}&amp;edit_reply=1#form"
							{/if}
						>
							{icon name="edit" _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
						</a>{$liend}
					{/if}
					{if $tiki_p_admin_forum eq 'y'}
						<a {if $first eq 'y'} href="{bootstrap_modal controller=forum action=delete_topic forumId={$forum_info.forumId} comments_threshold={$comments_threshold} forumtopic={$comment.threadId} comments_offset={$comments_offset} thread_sort_mode={$thread_sort_mode} comments_find={$smarty.request.topics_find} comments_per_page={$comments_per_page}}"
							{else} href="{bootstrap_modal controller=forum action=delete_topic forumId={$forum_info.forumId} comments_threshold={$comments_threshold} forumtopic={$comment.threadId} comments_offset={$comments_offset} thread_sort_mode={$thread_sort_mode} comments_per_page={$comments_per_page} comments_parentId={$comments_parentId} thread_style={$thread_style}}"
							{/if}
						>
							{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Delete post{/tr}"}
						</a>{$liend}
					{/if}
					{if $tiki_p_forums_report eq 'y'}
						{$libeg}{self_link report=$comment.threadId _icon_name='error' _menu_text='y' _menu_icon='y'}
							{tr}Report this post{/tr}
						{/self_link}{$liend}
					{/if}
					{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y' and $forumId}
						{$libeg}{self_link savenotepad=$comment.threadId _icon_name='floppy' _menu_text='y' _menu_icon='y'}
							{tr}Save to notepad{/tr}
						{/self_link}{$liend}
					{/if}
					{if $user and $prefs.feature_user_watches eq 'y' and $display eq ''}
						{if $first eq 'y'}
							{if $user_watching_topic eq 'n'}
								{$libeg}{self_link watch_event='forum_post_thread' watch_object=$comments_parentId watch_action='add' _menu_text='y' _menu_icon='y' _icon_name='watch'}
									{tr}Monitor{/tr}
								{/self_link}{$liend}
							{else}
								{$libeg}{self_link watch_event='forum_post_thread' watch_object=$comments_parentId watch_action='remove' _menu_text='y' _menu_icon='y' _icon_name='stop-watching'}
									{tr}Stop monitoring{/tr}
								{/self_link}{$liend}
							{/if}
							{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
								{$libeg}<a href="tiki-object_watches.php?objectId={$comments_parentId|escape:"url"}&amp;watch_event=forum_post_thread&amp;objectType=forum&amp;objectName={$comment.title|escape:"url"}&amp;objectHref={'tiki-view_forum_thread.php?comments_parentId='|cat:$comments_parentId|cat:'&forumId='|cat:$forumId|escape:"url"}">
									{icon name="watch-group" _menu_text='y' _menu_icon='y' alt="{tr}Group monitor{/tr}"}
								</a>{$liend}
							{/if}
						{/if}
					{/if}
				{/strip}
			{/capture}
			{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
			<a
				class="tips pull-right"
				title="{tr}Actions{/tr}"
				href="#"
				{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.comment_actions|escape:"javascript"|escape:"html"}{/if}
				style="padding:0; margin:0; border:0"
			>
				{icon name='wrench'}
			</a>
			{if $js === 'n'}
				<ul class="dropdown-menu" role="menu">{$smarty.capture.comment_actions}</ul></li></ul>
			{/if}
			{if $category_watched eq 'y'}<br>
				<div class="categbar pull-right">
					{tr}Watched by categories:{/tr}
					{section name=i loop=$watching_categories}
						<a href="tiki-browse_categories.php?parentId={$watching_categories[i].categId}">{$watching_categories[i].name|escape}</a>&nbsp;
					{/section}
				</div>
			{/if}
		</div>
	{/if}

	{if !isset($first) or $first neq 'y'}
	<div>
		{if $tiki_p_admin_forum eq 'y' and isset($comment.threadId) and $comment.threadId > 0}
		<input type="checkbox" name="forumtopic[]" value="{$comment.threadId|escape}" {if $smarty.request.forumthread and in_array($comment.threadId,$smarty.request.forumthread)}checked="checked"{/if}>
		{/if}
	</div>
	{/if}

	{if $comment.title neq '' && $comment.title neq 'Untitled' && (!isset($page) or $comment.title neq $page)}
	<!-- <div class="title"> -->
	{if isset($first) and $first eq 'y'}
		<h2 class=" panel-title">{$comment.title|escape}</h2>
	{/if}

	<!-- </div> -->
	{/if}

	{if $thread_style eq 'commentStyle_headers'}
		{include file='comment-footer.tpl' comment=$comments_coms[rep]}
	{/if}
</header>
