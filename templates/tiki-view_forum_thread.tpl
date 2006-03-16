{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-view_forum_thread.tpl,v 1.72 2006-03-16 13:43:12 sylvieg Exp $ *}

<h1><a href="tiki-view_forum.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;forumId={$forum_info.forumId}" class="pagetitle">{tr}Forum{/tr}: {$forum_info.name}</a></h1>

{if $unread > 0}
	<a class='link' href='messu-mailbox.php'>{tr}You have{/tr} {$unread} {tr} unread private messages{/tr}<br /><br /></a>
{/if}

{if $was_queued eq 'y'}
<div class="wikitext">
<small>{tr}Your message has been queued for approval, the message will be posted after
a moderator approves it.{/tr}</small>
</div>
{/if}
{if $tiki_p_admin_forum eq "y"}
<a class="linkbut" title="{tr}Edit Forum{/tr}" href="tiki-admin_forums.php?forumId={$forumId}">{tr}Edit Forum{/tr}</a><br />
{/if}
<a class="link" href="tiki-forums.php">{tr}Forums{/tr}</a>-&gt;<a class="link" href="tiki-view_forum.php?forumId={$forumId}">{$forum_info.name}</a>-><a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;forumId={$forumId}&amp;comments_parentId={$smarty.request.comments_parentId}">{$thread_info.title}</a>
<div align="right">
{if ($prev_topic and $prev_topic ne $comments_parentId) or $next_topic}[{if $prev_topic and $prev_topic ne $comments_parentId}<a href="tiki-view_forum_thread.php?forumId={$forumId}&amp;comments_parentId={$prev_topic}&amp;topics_offset={$topics_prev_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}{$comments_maxComments_param}{$comments_style_param}{$comments_sort_mode_param}{$comments_threshold_param}" class="link">{tr}prev topic{/tr}</a>{if $next_topic} | {/if}{/if}
{if $next_topic}<a href="tiki-view_forum_thread.php?forumId={$forumId}&amp;comments_parentId={$next_topic}&amp;topics_offset={$topics_next_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}{$comments_maxComments_param}{$comments_style_param}{$comments_sort_mode_param}{$comments_threshold_param}" class="link">{tr}next topic{/tr}</a>{/if}]{/if}
 </div>
<br /><br />
{if $openpost eq 'y'}
{assign var="postclass" value="forumpostopen"}
{else}
{assign var="postclass" value="forumpost"}
{/if}

<table class="normal">
<tr>
  <td rowspan="2" class="odd forumuser">
	  <div align="center">
		  {if $forum_info.ui_avatar eq 'y' and $thread_info.userName|avatarize}
		  {$thread_info.userName|avatarize}<br />
		  {/if}
		  {$thread_info.userName|userlink}
		  {if $forum_info.ui_flag eq 'y' and $thread_info.userName|countryflag}
		  <br />{$thread_info.userName|countryflag}
		  {/if}
		  {if $forum_info.ui_posts eq 'y' and $thread_info.user_posts}
		  <br /><small>{tr}posts:{/tr}{$thread_info.user_posts}</small>
		  {/if}
		  {if $forum_info.ui_level eq 'y' and $thread_info.user_level}
		  <br /><img src="img/icons/{$thread_info.user_level}stars.gif" alt='{$thread_info.user_level} {tr}stars{/tr}' title='{tr}user level{/tr}' />
		  {/if}
	  </div>
	</td>
  <td class="odd">
 		<b>{$thread_info.title}</b>
 	</td>
 	<td style="text-align:right;" class="odd">
	  {if $tiki_p_admin_forum eq 'y' or $thread_info.userName == $user}
	  <a href="tiki-view_forum.php?comments_offset={$smarty.request.topics_offset}{$comments_sort_mode_param}&amp;comments_threshold={$smarty.request.topics_threshold}{$comments_find_param}&amp;comments_threadId={$thread_info.threadId}&amp;openpost=1&amp;forumId={$forum_info.forumId}{$comments_maxComments_param}"
	     class="admlink"><img src='img/icons/edit.gif' border='0' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' /></a>
	  {/if}
	  {if $tiki_p_admin_forum eq 'y'}
	  <a href="tiki-view_forum.php?comments_offset={$smarty.request.topics_offset}{$comments_sort_mode_param}&amp;comments_threshold={$smarty.request.topics_threshold}{$comments_find_param}&amp;comments_remove=1&amp;comments_threadId={$thread_info.threadId}&amp;forumId={$forum_info.forumId}{$comments_maxComments_param}"
	     class="admlink">{html_image file='img/icons2/delete.gif' border='0' alt='{tr}remove{/tr}' title='{tr}remove{/tr}'}</a>
	  {/if}     
	  
	  {if $user and $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
		<a title="{tr}Save to notepad{/tr}" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forumId}{$comments_threshold_param}&amp;comments_offset={$comments_offset}{$comments_sort_mode_param}{$comments_maxComments_param}&amp;savenotepad={$thread_info.threadId}">{html_image file='img/icons/ico_save.gif' border='0' alt='{tr}save{/tr}'}</a>
	  {/if}
	
	  {if $user and $feature_user_watches eq 'y'}
		{if $user_watching_topic eq 'n'}
			<a href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;forumId={$forumId}&amp;comments_parentId={$comments_parentId}&amp;watch_event=forum_post_thread&amp;watch_object={$comments_parentId}&amp;watch_action=add"><img border='0' alt='{tr}monitor this forum{/tr}' title='{tr}monitor this topic{/tr}' src='img/icons/icon_watch.png' /></a>
		{else}
			<a href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;forumId={$forumId}&amp;comments_parentId={$comments_parentId}&amp;watch_event=forum_post_thread&amp;watch_object={$comments_parentId}&amp;watch_action=remove"><img border='0' alt='{tr}stop monitoring this forum{/tr}' title='{tr}stop monitoring this topic{/tr}' src='img/icons/icon_unwatch.png' /></a>
		{/if}
	  {/if}
	  
	  
	{if $tiki_p_forum_post eq 'y'}
		<a class="linkbut" href="#form">{tr}reply{/tr}</a>
	{/if}

  </td>
</tr>
<tr>
	<td class="even" colspan="3">
  <br /><br />
  {$thread_info.parsed}
  <br />
  {if count($thread_info.attachments) > 0}
	{section name=ix loop=$thread_info.attachments}
		<a class="link" href="tiki-download_forum_attachment.php?attId={$thread_info.attachments[ix].attId}">
		<img src="img/icons/attachment.gif" border="0" width="10" height= "13" alt='{tr}attachment{/tr}' />
		{$thread_info.attachments[ix].filename} ({$thread_info.attachments[ix].filesize|kbsize})</a>
		{if $tiki_p_admin_forum eq 'y'}
			<a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_find_param}{$topics_threshold_param}&amp;comments_offset={$smarty.request.topics_offset}{$comments_sort_mode_param}&amp;comments_threshold={$smarty.request.topics_threshold}{$comments_find_param}&amp;forumId={$forum_info.forumId}{$comments_maxComments_param}&amp;comments_parentId={$comments_parentId}&amp;remove_attachment={$thread_info.attachments[ix].attId}"><img src='img/icons2/delete.gif' border='0' alt='{tr}remove{/tr}' title='{tr}remove{/tr}' /></a>					
		{/if}
		<br />
	{/section}
  {/if}
  </td>
</tr>
<tr>
  <td class="odd" style="text-align:center;">
  	&nbsp;
  	{if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}   
	  <a class="admlink" href="messu-compose.php?to={$thread_info.userName}&amp;subject={tr}Re:{/tr}%20{$thread_info.title|escape:"htmlall"}"><img src='img/icons/myinfo.gif' border='0' alt='{tr}private message{/tr}' title='{tr}private message{/tr}' /></a>
    {/if}
		{if $thread_info.userName and $forum_info.ui_email eq 'y' and strlen($thread_info.user_email) > 0}  
		  <a href="mailto:{$thread_info.user_email|escape:'hex'}"><img src='img/icons/email.gif' alt='{tr}send email to user{/tr}' title='{tr}send email to user{/tr}' border='0' /></a>
		{/if}
	    {if $thread_info.userName and $forum_info.ui_online eq 'y'}
	    	{if $thread_info.user_online eq 'y'}
	  			<img src="img/icons/online.gif" border="0" width="16" height="16" alt='{tr}user online{/tr}' title='{tr}user online{/tr}' />
	  		{elseif $thread_info.user_online eq 'n'}
	  			<img src="img/icons/offline.gif" border="0" width="16" height="16" alt='{tr}user offline{/tr}' title='{tr}user offline{/tr}' />
	  		{/if}
	  	{/if}
  </td>
  <td class="odd" colspan="2">
	  <table class="commentinfo">
	  <tr>
  	  <td style="font-size:8pt;"><b>{tr}on{/tr}</b>: {$thread_info.commentDate|tiki_short_datetime}</td>
	    {if $forum_info.vote_threads eq 'y'}
	    <td style="font-size:8pt;"><b>{tr}score{/tr}</b>: {$thread_info.points}</td>	    
	    {if $tiki_p_admin_forum eq 'y' or $tiki_p_forum_vote eq 'y'}<td style="font-size:8pt;">
			  <b>{tr}Vote{/tr}</b>: 
			  <a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}{$comments_threshold_param}&amp;comments_threadId={$thread_info.threadId}&amp;comments_vote=1&amp;comments_offset={$comments_offset}{$comments_sort_mode_param}{$comments_maxComments_param}&amp;comments_parentId={$comments_parentId}">1</a>
			  <a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}{$comments_threshold_param}&amp;comments_threadId={$thread_info.threadId}&amp;comments_vote=2&amp;comments_offset={$comments_offset}{$comments_sort_mode_param}{$comments_maxComments_param}&amp;comments_parentId={$comments_parentId}">2</a>
			  <a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}{$comments_threshold_param}&amp;comments_threadId={$thread_info.threadId}&amp;comments_vote=3&amp;comments_offset={$comments_offset}{$comments_sort_mode_param}{$comments_maxComments_param}&amp;comments_parentId={$comments_parentId}">3</a>
			  <a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}{$comments_threshold_param}&amp;comments_threadId={$thread_info.threadId}&amp;comments_vote=4&amp;comments_offset={$comments_offset}{$comments_sort_mode_param}{$comments_maxComments_param}&amp;comments_parentId={$comments_parentId}">4</a>
			  <a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}{$comments_threshold_param}&amp;comments_threadId={$thread_info.threadId}&amp;comments_vote=5&amp;comments_offset={$comments_offset}{$comments_sort_mode_param}{$comments_maxComments_param}&amp;comments_parentId={$comments_parentId}">5</a>
			  
  	  </td>
  		{/if}
  		{/if}
  		<td style="font-size:8pt;">
  		{tr}reads{/tr}: {$thread_info.hits}		
  		</td>		
  	</tr>
	{if $feature_contribution eq 'y' and $feature_contribution_display_in_comment eq 'y'}<tr><td colspan={if $forum_info.vote_threads eq 'y'}{if $tiki_p_admin_forum eq 'y' or $tiki_p_forum_vote eq 'y'}"4"{else}"3"{/if}{else}"2"{/if} align="right" style="font-size:8pt;">{section name=ix loop=$thread_info.contributions} {$thread_info.contributions[ix].name|escape}{/section}</td></tr>{/if}
  </table>
  </td>
</tr>
</table>
<br />

<table class="normal" >
{include file="comments.tpl"}

{if $comments_threshold ne 0}<small>{$comments_below} {tr}Comments below your current threshold{/tr}</small>{/if}

<table >
<tr>
<td style="text-align:left;">
<form id='time_control' method="get" action="tiki-view_forum_thread.php">
    <input type="hidden" name="comments_offset" value="{$comments_offset|escape}" />
    <input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}" />
    <input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}" />
    <input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}" />
    <input type="hidden" name="comments_sort_mode" value="{$comments_sort_mode|escape}" />
    <input type="hidden" name="topics_offset" value="{$smarty.request.topics_offset|escape}" />
    <input type="hidden" name="topics_find" value="{$smarty.request.topics_find|escape}" />
    <input type="hidden" name="topics_sort_mode" value="{$smarty.request.topics_sort_mode|escape}" />    
    <input type="hidden" name="topics_threshold" value="{$smarty.request.topics_threshold|escape}" />    
    <input type="hidden" name="forumId" value="{$forumId|escape}" />
    <small>{tr}Show posts{/tr}:</small>
    <select name="time_control" onchange="javascript:document.getElementById('time_control').submit();">
    	<option value="" {if $smarty.request.time_control eq ''}selected="selected"{/if}>{tr}All posts{/tr}</option>
    	<option value="3600" {if $smarty.request.time_control eq 3600}selected="selected"{/if}>{tr}Last hour{/tr}</option>
    	<option value="86400" {if $smarty.request.time_control eq 86400}selected="selected"{/if}>{tr}Last 24 hours{/tr}</option>
    	<option value="172800" {if $smarty.request.time_control eq 172800}selected="selected"{/if}>{tr}Last 48 hours{/tr}</option>
    </select>
</form>
</td>
<td style="text-align:right;">
{if $feature_forum_quickjump eq 'y'}
<form id='quick' method="get" action="tiki-view_forum.php">
<small>{tr}Jump to forum{/tr}:</small>
<select name="forumId" onchange="javascript:document.getElementById('quick').submit();">
{section name=ix loop=$all_forums}
<option value="{$all_forums[ix].forumId|escape}" {if $all_forums[ix].forumId eq $forumId}selected="selected"{/if}>{$all_forums[ix].name}</option>
{/section}
</select>
</form>
{else}
&nbsp;
{/if}
</td>
</tr>
</table>
