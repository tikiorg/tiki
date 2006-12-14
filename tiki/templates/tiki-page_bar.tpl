{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-page_bar.tpl,v 1.56 2006-12-14 16:40:33 sylvieg Exp $ *}

<hr/>
<div id="page-bar">
  <table>
    <tr>

{if $edit_page eq 'y'} {* Show this button only in editing mode *}
  <td>
    <div class="button2">
      <a href="#edithelp" onclick="javascript:flip('edithelpzone'); return true;" name="edithelp" class="linkbut">{tr}wiki help{/tr}</a>
    </div>
  </td>
{else}

{* Check that page is not locked and edit permission granted. SandBox can be edited w/o perm *}
{if ($editable and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox')) or $tiki_p_admin_wiki eq 'y'}
    <td>
      <div class="button2" >
      <a title="{$semUser}" {ajax_href template="tiki-editpage.tpl" htmlelement="tiki-center"}tiki-editpage.php?page={$page|escape:"url"}{if $page_ref_id}&amp;page_ref_id={$page_ref_id}{/if}{/ajax_href} class="linkbut">
        {if $beingEdited eq 'y'}
          <span class="highlight">{tr}edit{/tr}</span>
        {else}
          {tr}edit{/tr}
        {/if}
      </a>
      </div>
    </td>
{else}
    {if $feature_source eq 'y' and $tiki_p_wiki_view_source eq 'y'}
    <td>
      <div class="button2" >
      <a href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;source=0" class="linkbut">
        {tr}source{/tr}
      </a>
      </div>
    </td>
    {/if}
{/if}

{if $page|lower ne 'sandbox'}

{if $tiki_p_remove eq 'y' && $editable}
<td><div class="button2"><a href="tiki-removepage.php?page={$page|escape:"url"}&amp;version=last" class="linkbut">{tr}remove{/tr}</a></div></td>
{/if}
{if $tiki_p_rename eq 'y' && $editable}
<td><div class="button2"><a href="tiki-rename_page.php?page={$page|escape:"url"}" class="linkbut">{tr}rename{/tr}</a></div></td>
{/if}
{if $lock and ($tiki_p_admin_wiki eq 'y' or ($user and ($user eq $page_user or $user eq "admin") and ($tiki_p_lock eq 'y') and ($feature_wiki_usrlock eq 'y')))}
<td><div class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=unlock" class="linkbut">{tr}unlock{/tr}</a></div></td>
{/if}
{if !$lock and ($tiki_p_admin_wiki eq 'y' or (($tiki_p_lock eq 'y') and ($feature_wiki_usrlock eq 'y')))}
<td><div class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=lock" class="linkbut">{tr}lock{/tr}</a></div></td>
{/if}
{if $tiki_p_admin_wiki eq 'y'}
<td><div class="button2"><a href="tiki-pagepermissions.php?page={$page|escape:"url"}" class="linkbut">{tr}perms{/tr}</a></div></td>
{/if}

{if $feature_history eq 'y' and $tiki_p_wiki_view_history eq 'y'}
<td><div class="button2"><a href="tiki-pagehistory.php?page={$page|escape:"url"}" class="linkbut">{tr}history{/tr}</a></div></td>
{/if}
{/if}

{if $feature_likePages eq 'y'}
<td><div class="button2"><a href="tiki-likepages.php?page={$page|escape:"url"}" class="linkbut">{tr}similar{/tr}</a></div></td>
{/if}
{if $feature_wiki_undo eq 'y' and $canundo eq 'y'}
<td><div class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;undo=1" class="linkbut">{tr}undo{/tr}</a></div></td>
{/if}
{if $wiki_uses_slides eq 'y'}
{if $show_slideshow eq 'y'}
<td><div class="button2"><a href="tiki-slideshow.php?page={$page|escape:"url"}" class="linkbut">{tr}slides{/tr}</a></div></td>
{elseif $structure eq 'y'}
<td><div class="button2"><a href="tiki-slideshow2.php?page_ref_id={$page_info.page_ref_id}" class="linkbut">{tr}slides{/tr}</a></div></td>
{/if}
{/if}
{if $feature_wiki_export eq 'y' and $tiki_p_admin_wiki eq 'y'}
<td><div class="button2"><a href="tiki-export_wiki_pages.php?page={$page|escape:"url"}" class="linkbut">{tr}export{/tr}</a></div></td>
{/if}
{if $feature_wiki_discuss eq 'y'}
<td><div class="button2"><a href="tiki-view_forum.php?forumId={$wiki_forum_id}&amp;comments_postComment=post&amp;comments_title={$page|escape:"url"}&amp;comments_data={$wiki_discussion_string|escape:"url"}: {"[tiki-index.php?page="}{$page|escape:"url"}{"|"}{$page|escape:"url"}{"]"}&amp;comment_topictype=n" class="linkbut">{tr}discuss{/tr}</a></div></td>
{/if}


{if $show_page == 'y'} {* Show this buttons only if page view mode *}

  {* don't show comments if feature disabled or not enough rights *}
  {if $feature_wiki_comments == 'y'
	&& $tiki_p_wiki_view_comments == 'y'
  && (($tiki_p_read_comments  == 'y'
  && $comments_cant != 0)
  ||  $tiki_p_post_comments  == 'y'
  ||  $tiki_p_edit_comments  == 'y')}
<td><div class="button2">
<a href="#comment" onclick="javascript:flip('comzone');flip('comzone_close','inline');return false;" class="linkbut">
{if $comments_cant == 0 or ($tiki_p_read_comments  == 'n' and $tiki_p_post_comments  == 'y')}
{tr}add comment{/tr}
{elseif $comments_cant == 1}
<span class="highlight">{tr}1 comment{/tr}</span>
{else}
<span class="highlight">{$comments_cant} {tr}comments{/tr}</span>
{/if}
<span id="comzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y'}inline{else}none{/if};">({tr}close{/tr})</span>
</a>
    </div>
   </td>
  {/if}

  {* don't show attachments button if feature disabled or no corresponding rights or no attached files and r/o*}

    {if $feature_wiki_attachments      == 'y'
  && ($tiki_p_wiki_view_attachments  == 'y'
  &&  count($atts) > 0
  ||  $tiki_p_wiki_attach_files      == 'y'
  ||  $tiki_p_wiki_admin_attachments == 'y')}

  <td>
    <div class="button2">
<a href="#" onclick="javascript:flip('attzone');flip('attzone_close','inline');return false;" class="linkbut">
{if $atts|@count == 0 || $tiki_p_wiki_attach_files == 'y' && $tiki_p_wiki_view_attachments == 'n' && $tiki_p_wiki_admin_attachments == 'n'}
{tr}attach file{/tr}
{elseif $atts|@count == 1}
<span class="highlight">{tr}1 file attached{/tr}</span>
{else}
<span class="highlight">{tr}{$atts|@count} files attached{/tr}</span>
{/if}
<span id="attzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_attzone) and $smarty.session.tiki_cookie_jar.show_attzone eq 'y'}inline{else}none{/if};">({tr}close{/tr})</span>
</a>
    </div>
  </td>
  {/if}{* attachments *}

  {if $feature_multilingual eq 'y' and $tiki_p_edit eq 'y' and !$lock}
     <td><div class="button2"><a href="tiki-edit_translation.php?page={$page|escape:'url'}" class="linkbut">{tr}translation{/tr}</a></div></td>
  {/if}
{/if}

{/if}
</tr>
</table>
</div>

{if $wiki_extras eq 'y' && $feature_wiki_attachments eq 'y' and $tiki_p_wiki_view_attachments eq 'y'}
{include file=attachments.tpl}
{/if}

{if $feature_wiki_comments eq 'y' and $tiki_p_wiki_view_comments == 'y'}
{include file=comments.tpl}
{/if}

