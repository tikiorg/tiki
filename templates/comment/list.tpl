{if ! $parentId && $allow_lock}
	{self_link controller=comment action=lock type=$type objectId=$objectId _icon=lock _class="confirm-prompt" _confirm="{tr}Do you really want to lock comments?{/tr}"}{tr}Lock{/tr}{/self_link}
{/if}

{if ! $parentId && $allow_unlock}
	{self_link controller=comment action=unlock type=$type objectId=$objectId _icon=lock_break _class="confirm-prompt" _confirm="{tr}Do you really want to unlock comments?{/tr}"}{tr}Unlock{/tr}{/self_link}
{/if}

{if $cant gt 0}
	<ol>
		{foreach from=$comments item=comment}
			<li class="comment {if $comment.archived eq 'y'}archived{/if} {if ! $parentId && $prefs.feature_wiki_paragraph_formatting eq 'y'}inline{/if}" data-comment-thread-id="{$comment.threadId|escape}">
				<article>
					<div style="float: right;">
						{if $allow_remove}
							{self_link controller=comment action=remove threadId=$comment.threadId _icon=cross _class="confirm-prompt" _confirm="{tr}Are you sure you want to remove this comment?{/tr}"}{tr}Remove{/tr}{/self_link}
						{/if}
						{if $allow_archive}
							{if $comment.archived eq 'y'}
								{self_link controller=comment action=archive do=unarchive threadId=$comment.threadId _icon=ofolder _class="confirm-prompt" _confirm="{tr}Are you sure you want to unarchive this comment?{/tr}"}{tr}Unarchive{/tr}{/self_link}
							{else}
								{self_link controller=comment action=archive do=archive threadId=$comment.threadId _icon=folder _class="confirm-prompt" _confirm="{tr}Are you sure you want to archive this comment?{/tr}"}{tr}Archive{/tr}{/self_link}
							{/if}
						{/if}
						{if $allow_moderate and $comment.approved neq 'y'}
							{self_link controller=comment action=moderate do=approve threadId=$comment.threadId _icon=comment_approve _class="confirm-prompt" _confirm="{tr}Are you sure you want to approve this comment?{/tr}"}{tr}Approve{/tr}{/self_link}
							{self_link controller=comment action=moderate do=reject threadId=$comment.threadId _icon=comment_reject _class="confirm-prompt" _confirm="{tr}Are you sure you want to reject this comment?{/tr}"}{tr}Reject{/tr}{/self_link}
						{/if}
					</div>
					{if $prefs.comments_notitle eq 'y'}
						<h4>{tr _0=$comment.userName|userlink _1=$comment.commentDate|tiki_long_datetime}Comment posted by %0 on %1{/tr}</h4>
					{else}
						<h4 class="title">{$comment.title}</h4>
						<span class="avatar">{$comment.userName|avatarize}</span>
						<div class="author_info">{tr _0=$comment.userName|userlink _1=$comment.commentDate|tiki_long_datetime}Comment posted by %0 on %1{/tr}</div>						
					{/if}
					<div class="body">
						{$comment.parsed}
					</div>

					<table style="width: 100%;">
						<tr>
							{if $allow_post && $comment.locked neq 'y'}
							<td>
								<div class="button comment-form">{self_link controller=comment action=post type=$type objectId=$objectId parentId=$comment.threadId}{tr}Reply{/tr}{/self_link}</div>
							</td>
							{/if}

							{if $prefs.wiki_comments_simple_ratings eq 'y'}
							<td>
								<form class="commentRatingForm" method="post" action="" style="float: right;">
									{rating type="comment" id=$comment.threadId}
									<input type="hidden" name="id" value="{$comment.threadId}" />
									<input type="hidden" name="type" value="comment" />
								</form>
								{jq}
									var crf = $('form.commentRatingForm').submit(function() {
										var vals = $(this).serialize();
										$.modal(tr('Loading...'));
										$.get('tiki-ajax_services.php?controller=rating&action=vote&' + vals, function() {
											$.modal();
											$.notify(tr('Thanks for rating!'));
										});
										return false;
									});
								{/jq}
							</td>
							{/if}
						</tr>
					</table>

					{if $comment.replies_info.numReplies gt 0}
						{include file='comment/list.tpl' comments=$comment.replies_info.replies cant=$comment.replies_info.numReplies parentId=$comment.threadId}
					{/if}
				</article>	
			</li>
		{/foreach}
	</ol>
{else}
	{remarksbox type=info title="{tr}No comments{/tr}"}
		{tr}There are no comments at this time.{/tr}
	{/remarksbox}
{/if}

{if ! $parentId && $allow_post}
	<div class="button comment-form">{self_link controller=comment action=post type=$type objectId=$objectId}{tr}Post new comment{/tr}{/self_link}</div>
{/if}

{if ! $parentId && $prefs.feature_wiki_paragraph_formatting eq 'y'}
	<a id="note-editor-comment" href="#" style="display:none;">{tr}Add Comment{/tr}</a>
{/if}

<script type="text/javascript">
var ajax_url = '{$base_url}';
var objectId = '{$objectId}';
</script>
{if $prefs.wiki_comments_form_displayed_default eq 'y'}
{jq}
function comment_load(url) {
	$('#top .note-list').remove();

	this.each(function () {
		var comment_container = this;
		$(this).load(url, function (response, status) {
			$(this).show();
			$('.comment.inline dt:contains("note")', this)
				.closest('.comment')
				.addnotes( $('#top') );

            if(jqueryTiki.useInlineComment) {
				$('#top').noteeditor($('.comment-form:last a', comment_container), '#note-editor-comment');
			} 

			$('.comment-form a', this).click(function () {
				$(this).parent().empty().removeClass('button').load($(this).attr('href'), function () {
					var form = $('form', this).submit(function () {
						var errors;
						$.post(form.attr('action'), $(this).serialize(), function (data, st) {
							if (data.threadId) {
								$(comment_container).empty().comment_load(url);
								$comm_counter = $('span.count_comments');
								if ($comm_counter.length != 0) {
									var comment_count = parseInt($comm_counter.text()) + 1;
									$comm_counter.text(comment_count);
								}
							} else {
								errors = $('ol.errors', form).empty();
								if (! errors.length) {
									$(':submit', form).after(errors = $('<ol class="errors"/>'));
								}
								
								$.each(data.errors, function (k, v) {
									errors.append($('<li/>').text(v));
								});
							}
						}, 'json');
						return false;
					});

					//allow syntax highlighting
					if ($.fn.flexibleSyntaxHighlighter) {
						// console.log(form.find('textarea.wikiedit'));
						form.find('textarea.wikiedit').flexibleSyntaxHighlighter();
					}
				});
				return false;
			});

			$('.button.comment-form.autoshow a').click(); // allow autoshowing of comment forms through autoshow css class 

			$('.confirm-prompt', this).requireConfirm({
				success: function (data) { 
					if (data.status === 'DONE') {
						$(comment_container).empty().comment_load(url);
						$comm_counter = $('span.count_comments');
						if ($comm_counter.length != 0) {
							var comment_count = parseInt($comm_counter.text()) - 1;
							$comm_counter.text(comment_count);
						}
					}
				}
			});
		});
	});

	return this;
};

var comm_href =  ajax_url + "tiki-ajax_services.php?controller=comment&action=post&type="+encodeURIComponent("wiki page")+"&objectId="+encodeURIComponent(objectId);
var url = 'tiki-ajax_services.php?controller=comment&action=list&type=wiki+page&objectId='+encodeURIComponent(objectId)+'#comment-container';
$this = $('.comment-form').last().find('a');
$this.parent().empty().removeClass('button').load($this.attr('href'), function () {
	var form = $('form', this).submit(function () {
		var errors;
		$.post(form.attr('action'), $(this).serialize(), function (data, st) {
			if (data.threadId) {
				$("#comment-container").empty().comment_load(url);
				$comm_counter = $('span.count_comments');
				if ($comm_counter.length != 0) {
					var comment_count = parseInt($comm_counter.text()) + 1;
					$comm_counter.text(comment_count);
				}
			} else {
				errors = $('ol.errors', form).empty();
				if (! errors.length) {
					$(':submit', form).after(errors = $('<ol class="errors"/>'));
				}
				
				$.each(data.errors, function (k, v) {
					errors.append($('<li/>').text(v));
				});
			}
		}, 'json');
		return false;
	});

	//allow syntax highlighting
	if ($.fn.flexibleSyntaxHighlighter) {
		// console.log(form.find('textarea.wikiedit'));
		form.find('textarea.wikiedit').flexibleSyntaxHighlighter();
	}
});
{/jq}
{/if}