{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/vidiki/tiki-page_bar.tpl,v 1.5 2006-11-19 20:14:56 mose Exp $ *}

<hr/>
<div id="page-bar">
<ul>

{* Check that page is not locked and edit permission granted. SandBox can be adited w/o perm *}
{if (!$lock and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox')) or $tiki_p_admin_wiki eq 'y'}
    <li>
      <a title="{$semUser}" href="tiki-editpage.php?page={$page|escape:"url"}{if $page_ref_id}&amp;page_ref_id={$page_ref_id}{/if}"
        {if $beingEdited eq 'y'}
          class="active">{tr}edit{/tr}
        {else}
          class="">{tr}edit{/tr}
        {/if}
      </a>
    </li>
{/if}

{if $page|lower ne 'sandbox'}

{if $tiki_p_remove eq 'y' && $editable}
<li><a href="tiki-removepage.php?page={$page|escape:"url"}&amp;version=last" class="">{tr}remove{/tr}</a></li>
{/if}
{if $tiki_p_rename eq 'y' && $editable}
<li><a href="tiki-rename_page.php?page={$page|escape:"url"}" class="">{tr}rename{/tr}</a></li>
{/if}
{if $lock and ($tiki_p_admin_wiki eq 'y' or ($user and ($user eq $page_user or $user eq "admin") and ($tiki_p_lock eq 'y') and ($feature_wiki_usrlock eq 'y')))}
<li><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=unlock" class="">{tr}unlock{/tr}</a></li>
{/if}
{if !$lock and ($tiki_p_admin_wiki eq 'y' or (($tiki_p_lock eq 'y') and ($feature_wiki_usrlock eq 'y')))}
<li><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=lock" class="">{tr}lock{/tr}</a></li>
{/if}
{if $tiki_p_admin_wiki eq 'y'}
<li><a href="tiki-pagepermissions.php?page={$page|escape:"url"}" class="">{tr}perms{/tr}</a></li>
{/if}

{if $feature_history eq 'y'}
<li><a href="tiki-pagehistory.php?page={$page|escape:"url"}" class="">{tr}history{/tr}</a></li>
{/if}
{/if}

{if $feature_likePages eq 'y'}
<li><a href="tiki-likepages.php?page={$page|escape:"url"}" class="">{tr}similar{/tr}</a></li>
{/if}
{if $feature_wiki_undo eq 'y' and $canundo eq 'y'}
<li><a href="tiki-index.php?page={$page|escape:"url"}&amp;undo=1" class="">{tr}undo{/tr}</a></li>
{/if}
{if $wiki_uses_slides eq 'y'}
{if $show_slideshow eq 'y'}
<li><a href="tiki-slideshow.php?page={$page|escape:"url"}" class="">{tr}slides{/tr}</a></li>
{elseif $structure eq 'y'}
<li><a href="tiki-slideshow2.php?page_ref_id={$page_info.page_ref_id}" class="">{tr}slides{/tr}</a></li>
{/if}
{/if}
{if $feature_wiki_export eq 'y' and $tiki_p_admin_wiki eq 'y'}
<li><a href="tiki-export_wiki_pages.php?page={$page|escape:"url"}" class="">{tr}export{/tr}</a></li>
{/if}
{if $feature_wiki_discuss eq 'y'}
<li><a href="tiki-view_forum.php?forumId={$wiki_forum_id}&amp;comments_postComment=post&amp;comments_title={$page|escape:"url"}&amp;comments_data={"Use this thread to discuss the [tiki-index.php?page="}{$page|escape:"url"}{"|"}{$page|escape:"url"}{"] page."}&amp;comment_topictype=n" class="">{tr}discuss{/tr}</a></li>
{/if}


{if $edit_page eq 'y'} {* Show this button only in editing mode *}
  <li>
      <a href="#" onclick="javascript:flip('edithelpzone'); return false;" class="">{tr}wiki help{/tr}</a>
  </li>
{/if}

{if $show_page == 'y'} {* Show this buttons only if page view mode *}

  {* don't show comments if feature disabled or not enough rights *}
  {if $feature_wiki_comments == 'y'
	&& $tiki_p_wiki_view_comments == 'y'
  && (($tiki_p_read_comments  == 'y'
  && $comments_cant != 0)
  ||  $tiki_p_post_comments  == 'y'
  ||  $tiki_p_edit_comments  == 'y')}
   <li>
		<a href="#" onclick="javascript:flip('comzone');flip('comzone_close','inline');return false;">
	{if $comments_cant == 0}
          {tr}add comment{/tr}
        {elseif $comments_cant == 1}
          <span class="highlight">{tr}1 comment{/tr}</span>
        {else}
          <span class="highlight">{$comments_cant} {tr}comments{/tr}</span>
        {/if}
			<span id="comzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y'}inline{else}none{/if};">({tr}close{/tr})</span>
      </a>
   </li>
  {/if}

  {* don't show attachments button if feature disabled or no corresponding rights or no attached files and r/o*}

  {php} global $atts; global $smarty; $smarty->assign('atts_cnt', count($atts["data"])); {/php}
  {if $feature_wiki_attachments      == 'y'
  && ($tiki_p_wiki_view_attachments  == 'y'
  &&  count($atts) > 0
  ||  $tiki_p_wiki_attach_files      == 'y'
  ||  $tiki_p_wiki_admin_attachments == 'y')}

  <li>
    
      <a href="#" onclick="javascript:flip('attzone');flip('attzone_close','inline');return false;">

        {* display 'attach file' only if no attached files or
         * only $tiki_p_wiki_attach_files perm
         *}
        {if $atts_cnt == 0
         || $tiki_p_wiki_attach_files == 'y'
         && $tiki_p_wiki_view_attachments == 'n'
         && $tiki_p_wiki_admin_attachments == 'n'}
          class="">{tr}attach file{/tr}
        {elseif $atts_cnt == 1}
          class="active">{tr}1 file attached{/tr}
        {else}
          class="active">{tr}{$atts_cnt} files attached{/tr}
        {/if}
				<span id="attzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_attzone) and $smarty.session.tiki_cookie_jar.show_attzone eq 'y'}inline{else}none{/if};">({tr}close{/tr})</span>
      </a>
  </li>
  {/if}{* attachments *}

  {if $feature_multilingual eq 'y' and $tiki_p_edit eq 'y' and !$lock}
     <li><a href="tiki-edit_translation.php?page={$page|escape:'url'}" class="">{tr}translation{/tr}</a></li>
  {/if}

{/if}

</ul>
</div>
<br />
{if $wiki_extras eq 'y' && $feature_wiki_attachments eq 'y' and $tiki_p_wiki_view_attachments eq 'y'}
{include file=attachments.tpl}
{/if}

{if $feature_wiki_comments eq 'y' and $tiki_p_wiki_view_comments == 'y'}
{include file=comments.tpl}
{/if}
