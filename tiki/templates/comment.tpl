{if $comment.doNotShow != 1 }
<a name="threadId{$comment.threadId}"></a>
{if $comments_style != 'commentStyle_plain'}
<table class="normal">
{/if}
  <tr>
		<td class="heading forumuser">{tr}author{/tr}</td>
		<td class="heading">{tr}message{/tr}</td>
  </tr>
  <tr>
		{if $comments_style != 'commentStyle_headers'}
		<td rowspan="2" class="odd">
	    <div align="center">
			{if $forum_info.ui_avatar eq 'y' and $comment.userName|avatarize}
			  {$comment.userName|avatarize}<br />
			{/if}
			{$comment.userName|userlink}
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
			  <a class="admlink" href="messu-compose.php?to={$comment.userName}&amp;subject={tr}Re:{/tr}%20{$comment.title}"><img src='pics/icons/user_go.png' border='0' alt='{tr}private message{/tr}' title='{tr}private message{/tr}' width='16' height='16' /></a>
			{/if}
			{if $comment.userName and $forum_info.ui_email eq 'y' and strlen($comment.user_email) > 0}  
			  <a href="mailto:{$comment.user_email|escape:'hex'}"><img src='pics/icons/email.png' alt='{tr}send email to user{/tr}' title='{tr}send email to user{/tr}' border='0' width='16' height='16' /></a>
			{/if}
			{if $comment.userName and $forum_info.ui_online eq 'y' }
			  {if $comment.user_online eq 'y'}
					<img src="pics/icons/user_red.png" border="0" width="16" height="16" alt='{tr}user online{/tr}' title='{tr}user online{/tr}' />
			  {elseif $comment.user_online eq 'n'}
					<img src="pics/icons/user_gray.png" border="0" width="16" height="16" alt='{tr}user offline{/tr}' title='{tr}user offline{/tr}' />
			  {/if}
			{/if}
	    </div>
		</td>
		{/if} {* if $comments_style != 'commentStyle_headers' *}
		<td class="odd">
	    {if $tiki_p_admin_forum eq 'y' and $forum_mode eq 'y'}
				<input type="checkbox" name="forumthread[]" value="{$comment.threadId|escape}" {if $smarty.request.forumthread and in_array($comment.threadId,$smarty.request.forumthread)}checked="checked"{/if} />
    	{/if}
    	<span class="commentstitle">
    	{if $comments_reply_threadId == $comment.threadId}
				<img src="pics/icons/flag_blue.png" border="0" width="16" height="16" alt='' /><span class="highlight">
				<a class="link" href="{$comments_complete_father}comments_parentId={$comment.threadId}&amp;comments_maxComments=1&amp;comments_style={$comments_style}">{$comment.title}</a>
				</span>
    	{else}
				<a class="link" href="{$comments_complete_father}comments_parentId={$comment.threadId}&amp;comments_maxComments=1&amp;comments_style={$comments_style}">{$comment.title}</a>
    	{/if}
	    </span><br />

			<table class="commentinfo">
			<tr>
  	    <td style="font-size:8pt;">
				  <b>{tr}on{/tr}</b>: {$comment.commentDate|tiki_short_datetime}
  	  	</td>
	    	<td style="font-size:8pt;">
					<b>{tr}score{/tr}</b>: {$comment.average|string_format:"%.2f"}

	    	</td>

	    	{if $comments_style != 'commentStyle_headers'}
				{if (($tiki_p_vote_comments eq 'y' and $forum_mode ne 'y') or ($tiki_p_forum_vote eq 'y' and $forum_mode eq 'y')) and $comment.userName ne $user}
				<td style="font-size:8pt;">
					<b>{tr}Vote{/tr}</b>:  
					<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=1&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">1</a>
					<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=2&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">2</a>
					<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=3&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">3</a>
					<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=4&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">4</a>
					<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_vote=5&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">5</a>
				</td>
				{/if} {* if ($tiki_p_vote_comments eq 'y' ... *}
				<td align="right">
					{if $tiki_p_edit_comments eq 'y' || $user == $comment.userName || ($tiki_p_admin_forum eq 'y' and $forum_mode eq 'y')}
					  &nbsp;&nbsp;<a title="{tr}edit{/tr}" class="link" href="{$comments_complete_father}comments_threadId={$comment.threadId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}&amp;edit_reply=1#form"><img border='0' width='16' height='16' alt='{tr}Edit{/tr}' src='pics/icons/page_edit.png' /></a>
					{/if}
					{if ($tiki_p_remove_comments eq 'y' && $forum_mode ne 'y') || ($tiki_p_admin_forum eq 'y' and $forum_mode eq 'y')}
					  &nbsp;&nbsp;<a title="{tr}delete{/tr}" class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comment.threadId}&amp;comments_remove=1&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}"><img src='pics/icons/cross.png' border='0' width='16' height='16' alt='{tr}Remove{/tr}' /></a>
					{/if}
					{if ($tiki_p_post_comments == 'y' and $forum_mode ne 'y') or ($tiki_p_forum_post eq 'y' and $forum_mode eq 'y') }
						{if $forum_mode neq 'y'}
							<a class="linkbut" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_reply_threadId={$comment.threadId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_grandParentId={$comment.parentId}&amp;comments_parentId={$comment.threadId}&amp;comments_style={$comments_style}&amp;post_reply=1#form">{tr}reply{/tr}</a>
						{else}
				    	{if $comments_grandParentId}
								<a class="linkbut" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_reply_threadId={$comment.threadId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_grandParentId={$comments_grandParentId}&amp;comments_parentId={$comments_grandParentId}&amp;comments_style={$comments_style}&amp;post_reply=1#form">{tr}reply{/tr}</a>
			    		{else}
						  	<a class="linkbut" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_reply_threadId={$comment.threadId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_grandParentId={$comment.parentId}&amp;comments_parentId={$comment.parentId}&amp;comments_style={$comments_style}&amp;post_reply=1#form">{tr}reply{/tr}</a>
							{/if}
		  	    {/if}
			    {/if} {* if $tiki_p_post_comments == 'y' *}
				</td>
				{/if} {* if $comments_style != 'commentStyle_headers' *}

			</tr>
			{if $feature_contribution eq 'y' and $feature_contribution_display_in_comment eq 'y'}<tr><td align="right" style="font-size:8pt;" colspan={if $comments_style != 'commentStyle_headers'}{if (($tiki_p_vote_comments eq 'y' and $forum_mode ne 'y') or ($tiki_p_forum_vote eq 'y' and $forum_mode eq 'y')) and $comment.userName ne $user}"4"{else}"3"{/if}{else}"2"{/if}>{section name=ix loop=$comment.contributions} {$comment.contributions[ix].name|escape}{/section}</td></tr>{/if}
			</table>
		</td>
  </tr>
  {* /if *} {* $comments_style != 'commentStyle_headers' *}

	{if $comments_style != 'commentStyle_headers'}
  <tr>
  	<td colspan="3" class="even">
			{$comment.parsed}
			<br />
			{if count($comment.attachments) > 0}
				{section name=ix loop=$comment.attachments}
				<a class="link" href="tiki-download_forum_attachment.php?attId={$comment.attachments[ix].attId}">
				<img src="pics/icons/attach.png" border="0" width="16" height= "16" alt='{tr}attachment{/tr}' />
				{$comment.attachments[ix].filename} ({$comment.attachments[ix].filesize|kbsize})</a>
				{if $tiki_p_admin_forum eq 'y'}
					<a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_find={$smarty.request.topics_find}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;comments_offset={$smarty.request.topics_offset}&amp;comments_sort_mode={$smarty.request.topics_sort_mode}&amp;comments_threshold={$smarty.request.topics_threshold}&amp;comments_find={$smarty.request.topics_find}&amp;forumId={$forum_info.forumId}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;remove_attachment={$comment.attachments[ix].attId}"><img src="pics/icons/cross.png" border="0" width="16" height="16" alt='{tr}Remove{/tr}' /></a>
			{/if}
		<br />
	{/section}
  {/if}
		</td>
	</tr>
	{/if}

	<tr>
		<td colspan="3">
		{* /if *} {* if $comments_style != 'commentStyle_headers' *}
	  {if $comment.replies_info.numReplies > 0 && $comment.replies_info.numReplies != ''}
			{foreach from=$comment.replies_info.replies item="comment" name="com"}
<br />
		    <div class="subcomment">
				{include file="comment.tpl"  comment=$comment}
		    </div>
		  {/foreach}
	  {/if} {* if $comment.replies_info.numReplies > 0 && $comment.replies_info.numReplies != '' *}
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
