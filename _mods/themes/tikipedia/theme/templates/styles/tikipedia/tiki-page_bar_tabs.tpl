<div class="tabt2">
	{if $print_page ne 'y'}
		{if $editable and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox') and $beingEdited ne 'y'}
			<span class="tabbut"><a title="{tr}edit{/tr}" class="tablink" href="tiki-editpage.php?page={$page|escape:"url"}" class="tablink">{tr}edit{/tr}</a></span>
		{/if}
		{if $cached_page eq 'y'}
			<span class="tabbut"><a title="{tr}refresh{/tr}" class="tablink" href="tiki-index.php?page={$page|escape:"url"}&amp;refresh=1">{tr}refresh{/tr}</a></span>
		{/if}
		{if $page|lower ne 'sandbox'}
			{if $tiki_p_remove eq 'y' && $editable}
				<span class="tabbut"><a href="tiki-removepage.php?page={$page|escape:"url"}&amp;version=last" class="tablink">{tr}remove{/tr}</a></span>
			{/if}
			{if $tiki_p_rename eq 'y' && $editable}
				<span class="tabbut"><a href="tiki-rename_page.php?page={$page|escape:"url"}" class="tablink">{tr}rename{/tr}</a></span>
			{/if}
			{if $lock and ($tiki_p_admin_wiki eq 'y' or ($user and ($user eq $page_user or $user eq "admin") and ($tiki_p_lock eq 'y') and ($feature_wiki_usrlock eq 'y')))}
				<span class="tabbut"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=unlock" class="tablink">{tr}unlock{/tr}</a></span>
			{/if}
			{if !$lock and ($tiki_p_admin_wiki eq 'y' or (($tiki_p_lock eq 'y') and ($feature_wiki_usrlock eq 'y')))}
				<span class="tabbut"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=lock" class="tablink">{tr}lock{/tr}</a></span>
			{/if}
			{if $tiki_p_admin_wiki eq 'y'}
				<span class="tabbut"><a href="tiki-pagepermissions.php?page={$page|escape:"url"}" class="tablink">{tr}perms{/tr}</a></span>
			{/if}
			{if $feature_history eq 'y' and $tiki_p_wiki_view_history eq 'y'}
				<span class="tabbut"><a href="tiki-pagehistory.php?page={$page|escape:"url"}" class="tablink">{tr}history{/tr}</a></span>
			{/if}
			{if $feature_wiki_undo eq 'y' and $canundo eq 'y'}
				<span class="tabbut"><a href="tiki-index.php?page={$page|escape:"url"}&amp;undo=1" class="tablink">{tr}undo{/tr}</a></span>
			{/if}
			{if $wiki_uses_slides eq 'y'}
				{if $show_slideshow eq 'y'}
					<span class="tabbut"><a href="tiki-slideshow.php?page={$page|escape:"url"}" class="tablink">{tr}slides{/tr}</a></span>
				{/if}
			{/if}	
		{*	{if $feature_wiki_export eq 'y' and $tiki_p_admin_wiki eq 'y'
				<span class="tabbut"><a href="tiki-export_wiki_pages.php?page={$page|escape:"url"}" class="tablink">{tr}export{/tr}</a></span>
			{/if}*}
			{if $feature_wiki_discuss eq 'y'}
				<span class="tabbut"><a href="tiki-view_forum.php?forumId={$wiki_forum_id}&amp;comments_postComment=post&amp;comments_title={$page|escape:"url"}&amp;comments_data={$wiki_discussion_string|escape:"url"}: {"[tiki-index.php?page="}{$page|escape:"url"}{"|"}{$page|escape:"url"}{"]"}&amp;comment_topictype=n" class="tablink">{tr}discuss{/tr}</a></span>
			{/if}
		{/if}
	{/if}

	{if $show_page == 'y'} {* Show this buttons only if page view mode *}
  	{* don't show comments if feature disabled or not enough rights *}
  		{if $feature_wiki_comments == 'y'
			&& $tiki_p_wiki_view_comments == 'y'
			&& (($tiki_p_read_comments  == 'y'
			&& $comments_cant != 0)
			||  $tiki_p_post_comments  == 'y'
 			||  $tiki_p_edit_comments  == 'y')}
  				 <span class="tabbut">
  				 <a href="{if $comments_show ne 'y'}tiki-index.php?page={$page|escape:"url"}&amp;comzone=show#comments{else}tiki-index.php?page={$page|escape:"url"}&amp;comzone=hide{/if}" onclick="javascript:flip('comzone{if $comments_show eq 'y'}open{/if}');{if $comments_show eq 'y'} return false;{/if}"
         			class="tablink">
		{if $comments_cant == 0}
        		  {tr}add comment{/tr}
      		{elseif $comments_cant == 1}
      			 {tr}1 comment{/tr}
      		 {else}
      			 {$comments_cant} {tr}comments{/tr}
      		{/if}
      			</a></span>
      	{/if}

  {* don't show attachments button if feature disabled or no corresponding rights or no attached files and r/o*}

  {php} global $atts; global $smarty; $smarty->assign('atts_cnt', count($atts["data"])); {/php}
	{if $feature_wiki_attachments      == 'y'
	&& ($tiki_p_wiki_view_attachments  == 'y'
	&&  count($atts) > 0
	||  $tiki_p_wiki_attach_files      == 'y'
	||  $tiki_p_wiki_admin_attachments == 'y')}
		<span class="tabbut">
		<a href="#attachments" onclick="javascript:flip('attzone');" class="tablink">
		{* display 'attach file' only if no attached files or
         	* only $tiki_p_wiki_attach_files perm
         	*}
        	{if $atts_cnt == 0
        	|| $tiki_p_wiki_attach_files == 'y'
         	&& $tiki_p_wiki_view_attachments == 'n'
         	&& $tiki_p_wiki_admin_attachments == 'n'}
         		{tr}attach file{/tr}
         	{elseif $atts_cnt == 1}
         		{tr}1 file attached{/tr}
         	{else}
         		{tr}{$atts_cnt} files attached{/tr}
         	{/if}
         	</a></span>
         {/if}{* attachments *}
{/if}
</div>