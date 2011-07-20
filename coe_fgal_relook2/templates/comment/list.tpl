{if ! $parentId && $allow_lock}
	{self_link controller=comment action=lock type=$type objectId=$objectId _icon=lock _class=confirm-prompt _confirm="{tr}Do you really want to lock comments?{/tr}}{tr}Lock{/tr}{/self_link}
{/if}

{if ! $parentId && $allow_unlock}
	{self_link controller=comment action=unlock type=$type objectId=$objectId _icon=lock_break _class=confirm-prompt _confirm="{tr}Do you really want to unlock comments?{/tr}"}{tr}Unlock{/tr}{/self_link}
{/if}

{if $cant gt 0}
	<ol>
		{foreach from=$comments item=comment}
			<li class="comment {if $comment.archived eq 'y'}archived{/if}" data-comment-thread-id="{$comment.threadId|escape}">
				<div style="float: right;">
					{if $allow_remove}
						{self_link action=remove threadId=$comment.threadId _icon=cross _class=confirm-prompt _confirm="{tr}Are you sure you want to remove this comment?{/tr}"}{tr}Remove{/tr}{/self_link}
					{/if}
					{if $allow_archive}
						{if $comment.archived eq 'y'}
							{self_link action=archive do=unarchive threadId=$comment.threadId _icon=ofolder _class=confirm-prompt _confirm="{tr}Are you sure you want to unarchive this comment?{/tr}"}{tr}Unarchive{/tr}{/self_link}
						{else}
							{self_link action=archive do=archive threadId=$comment.threadId _icon=folder _class=confirm-prompt _confirm="{tr}Are you sure you want to archive this comment?{/tr}"}{tr}Archive{/tr}{/self_link}
						{/if}
					{/if}
					{if $allow_moderate and $comment.approved neq 'y'}
						{self_link action=moderate do=approve threadId=$comment.threadId _icon=comment_approve _class=confirm-prompt _confirm="{tr}Are you sure you want to approve this comment?{/tr}"}{tr}Approve{/tr}{/self_link}
						{self_link action=moderate do=reject threadId=$comment.threadId _icon=comment_reject _class=confirm-prompt _confirm="{tr}Are you sure you want to reject this comment?{/tr}"}{tr}Reject{/tr}{/self_link}
					{/if}
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
					{include file='comment/list.tpl' comments=$comment.replies_info.replies cant=$comment.replies_info.numReplies parentId=$comment.threadId}
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

