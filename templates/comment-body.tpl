{* $Header: /cvsroot/tikiwiki/tiki/templates/comment-body.tpl,v 1.7.2.1 2007-11-08 17:15:42 jyhem Exp $ *}
<div class="content">

	<div class="author">

	{if $thread_style != 'commentStyle_headers'}
		{if $forum_info.ui_avatar eq 'y' and $comment.userName|avatarize}
		<span class="avatar">{$comment.userName|avatarize}</span>
		{/if}
	{/if}

		<span class="author_info">

			<span class="author_post_info">
				{if $comment.commentDate > 0}
					{tr}on{/tr} <span class="author_post_info_on">{$comment.commentDate|tiki_short_datetime}</span>{if $comment.userName},{/if}
				{/if}
				{if $comment.userName}
					{tr}by{/tr} <span class="author_post_info_by">{$comment.userName|userlink}</span>
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
			<span class="icons">
			<span class="actions">
			{if $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}   
				<a class="admlink" href="messu-compose.php?to={$comment.userName}&amp;subject={tr}Re:{/tr}%20{$comment.title|escape:"htmlall"}"><img src="pics/icons/user_go.png" border="0" alt="{tr}private message{/tr}" title="{tr}private message{/tr}" /></a>
			{/if}
			{if $forum_info.ui_email eq 'y' and strlen($comment.user_email) > 0}  
				<a href="mailto:{$comment.user_email|escape:'hex'}"><img src="pics/icons/email.png" alt="{tr}Send eMail to User{/tr}" title="{tr}Send eMail to User{/tr}" border='0' /></a>
			{/if}
			</span>
			<span class="infos">
			{if $forum_info.ui_online eq 'y'}
				{if $comment.user_online eq 'y'}
				<img src="pics/icons/user_red.png" border="0" alt='{tr}user online{/tr}' title='{tr}user online{/tr}' />
				{elseif $comment.user_online eq 'n'}
			  	<img src="pics/icons/user_gray.png" border="0" alt='{tr}user offline{/tr}' title='{tr}user offline{/tr}' />
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

{if $thread_style != 'commentStyle_headers'}
	{$comment.parsed}
	{* <span class="signature"><!-- SIGNATURE --></span> *}
{/if}

</div>

{if $thread_style != 'commentStyle_headers' and count($comment.attachments) > 0}
<div class="attachments">
	{section name=ix loop=$comment.attachments}
	<a class="link" href="tiki-download_forum_attachment.php?attId={$comment.attachments[ix].attId}">
	{html_image file="pics/icons/attach.png" border="0" alt='{tr}Attachment{/tr}'}
	{$comment.attachments[ix].filename} ({$comment.attachments[ix].filesize|kbsize})</a>
	{if $tiki_p_admin_forum eq 'y'}
	<a class="link"
		{if $first eq 'y'}
		href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_find_param}{$topics_threshold_param}&amp;comments_offset={$smarty.request.topics_offset}{$thread_sort_mode_param}&amp;comments_threshold={$smarty.request.topics_threshold}{$comments_find_param}&amp;forumId={$forum_info.forumId}{$comments_per_page_param}&amp;comments_parentId={$comments_parentId}&amp;remove_attachment={$comment.attachments[ix].attId}"
		{else}
		href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_find={$smarty.request.topics_find}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;comments_offset={$smarty.request.topics_offset}&amp;thread_sort_mode={$smarty.request.topics_sort_mode}&amp;comments_threshold={$smarty.request.topics_threshold}&amp;comments_find={$smarty.request.topics_find}&amp;forumId={$forum_info.forumId}&amp;comments_per_page={$comments_per_page}&amp;comments_parentId={$comments_parentId}&amp;remove_attachment={$comment.attachments[ix].attId}"
		{/if}
	>{html_image file="pics/icons/cross.png" border="0" alt='{tr}Remove{/tr}'}</a>
	{/if}
	<br />
	{/section}
</div>
{/if}
