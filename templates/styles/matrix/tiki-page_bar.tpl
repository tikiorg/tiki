{* $Header: *}

<hr/>
<div id="page-bar">
<table>
<tr>
{if !$lock}
{if $tiki_p_edit eq 'y' or $page eq 'SandBox'}
{if $beingEdited eq 'y'}
<td><div class="button2" ><a title="{$semUser}" style="background: #FFAAAA;" href="tiki-editpage.php?page={$page|escape:"url"}" class="linkbut">{tr}edit{/tr}</a></div></td>
{else}
<td><div class="button2"><a href="tiki-editpage.php?page={$page|escape:"url"}" class="linkbut">{tr}edit{/tr}</a></div></td>
{/if}
{/if}
{/if}
{if $page ne 'SandBox'}
{if $tiki_p_remove eq 'y'}
<td><div class="button2"><a href="tiki-removepage.php?page={$page|escape:"url"}&amp;version=last" class="linkbut">{tr}remove{/tr}</a></div></td>
{/if}
{if $tiki_p_rename eq 'y'}
<td><div class="button2"><a href="tiki-rename_page.php?page={$page|escape:"url"}" class="linkbut">{tr}rename{/tr}</a></div></td>
{/if}
{/if}
{if $page ne 'SandBox'}
{if $tiki_p_admin_wiki eq 'y' or ($user and ($user eq $page_user) and ($tiki_p_lock eq 'y') and ($feature_wiki_usrlock eq 'y'))}
{if $lock}
<td><div class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=unlock" class="linkbut">{tr}unlock{/tr}</a></div></td>
{else}
<td><div class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=lock" class="linkbut">{tr}lock{/tr}</a></div></td>
{/if}
{/if}
{if $tiki_p_admin_wiki eq 'y'}
<td><div class="button2"><a href="tiki-pagepermissions.php?page={$page|escape:"url"}" class="linkbut">{tr}perms{/tr}</a></div></td>
{/if}
{/if}

{if $page ne 'SandBox'}
{if $feature_history eq 'y'}
<td><div class="button2"><a href="tiki-pagehistory.php?page={$page|escape:"url"}" class="linkbut">{tr}history{/tr}</a></div></td>
{/if}
{/if}

{if $feature_likePages eq 'y'}
<td><div class="button2"><a href="tiki-likepages.php?page={$page|escape:"url"}" class="linkbut">{tr}similar{/tr}</a></div></td>
{/if}
{if $feature_wiki_undo eq 'y' and $canundo eq 'y'}
<td><div class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;undo=1" class="linkbut">{tr}undo{/tr}</a></div></td>
{/if}
{if $show_slideshow eq 'y'}
<td><div class="button2"><a href="tiki-slideshow.php?page={$page|escape:"url"}" class="linkbut">{tr}slides{/tr}</a></div></td>
{elseif $structure eq 'y'}
<td><div class="button2"><a href="tiki-slideshow2.php?page={$page|escape:"url"}" class="linkbut">{tr}slides{/tr}</a></div></td>
{/if}
<td><div class="button2"><a href="tiki-export_wiki_pages.php?page={$page|escape:"url"}" class="linkbut">{tr}export{/tr}</a></div></td>
{if $feature_wiki_discuss eq 'y'}
<td><div class="button2"><a href="tiki-view_forum.php?forumId={$wiki_forum_id}&amp;comments_postComment=post&amp;comments_title={$page|escape:"url"}&amp;comments_data={"Use this thread to discuss the [tiki-index.php?page="}{$page|escape:"url"}{"|"}{$page|escape:"url"}{"] page."}&amp;comment_topictype=n" class="linkbut">{tr}discuss{/tr}</a></div></td>
{/if}


{if $edit_page eq 'y'} {* Show this button only in editing mode *}
  <td>
    <div class="button2">
      <a href="javascript:toggle('edithelpzone');" class="linkbut">{tr}wiki quick help{/tr}</a>
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
      <a title="{$comments_cant} {tr}comments{/tr}" href="javascript:toggle('comzone{if $comments_show eq 'y'}open{/if}');" class="linkbut">{tr}comments{/tr}</a>
    </div>
   </td>
  {/if}

  {* don't show attachments button if feature disabled or no corresponding rights*}
  {if $feature_wiki_attachments      == 'y'
  && ($tiki_p_wiki_view_attachments  == 'y'
  ||  $tiki_p_wiki_attach_files      == 'y'
  ||  $tiki_p_wiki_admin_attachments == 'y')}
 
  <td>
    <div class="button2">
      <a href="javascript:toggle('attzone');" class="linkbut">{tr}attachments{/tr}</a>
    </div>
  </td>
  {/if}{* attachments *}

{/if}

</tr>
</table>
</div>
