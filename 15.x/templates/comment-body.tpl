{* $Id$ *}
<div class="postbody-content panel-body">

	<div class="clearfix author">

	{if $thread_style != 'commentStyle_headers'}
		{if $forum_info.ui_avatar eq 'y' and isset($comment.userName) and $comment.userName|avatarize}
			<span class="avatar">
				{$comment.userName|avatarize}
			</span>
		{/if}
	{/if}

		<span class="author_info">

			<span class="author_post_info">
				{if $first neq 'y' and $forum_info.ui_rating_choice_topic eq 'y' }
					{rating_choice comment_author=$comment.userName type=comment id=$comments_parentId }
				{/if}
				{if isset($comment.anonymous_name) and $comment.anonymous_name}
					{tr}Posted by{/tr} <span class="author_post_info_by">{if $comment.website}<a href="{$comment.website}" target="_blank">{/if}{$comment.anonymous_name}{if $comment.website}</a>{/if}</span>
				{elseif isset($comment.userName)}
					{tr}Posted by{/tr} <span class="author_post_info_by">{$comment.userName|userlink}</span>
				{/if}
				{if $comment.commentDate > 0}
					<span class="author_post_info_on">{$comment.commentDate|tiki_short_datetime:on}</span>
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
				<img src="img/icons/{$comment.user_level}stars.gif" alt="{$comment.user_level} {tr}stars{/tr}" title="{tr}User Level{/tr}">
			</span>
			{/if}

			{if isset($comment.userName) and not empty($comment.user_exists)}
			<span class="icons">
				<span class="actions">
					{if $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}
						<a class="tips" title=":{tr}Private message{/tr}" href="messu-compose.php?to={$comment.userName}&amp;subject={tr}Re:{/tr}%20{$comment.title|escape:"htmlall"}">
							{icon name='user' alt="{tr}private message{/tr}"}
						</a>
					{/if}
					{if $forum_info.ui_email eq 'y' and strlen($comment.user_email) > 0 and $display eq ''}
						<a class="tips" title=":{tr}Send eMail to user{/tr}" href="mailto:{$comment.user_email|escape:'hex'}">
							{icon name='envelope'}
						</a>
					{/if}
				</span>
				<span class="infos">
					{if $forum_info.ui_online eq 'y'}
						{if $comment.user_online eq 'y'}
							{icon name='ok' class="tips" title=":{tr}User online{/tr}"}
						{elseif $comment.user_online eq 'n'}
							{icon name='ban' class="tips" title=":{tr}User offline{/tr}"}
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

{if $thread_style != 'commentStyle_headers' and isset($comment.attachments) and count($comment.attachments) > 0}
<div class="attachments{$comment.threadId}">
	{section name=ix loop=$comment.attachments}
	<a class="tips" href="tiki-download_forum_attachment.php?attId={$comment.attachments[ix].attId}" title=":{tr}Download attachment{/tr}">
	{icon name='attach' alt="{tr}Attachment{/tr}"}
	{$comment.attachments[ix].filename} ({$comment.attachments[ix].filesize|kbsize})</a>
	{if $tiki_p_admin_forum eq 'y'}
		<a
			{if $first eq 'y'}
				href="{bootstrap_modal controller=forum action=delete_attachment topics_offset={$smarty.request.topics_offset} topics_sort_mode={$smarty.request.topics_sort_mode} topics_find={$smarty.request.topics_find} topics_threshold={$smarty.request.topics_threshold} comments_threshold={$smarty.request.topics_threshold} comments_find={$smarty.request.topics_find} forumId={$forum_info.forumId} comments_per_page={$comments_per_page} comments_parentId={$comments_parentId} remove_attachment={$comment.attachments[ix].attId} filename={$comment.attachments[ix].filename}}"
			{else}
				href="{bootstrap_modal controller=forum action=delete_attachment topics_offset={$smarty.request.topics_offset} topics_sort_mode={$smarty.request.topics_sort_mode} topics_find={$smarty.request.topics_find} topics_threshold={$smarty.request.topics_threshold} comments_threshold={$smarty.request.topics_threshold} comments_find={$smarty.request.topics_find} forumId={$forum_info.forumId} comments_per_page={$comments_per_page} comments_parentId={$comments_parentId} remove_attachment={$comment.attachments[ix].attId} filename={$comment.attachments[ix].filename} comments_offset={$smarty.request.topics_offset} thread_sort_mode={$thread_sort_mode}}"
			{/if}
			class="btn-link tips"
			title=":{tr}Remove attachment{/tr}"
		>
				{icon name='remove' alt="{tr}Remove attachment{/tr}"}
		</a>
	{/if}
	<br>
	{/section}
</div>
{/if}

{if !empty($comment.deliberations) and $tiki_p_forum_vote eq 'y' and $comment.type eq 'd'}
	<div>
		<div class="ui-widget-header">{tr}Deliberation Items{/tr}</div>
		{foreach from=$comment.deliberations item=deliberation}
			<div class="ui-widget-content">
				{$deliberation.data}
				<form class="forumDeliberationRatingForm" method="post" action="" style="float: right;">
					{rating type="comment" id=$deliberation.threadId}
					<input type="hidden" name="id" value="{$deliberation.threadId}">
					<input type="hidden" name="type" value="comment">
				</form>
				{if $tiki_p_ratings_view_results eq 'y' or $tiki_p_admin eq 'y'}
					{rating_result type="comment" id=$deliberation.threadId}
				{/if}
			</div>
		{/foreach}
		{jq}
			var crf = $('form.forumDeliberationRatingForm').submit(function() {
				var vals = $(this).serialize();
				$.tikiModal(tr('Loading...'));
				$.get('tiki-ajax_services.php?controller=rating&action=vote&' + vals, function() {
					$.tikiModal();
					$.notify(tr('Thanks for deliberating!'));
					if ($('div.ratingDeliberationResultTable').length) document.location = document.location + '';
				});
				return false;
			});
		{/jq}
	</div>
{/if}
