<a class="pagetitle" href="tiki-view_forum.php?topics_offset={$smary.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;forumId={$forum_info.forumId}" class="forumspagetitle">{tr}Forum{/tr}: {$forum_info.name}</a>
<br/><br/>
<a class="link" href="tiki-forums.php">{tr}Tiki forums{/tr}</a>-><a class="link" href="tiki-view_forum.php?forumId={$forumId}">{$forum_info.name}</a>-><a class="link" href="tiki-view_forum_thread?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&topics_find={$smarty.request.topics_find}&amp;forumId={$forumId}&amp;comments_parentId={$smarty.request.comments_parentId}">{$thread_info.title}</a>
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
  {$thread_info.userName|avatarize}<br/>
  <b>{tr}author{/tr}</b>:<br/>
  
  {$thread_info.userName|userlink}
  <br/>{$thread_info.userName|countryflag}
  </td>
  <td class="viewthreadr" width="85%">
  <b>{$thread_info.title}</b><br/><br/>
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
      <td rowspan="2" class="forumform">
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
  {$comments_coms[ix].userName|avatarize}<br/>
  <b>{tr}author{/tr}</b><br/>{$comments_coms[ix].userName|userlink}<br/>{$comments_coms[ix].userName|countryflag}
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
  {$comments_below} {tr}Comments below your current threshold{/tr}
  
