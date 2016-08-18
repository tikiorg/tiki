<ol class="media media-list comment-list">
	{foreach from=$comments item=comment}
		<li class="media comment{if $comment.archived eq 'y'} archived well well-sm{/if} {if $allow_moderate}{if $comment.approved eq 'n'} pending bg-warning{elseif $comment.approved eq 'r'} rejected bg-danger{/if}{/if}{if ! $parentId && $prefs.feature_wiki_paragraph_formatting eq 'y'} inline{/if}" data-comment-thread-id="{$comment.threadId|escape}">
			<div class="media-left">
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
						{block name="buttons"}
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
									<span class="label label-default">{tr}Archived{/tr}</span>
									<a class="btn btn-link btn-sm" href="{service controller=comment action=archive do=unarchive threadId=$comment.threadId}">{tr}Unarchive{/tr}</a>
								{else}
									<a class="btn btn-link btn-sm" href="{service controller=comment action=archive do=archive threadId=$comment.threadId}">{tr}Archive{/tr}</a>
								{/if}
							{/if}
						{/block}
						{if $allow_moderate and $comment.approved neq 'y'}
							{if $comment.approved eq 'n'}
								<span class="label label-warning">{tr}Pending{/tr}</span>
							{/if}
							{if $comment.approved eq 'r'}
								<span class="label label-danger">{tr}Rejected{/tr}</span>
							{/if}
							<a href="{service controller=comment action=moderate do=approve threadId=$comment.threadId}" class="btn btn-default btn-sm tips" title="{tr}Approve{/tr}">{icon name="ok"}</a>
							{if $comment.approved eq 'n'}
								<a href="{service controller=comment action=moderate do=reject threadId=$comment.threadId}" class="btn btn-default btn-sm tips" title="{tr}Reject{/tr}">{icon name="remove"}</a>
							{/if}
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
