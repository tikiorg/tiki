{if $comment.doNotShow != 1 }
{if $comments_style != 'commentStyle_plain'}
  <table class="normal">
{/if}
  <tr>
  	<td class="odd">
  		<a name="threadId{$comment.threadId}"></a>
  		<table>
  			<tr>
			  	<td>
			    	<span class="commentstitle">{if $comments_reply_threadId == $comment.threadId}<img src="img/flagged.gif" /><span class="highlight">
<a class="link" href="{$comments_complete_father}comments_parentId={$comment.threadId}&amp;comments_maxComments=1&amp;comments_style={$comments_style}">{$comment.title}</a>
</span>
{else}
<a class="link" href="{$comments_complete_father}comments_parentId={$comment.threadId}&amp;comments_maxComments=1&amp;comments_style={$comments_style}">{$comment.title}</a>
{/if}</span><br />
			  		{tr}by{/tr} <a class="link" href="tiki-user_information.php?view_user={$comment.userName}">{$comment.userName}</a> {tr}on{/tr} {$comment.commentDate|tiki_long_datetime} ({tr}Score{/tr}:{$comment.average|string_format:"%.2f"})
			  	</td>
{if $comments_style != 'commentsStyle_headers'}
			  	<td valign="top" style="text-align:right;" >
			    	{if ($tiki_p_vote_comments eq 'y' or $tiki_p_remove_comments eq 'y' or $tiki_p_edit_comments eq 'y') and $comment.userName ne $user}
			  			{tr}Vote{/tr}: 
			  				<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=1&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">1</a>
			  				<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=2&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">2</a>
			  				<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=3&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">3</a>
			  				<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=4&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">4</a>
			  				<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=5&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">5</a>
			  		{/if}
			  		{if $tiki_p_remove_comments eq 'y'}
			  			&nbsp;&nbsp;<a title="{tr}delete{/tr}" class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_remove=1&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}" 
><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>
			  		{/if}
			  		{if $tiki_p_edit_comments eq 'y'}
			  			&nbsp;&nbsp;<a title="{tr}edit{/tr}" class="link" href="{$comments_complete_father}comments_threadId={$comment.threadId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
			  		{/if}
		{if $tiki_p_post_comments == 'y'}
  		<a class="linkbut" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_reply_threadId={$comment.threadId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comment.threadId}&amp;comments_style={$comments_style}&amp;post_reply=1#form">{tr}reply{/tr}</a>
		{/if}
			  	</td>
{/if}
			 </tr>
		</table>
	</td>
  </tr>
  <tr>
  	<td class="even">
{if $comments_style != 'commentsStyle_headers'}
  		{$comment.parsed}
			<br />
{/if}
  		{if $comment.replies_info.numReplies > 0 && $comment.replies_info.numReplies != ''}
			{foreach from=$comment.replies_info.replies item="comment"}
					<ul class="subcomment">
					{include file="comment.tpl"  comment=$comment}
					</ul>
			{/foreach}
  		{/if}
			</div>
  	</td>
  </tr>
{if $comments_style != 'commentStyle_plain' }
  </table>
{/if}
{else}
  		{if $comment.replies_info.numReplies > 0 && $comment.replies_info.numReplies != ''}
			{foreach from=$comment.replies_info.replies item="comment"}
					{include file="comment.tpl"  comment=$comment}
			{/foreach}
  		{/if}
{/if}
<br />
