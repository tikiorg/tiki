<div class="forumspagetitle">
<a href="tiki-view_forum.php?forumId={$forum_info.forumId}" class="forumspagetitle">{$forum_info.name}</a>
</div>
{if $openpost eq 'y'}
{assign var="postclass" value="forumpostopen"}
{else}
{assign var="postclass" value="forumpost"}
{/if}
{if $tiki_p_forum_post_topic eq 'y'}
[<a class="forumbutlink" href="javascript:show('{$postclass}');">{tr}Show Post Form{/tr}</a> |
<a class="forumbutlink" href="javascript:hide('{$postclass}');">{tr}Hide Post Form{/tr}</a> |
<a class="forumbutlink" href="tiki-forum_rss.php?forumId={$forumId}">{tr}RSS{/tr}</a> |
<a class="forumbutlink" href="tiki-forums.php">{tr}Forum List{/tr}</a> 
{if $tiki_p_admin_forum eq 'y'}
| <a class="forumbutlink" href="tiki-admin_forums.php?forumId={$forum_info.forumId}">{tr}Edit Forum{/tr}</a>{/if}]

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

<div id='{$postclass}'>
  <br/>
  {if $comments_threadId > 0}
    {tr}Editing comment{/tr}: {$comments_threadId} (<a class="forumbutlink" href="tiki-view_forum.php?openpost=1&amp;forumId={$forum_info.forumId}&amp;comments_threadId=0&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">{tr}post new comment{/tr}</a>)
    {/if}
    <form method="post" action="tiki-view_forum.php">
    <input type="hidden" name="comments_offset" value="{$comments_offset}" />
    <input type="hidden" name="comments_threadId" value="{$comments_threadId}" />
    <input type="hidden" name="comments_threshold" value="{$comments_threshold}" />
    <input type="hidden" name="comments_sort_mode" value="{$comments_sort_mode}" />
    <input type="hidden" name="forumId" value="{$forumId}" />
    <table class="forumformtable">
    <tr>
      <td class="forumform">{tr}Post{/tr}</td>
      <td class="forumform">
      <input type="submit" name="comments_previewComment" value="{tr}preview{/tr}"/>
      <input type="submit" name="comments_postComment" value="{tr}post{/tr}"/>
      </td>
      {if $feature_smileys eq 'y'}<td class="forumform">{tr}smileys{/tr}</td>{/if}
    </tr>
    <tr>
      <td class="forumform">{tr}Title{/tr}</td>
      <td class="forumform"><input type="text" name="comments_title" value="{$comment_title}" /></td>
      
      {if $feature_smileys eq 'y'}<td rowspan="3" class="forumform">
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
      <td class="forumform">{tr}Type{/tr}</td>
      <td class="forumform">
      <select name="comment_topictype">
      <option value="n" {if $comment_topictype eq 'n'}selected="selected"{/if}>{tr}normal{/tr}</option>
      {if $tiki_p_admin_forum eq 'y'}
      <option value="a" {if $comment_topictype eq 'a'}selected="selected"{/if}>{tr}announce{/tr}</option>
      <option value="h" {if $comment_topictype eq 'h'}selected="selected"{/if}>{tr}hot{/tr}</option>
      <option value="s" {if $comment_topictype eq 's'}selected="selected"{/if}>{tr}sticky{/tr}</option>
      <option value="l" {if $comment_topictype eq 'l'}selected="selected"{/if}>{tr}locked{/tr}</option>
      {/if}
      </select>
      </td>
    </tr>
    <tr>
      <td class="forumform">Comment</td>
      <td class="forumform"><textarea id='editpost' name="comments_data" rows="6" cols="30">{$comment_data}</textarea></td>
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
<table class="forumstable">
<tr>
  <td width="2%" class="forumheading"><a class="lforumheading" href="tiki-view_forum.php?comments_threshold={$comments_threshold}&amp;forumId={$forum_info.forumId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={if $comments_sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">type</a></td>
  <td width="40%" class="forumheading"><a class="lforumheading" href="tiki-view_forum.php?comments_threshold={$comments_threshold}&amp;forumId={$forum_info.forumId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={if $comments_sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">title</a></td>
  <td class="forumheading"><a class="lforumheading" href="tiki-view_forum.php?comments_threshold={$comments_threshold}&amp;forumId={$forum_info.forumId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={if $comments_sort_mode eq 'replies_desc'}replies_asc{else}replies_desc{/if}">replies</a></td>
  <td class="forumheading"><a class="lforumheading" href="tiki-view_forum.php?comments_threshold={$comments_threshold}&amp;forumId={$forum_info.forumId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={if $comments_sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">reads</a></td>
  <td class="forumheading"><a class="lforumheading" href="tiki-view_forum.php?comments_threshold={$comments_threshold}&amp;forumId={$forum_info.forumId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={if $comments_sort_mode eq 'average_desc'}average_asc{else}average_desc{/if}">pts</a></td>
  <td class="forumheading"><a class="lforumheading" href="tiki-view_forum.php?comments_threshold={$comments_threshold}&amp;forumId={$forum_info.forumId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={if $comments_sort_mode eq 'lastPost_desc'}lastPost_asc{else}lastPost_desc{/if}">last post</a></td>
  <td class="forumheading"><a class="lforumheading" href="tiki-view_forum.php?comments_threshold={$comments_threshold}&amp;forumId={$forum_info.forumId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={if $comments_sort_mode eq 'userName_desc'}userName_asc{else}userName_desc{/if}">author</a></td>
</tr>
{section name=ix loop=$comments_coms}
{if $comments_coms[ix].commentDate > $last_forum_visit}
{assign var="newtopic" value="_new"}
{else}
{assign var="newtopic" value=""}
{/if}
{if $smarty.section.ix.index % 2}
<tr>
  <td class="topictitleodd">
  {if $comments_coms[ix].type eq 'n'}<img src="img/icons/folder{$newtopic}.gif" alt="folder" />{/if}
  {if $comments_coms[ix].type eq 'a'}<img src="img/icons/folder_announce{$newtopic}.gif" alt="announce" />{/if}
  {if $comments_coms[ix].type eq 'h'}<img src="img/icons/folder_hot{$newtopic}.gif" alt="hot" />{/if}
  {if $comments_coms[ix].type eq 's'}<img src="img/icons/folder_sticky{$newtopic}.gif" alt="sticky" />{/if}
  {if $comments_coms[ix].type eq 'l'}<img src="img/icons/folder_locked{$newtopic}.gif" alt="locked" />{/if}
  </td>
  <td class="topictitleodd"><a class="forumname" href="tiki-view_forum_thread.php?comments_parentId={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}">{$comments_coms[ix].title}</a>
  {if $tiki_p_admin_forum eq 'y'}
  <a href="tiki-view_forum.php?openpost=1&amp;comments_threadId={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}"
     class="admlink">edit</a>
  <a href="tiki-view_forum.php?comments_remove=1&amp;comments_threadId={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}"
     class="admlink">x</a>
  {/if}     
  </td>
  <td class="topicrepliesodd">{$comments_coms[ix].replies.numReplies}</td>
  <td class="topicreadsodd">{$comments_coms[ix].hits}</td>
  <td class="topicscoreodd">{$comments_coms[ix].average|string_format:"%.2f"}</td>
  <td class="topiclastpostodd">{$comments_coms[ix].lastPost|date_format:"%d of %b [%H:%M]"}</td>
  <td class="topicauthorodd">{$comments_coms[ix].userName}</td>
</tr>
{else}
<tr>
  <td class="topictitleeven">
  {if $comments_coms[ix].type eq 'n'}<img src="img/icons/folder{$newtopic}.gif" alt="folder" />{/if}
  {if $comments_coms[ix].type eq 'a'}<img src="img/icons/folder_announce{$newtopic}.gif" alt="announce" />{/if}
  {if $comments_coms[ix].type eq 'h'}<img src="img/icons/folder_hot{$newtopic}.gif" alt="hot" />{/if}
  {if $comments_coms[ix].type eq 's'}<img src="img/icons/folder_sticky{$newtopic}.gif" alt="sticky" />{/if}
  {if $comments_coms[ix].type eq 'l'}<img src="img/icons/folder_locked{$newtopic}.gif" alt="locked" />{/if}
  </td>
  <td class="topictitleeven"><a class="forumname" href="tiki-view_forum_thread.php?comments_parentId={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}">{$comments_coms[ix].title}
  {if $tiki_p_admin_forum eq 'y'}
  <a href="tiki-view_forum.php?openpost=1&amp;comments_threadId={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}"
     class="admlink">edit</a>
  <a href="tiki-view_forum.php?comments_remove=1&amp;comments_threadId={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}"
     class="admlink">x</a>
  {/if}     
  </td>
  <td class="topicreplieseven">{$comments_coms[ix].replies.numReplies}</td>
  <td class="topicreadseven">{$comments_coms[ix].hits}</td>
  <td class="topicscoreeven">{$comments_coms[ix].average|string_format:"%.2f"}</td>
  <td class="topiclastposteven">{$comments_coms[ix].lastPost|date_format:"%d of %b [%H:%M]"}</td>
  <td class="topicauthoreven">{$comments_coms[ix].userName}</td>
{/if}
</tr>
{/section}
</table>

<br/>
  <div align="center">
  <div class="mini">
  {if $comments_prev_offset >= 0}
  [<a class="forumprevnext" href="tiki-view_forum.php?forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_prev_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">{tr}prev{/tr}</a>]&nbsp;
  {/if}
  {tr}Page{/tr}: {$comments_actual_page}/{$comments_cant_pages}
  {if $comments_next_offset >= 0}
  &nbsp;[<a class="forumprevnext" href="tiki-view_forum.php?forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_next_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">{tr}next{/tr}</a>]
  {/if}
  </div>
  <br/>
  </div>