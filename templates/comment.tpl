{if $comment.doNotShow != 1 }
{if $comments_style != 'commentStyle_plain'}
<table class="normal">
{/if}
    <tr>
	<td class="heading">{tr}author{/tr}</td>
	<td class="heading">{tr}message{/tr}</td>
    </tr>
    <tr>
	<td>
	    <div align="center">
		{if $forum_info.ui_avatar eq 'y' and $comment.userName|avatarize}
		    {$comment.userName|avatarize}<br/>
		{/if}
		<br />{$comment.userName|userlink}
		{if $forum_info.ui_flag eq 'y' and $comment.userName|countryflag}
		    <br />{$comment.userName|countryflag}
		{/if}
		{if $forum_info.ui_posts eq 'y' and $comment.user_posts}
		    <br /><small>posts:{$comment.user_posts}</small>
		{/if}
		{if $forum_info.ui_level eq 'y' and $comment.user_level}
		    <br /><img src="img/icons/{$comment.user_level}stars.gif" alt='{$comment.user_level} {tr}stars{/tr}' title='{tr}user level{/tr}' />
		{/if}
		<br />
		{if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}   
		    <a class="admlink" href="messu-compose.php?to={$comment.userName}&amp;subject={tr}Re:{/tr}%20{$comment.title}"><img src='img/icons/myinfo.gif' border='0' alt='{tr}private message{/tr}' title='{tr}private message{/tr}' /></a>
		{/if}
		{if $comment.userName and $forum_info.ui_email eq 'y' and strlen($comment.user_email) > 0}  
		    <a href="mailto:{$comment.user_email|escape:'hex'}"><img src='img/icons/email.gif' alt='{tr}send email to user{/tr}' title='{tr}send email to user{/tr}' border='0' /></a>
		{/if}
		{if $comment.userName and $forum_info.ui_online eq 'y' }
		    {if $comment.user_online eq 'y'}
			<img src='img/icons/online.gif' alt='{tr}user online{/tr}' title='{tr}user online{/tr}' />
		    {elseif $comment.user_online eq 'n'}
			<img src='img/icons/offline.gif' alt='{tr}user offline{/tr}' title='{tr}user offline{/tr}' />
		    {/if}
		{/if}
	    </div>
	</td>
	<td>
	    {if $tiki_p_admin_forum eq 'y' and $forum_mode eq 'y'}
		<input type="checkbox" name="forumthread[]" value="{$comment.threadId|escape}" {if $smarty.request.forumthread and in_array($comment.threadId,$smarty.request.forumthread)}checked="checked"{/if} />
	    {/if}
	    <span class="commentstitle">
	    {if $comments_reply_threadId == $comment.threadId}
		<img src="img/flagged.gif" /><span class="highlight">
		<a class="link" href="{$comments_complete_father}comments_parentId={$comment.threadId}&amp;comments_maxComments=1&amp;comments_style={$comments_style}">{$comment.title}</a>
		</span>
	    {else}
		<a class="link" href="{$comments_complete_father}comments_parentId={$comment.threadId}&amp;comments_maxComments=1&amp;comments_style={$comments_style}">{$comment.title}</a>
	    {/if}

	    </span><br />

	    {tr}by{/tr} <a class="link" href="tiki-user_information.php?view_user={$comment.userName}">{$comment.userName}</a> {tr}on{/tr} {$comment.commentDate|tiki_long_datetime} ({tr}Score{/tr}:{$comment.average|string_format:"%.2f"})

	    {if $comments_style != 'commentsStyle_headers'}
		{if ($tiki_p_vote_comments eq 'y' or $tiki_p_remove_comments eq 'y' or $tiki_p_edit_comments eq 'y') and $comment.userName ne $user}
		    {tr}Vote{/tr}: 
		    <a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=1&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">1</a>
		    <a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=2&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">2</a>
		    <a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=3&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">3</a>
		    <a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=4&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">4</a>
		    <a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=5&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">5</a>
		{/if}
		{if $tiki_p_remove_comments eq 'y'}
		    &nbsp;&nbsp;<a title="{tr}delete{/tr}" class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_remove=1&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>
		{/if}
		{if $tiki_p_edit_comments eq 'y'}
		    &nbsp;&nbsp;<a title="{tr}edit{/tr}" class="link" href="{$comments_complete_father}comments_threadId={$comment.threadId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}&amp;edit_reply=1#form"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
		{/if}
		{if $tiki_p_post_comments == 'y'}
			{if $forum_mode neq 'y'}
				<a class="linkbut" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_reply_threadId={$comment.threadId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_grandParentId={$comment.parentId}&amp;comments_parentId={$comment.threadId}&amp;comments_style={$comments_style}&amp;post_reply=1#form">{tr}reply{/tr}</a>
			{else}
		    	{if $comments_grandParentId}
					<a class="linkbut" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_reply_threadId={$comment.threadId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_grandParentId={$comments_grandParentId}&amp;comments_parentId={$comments_grandParentId}&amp;comments_style={$comments_style}&amp;post_reply=1#form">{tr}reply{/tr}</a>
		    	{else}
					<a class="linkbut" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_reply_threadId={$comment.threadId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_grandParentId={$comment.parentId}&amp;comments_parentId={$comment.parentId}&amp;comments_style={$comments_style}&amp;post_reply=1#form">{tr}reply{/tr}</a>
		    	{/if}
		    {/if}
		{/if}
		<hr />
	    {/if} {* $comments_style != 'commentsStyle_headers' *}
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
	</td>
    </tr>

{if $comments_style != 'commentStyle_plain' }
</table>
{/if}

{else}{* doNotShow *}
    {if $comment.replies_info.numReplies > 0 && $comment.replies_info.numReplies != ''}
	{foreach from=$comment.replies_info.replies item="comment"}
	    {include file="comment.tpl"  comment=$comment}
	{/foreach}
    {/if}
{/if} {* doNotShow *}
<br />
