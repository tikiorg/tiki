<a class="pagetitle" href="tiki-view_forum.php?topics_offset={$smary.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;forumId={$forum_info.forumId}" class="forumspagetitle">{tr}Forum{/tr}: {$forum_info.name}</a>
<br/><br/>
{if $was_queued eq 'y'}
<div class="wikitext">
<small>{tr}Your message has been queued for approval, the message will be posted after
a moderator approves it.{/tr}</small>
</div>
{/if}
<a class="link" href="tiki-forums.php">{tr}Tiki forums{/tr}</a>-><a class="link" href="tiki-view_forum.php?forumId={$forumId}">{$forum_info.name}</a>-><a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&topics_find={$smarty.request.topics_find}&amp;forumId={$forumId}&amp;comments_parentId={$smarty.request.comments_parentId}">{$thread_info.title}</a>
<br/><br/>
{if $openpost eq 'y'}
{assign var="postclass" value="forumpostopen"}
{else}
{assign var="postclass" value="forumpost"}
{/if}
<div class="viewthread">
<table class="viewthread">
<tr>
  <td class="viewthreadl" width="15%">
  <div align="center">
  {if $forum_info.ui_avatar eq 'y'}
  {$thread_info.userName|avatarize}<br/>
  {/if}
  {$thread_info.userName|userlink}
  {if $forum_info.ui_flag eq 'y'}
  <br/>{$thread_info.userName|countryflag}
  {/if}
  {if $thread_info.userName and $forum_info.ui_posts eq 'y'}
  <br/><small>posts:{$thread_info.user_posts}</small>
  {/if}
  {if $thread_info.userName and $forum_info.ui_level eq 'y'}
  <br/><img src="img/icons/{$thread_info.user_level}stars.gif" alt='{$thread_info.user_level} {tr}stars{/tr}' title='{tr}user level{/tr}' />
  {/if}
  {if $thread_info.userName and $forum_info.ui_online eq 'y' and $thread_info.user_online eq 'y'}
  <br/><small>now online</small>
  {/if}
  </div>
  </td>
  <td class="viewthreadr" width="85%">
  <table width="100%">
  <tr>
  	<td>
  		<b>{$thread_info.title}</b>
  	</td>
  	<td style="text-align:right;">
	  
	  {if $tiki_p_admin_forum eq 'y'}
	  <a href="tiki-view_forum.php?comments_offset={$smarty.request.topics_offset}&amp;comments_sort_mode={$smarty.request.topics_sort_mode}&amp;comments_threshold={$smarty.request.topics_threshold}&amp;comments_find={$smarty.request.topics_find}&amp;comments_threadId={$thread_info.threadId}&amp;openpost=1&amp;forumId={$forum_info.forumId}&amp;comments_maxComments={$comments_maxComments}"
	     class="admlink"><img src='img/icons/edit.gif' border='0' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' /></a>
	  <a href="tiki-view_forum.php?comments_offset={$smarty.request.topics_offset}&amp;comments_sort_mode={$smarty.request.topics_sort_mode}&amp;comments_threshold={$smarty.request.topics_threshold}&amp;comments_find={$smarty.request.topics_find}&amp;comments_remove=1&amp;comments_threadId={$thread_info.threadId}&amp;forumId={$forum_info.forumId}&amp;comments_maxComments={$comments_maxComments}"
	     class="admlink"><img src='img/icons/trash.gif' border='0' alt='{tr}remove{/tr}' title='{tr}remove{/tr}' /></a>
	  {/if}     
	  
	  {if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}   
	  <a class="admlink" href="messu-compose.php?to={$thread_info.userName}&amp;subject=Re:{$thread_info.title}"><img src='img/icons/myinfo.gif' border='0' alt='{tr}private message{/tr}' title='{tr}private message{/tr}' /></a>
	  {/if}
	  {if $user and $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
		<a title="{tr}Save to notepad{/tr}" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;savenotepad={$thread_info.threadId}"><img border="0" src="img/icons/ico_save.gif" alt="{tr}save{/tr}" /></a>
	  {/if}

	  {if $thread_info.userName and $forum_info.ui_email eq 'y' and strlen($thread_info.user_email) > 0}  
		  <a href="mailto:{$thread_info.user_email|escape:'hex'}"><img src='img/icons/email.gif' alt='{tr}send email to user{/tr}' title='{tr}send email to user{/tr}' border='0' /></a>
	  {/if}
	  {if $user and $feature_user_watches eq 'y'}
		{if $user_watching_topic eq 'n'}
			<a href="tiki-view_forum_thread.php?topics_offset={$smary.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;forumId={$forumId}&amp;comments_parentId={$comments_parentId}&amp;watch_event=forum_post_thread&amp;watch_object={$comments_parentId}&amp;watch_action=add"><img border='0' alt='{tr}monitor this forum{/tr}' title='{tr}monitor this topic{/tr}' src='img/icons/icon_watch.png' /></a>
		{else}
			<a href="tiki-view_forum_thread.php?topics_offset={$smary.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;forumId={$forumId}&amp;comments_parentId={$comments_parentId}&amp;watch_event=forum_post_thread&amp;watch_object={$comments_parentId}&amp;watch_action=remove"><img border='0' alt='{tr}stop monitoring this forum{/tr}' title='{tr}stop monitoring this topic{/tr}' src='img/icons/icon_unwatch.png' /></a>
		{/if}
	  {/if}


  	</td>
  </tr>
  </table>
  <br/><br/>
  {$thread_info.parsed}
  <br/><br/>
  <table width="100%" border="1" style="border: 1px solid black;">
  <tr>
    <td style="font-size:8pt;">{tr}on{/tr}</b>: {$thread_info.commentDate|tiki_short_datetime}</td>
    {if $forum_info.vote_threads eq 'y'}
    <td style="font-size:8pt;">{tr}score{/tr}</b>: {$thread_info.points}</td>
    {if $tiki_p_admin_forum eq 'y' or $tiki_p_forum_vote eq 'y'}<td style="font-size:8pt;">
	  <b>{tr}Vote{/tr}</b>: 
	  <a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$thread_info.threadId}&amp;comments_vote=1&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">1</a>
	  <a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$thread_info.threadId}&amp;comments_vote=2&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">2</a>
	  <a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$thread_info.threadId}&amp;comments_vote=3&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">3</a>
	  <a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$thread_info.threadId}&amp;comments_vote=4&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">4</a>
	  <a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$thread_info.threadId}&amp;comments_vote=5&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">5</a>
  	  </td>
  	{/if}
  	{/if}
  <td style="font-size:8pt;">
  {tr}reads{/tr}: {$thread_info.hits}
  </td>
  </tr>
  </table>
  </td>
</tr>
</table>
<br/>
{if $tiki_p_admin_form eq 'y' or $thread_info.type ne 'l'}
{if $tiki_p_admin_forum eq 'y' or $tiki_p_forum_post eq 'y'}
<div>
[<a class="forumbutlink" href="javascript:show('{$postclass}');">{tr}Show Post Form{/tr}</a> |
 <a class="forumbutlink" href="javascript:hide('{$postclass}');">{tr}Hide Post Form{/tr}</a>]
[{if $prev_topic}<a href="tiki-view_forum_thread.php?topics_offset={$topics_prev_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;forumId={$forumId}&amp;comments_parentId={$prev_topic}" class="link">{tr}prev topic{/tr}</a>{if $next_topic} | {/if}{/if}
{if $next_topic}<a href="tiki-view_forum_thread.php?topics_offset={$topics_next_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;forumId={$forumId}&amp;comments_parentId={$next_topic}" class="link">{tr}next topic{/tr}</a>{/if}] 
 
 {if $comment_preview eq 'y'}
  <br/><br/>
  <b>{tr}Preview{/tr}</b>
  <div class="commentscomment">
  <div class="commentheader">
  <table width="97%">
  <tr>
  <td>
  <div class="commentheader">
  <span class="commentstitle">{$comments_preview_title}</span><br/>
  {tr}by{/tr} {$user}
  </div>
  </td>
  <td valign="top" align="right" width="20%">
  <div class="commentheader">
  </div>
  </td>
  </tr>
  </table>
  </div>
  <div class="commenttext">
  {$comments_preview_data}
  <br/>
  </div>
  </div>
  {/if}
 
<div id='{$postclass}' class="threadpost">
  <br/>
  {if $comments_threadId > 0}
    {tr}Editing comment{/tr}: {$comments_threadId} (<a class="forumbutlink" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$smarty.request.comments_parentId}&amp;forumId={$forumId}&amp;comments_threadId=0&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">{tr}post new comment{/tr}</a>)
    {/if}
    <form method="post" action="tiki-view_forum_thread.php">
    <input type="hidden" name="comments_offset" value="{$comments_offset}" />
    <input type="hidden" name="comments_threadId" value="{$comments_threadId}" />
    <input type="hidden" name="comments_parentId" value="{$comments_parentId}" />
    <input type="hidden" name="comments_threshold" value="{$comments_threshold}" />
    <input type="hidden" name="comments_sort_mode" value="{$comments_sort_mode}" />
    <input type="hidden" name="topics_offset" value="{$smarty.request.topics_offset}" />
    <input type="hidden" name="topics_find" value="{$smarty.request.topics_find}" />
    <input type="hidden" name="topics_sort_mode" value="{$smarty.request.topics_sort_mode}" />    
    <input type="hidden" name="topics_threshold" value="{$smarty.request.topics_threshold}" />    
    <input type="hidden" name="forumId" value="{$forumId}" />
    <table class="forumformtable">
    <tr>
      <td class="forumform">{tr}Post{/tr}</td>
      <td class="forumform">
      <input type="submit" name="comments_previewComment" value="{tr}preview{/tr}"/>
      <input type="submit" name="comments_postComment" value="{tr}post{/tr}"/></td>
      {if $feature_smileys eq 'y'}<td class="forumform">{tr}smileys{/tr}</td>{/if}
    </tr>
    <tr>
      <td class="forumform">{tr}Title{/tr}</td>
      <td class="forumform"><input type="text" name="comments_title" value="{$comment_title}" /></td>
      {if $feature_smileys eq 'y'}
      <td rowspan="3" class="forumform">
      <table>
      <tr><td><a href="javascript:setSomeElement('editpost','(:biggrin:)');"><img src="img/smiles/icon_biggrin.gif" alt="big grin" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:confused:)');"><img src="img/smiles/icon_confused.gif" alt="confused" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:cool:)');"><img src="img/smiles/icon_cool.gif" alt="cool" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:cry:)');"><img src="img/smiles/icon_cry.gif" alt="cry" border="0" /></a></td>
       </tr>
       <tr><td><a href="javascript:setSomeElement('editpost','(:eek:)');"><img src="img/smiles/icon_eek.gif" alt="eek" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:evil:)');"><img src="img/smiles/icon_evil.gif" alt="evil" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:exclaim:)');"><img src="img/smiles/icon_exclaim.gif" alt="exclaim" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:frown:)');"><img src="img/smiles/icon_frown.gif" alt="frown" border="0" /></a></td>
       </tr>
       <tr><td><a href="javascript:setSomeElement('editpost','(:idea:)');"><img src="img/smiles/icon_idea.gif" alt="idea" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:lol:)');"><img src="img/smiles/icon_lol.gif" alt="lol" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:mad:)');"><img src="img/smiles/icon_mad.gif" alt="mad" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:mrgreen:)');"><img src="img/smiles/icon_mrgreen.gif" alt="mr green" border="0" /></a></td>
       </tr>
       <tr><td><a href="javascript:setSomeElement('editpost','(:neutral:)');"><img src="img/smiles/icon_neutral.gif" alt="neutral" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:question:)');"><img src="img/smiles/icon_question.gif" alt="question" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:razz:)');"><img src="img/smiles/icon_razz.gif" alt="razz" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:redface:)');"><img src="img/smiles/icon_redface.gif" alt="redface" border="0" /></a></td>
       </tr>
       <tr><td><a href="javascript:setSomeElement('editpost','(:rolleyes:)');"><img src="img/smiles/icon_rolleyes.gif" alt="rolleyes" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:sad:)');"><img src="img/smiles/icon_sad.gif" alt="sad" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:smile:)');"><img src="img/smiles/icon_smile.gif" alt="smile" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:surprised:)');"><img src="img/smiles/icon_surprised.gif" alt="surprised" border="0" /></a></td>
       </tr>
       <tr><td><a href="javascript:setSomeElement('editpost','(:twisted:)');"><img src="img/smiles/icon_twisted.gif" alt="twisted" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:wink:)');"><img src="img/smiles/icon_wink.gif" alt="wink" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editpost','(:arrow:)');"><img src="img/smiles/icon_arrow.gif" alt="arrow" border="0" /></a></td>
          
       </tr>
      </table>
      </td>
      {/if}
    </tr>
    {if $forum_info.forum_use_password eq 'a'}
    <tr>
    	<td class='forumform'>{tr}Password{/tr}</td>
    	<td class='forumform'>
    		<input type="password" name="password" />
    	</td>
    </tr>
    {/if}
    <tr>
      <td class="forumform">Comment</td>
      <td class="forumform"><textarea id='editpost' name="comments_data" rows="8" cols="60">{$comment_data}</textarea></td>
    </tr>
    </table>
    </form>
    
   <br/>    
  <div class="commentsedithelp"><b>{tr}Posting comments{/tr}:</b><br/><br/>
  {tr}Use{/tr} [http://www.foo.com] {tr}or{/tr} [http://www.foo.com|description] {tr}for links{/tr}<br/>
  {tr}HTML tags are not allowed inside comments{/tr}
  </div>
  <br/>
    
</div>


<br/><br/>
{/if}
{/if}
<!-- TOOLBAR -->
  <div class="forumtoolbar">
  <form method="post" action="tiki-view_forum_thread.php">
  <input type="hidden" name="forumId" value="{$forum_info.forumId}" />    
  <input type="hidden" name="comments_parentId" value="{$comments_parentId}" />    
  <input type="hidden" name="comments_offset" value="0" />
  <input type="hidden" name="topics_offset" value="{$smarty.request.topics_offset}" />
  <input type="hidden" name="topics_find" value="{$smarty.request.topics_find}" />
  <input type="hidden" name="topics_sort_mode" value="{$smarty.request.topics_sort_mode}" />    
  <input type="hidden" name="topics_threshold" value="{$smarty.request.topics_threshold}" />    

  <table width="95%" cellpadding="0" cellspacing="0">
  <tr>
    <td class="forumtoolbar">{tr}Comments{/tr} 
        <select name="comments_maxComments">
        <option value="10" {if $comments_maxComments eq 10 }selected="selected"{/if}>10</option>
        <option value="20" {if $comments_maxComments eq 20 }selected="selected"{/if}>20</option>
        <option value="30" {if $comments_maxComments eq 30 }selected="selected"{/if}>30</option>
        <option value="999999" {if $comments_maxComments eq 999999 }selected="selected"{/if}>All</option>
        </select>
    </td>
    <td class="forumtoolbar">{tr}Sort{/tr}
        <select name="comments_sort_mode">
          <option value="commentDate_desc" {if $comments_sort_mode eq 'commentDate_desc'}selected="selected"{/if}>{tr}Date{/tr}</option>
          <option value="points_desc" {if $comments_sort_mode eq 'points_desc'}selected="selected"{/if}>{tr}Score{/tr}</option>
          <option value="title_desc" {if $comments_sort_mode eq 'title_desc'}selected="selected"{/if}>{tr}Title{/tr}</option>
        </select>
    </td>
    <td class="forumtoolbar">{tr}Threshold{/tr}
        <select name="comments_threshold">
        <option value="0" {if $comments_threshold eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
        <option value="0.01" {if $comments_threshold > '0.01'}selected="selected"{/if}>0</option>
        <option value="1" {if $comments_threshold eq 1}selected="selected"{/if}>1</option>
        <option value="2" {if $comments_threshold eq 2}selected="selected"{/if}>2</option>
        <option value="3" {if $comments_threshold eq 3}selected="selected"{/if}>3</option>
        <option value="4" {if $comments_threshold eq 4}selected="selected"{/if}>4</option>
        </select>
    
    </td>
    <td class="forumtoolbar">{tr}Search{/tr}
        <input type="text" size="7" name="comments_commentFind" value="{$comments_commentFind}" />
    </td>
    
    <td><input type="submit" name="comments_setOptions" value="{tr}set{/tr}" /></td>
    <td class="forumtoolbar">
    &nbsp;<a class="toolbarlink" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset=0&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">{tr}Top{/tr}</a>
    </td>
  </tr>
  </table>
  </form>
  </div>
<!-- TOOLBAR ENDS -->

<form method="post" action="tiki-view_forum_thread.php">
    <input type="hidden" name="comments_offset" value="{$comments_offset}" />
    <input type="hidden" name="comments_threadId" value="{$comments_threadId}" />
    <input type="hidden" name="comments_parentId" value="{$comments_parentId}" />
    <input type="hidden" name="comments_threshold" value="{$comments_threshold}" />
    <input type="hidden" name="comments_sort_mode" value="{$comments_sort_mode}" />
    <input type="hidden" name="topics_offset" value="{$smarty.request.topics_offset}" />
    <input type="hidden" name="topics_find" value="{$smarty.request.topics_find}" />
    <input type="hidden" name="topics_sort_mode" value="{$smarty.request.topics_sort_mode}" />    
    <input type="hidden" name="topics_threshold" value="{$smarty.request.topics_threshold}" />    
    <input type="hidden" name="forumId" value="{$forumId}" />
{if $tiki_p_admin_forum eq 'y'}
<table class="normal">
	<tr>
		<td colspan="3" class="heading">{tr}Moderator actions{/tr}</td>
	</tr>
	<tr>
		<td class="odd">
			<input type="submit" name="delsel" value="{tr}delete selected{/tr}" />
		</td>
		<td class="odd">
			{tr}Move to topic:{/tr}
			<select name="moveto">
			{section name=ix loop=$topics}
				{if $topics[ix].threadId ne $comments_parentId}
					<option value="{$topics[ix].threadId}">{$topics[ix].title}</option>
				{/if}
			{/section}
			</select>
			<input type="submit" name="movesel" value="{tr}move{/tr}" />
		</td>
		<td style="text-align:right;" class="odd">
			<small><a class="link" href="tiki-forum_queue.php?forumId={$forumId}">{tr}queued messages:{/tr}{$queued}</a></small>
		</td>

	</tr>
</table>
{/if}

<table class="threads" >
<tr>
  <td class="forumheading">{tr}author{/tr}</td>
  <td class="forumheading">{tr}message{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$comments_coms}
<tr>
  <td  class="threads{cycle advance=false}l" width="15%">
  <div align="center">
  {if $forum_info.ui_avatar eq 'y'}
  {$comments_coms[ix].userName|avatarize}<br/>
  {/if}
  <br/>{$comments_coms[ix].userName|userlink}
  {if $forum_info.ui_flag eq 'y'}
  <br/>{$comments_coms[ix].userName|countryflag}
  {/if}
  {if $comments_coms[ix].userName and $forum_info.ui_posts eq 'y'}
  <br/><small>posts:{$comments_coms[ix].user_posts}</small>
  {/if}
  {if $comments_coms[ix].userName and $forum_info.ui_level eq 'y'}
  <br/><img src="img/icons/{$comments_coms[ix].user_level}stars.gif" alt='{$comments_coms[ix].user_level} {tr}stars{/tr}' title='{tr}user level{/tr}' />
  {/if}
  {if $comments_coms[ix].userName and $forum_info.ui_online eq 'y' and $comments_coms[ix].user_online eq 'y'}
  <br/><small>now online</small>
  {/if}

  </div>
  </td>
  <td  class="threads{cycle}r" width="85%">
  <table width="100%">
  <tr>
  	<td>
  		<b>{$comments_coms[ix].title}</b>
  	</td>
  	<td style="text-align:right;">
	  {if $tiki_p_admin_forum eq 'y'}
		<input type="checkbox" name="forumthread[]" value="{$comments_coms[ix].threadId}"  {if $smarty.request.forumthread and in_array($comments_coms[ix].threadId,$smarty.request.forumthread)}checked="checked"{/if} />
	  {/if}	

	  {if $tiki_p_admin_forum eq 'y'}
	  <a href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;openpost=1&amp;comments_threadId={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}"
	     class="admlink"><img src='img/icons/edit.gif' border='0' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' /></a>
	  <a href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;comments_remove=1&amp;comments_threadId={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}"
	     class="admlink"><img src='img/icons/trash.gif' border='0' alt='{tr}remove{/tr}' title='{tr}remove{/tr}' /></a>
	  {/if}     
	  <a href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;quote={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}"
	     class="admlink"><img src='img/icons/linkto.gif' border='0' alt='{tr}reply{/tr}' title='{tr}reply{/tr}' /></a>
	  {if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}   
	  <a class="admlink" href="messu-compose.php?to={$comments_coms[ix].userName}&amp;subject=Re:{$comments_coms[ix].title}"><img src='img/icons/myinfo.gif' border='0' alt='{tr}private message{/tr}' title='{tr}private message{/tr}' /></a>
	  {/if}
	  {if $user and $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
		<a title="{tr}Save to notepad{/tr}" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;savenotepad={$comments_coms[ix].threadId}"><img border="0" src="img/icons/ico_save.gif" alt="{tr}save{/tr}" /></a>
	  {/if}

	  {if $comments_coms[ix].userName and $forum_info.ui_email eq 'y' and strlen($comments_coms[ix].user_email) > 0}  
		  <a href="mailto:{$comments_coms[ix].user_email|escape:'hex'}"><img src='img/icons/email.gif' alt='{tr}send email to user{/tr}' title='{tr}send email to user{/tr}' border='0' /></a>
	  {/if}


	</td>
  </tr>
  </table>
  <br/><br/>
  {$comments_coms[ix].parsed}
  <br/><br/>
  <table style="border: 1px solid black;" width="100%">
  <tr>
    <td style="font-size:8pt;">
    {tr}on{/tr}: {$comments_coms[ix].commentDate|tiki_short_datetime}  
    </td>
    {if $forum_info.vote_threads eq 'y'}
    <td style="font-size:8pt;">
    {tr}score{/tr}</b>: {$comments_coms[ix].points}
    </td>
    {if $tiki_p_admin_forum eq 'y' or $tiki_p_forum_vote eq 'y'}
    <td align="right" style="font-size:8pt;">
    <b>{tr}Vote{/tr}</b>: 
    
  <a class="forumvotelink" href="tiki-view_forum_thread.php?topics_offset={$smary.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[ix].threadId}&amp;comments_vote=1&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">1</a>
  <a class="forumvotelink" href="tiki-view_forum_thread.php?topics_offset={$smary.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[ix].threadId}&amp;comments_vote=2&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">2</a>
  <a class="forumvotelink" href="tiki-view_forum_thread.php?topics_offset={$smary.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[ix].threadId}&amp;comments_vote=3&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">3</a>
  <a class="forumvotelink" href="tiki-view_forum_thread.php?topics_offset={$smary.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[ix].threadId}&amp;comments_vote=4&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">4</a>
  <a class="forumvotelink" href="tiki-view_forum_thread.php?topics_offset={$smary.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[ix].threadId}&amp;comments_vote=5&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">5</a>
    </td>
    {/if}
    {/if}
  </tr>
  </table>
  </td>
</tr>
<tr>
  <td colspan="2" class="threadseparator"></td>
</tr>
{/section}
</table>
</form>

<br/>
  <div align="center">
  <div class="mini">
  {if $comments_prev_offset >= 0}
  [<a class="prevnext" href="tiki-view_forum_thread.php?topics_offset={$smary.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_prev_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">{tr}prev{/tr}</a>]&nbsp;
  {/if}
  {tr}Page{/tr}: {$comments_actual_page}/{$comments_cant_pages}
  {if $comments_next_offset >= 0}
  &nbsp;[<a class="prevnext" href="tiki-view_forum_thread.php?topics_offset={$smary.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_next_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">{tr}next{/tr}</a>]
  {/if}
  {if $direct_pagination eq 'y'}
<br/>
{section loop=$comments_cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$comments_maxComments}
<a class="prevnext" href="tiki-view_forum_thread.php?topics_offset={$smary.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$selector_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
  </div>
  <br/>
  </div>
<small>{$comments_below} {tr}Comments below your current threshold{/tr}</small>
  
{if $feature_forum_quickjump eq 'y'}
<form id='quick' method="post" action="tiki-view_forum.php">
<table width="100%">
<tr>
<td style="text-align:right;">
<small>{tr}Jump to forum{/tr}:</small>
<select name="forumId" onChange="javascript:document.getElementById('quick').submit();">
{section name=ix loop=$all_forums}
<option value="{$all_forums[ix].forumId}" {if $all_forums[ix].forumId eq $forumId}selected="selected"{/if}>{$all_forums[ix].name}</option>
{/section}
</select>
</td>
</tr>
</table>
</form>
{/if}