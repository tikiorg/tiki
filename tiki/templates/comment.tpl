  <table class="normal">
  <tr>
  	<td class="odd">
  		<a name="threadId{$comment.threadId}"></a>
  		<table>
  			<tr>
			  	<td>
			    	<span class="commentstitle">{if $comments_reply_threadId == $comment.threadId}<img src="img/flagged.gif" /><span class="highlight">{$comment.title}</span>{else}{$comment.title}{/if}</span><br />
			  		{tr}by{/tr} <a class="link" href="tiki-user_information.php?view_user={$comment.userName}">{$comment.userName}</a> {tr}on{/tr} {$comment.commentDate|tiki_long_datetime} ({tr}Score{/tr}:{$comment.average|string_format:"%.2f"})
			  	</td>
			  	<td valign="top" style="text-align:right;" >
			    	{if ($tiki_p_vote_comments eq 'y' or $tiki_p_remove_comments eq 'y' or $tiki_p_edit_comments eq 'y') and $comment.userName ne $user}
			  			{tr}Vote{/tr}: 
			  				<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=1&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">1</a>
			  				<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=2&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">2</a>
			  				<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=3&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">3</a>
			  				<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=4&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">4</a>
			  				<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=5&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">5</a>
			  		{/if}
			  		{if $tiki_p_remove_comments eq 'y'}
			  			&nbsp;&nbsp;<a title="{tr}delete{/tr}" class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_remove=1&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}" 
><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>
			  		{/if}
			  		{if $tiki_p_edit_comments eq 'y'}
			  			&nbsp;&nbsp;<a title="{tr}edit{/tr}" class="link" href="{$comments_complete_father}comments_threadId={$comment.threadId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
			  		{/if}
		{if $tiki_p_post_comments == 'y'}
					<br /><br />
  		<a class="linkbut" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_reply_threadId={$comment.threadId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comment.threadId}&amp;post_reply=1#form">{tr}reply{/tr}</a>
		{/if}
			  	</td>
			 </tr>
		</table>
	</td>
  </tr>
  <tr>
  	<td class="even">
  		{$comment.parsed}
  		{if $comment.replies.numReplies > 0}
			{foreach from=$comment.replies.replies item="comment"}
					<ul class="subcomment">
					{include file="comment.tpl"  comment=$comment}
					</ul>
			{/foreach}
  		{/if}
  	</td>
  </tr>
  </table>