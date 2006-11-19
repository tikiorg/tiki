{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/gemsi/tiki-page_bar.tpl,v 1.11 2006-11-19 20:14:56 mose Exp $ *}

<div id="page-bar">

{if $cached_page eq 'y'}<div class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;refresh=1" class="linkbut">{tr}refresh{/tr}</a></div>{/if}

{if !$lock and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox')}
<div class="button2"><a title="{$semUser}" href="tiki-editpage.php?page={$page|escape:"url"}{if $page_ref_id}&amp;page_ref_id={$page_ref_id}{/if}" 
class="linkbut">{if $beingEdited eq 'y'}<span class="highlight">{tr}edit{/tr}</span>{else}{tr}edit{/tr}{/if}</a></div>
{/if}

{if $page|lower ne 'sandbox'}

{if $tiki_p_remove eq 'y'}
<div class="button2"><a href="tiki-removepage.php?page={$page|escape:"url"}&amp;version=last" class="linkbut">{tr}remove{/tr}</a></div>
{/if}

{if $tiki_p_rename eq 'y'}
<div class="button2"><a href="tiki-rename_page.php?page={$page|escape:"url"}" class="linkbut">{tr}rename{/tr}</a></div>
{/if}
{if $lock and ($tiki_p_admin_wiki eq 'y' or ($user and ($user eq $page_user or $user eq "admin") and ($tiki_p_lock eq 'y') and ($feature_wiki_usrlock eq 'y')))}
<div class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=unlock" class="linkbut">{tr}unlock{/tr}</a></div>
{/if}
{if !$lock and ($tiki_p_admin_wiki eq 'y' or (($tiki_p_lock eq 'y') and ($feature_wiki_usrlock eq 'y')))}<div class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=lock" class="linkbut">{tr}lock{/tr}</a></div>

{/if}

{if $tiki_p_admin_wiki eq 'y'}
<div class="button2"><a href="tiki-pagepermissions.php?page={$page|escape:"url"}" class="linkbut">{tr}perms{/tr}</a></div>
{/if}

{if $feature_history eq 'y' and $tiki_p_edit eq 'y' and $tiki_p_wiki_view_history eq 'y'}
<div class="button2"><a href="tiki-pagehistory.php?page={$page|escape:"url"}" class="linkbut">{tr}history{/tr}</a></div>
{/if}
{/if}

{if $feature_likePages eq 'y'}
<div class="button2"><a href="tiki-likepages.php?page={$page|escape:"url"}" class="linkbut">{tr}similar{/tr}</a></div>
{/if}

{if $feature_wiki_undo eq 'y' and $canundo eq 'y'}
<div class="button2"><a href="tiki-index.php?page={$page|escape:"url"}&amp;undo=1" class="linkbut">{tr}undo{/tr}</a></div>
{/if}

{if $wiki_uses_slides eq 'y'}
{if $show_slideshow eq 'y'}
<div class="button2"><a href="tiki-slideshow.php?page={$page|escape:"url"}" class="linkbut">{tr}slides{/tr}</a></div>
{elseif $structure eq 'y'}
<div class="button2"><a href="tiki-slideshow2.php?page_ref_id={$page_info.page_ref_id}" class="linkbut">{tr}slides{/tr}</a></div>
{/if}
{/if}

{if $tiki_p_admin_wiki eq 'y'}
<div class="button2"><a href="tiki-export_wiki_pages.php?page={$page|escape:"url"}" class="linkbut">{tr}export{/tr}</a></div>
{/if}

{if $feature_wiki_print eq 'y'}
<div class="button2"><a href="tiki-print.php?page={$page|escape:"url"}" class="linkbut">{tr}print{/tr}</a></div>
{/if}

{if $feature_wiki_pdf eq 'y'}
<div class="button2"><a href="tiki-config_pdf.php?page={$page|escape:"url"}" class="linkbut">{tr}pdf{/tr}</a></div>
{/if}

{if $feature_wiki_discuss eq 'y'}
<div class="button2"><a href="tiki-view_forum.php?forumId={$wiki_forum_id}&amp;comments_postComment=post&amp;comments_title={$page|escape:"url"}&amp;comments_data={"Use this thread to discuss the [tiki-index.php?page="}{$page|escape:"url"}{"|"}{$page|escape:"url"}{"] page."}&amp;comment_topictype=n" class="linkbut">{tr}discuss{/tr}</a></div>
{/if}

{if $show_page == 'y'}
  {if $feature_wiki_comments == 'y'
	&& $tiki_p_wiki_view_comments == 'y'
  && ($tiki_p_read_comments  == 'y'
  ||  $tiki_p_post_comments  == 'y'
  ||  $tiki_p_edit_comments  == 'y')}
<div class="button2">
<a href="#" onclick="javascript:flip('comzone');flip('comzone_close','inline');return false;" class="linkbut">
{if $comments_cant == 0}{tr}add comment{/tr}{elseif $comments_cant == 1}<span class="highlight">{tr}1 comment{/tr}</span>{else}<span class="highlight">{$comments_cant} {tr}comments{/tr}</span>{/if}
<span id="comzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y'}inline{else}none{/if};">({tr}close{/tr})</span>
</a></div>
{/if}

  {* don't show attachments button if feature disabled or no corresponding rights or no attached files and r/o*}

{php} global $atts; global $smarty; $smarty->assign('atts_cnt', count($atts["data"])); {/php}
{if $feature_wiki_attachments      == 'y'
  && ($tiki_p_wiki_view_attachments  == 'y'
  &&  count($atts) > 0
  ||  $tiki_p_wiki_attach_files      == 'y'
  ||  $tiki_p_wiki_admin_attachments == 'y')}
 
<div class="button2">
<a href="#" onclick="javascript:flip('attzone');flip('attzone_close','inline');return false;" class="linkbut">
{if $atts_cnt == 0
         || $tiki_p_wiki_attach_files == 'y'
         && $tiki_p_wiki_view_attachments == 'n'
         && $tiki_p_wiki_admin_attachments == 'n'}
          {tr}attach file{/tr}
        {elseif $atts_cnt == 1}
          <span class="highlight">{tr}1 file attached{/tr}</span>
        {else}
          <span class="highlight">{tr}{$atts_cnt} files attached{/tr}</span>
        {/if}
			<span id="attzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_attzone) and $smarty.session.tiki_cookie_jar.show_attzone eq 'y'}inline{else}none{/if};">({tr}close{/tr})</span>
      </a>
    </div>
  {/if}{* attachments *}

{/if}
{if $feature_multilingual eq 'y' and $tiki_p_edit eq 'y'  and !$lock}
     <div class="button2"><a href="tiki-edit_translation.php?page={$page|escape:'url'}" class="linkbut">{tr}translation{/tr}</a></div>
  {/if}

</div>

{if $wiki_extras eq 'y'}
{if $feature_wiki_attachments eq 'y' and $tiki_p_wiki_view_attachments eq 'y'}
{include file=attachments.tpl}
{/if}
{/if}

{if $feature_wiki_comments eq 'y' and $tiki_p_wiki_view_comments eq 'y'}
{include file=comments.tpl}
{/if}
