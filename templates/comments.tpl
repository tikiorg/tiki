{if $tiki_p_read_comments eq 'y'}
<br/>
({$comments_cant} {tr}comments{/tr})
{if $comments_show eq 'y'}
[<a href="javascript:show('comzoneopen');" class="opencomlink">{tr}Show comments{/tr}</a>|
<a href="javascript:hide('comzoneopen');" class="opencomlink">{tr}Hide comments{/tr}</a>]
{else}
[<a href="javascript:show('comzone');" class="opencomlink">{tr}Show comments{/tr}</a>|
<a href="javascript:hide('comzone');" class="opencomlink">{tr}Hide comments{/tr}</a>]
{/if}
<br/><br/>
{/if}
{if $comments_show eq 'y'}
<div id="comzoneopen">
{else}
<div id="comzone">
{/if}
  
  {if $comment_preview eq 'y'}
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
  <br/><br/>
  </div>
  </div>
  {/if}
  
  {if $tiki_p_read_comments eq 'y'}
  {if $tiki_p_post_comments eq 'y'}
    <div class="commentspost">
    {if $comments_threadId > 0}
    {tr}Editing comment{/tr}: {$comments_threadId} (<a class="commentshlink" href="{$comments_complete_father}comments_threadId=0&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">{tr}post new comment{/tr}</a>)
    {/if}
    <form method="post" action="{$comments_father}">
    <input type="hidden" name="comments_parentId" value="{$comments_parentId}" />
    <input type="hidden" name="comments_offset" value="{$comments_offset}" />
    <input type="hidden" name="comments_threadId" value="{$comments_threadId}" />
    <input type="hidden" name="comments_threshold" value="{$comments_threshold}" />
    <input type="hidden" name="comments_sort_mode" value="{$comments_sort_mode}" />
    {* Traverse request variables that were set to this page adding them as hidden data *}
    {section name=i loop=$comments_request_data}
    <input type="hidden" name="{$comments_request_data[i].name}" value="{$comments_request_data[i].value}" />
    {/section}
    <table width="100%">
    <tr>
      <td class="comform">{tr}Post new comment{/tr}</td>
      <td class="comform">
      <input type="submit" name="comments_previewComment" value="{tr}preview{/tr}"/>
      <input type="submit" name="comments_postComment" value="{tr}post{/tr}"/>
      </td>
      <td width="20%" class="comform">{tr}Smileys{/tr}</td>
    </tr>
    <tr>
      <td class="comform">{tr}Title{/tr}</td>
      <td class="comform"><input type="text" name="comments_title" value="{$comment_title}" /></td>
      <td class="comform" rowspan="2">
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
    </tr>
    <tr>
      <td class="comform">Comment</td>
      <td class="comform"><textarea id="editpost" name="comments_data" rows="6" cols="62">{$comment_data}</textarea></td>
    </tr>
    </table>
    </form>
    </div>
  {/if}

  <div class="commentsedithelp"><b>{tr}Posting comments{/tr}:</b><br/><br/>
  {tr}Use{/tr} [http://www.foo.com] {tr}or{/tr} [http://www.foo.com|description] {tr}for links{/tr}<br/>
  {tr}HTML tags are not allowed inside comments{/tr}<br/>
  <!--{tr}Use{/tr} {literal}{{/literal}cookie} {tr}to insert a random tagline{/tr}<br/>-->
  </div>
  <br/>

  <div class="commentstoolbar">
  <form method="post" action="{$comments_father}">
  {section name=i loop=$comments_request_data}
  <input type="hidden" name="{$comments_request_data[i].name}" value="{$comments_request_data[i].value}" />
  {/section}
  <input type="hidden" name="comments_parentId" value="{$comments_parentId}" />    
  <input type="hidden" name="comments_offset" value="0" />
  <table width="97%" cellpadding="0" cellspacing="0">
  <tr>
    <td class="form">{tr}Comments{/tr} 
        <select name="comments_maxComments">
        <option value="10" {if $comments_maxComments eq 10 }selected="selected"{/if}>10</option>
        <option value="20" {if $comments_maxComments eq 20 }selected="selected"{/if}>20</option>
        <option value="30" {if $comments_maxComments eq 30 }selected="selected"{/if}>30</option>
        <option value="999999" {if $comments_maxComments eq 999999 }selected="selected"{/if}>All</option>
        </select>
    </td>
    <td class="form">{tr}Sort{/tr}
        <select name="comments_sort_mode">
          <option value="commentDate_desc" {if $comments_sort_mode eq 'commentDate_desc'}selected="selected"{/if}>{tr}Date{/tr}</option>
          <option value="points_desc" {if $comments_sort_mode eq 'points_desc'}selected="selected"{/if}>{tr}Score{/tr}</option>
        </select>
    </td>
    <td class="form">{tr}Threshold{/tr}
        <select name="comments_threshold">
        <option value="0" {if $comments_threshold eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
        <option value="0.01" {if $comments_threshold eq 0.01}selected="selected"{/if}>0</option>
        <option value="1" {if $comments_threshold eq 1}selected="selected"{/if}>1</option>
        <option value="2" {if $comments_threshold eq 2}selected="selected"{/if}>2</option>
        <option value="3" {if $comments_threshold eq 3}selected="selected"{/if}>3</option>
        <option value="4" {if $comments_threshold eq 4}selected="selected"{/if}>4</option>
        </select>
    
    </td>
    <td class="form">{tr}Search{/tr}
        <input type="text" size="7" name="comments_commentFind" value="{$comments_commentFind}" />
    </td>
    
    <td><input type="submit" name="comments_setOptions" value="{tr}set{/tr}" /></td>
    <td class="form" valign="bottom">
    &nbsp;<a class="commentshlink" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId=0">{tr}Top{/tr}</a>
    </td>
  </tr>
  </table>
  </form>
  </div>
  
  {section name=com loop=$comments_coms}
  <a name="threadId{$comments_coms[com].threadId}" />
  <div class="commentscomment">
  <div class="commentheader">
  <table width="97%">
  <tr>
  <td>
  <div class="commentheader">
  <span class="commentstitle">{$comments_coms[com].title}</span><br/>
  {tr}by{/tr} {$comments_coms[com].userName} on {$comments_coms[com].commentDate|date_format:"%A %d of %B, %Y [%H:%M:%S]"} [{tr}Score{/tr}:{$comments_coms[com].average|string_format:"%.2f"}]
  </div>
  </td>
  <td valign="top" align="right" width="20%">
  <div class="commentheader">
  {if $tiki_p_vote_comments eq 'y' or $tiki_p_remove_comments eq 'y' or $tiki_p_edit_comments eq 'y'}
  {if $tiki_p_vote_comments eq 'y'}
  {tr}Vote{/tr}: 
  <a class="commentshlink" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[com].threadId}&amp;comments_vote=1&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">1</a>
  <a class="commentshlink" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[com].threadId}&amp;comments_vote=2&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">2</a>
  <a class="commentshlink" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[com].threadId}&amp;comments_vote=3&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">3</a>
  <a class="commentshlink" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[com].threadId}&amp;comments_vote=4&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">4</a>
  <a class="commentshlink" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[com].threadId}&amp;comments_vote=5&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">5</a>
  {/if}
  {if $tiki_p_remove_comments eq 'y'}
  (<a class="commentshlink" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[com].threadId}&amp;comments_remove=1&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">{tr}remove{/tr}</a>)
  {/if}
  {if $tiki_p_edit_comments eq 'y'}
  (<a class="commentshlink" href="{$comments_complete_father}comments_threadId={$comments_coms[com].threadId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">{tr}edit{/tr}</a>)
  {/if}
  {/if}
  </div>
  </td>
  </tr>
  </table>
  </div>
  <div class="commenttext">
  {$comments_coms[com].parsed}
  <br/><br/>
  [<a class="commentslink" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_coms[com].threadId}">{tr}reply to this{/tr}</a>
  {if $comments_parentId > 0}
  |<a class="commentslink" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_coms[com].grandFather}">parent</a>
  {/if}
  ]
  {if $comments_coms[com].replies.numReplies > 0}
  <br/>
  <ul>
  {section name=rep loop=$comments_coms[com].replies.replies}
  <li><a class="commentshlink" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_coms[com].threadId}#threadId{$comments_coms[com].replies.replies[rep].threadId}">{$comments_coms[com].replies.replies[rep].title}</a>
   <a class="commentshlink">{tr}by{/tr} {$comments_coms[com].replies.replies[rep].userName} ({tr}Score{/tr}: {$comments_coms[com].replies.replies[rep].points}) {tr}on{/tr} {$comments_coms[com].replies.replies[rep].commentDate|date_format:"%A %d of %B, %Y [%H:%M:%S]"}</a></li>
  {/section}
  </ul>
  {/if}
  </div>
  </div>
  {/section}
  
   
  <div class="mini">
  {if $comments_prev_offset >= 0}
  [<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_prev_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">{tr}prev{/tr}</a>]&nbsp;
  {/if}
  {tr}Page{/tr}: {$comments_actual_page}/{$comments_cant_pages}
  {if $comments_next_offset >= 0}
  &nbsp;[<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_next_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">{tr}next{/tr}</a>]
  {/if}
  </div>
  <br/>
  {$comments_below} {tr}Comments below your current threshold{/tr}
  {/if}
</div>
