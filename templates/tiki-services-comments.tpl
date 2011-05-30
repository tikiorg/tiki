{if $cant gt 0}
	<ol>
		{foreach from=$comments item=comment}
			<li class="comment" data-comment-thread-id="{$comment.threadId|escape}">
				<h6>{tr 0=$comment.userName|userlink 1=$comment.commentDate|tiki_long_datetime}Comment posted by %0 on %1{/tr}</h6>
				<div class="body">
					<span class="avatar">{comment.userName|avatarize}</span>
					{$comment.parsed}
				</div>

				{if $comment.replies_info.numReplies gt 0}
					{include file=tiki-services-comments.tpl comments=$comment.replies_info.replies cant=$comment.replies_info.numReplies}
				{/if}
			</li>
		{/foreach}
	</ol>
{else}
	{remarksbox type=info title="{tr}No comments{/tr}"}
		{tr}There are no comments at this time. Come back later or start a discussion.{/tr}
	{/remarksbox}
{/if}
