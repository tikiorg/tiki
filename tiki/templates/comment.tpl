{* $Header: /cvsroot/tikiwiki/tiki/templates/comment.tpl,v 1.34 2007-07-02 20:53:20 nyloth Exp $ *}
{if $comment.doNotShow != 1 }
<div class="post {$comments_style}" id="threadId{$comment.threadId}">
	<div class="inner"><span class="corners-top"><span></span></span>

	<div class="postbody">
		{include file="comment-header.tpl"}
		{include file="comment-body.tpl"}
	</div>

{if $comments_style != 'commentStyle_headers'}
	{include file="comment-footer.tpl"}
	<br style="clear: both;" />
{/if}

	<span class="corners-bottom"><span></span></span></div>
</div>
{/if}

{if $first neq 'y' and $comment.replies_info.numReplies > 0 && $comment.replies_info.numReplies != ''}
	{foreach from=$comment.replies_info.replies item="comment"}
		{if $comment.doNotShow != 1 }
		<div class="sub_comment_area">
			<div class="sub_comment">
		{/if}
		{include file="comment.tpl" comment=$comment}
		{if $comment.doNotShow != 1 }
			</div>
		</div>
		{/if}
	{/foreach}
{/if}
