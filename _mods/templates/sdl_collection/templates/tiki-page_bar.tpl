{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/tiki-page_bar.tpl,v 1.1 2004-05-09 23:09:15 damosoft Exp $ *}

<hr/>
<div id="page-bar">
  <table>
    <tr>

{* Check that page is not locked and edit permission granted. SandBox can be adited w/o perm *}
{if !$lock and ($tiki_p_edit eq 'y' or $page eq 'SandBox')}
    <td>
      <div class="button2" >
      <a title="{$semUser}" href="tiki-editpage.php?page={$page|escape:"url"}" class="linkbut">
        {if $beingEdited eq 'y'}
          <span class="highlight">{tr}Edit{/tr}</span>
        {else}
          {tr}Edit{/tr}
        {/if}
      </a>
      </div>
    </td>
{/if}

{if $page ne 'SandBox'}

{if $tiki_p_remove eq 'y'}
<td><div class="button2"><a href="tiki-removepage.php?page={$page|escape:"url"}&amp;version=last" class="linkbut">{tr}Remove{/tr}</a></div></td>
{/if}
{if $tiki_p_rename eq 'y'}
<td><div class="button2"><a href="tiki-rename_page.php?page={$page|escape:"url"}" class="linkbut">{tr}Rename{/tr}</a></div></td>
{/if}
{if $tiki_p_admin_wiki eq 'y' or ($user and ($user eq $page_user) and ($tiki_p_lock eq 'y') and ($feature_wiki_usrlock eq 'y'))}
{if $lock}
<td><div class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=unlock" class="linkbut">{tr}Unlock{/tr}</a></div></td>
{else}
<td><div class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=lock" class="linkbut">{tr}Lock{/tr}</a></div></td>
{/if}
{/if}
{if $tiki_p_admin_wiki eq 'y'}
<td><div class="button2"><a href="tiki-pagepermissions.php?page={$page|escape:"url"}" class="linkbut">{tr}Perms{/tr}</a></div></td>
{/if}

{if $feature_history eq 'y'}
<td><div class="button2"><a href="tiki-pagehistory.php?page={$page|escape:"url"}" class="linkbut">{tr}History{/tr}</a></div></td>
{/if}
{/if}

{if $feature_likePages eq 'y'}
<td><div class="button2"><a href="tiki-likepages.php?page={$page|escape:"url"}" class="linkbut">{tr}Similar{/tr}</a></div></td>
{/if}
{if $feature_wiki_undo eq 'y' and $canundo eq 'y'}
<td><div class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;undo=1" class="linkbut">{tr}Undo{/tr}</a></div></td>
{/if}
{if $wiki_uses_slides eq 'y'}
{if $show_slideshow eq 'y'}
<td><div class="button2"><a href="tiki-slideshow.php?page={$page|escape:"url"}" class="linkbut">{tr}Slides{/tr}</a></div></td>
{elseif $structure eq 'y'}
<td><div class="button2"><a href="tiki-slideshow2.php?page_ref_id={$page_info.page_ref_id}" class="linkbut">{tr}Slides{/tr}</a></div></td>
{/if}
{/if}
<td><div class="button2"><a href="tiki-export_wiki_pages.php?page={$page|escape:"url"}" class="linkbut">{tr}Export{/tr}</a></div></td>
{if $feature_wiki_discuss eq 'y'}
<td><div class="button2"><a href="tiki-view_forum.php?forumId={$wiki_forum_id}&amp;comments_postComment=post&amp;comments_title={$page|escape:"url"}&amp;comments_data={"Use this thread to discuss the [tiki-index.php?page="}{$page|escape:"url"}{"|"}{$page|escape:"url"}{"] page."}&amp;comment_topictype=n" class="linkbut">{tr}Discuss{/tr}</a></div></td>
{/if}


{if $edit_page eq 'y'} {* Show this button only in editing mode *}
  <td>
    <div class="button2">
      <a title="Show quick help for wiki syntax"
         href="javascript:flip('edithelpzone');" class="linkbut">{tr}Wiki Help{/tr}</a>
    </div>
  </td>
{/if}

{if $show_page == 'y'} {* Show this buttons only if page view mode *}

  {* don't show comments if feature disabled or not enough rights *}
  {if $feature_wiki_comments == 'y'
  && ($tiki_p_read_comments  == 'y'
  ||  $tiki_p_post_comments  == 'y'
  ||  $tiki_p_edit_comments  == 'y')}
   <td>
    <div class="button2">
      <a title="View/post comments"
         href="javascript:document.location='#comments';flip('comzone{if $comments_show eq 'y'}open{/if}');"
         class="linkbut">
	{if $comments_cant == 0}
          {tr}Add Comment{/tr}
        {elseif $comments_cant == 1}
          <span class="highlight">{tr}1 Comment{/tr}</span>
        {else}
          <span class="highlight">{$comments_cant} {tr}Comments{/tr}</span>
        {/if}
      </a>
    </div>
   </td>
  {/if}

  {* don't show attachments button if feature disabled or no corresponding rights or no attached files and r/o*}

  {php} global $atts; global $smarty; $smarty->assign('atts_cnt', count($atts["data"])); {/php}
  {if $feature_wiki_attachments      == 'y'
  && ($tiki_p_wiki_view_attachments  == 'y'
  &&  count($atts) > 0
  ||  $tiki_p_wiki_attach_files      == 'y'
  ||  $tiki_p_wiki_admin_attachments == 'y')}
 
  <td>
    
    <div class="button2">
      <a title="Manage attachments for this page" href="javascript:document.location='#attachments';flip('attzone');" class="linkbut">

        {* display 'attach file' only if no attached files or 
         * only $tiki_p_wiki_attach_files perm
         *}
        {if $atts_cnt == 0
         || $tiki_p_wiki_attach_files == 'y'
         && $tiki_p_wiki_view_attachments == 'n'
         && $tiki_p_wiki_admin_attachments == 'n'}
          {tr}Attach File{/tr}
        {elseif $atts_cnt == 1}
          <span class="highlight">{tr}1 File Attached{/tr}</span>
        {else}
          <span class="highlight">{tr}{$atts_cnt} Files Attached{/tr}</span>
        {/if}
      </a>
    </div>
  </td>
  {/if}{* attachments *}

{/if}

</tr>
</table>
</div>

{if $wiki_extras eq 'y'}
{if $feature_wiki_attachments eq 'y'}
{include file=attachments.tpl}
{/if}
{/if}

{if $feature_wiki_comments}
{include file=comments.tpl}
{/if}
