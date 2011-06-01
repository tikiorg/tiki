{if $cant gt 0}
	<ol>
		{foreach from=$comments item=comment}
			<li class="comment" data-comment-thread-id="{$comment.threadId|escape}">
				<div style="float: right;">
					{self_link action=remove threadId=$comment.threadId _icon=cross _class=confirm-prompt}{tr}Remove{/tr}{/self_link}
				</div>
				<h6>{tr 0=$comment.userName|userlink 1=$comment.commentDate|tiki_long_datetime}Comment posted by %0 on %1{/tr}</h6>
				<div class="body">
					<span class="avatar">{comment.userName|avatarize}</span>
					{$comment.parsed}

					{if $allow_post && $comment.locked neq 'y'}
						<div class="button comment-form">{self_link controller=comment action=post type=$type objectId=$objectId parentId=$comment.threadId}{tr}Post new comment{/tr}{/self_link}</div>
					{/if}
				</div>

				{if $comment.replies_info.numReplies gt 0}
					{include file=$template comments=$comment.replies_info.replies cant=$comment.replies_info.numReplies parentId=$comment.threadId}
				{/if}
			</li>
		{/foreach}
	</ol>
{else}
	{remarksbox type=info title="{tr}No comments{/tr}"}
		{tr}There are no comments at this time. Come back later or start a discussion.{/tr}
	{/remarksbox}
{/if}

{if ! $parentId && $allow_post}
	<div class="button comment-form">{self_link controller=comment action=post type=$type objectId=$objectId}{tr}Post new comment{/tr}{/self_link}</div>
{/if}

