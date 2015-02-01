<ol class="media-list">
	{foreach from=$comments item=comment}
		<li class="media comment {if $comment.archived eq 'y'}archived{/if} {if ! $parentId && $prefs.feature_wiki_paragraph_formatting eq 'y'}inline{/if}" data-comment-thread-id="{$comment.threadId|escape}">
			<div class="pull-left">
				<span class="avatar">{$comment.userName|avatarize:'':'img/noavatar.png'}</span>
			</div>
			<div class="media-body">
				<div class="comment-item">
					{if $prefs.comments_notitle eq 'y'}
						<h4 class="media-heading">
							<div class="comment-info">
								{tr _0=$comment.userName|userlink}%0{/tr} <small class="date">{tr _0=$comment.commentDate|tiki_short_datetime}%0{/tr}</small>
							</div>
						</h4>
					{else}
						<h4 class="media-heading">
							<div class="comment-title">
								{$comment.title}
							</div>
							<div class="comment-info">
								{tr _0=$comment.userName|userlink}%0{/tr} <small class="date">{tr _0=$comment.commentDate|tiki_short_datetime}%0{/tr}</small>
							</div>
						</h4>
					{/if}
					<div class="comment-body">
						{$comment.parsed}
					</div>
					<div class="buttons comment-form comment-footer">
						{if $allow_post && $comment.locked neq 'y'}
							<a class='btn btn-link btn-sm' href="{service controller=comment action=post type=$type objectId=$objectId parentId=$comment.threadId}">{tr}Reply{/tr}</a>
						{/if}
						{if $comment.can_edit}
							<a class='btn btn-link btn-sm' href="{service controller=comment action=edit threadId=$comment.threadId}">{tr}Edit{/tr}</a>
						{/if}
						{if $allow_remove}
							<a class="btn btn-link btn-sm" href="{service controller=comment action=remove threadId=$comment.threadId}">{tr}Delete{/tr}</a>
						{/if}
						{if $allow_archive}
							{if $comment.archived eq 'y'}
								<a class="btn btn-link btn-sm" href="{service controller=comment action=archive do=unarchive threadId=$comment.threadId}">{tr}Unarchive{/tr}</a>
							{else}
								<a class="btn btn-link btn-sm" href="{service controller=comment action=archive do=archive threadId=$comment.threadId}">{tr}Archive{/tr}</a>
							{/if}
						{/if}
						{if $allow_moderate and $comment.approved neq 'y'}
							{self_link controller=comment action=moderate do=approve threadId=$comment.threadId _icon=comment_approve _class="confirm-prompt btn btn-default btn-sm" _confirm="{tr}Are you sure you want to approve this comment?{/tr}"}{tr}Approve{/tr}{/self_link}
							{self_link controller=comment action=moderate do=reject threadId=$comment.threadId _icon=comment_reject _class="confirm-prompt btn btn-default btn-sm" _confirm="{tr}Are you sure you want to reject this comment?{/tr}"}{tr}Reject{/tr}{/self_link}
						{/if}
						{if $comment.userName ne $user and $comment.approved eq 'y' and $prefs.wiki_comments_simple_ratings eq 'y' and ($tiki_p_vote_comments eq 'y' or $tiki_p_admin_comments eq 'y' )}
							<form class="commentRatingForm" method="post">
								{rating type="comment" id=$comment.threadId}
								<input type="hidden" name="id" value="{$comment.threadId}" />
								<input type="hidden" name="type" value="comment" />
							</form>
							{jq}
								var crf = $('form.commentRatingForm').submit(function() {
									var vals = $(this).serialize();
									$.tikiModal(tr('Loading...'));
									$.post($.service('rating', 'vote'), vals, function() {
										$.tikiModal();
										$.notify(tr('Thanks for rating!'));
									});
									return false;
								});
							{/jq}
						{/if}
						{if $prefs.wiki_comments_simple_ratings eq 'y' && ($tiki_p_ratings_view_results eq 'y' or $tiki_p_admin eq 'y')}
							{rating_result type="comment" id=$comment.threadId}
						{/if}

					</div><!-- End of comment-footer -->
				</div><!-- End of comment-item -->
				{if $comment.replies_info.numReplies gt 0}
					{include file='comment/list_inner.tpl' comments=$comment.replies_info.replies cant=$comment.replies_info.numReplies parentId=$comment.threadId}
				{/if}
			</div><!-- End of media-body -->
		</li>
	{/foreach}
</ol>
