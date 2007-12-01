{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-page_bar.tpl,v 1.82.2.4 2007-12-01 15:33:25 mose Exp $ *}

{strip}
<hr/>

<div id="page-bar">
{if $edit_page eq 'y'}
  {if $wysiwyg eq 'n' or $prefs.wysiwyg_wiki_parsed eq 'y' or $prefs.wysiwyg_wiki_semi_parsed eq 'y'} {* Show this button only in normal editing mode *}
    <span class="button2">
      <a href="#edithelp" onclick="javascript:flip('edithelpzone'); return true;" name="edithelp" class="linkbut">{tr}Wiki Help{/tr}</a>
    </span>
  {/if}
{else}

{* Check that page is not locked and edit permission granted. SandBox can be edited w/o perm *}
{if ($editable and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox')) or $tiki_p_admin_wiki eq 'y' or $canEditStaging eq 'y'}
      <span class="button2" >
      <a title="{$semUser}" {ajax_href template="tiki-editpage.tpl" htmlelement="tiki-center"}tiki-editpage.php?page={if $needsStaging eq 'y'}{$stagingPageName|escape:"url"}{else}{$page|escape:"url"}{/if}{if $page_ref_id and $needsStaging neq 'y'}&amp;page_ref_id={$page_ref_id}{/if}{/ajax_href} class="linkbut">      		
        {if $beingEdited eq 'y'}
          <span class="highlight">{tr}Edit{/tr}</span>
        {else}
          {tr}Edit{/tr}
        {/if}
        </a>
      </span>
{else}
    {if $prefs.feature_source eq 'y' and $tiki_p_wiki_view_source eq 'y'}
      <span class="button2" >
      <a href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;source=0" class="linkbut">
        {tr}Source{/tr}
      </a>
      </span>
    {/if}
{/if}

{if $page|lower ne 'sandbox'}

{if $tiki_p_remove eq 'y' && $editable}
<span class="button2"><a href="tiki-removepage.php?page={$page|escape:"url"}&amp;version=last" class="linkbut">{tr}Remove{/tr}</a></span>
{/if}
{if $tiki_p_rename eq 'y' && $editable}
<span class="button2"><a href="tiki-rename_page.php?page={if $beingStaged eq 'y'}{$approvedPageName|escape:"url"}{else}{$page|escape:"url"}{/if}" class="linkbut">{tr}Rename{/tr}</a></span>
{/if}
{if $lock and ($tiki_p_admin_wiki eq 'y' or ($user and ($user eq $page_user or $user eq "admin") and ($tiki_p_lock eq 'y') and ($prefs.feature_wiki_usrlock eq 'y')))}
<span class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=unlock" class="linkbut">{tr}Unlock{/tr}</a></span>
{/if}
{if !$lock and ($tiki_p_admin_wiki eq 'y' or (($tiki_p_lock eq 'y') and ($prefs.feature_wiki_usrlock eq 'y')))}
<span class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=lock" class="linkbut">{tr}Lock{/tr}</a></span>
{/if}
{if $tiki_p_share_page eq 'y'}
<span class="button2"><a href="tiki-p_share_page.php?objectId={$page|escape:"url"}&amp;objectType=wiki+page" class="linkbut">{tr}Share Page{/tr}</a></span>
{/if}
{if $tiki_p_admin_wiki eq 'y'}
<span class="button2"><a href="tiki-objectpermissions.php?objectId={$page|escape:"url"}&amp;objectName={$page|escape:"url"}&amp;objectType=wiki+page&amp;permType=wiki" class="linkbut">{tr}Perms{/tr}</a></span>
{/if}

{if $prefs.feature_history eq 'y' and $tiki_p_wiki_view_history eq 'y'}
<span class="button2"><a href="tiki-pagehistory.php?page={$page|escape:"url"}" class="linkbut">{tr}History{/tr}</a></span>
{/if}
{/if}

{if $prefs.feature_likePages eq 'y'}
<span class="button2"><a href="tiki-likepages.php?page={$page|escape:"url"}" class="linkbut">{tr}Similar{/tr}</a></span>
{/if}
{if $prefs.feature_wiki_undo eq 'y' and $canundo eq 'y'}
<span class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;undo=1" class="linkbut">{tr}Undo{/tr}</a></span>
{/if}
{if $prefs.feature_wiki_make_structure eq 'y' and $tiki_p_edit_structures eq 'y' and $editable and $structure eq 'n' and count($showstructs) eq 0}
<span class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;convertstructure=1" class="linkbut">{tr}Make Structure{/tr}</a></span>
{/if}
{if $prefs.wiki_uses_slides eq 'y'}
{if $show_slideshow eq 'y'}
<span class="button2"><a href="tiki-slideshow.php?page={$page|escape:"url"}" class="linkbut">{tr}Slides{/tr}</a></span>
{elseif $structure eq 'y'}
<span class="button2"><a href="tiki-slideshow2.php?page_ref_id={$page_info.page_ref_id}" class="linkbut">{tr}Slides{/tr}</a></span>
{/if}
{/if}
{if $prefs.feature_wiki_export eq 'y' and $tiki_p_admin_wiki eq 'y'}
<span class="button2"><a href="tiki-export_wiki_pages.php?page={$page|escape:"url"}" class="linkbut">{tr}Export{/tr}</a></span>
{/if}
{if $prefs.feature_wiki_discuss eq 'y'}
<span class="button2"><a href="tiki-view_forum.php?forumId={$prefs.wiki_forum_id}&amp;comments_postComment=post&amp;comments_title={$page|escape:"url"}&amp;comments_data={$wiki_discussion_string|escape:"url"}: {"[tiki-index.php?page="}{$page|escape:"url"}{"|"}{$page|escape:"url"}{"]"}&amp;comment_topictype=n" class="linkbut">{tr}Discuss{/tr}</a></span>
{/if}

{if $show_page == 'y'} {* Show this buttons only if page view mode *}

  {* don't show comments if feature disabled or not enough rights *}
  {if $prefs.feature_wiki_comments == 'y'
	&& $tiki_p_wiki_view_comments == 'y'
  && (($tiki_p_read_comments  == 'y'
  && $comments_cant != 0)
  ||  $tiki_p_post_comments  == 'y'
  ||  $tiki_p_edit_comments  == 'y')}
  {strip}
  <span class="button2">
    <a href="#comments" onclick="javascript:flip('comzone');flip('comzone_close','inline');return false;" class="linkbut">
      {if $comments_cant == 0 or ($tiki_p_read_comments  == 'n' and $tiki_p_post_comments  == 'y')}
        {tr}Add Comment{/tr}
      {elseif $comments_cant == 1}
        <span class="highlight">{tr}1 comment{/tr}</span>
      {else}
        <span class="highlight">{$comments_cant} {tr}comments{/tr}</span>
      {/if}
    <span id="comzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y'}inline{else}none{/if};">({tr}Hide{/tr})</span>
    </a>
  </span>
  {/strip}   
{/if}

{* don't show attachments button if feature disabled or no corresponding rights or no attached files and r/o*}

{if $prefs.feature_wiki_attachments == 'y'
  && (
        $tiki_p_wiki_view_attachments  == 'y'
      &&  count($atts) > 0
      ||  $tiki_p_wiki_attach_files      == 'y'
      ||  $tiki_p_wiki_admin_attachments == 'y'
    )
}

{strip}
  <span class="button2">
    <a href="#attachments" onclick="javascript:flip('attzone');flip('attzone_close','inline');return false;" class="linkbut">
    {if $atts|@count == 0 || $tiki_p_wiki_attach_files == 'y' && $tiki_p_wiki_view_attachments == 'n' && $tiki_p_wiki_admin_attachments == 'n'}
      {tr}Attach File{/tr}
    {elseif $atts|@count == 1}
      <span class="highlight">{tr}1 file attached{/tr}</span>
    {else}
      <span class="highlight">{tr}{$atts|@count} files attached{/tr}</span>
    {/if}
    <span id="attzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_attzone) and $smarty.session.tiki_cookie_jar.show_attzone eq
'y'}inline{else}none{/if};">({tr}Hide{/tr})
    </span>
    </a>
  </span>
{/strip}
{/if}{* attachments *}

  {if $prefs.feature_multilingual eq 'y' and $tiki_p_edit eq 'y' and !$lock}
     <span class="button2"><a href="tiki-edit_translation.php?page={$page|escape:'url'}" class="linkbut">{tr}Translation{/tr}</a></span>
  {/if}
{/if}

{/if}
</div>

{if $wiki_extras eq 'y' && $prefs.feature_wiki_attachments eq 'y' and $tiki_p_wiki_view_attachments eq 'y'}
<a name="attachments"></a>
{include file=attachments.tpl}
{/if}

{if $prefs.feature_wiki_comments eq 'y' and $tiki_p_wiki_view_comments == 'y' and $edit_page ne 'y'}
<a name="comments"></a>
{include file=comments.tpl}
{/if}

{/strip}
