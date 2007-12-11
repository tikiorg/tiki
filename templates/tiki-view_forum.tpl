{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-view_forum.tpl,v 1.111.2.8 2007-12-11 22:32:18 nkoth Exp $ *}

<h1><a class="pagetitle" href="tiki-view_forum.php?forumId={$forum_info.forumId}">{$forum_info.name}</a></h1>
{if $forum_info.show_description eq 'y'}
<div class="description">{$forum_info.description}</div>
<br />
{/if}

<a class="link" href="tiki-forums.php">{tr}Forums{/tr}</a> -&gt; <a class="link" href="tiki-view_forum.php?forumId={$forumId}">{$forum_info.name}</a>

<div class="navbar">
<table width="97%">
<tr>
<td>
{if $tiki_p_forum_post_topic eq 'y'}
<a class="linkbut" href="#" onclick="flip('forumpost');return false;">{tr}New Topic{/tr}</a>
{/if}
{if $tiki_p_admin_forum eq 'y' or $all_forums|@count > 1 }{* No need for users to go to forum list if they are already looking at the only forum *}
<a class="linkbut" href="tiki-forums.php">{tr}Forum List{/tr}</a> 
{/if}
{if $tiki_p_admin_forum eq 'y'}
<a class="linkbut" href="tiki-admin_forums.php?forumId={$forum_info.forumId}">{tr}Edit Forum{/tr}</a>
{/if}

</td>
<td style="text-align:right;">
{if $prefs.rss_forum eq 'y'}
<a href="tiki-forum_rss.php?forumId={$forumId}"><img src='img/rss.png' border='0' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' /></a>
{/if}
{if $user and $prefs.feature_user_watches eq 'y'}
		{if $user_watching_forum eq 'n'}
			<a href="tiki-view_forum.php?forumId={$forumId}&amp;watch_event=forum_post_topic&amp;watch_object={$forumId}&amp;watch_action=add" title='{tr}Monitor Topics of this Forum{/tr}'><img border='0' alt='{tr}Monitor Topics of this Forum{/tr}' src='pics/icons/eye.png' /></a>
		{else}
			<a href="tiki-view_forum.php?forumId={$forumId}&amp;watch_event=forum_post_topic&amp;watch_object={$forumId}&amp;watch_action=remove" title='{tr}Stop Monitoring Topics of this Forum{/tr}'><img border='0' alt='{tr}Stop Monitoring Topics of this Forum{/tr}' src='pics/icons/no_eye.png' /></a>
		{/if}			
{/if}
{if $user and $prefs.feature_user_watches eq 'y'}
	{if $user_watching_forum_topic_and_thread eq 'n'}
		<a href="tiki-view_forum.php?forumId={$forumId}&amp;watch_event=forum_post_topic_and_thread&amp;watch_object={$forumId}&amp;watch_action=add" title='{tr}Monitor Topics and Threads of this Forum{/tr}'><img border='0' alt='{tr}Monitor Topics and Threads of this Forum{/tr}' src='pics/icons/eye_magnifier.png' /></a>
	{else}
		<a href="tiki-view_forum.php?forumId={$forumId}&amp;watch_event=forum_post_topic_and_thread&amp;watch_object={$forumId}&amp;watch_action=remove" title='{tr}Stop Monitoring Topics and Threads of this Forum{/tr}'><img border='0' alt='{tr}Stop Monitoring Topics and Threads of this Forum{/tr}' src='pics/icons/no_eye.png' /></a>
	{/if}
{/if}

<div class="navbar" align="right" >
	{if $user and $prefs.feature_user_watches eq 'y'}
		{if $category_watched eq 'y'}
			{tr}Watched by categories{/tr}:
			{section name=i loop=$watching_categories}
				<a href="tiki-browse_categories?parentId={$watching_categories[i].categId}">{$watching_categories[i].name}</a>&nbsp;
			{/section}
		{/if}	
	{/if}
</div>

{if $prefs.feature_forum_content_search eq 'y'}
  <form  class="forms" method="get" action="{if $prefs.feature_forum_local_tiki_search eq 'y'}tiki-searchindex.php{else}tiki-searchresults.php{/if}">
    <input name="highlight" size="30" type="text" />
    <input type="hidden" name="where" value="forums" />
    <input type="hidden" name="forumId" value={$forum_info.forumId} />
    <input type="submit" class="wikiaction" name="search" value="{tr}Find{/tr}"/>
  </form>
{/if}

</td>
</tr>
</table>
</div>

{if $unread > 0}
<a class='link' href='messu-mailbox.php'>{tr}You have {$unread} unread private messages{/tr}<br /></a>
{/if}

{if $was_queued eq 'y'}
<div class="rbox">
<div class="rbox-data" name="warning">
{tr}Your message has been queued for approval, the message will be posted after
a moderator approves it.{/tr}
</div>
</div>
{/if}


{if $tiki_p_forum_post_topic eq 'y'}
  {if $comment_preview eq 'y'}
  <br /><br />
  <b>{tr}Preview{/tr}</b>
  <div class="commentscomment">
  <div class="commentheader">
  <table >
  <tr>
  <td>
  <div class="commentheader">
  <span class="commentstitle">{$comments_preview_title}</span><br />
  {tr}by{/tr} {$user}
  </div>
  </td>
  <td valign="top" align="right" >
  <div class="commentheader">
  </div>
  </td>
  </tr>
  </table>
  </div>
  <div class="commenttext">
  {$comments_preview_data}
  <br />
  </div>
  </div>
  {/if}

  {if $warning eq 'y'}
  <br /><br />
  <div class="simplebox highlight"><br /><b>{tr}You have to enter a title and text{/tr}!</b><br /><br />
  </div>
  <br />
  {/if}
{if $contribution_needed eq 'y'}
  <br /><br />
  <div class="simplebox highlight"><br /><b>{tr}A contribution is mandatory{/tr}</b><br /><br />
  </div>
  <br />
  {/if}
{if $duplic eq 'y'}
<div class="simplebox highlight"><br /><b>{tr}Another post with the same title and content already exists.{/tr} {tr}Please change your title or content then click Post.{/tr}</b><br /><br /></div>
{/if}

<div id="forumpost" style="display:{if $comments_threadId > 0 or $openpost eq 'y' or $warning eq 'y' or $comment_title neq '' or $smarty.request.comments_previewComment neq ''}block{else}none{/if};">
  {if $comments_threadId > 0}
    {tr}Editing{/tr}: {$comment_title|escape} (<a class="forumbutlink" href="tiki-view_forum.php?openpost=1&amp;forumId={$forum_info.forumId}&amp;comments_threadId=0&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}">{tr}Post New{/tr}</a>)
    {/if}
    <form method="post" enctype="multipart/form-data" action="tiki-view_forum.php" id="editpageform">
    <input type="hidden" name="comments_offset" value="{$comments_offset|escape}" />
    <input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}" />
    <input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}" />
    <input type="hidden" name="thread_sort_mode" value="{$thread_sort_mode|escape}" />
    <input type="hidden" name="forumId" value="{$forumId|escape}" />
    <table class="normal">
    <tr class="formcolor">
      <td>{tr}Title{/tr}</td>
      <td><input type="text" name="comments_title" value="{$comment_title|escape}" size="80" /></td>
    </tr>      
    {if $forum_info.forum_use_password ne 'n'}
    <tr class="formcolor">
    	<td>{tr}Password{/tr}</td>
    	<td>
    		<input type="password" name="password" />
    	</td>
    </tr>
    {/if}
	{if $tiki_p_admin_forum eq 'y' or $forum_info.topic_smileys eq 'y'}
    <tr class="formcolor">
      <td>{tr}Type{/tr}</td>
      <td>
      {if $tiki_p_admin_forum eq 'y'}
      <select name="comment_topictype">
      <option value="n" {if $comment_topictype eq 'n'}selected="selected"{/if}>{tr}normal{/tr}</option>
      <option value="a" {if $comment_topictype eq 'a'}selected="selected"{/if}>{tr}announce{/tr}</option>
      <option value="h" {if $comment_topictype eq 'h'}selected="selected"{/if}>{tr}hot{/tr}</option>
      <option value="s" {if $comment_topictype eq 's'}selected="selected"{/if}>{tr}sticky{/tr}</option>
      <option value="l" {if $comment_topictype eq 'l'}selected="selected"{/if}>{tr}locked{/tr}</option>
      </select>
      {/if}
      {if $forum_info.topic_smileys eq 'y'}
      <select name="comment_topicsmiley">
      <option value="" {if $comment_topicsmiley eq ''}selected="selected"{/if}>{tr}no feeling{/tr}</option>
      <option value="icon_frown.gif" {if $comment_topicsmiley eq 'icon_frown.gif'}selected="selected"{/if}>{tr}frown{/tr}</option>
      <option value="icon_exclaim.gif" {if $comment_topicsmiley eq 'icon_exclaim.gif'}selected="selected"{/if}>{tr}exclaim{/tr}</option>
      <option value="icon_idea.gif" {if $comment_topicsmiley eq 'icon_idea.gif'}selected="selected"{/if}>{tr}idea{/tr}</option>
      <option value="icon_mad.gif" {if $comment_topicsmiley eq 'icon_mad.gif'}selected="selected"{/if}>{tr}mad{/tr}</option>      
      <option value="icon_neutral.gif" {if $comment_topicsmiley eq 'icon_neutral.gif'}selected="selected"{/if}>{tr}neutral{/tr}</option>      
      <option value="icon_question.gif" {if $comment_topicsmiley eq 'icon_question.gif'}selected="selected"{/if}>{tr}question{/tr}</option>      
      <option value="icon_sad.gif" {if $comment_topicsmiley eq 'icon_sad.gif'}selected="selected"{/if}>{tr}sad{/tr}</option>      
      <option value="icon_smile.gif" {if $comment_topicsmiley eq 'icon_smile.gif'}selected="selected"{/if}>{tr}happy{/tr}</option>
      <option value="icon_wink.gif" {if $comment_topicsmiley eq 'icon_wink.gif'}selected="selected"{/if}>{tr}wink{/tr}</option>
      </select>
      {/if}
      </td>
    </tr>
	{/if}
    {if $forum_info.topic_summary eq 'y'}
    <tr class="formcolor">
    	<td>{tr}Summary{/tr}</td>
    	<td>
    		<input type="text" size="60" name="comment_topicsummary" value="{$comment_topicsummary|escape}" maxlength="240" />
    	</td>
    </tr>
    {/if}
    {if $prefs.feature_smileys eq 'y'}
     <tr class="formcolor">
	<td>{tr}Smileys{/tr}</td>
	<td>{include file="tiki-smileys.tpl" area_name='editpost'}</td>
     </tr>
    {/if}
    
    <tr class="formcolor">
      <td>{tr}Edit{/tr}
			<br /><br />{include file="textareasize.tpl" area_name='editpost' formId='editpageform'}
			{if $prefs.feature_forum_parse eq 'y' and $prefs.quicktags_over_textarea neq 'y'}
			  {include file=tiki-edit_help_tool.tpl area_name="editpost"}
			{/if}
			</td>
      <td>
        {if $prefs.feature_forum_parse eq 'y' and $prefs.quicktags_over_textarea eq 'y'}
          {include file=tiki-edit_help_tool.tpl area_name='editpost'}
        {/if}
        <textarea id='editpost' name="comments_data" rows="{$rows}" cols="{$cols}">{$comment_data|escape}</textarea><input type="hidden" name="rows" value="{$rows}"/>
        <input type="hidden" name="cols" value="{$cols}"/>
      </td>
    </tr>
    {if ($forum_info.att eq 'att_all') or ($forum_info.att eq 'att_admin' and $tiki_p_admin_forum eq 'y') or ($forum_info.att eq 'att_perm' and $tiki_p_forum_attach eq 'y')}
    <tr class="formcolor">
	  <td>{tr}Attach file{/tr}</td>
	  <td>
	  	<input type="hidden" name="MAX_FILE_SIZE" value="{$forum_info.att_max_size|escape}" /><input name="userfile1" type="file" />
	  </td>   
    </tr>
    {/if}
	{if $prefs.feature_contribution eq 'y'}
	{include file="contribution.tpl"}
	{/if}

	{if $prefs.feature_antibot eq 'y'}
		{include file="antibot.tpl"}
	{/if}
   
   {if $prefs.feature_freetags eq 'y' and $tiki_p_freetags_tag eq 'y'}
     {include file=freetag.tpl}
   {/if}

    <tr class="formcolor">
      <td>{tr}Post{/tr}</td>
      <td>
      <input type="submit" name="comments_previewComment" value="{tr}Preview{/tr}"/>
      <input type="submit" name="comments_postComment" value="{tr}Post{/tr}"/>
      <input type="button" name="comments_postComment" value="{tr}Cancel{/tr}" onclick="hide('forumpost');"/>
      </td>
    </tr>
    </table>
    </form>
<br />    
  <table class="normal" id="commentshelp">
  <tr><td class="even">
  <b>{tr}Editing posts{/tr}:</b>
  <br />
  <br />
  {tr}Use{/tr} [http://www.foo.com] {tr}or{/tr} [http://www.foo.com|description] {tr}for links{/tr}<br />
  {tr}HTML tags are not allowed inside posts{/tr}<br />
  </td>
  </tr>
  </table>
   
</div>

<br />
{/if}

<form method="post" action="tiki-view_forum.php">
    <input type="hidden" name="comments_offset" value="{$comments_offset|escape}" />
    <input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}" />
    <input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}" />
    <input type="hidden" name="thread_sort_mode" value="{$thread_sort_mode|escape}" />
    <input type="hidden" name="forumId" value="{$forumId|escape}" />
<table class="normal">
{if $tiki_p_admin_forum eq 'y'}
<tr>
	<td class="heading" colspan='18'>{tr}Moderator Actions{/tr}</td>
</tr>
<tr>	
	<td class="odd" colspan="3">
	<input type="image" name="movesel" src="img/icons/topic_move.gif" border='0' alt='{tr}Move{/tr}' title='{tr}Move Selected Topics{/tr}' />
	<input type="image" name="unlocksel" src="img/icons/topic_unlock.gif" border='0' alt='{tr}Unlock{/tr}' title='{tr}Unlock Selected Topics{/tr}' />
	<input type="image" name="locksel" src="img/icons/topic_lock.gif" border='0' alt='{tr}Lock{/tr}' title='{tr}Lock Selected Topics{/tr}' />
	<input type="image" name="delsel" src="img/icons/topic_delete.gif" border='0' alt='{tr}Delete{/tr}' title='{tr}Delete Selected Topics{/tr}' />
	<input type="image" name="splitsel" src="img/icons/topic_split.gif" border='0' alt='{tr}Merge{/tr}' title='{tr}Merge Selected Topics{/tr}' />
	</td>
	<td style="text-align:right;" class="odd" colspan="10">
	{if $reported > 0}
	<a class="link" href="tiki-forums_reported.php?forumId={$forumId}">{tr}Reported Messages:{/tr}{$reported}</a><br />
	{/if}
	<a class="link" href="tiki-forum_queue.php?forumId={$forumId}">{tr}Queued Messages:{/tr}{$queued}</a>
	</td>
</tr>
{if $smarty.request.movesel_x} 
<tr>
	<td class="odd" colspan="18">
	{tr}Move to{/tr}:
	<select name="moveto">
		{section name=ix loop=$all_forums}
			{if $all_forums[ix].forumId ne $forumId}
				<option value="{$all_forums[ix].forumId|escape}">{$all_forums[ix].name}</option>
			{/if}
		{/section}
	</select>
	<input type='submit' name='movesel' value='{tr}Move{/tr}' />
	
	</td>
</tr>
{/if}
{if $smarty.request.splitsel_x} 
<tr>
	<td class="odd" colspan="18">
	{tr}Merge into topic{/tr}:
	<select name="mergetopic">
		{section name=ix loop=$comments_coms}
			{if in_array($comments_coms[ix].threadId,$smarty.request.forumtopic)}
				<option value="{$comments_coms[ix].threadId|escape}">{$comments_coms[ix].title}</option>
			{/if}
		{/section}
	</select>
	<input type="submit" name="mergesel" value="{tr}Merge{/tr}" />
	</td>
</tr>
{/if}

<tr id='moveop' style="display:none;">
	<td class="odd" colspan="18">
		{tr}Move{/tr}
	</td>
</tr>
{/if}
<tr>
  {if $tiki_p_admin_forum eq 'y'}
  <td class="heading">&nbsp;</td>
  {/if}
  <td class="heading"><a class="tableheading" href="tiki-view_forum.php?comments_threshold={$comments_threshold}&amp;forumId={$forum_info.forumId}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={if $thread_sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}Type{/tr}</a></td>
  {if $forum_info.topic_smileys eq 'y'}
  <td class="heading"><a class="tableheading" href="tiki-view_forum.php?comments_threshold={$comments_threshold}&amp;forumId={$forum_info.forumId}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={if $thread_sort_mode eq 'smiley_desc'}smiley_asc{else}smiley_desc{/if}">{tr}Emot{/tr}</a></td>
  {/if}
  <td class="heading"><a class="tableheading" href="tiki-view_forum.php?comments_threshold={$comments_threshold}&amp;forumId={$forum_info.forumId}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={if $thread_sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></td>
  {if $forum_info.topics_list_replies eq 'y'}
  	<td class="heading"><a class="tableheading" href="tiki-view_forum.php?comments_threshold={$comments_threshold}&amp;forumId={$forum_info.forumId}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={if $thread_sort_mode eq 'replies_desc'}replies_asc{else}replies_desc{/if}">{tr}Replies{/tr}</a></td>
  {/if}
  {if $forum_info.topics_list_reads eq 'y'}
  	<td class="heading"><a class="tableheading" href="tiki-view_forum.php?comments_threshold={$comments_threshold}&amp;forumId={$forum_info.forumId}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={if $thread_sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Reads{/tr}</a></td>
  {/if}
  {if $forum_info.topics_list_pts eq 'y'}
  	<td class="heading"><a class="tableheading" href="tiki-view_forum.php?comments_threshold={$comments_threshold}&amp;forumId={$forum_info.forumId}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={if $thread_sort_mode eq 'average_desc'}average_asc{else}average_desc{/if}">{tr}pts{/tr}</a></td>
  {/if}
  {if $forum_info.topics_list_lastpost eq 'y'}
  	<td class="heading"><a class="tableheading" href="tiki-view_forum.php?comments_threshold={$comments_threshold}&amp;forumId={$forum_info.forumId}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={if $thread_sort_mode eq 'lastPost_desc'}lastPost_asc{else}lastPost_desc{/if}">{tr}Last Post{/tr}</a></td>
  {/if}
  {if $forum_info.topics_list_author eq 'y'}
  	<td class="heading"><a class="tableheading" href="tiki-view_forum.php?comments_threshold={$comments_threshold}&amp;forumId={$forum_info.forumId}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={if $thread_sort_mode eq 'userName_desc'}userName_asc{else}userName_desc{/if}" title="sort by">{tr}Author{/tr}</a></td>
  {/if}
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$comments_coms}
{if $userinfo && $comments_coms[ix].lastPost > $userinfo.lastLogin}
{assign var="newtopic" value="_new"}
{else}
{assign var="newtopic" value=""}
{/if}
<tr>
  {if $tiki_p_admin_forum eq 'y'}
  <td class="{cycle advance=false}">
  	
	<input type="checkbox" name="forumtopic[]" value="{$comments_coms[ix].threadId|escape}"  {if $smarty.request.forumtopic and in_array($comments_coms[ix].threadId,$smarty.request.forumtopic)}checked="checked"{/if} />
  </td>
  {/if}	
  <td style="text-align:center;" class="{cycle advance=false}">
  {if $comments_coms[ix].type eq 'n'}<img src="img/silk/page{$newtopic}.png" alt="{tr}Normal{/tr}" title="{tr}Normal{/tr}{if $newtopic}-{tr}New{/tr}{/if}" />{/if}
  {if $comments_coms[ix].type eq 'a'}<img src="img/silk/announce{$newtopic}.png" alt="{tr}Announce{/tr}" title="{tr}Announce{/tr}{if $newtopic}-{tr}New{/tr}{/if}" />{/if}
  {if $comments_coms[ix].type eq 'h'}<img src="img/silk/hot{$newtopic}.png" alt="{tr}Hot{/tr}" title="{tr}Hot{/tr}{if $newtopic}-{tr}New{/tr}{/if}" />{/if}
  {if $comments_coms[ix].type eq 's'}<img src="img/silk/sticky{$newtopic}.png" alt="{tr}Sticky{/tr}" title="{tr}Sticky{/tr}{if $newtopic}-{tr}New{/tr}{/if}" />{/if}
  {if $comments_coms[ix].type eq 'l'}<img src="img/silk/locked{$newtopic}.png" alt="{tr}Locked{/tr}" title="{tr}Locked{/tr}{if $newtopic}-{tr}New{/tr}{/if}" />{/if}
  </td>
  {if $forum_info.topic_smileys eq 'y'}
  <td style="text-align:center;" class="{cycle advance=false}">
  	{if strlen($comments_coms[ix].smiley) > 0}
  		<img src='img/smiles/{$comments_coms[ix].smiley}' alt=''/>
  	{else}
  	&nbsp;{$comments_coms[ix].smiley}
  	{/if}
  </td>
  {/if}  
  
  <td class="{cycle advance=false}">
  <table width="100%"><tr><td>
  <a {if $comments_coms[ix].is_marked}class="forumnameread"{else}class="forumname"{/if}  href="tiki-view_forum_thread.php?comments_parentId={$comments_coms[ix].threadId}{if $comments_threshold}&amp;topics_threshold={$comments_threshold}{/if}{if $comments_offset or $smarty.section.ix.index}&amp;topics_offset={math equation="x + y" x=$comments_offset y=$smarty.section.ix.index}{/if}{if $thread_sort_mode ne 'commentDate_desc'}&amp;topics_sort_mode={$thread_sort_mode}{/if}{if $topics_find}&amp;topics_find={$comments_find}{/if}&amp;forumId={$forum_info.forumId}">{$comments_coms[ix].title}</a>
  {if $forum_info.topic_summary eq 'y'}
  <br /><small>{$comments_coms[ix].summary|truncate:240:"...":true}</small>     
  {/if}
  </td>
  
  <td style="text-align:right;" nowrap="nowrap">
  {if count($comments_coms[ix].attachments) or $tiki_p_admin_forum eq 'y'}
  {if count($comments_coms[ix].attachments)}
  	<img src='img/icons/attachment.gif' alt='attachments' />
  {/if}
  {else}
  	&nbsp;
  {/if}

  {if $tiki_p_admin_forum eq 'y' or ($comments_coms[ix].userName == $user && $tiki_p_forum_post eq 'y') }
  <a href="tiki-view_forum.php?openpost=1&amp;comments_threadId={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}"
     class="admlink">{html_image file='pics/icons/page_edit.png' border='0'  width="16" height="16" alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}'}</a>
   {/if}
  {if $tiki_p_admin_forum eq 'y' }
   <a href="tiki-view_forum.php?comments_remove=1&amp;comments_threadId={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}"
     class="admlink"><img src="pics/icons/cross.png" border="0" width="16" height="16"  alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' /></a>
  {/if}

  </td>   
  
  </tr></table>
  </td>
  {if $forum_info.topics_list_replies eq 'y'}
  	<td style="text-align:right;" class="{cycle advance=false}">{$comments_coms[ix].replies}</td>
  {/if}
  {if $forum_info.topics_list_reads eq 'y'}
  	<td style="text-align:right;" class="{cycle advance=false}">{$comments_coms[ix].hits}</td>
  {/if}
  {if $forum_info.topics_list_pts eq 'y'}
  	<td style="text-align:right;" class="{cycle advance=false}">{$comments_coms[ix].average|string_format:"%.2f"}</td>
  {/if}
  {if $forum_info.topics_list_lastpost eq 'y'}
  	  <td class="{cycle advance=false}">{$comments_coms[ix].lastPost|tiki_short_datetime} {* date_format:"%b %d [%H:%M]" *}
	  {if $comments_coms[ix].replies}
	  <br />
	  <small><i>{$comments_coms[ix].lastPostData.title}</i> {tr}by{/tr} {$comments_coms[ix].lastPostData.userName}</small>     
	  {/if}
	  </td>
  {/if}
  {if $forum_info.topics_list_author eq 'y'}
  	<td class="{cycle}">{$comments_coms[ix].userName|userlink}</td>
  {/if}
</tr>
{sectionelse}
<tr>
	<td class="odd" colspan="8">{tr}No topics yet{/tr}</td>
</tr>
{/section}
</table>
</form>
  <div class="mini">

{if $comments_cant_pages >1}

  {if $comments_prev_offset >= 0}
  [<a class="forumprevnext" href="tiki-view_forum.php?forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_prev_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}">{tr}Prev{/tr}</a>]&nbsp;
  {/if}
  {tr}Page{/tr}: {$comments_actual_page}/{$comments_cant_pages}
  {if $comments_next_offset >= 0}
  &nbsp;[<a class="forumprevnext" href="tiki-view_forum.php?forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_next_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}">{tr}Next{/tr}</a>]
  {/if}
  {if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$comments_cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$comments_per_page}
<a class="prevnext" href="tiki-view_forum.php?forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$selector_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

{/if}

  </div>
  <br />

{if $forum_info.forum_last_n > 0}
	{* Last n titles *}
	{cycle values="odd,even" print=false}
	<table class="normal">
	<tr>
	 	<td class="heading">{tr}Last{/tr} {$forum_info.forum_last_n} {tr}posts in this forum{/tr}</td>
	</tr>
 	{section name=ix loop=$last_comments}
	 	<tr>
	 		<td class="{cycle}">
	 		{if $last_comments[ix].parentId eq 0}
	 		 	{assign var="idt" value=$last_comments[ix].threadId}
	 		{else}
	 			{assign var="idt" value=$last_comments[ix].parentId}
	 		{/if}
	 		<a class="forumname" href="tiki-view_forum_thread.php?comments_parentId={$idt}&amp;topics_threshold={$comments_threshold}&amp;topics_offset={math equation="x + y" x=$comments_offset y=$smarty.section.ix.index}&amp;topics_sort_mode={$thread_sort_mode}&amp;topics_find={$comments_find}&amp;forumId={$forum_info.forumId}">{$last_comments[ix].title}</a>
	 		</td>
	 	</tr>
 	{/section}
	</table>
	<br />
{/if}

<table >  
<tr>
<td style="text-align:left;">

<form id='time_control' method="post" action="tiki-view_forum.php">
    <input type="hidden" name="comments_offset" value="{$comments_offset|escape}" />
    <input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}" />
    <input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}" />
    <input type="hidden" name="thread_sort_mode" value="{$thread_sort_mode|escape}" />
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
{if $prefs.feature_forum_quickjump eq 'y'}
<form id='quick' method="post" action="tiki-view_forum.php">
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
