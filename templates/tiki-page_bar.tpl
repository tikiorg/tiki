{if !isset($versioned) or not $versioned}
	{strip}

	{assign var=thispage value=$page|escape:"url"}

	{if isset($beingStaged) and $beingStaged eq 'y'}
		{assign var=thisapprovedPageName value=$approvedPageName|escape:"url"}
	{/if}

	{capture assign=page_bar}
		{if $edit_page neq 'y'}
			{* Check that page is not locked and edit permission granted. SandBox can be edited w/o perm *}
			{if ($editable and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox') or ((!isset($user) or !$user) and $prefs.wiki_encourage_contribution eq 'y')) or $tiki_p_admin_wiki eq 'y' or (isset($canEditStaging) and $canEditStaging eq 'y')}
				{if isset($needsStaging) and $needsStaging eq 'y'}
					{assign var=thisPageName value=$stagingPageName|escape:"url"}
				{else}
					{assign var=thisPageName value=$thispage}
				{/if}
				{if $page_ref_id and $needsStaging neq 'y'}
					{assign var=thisPageRefId value="&amp;page_ref_id=$page_ref_id"}
				{else}
					{assign var=thisPageRefId value=""}
				{/if}
				{if $beingEdited eq 'y'}
					{assign var=thisPageClass value='+highlight'}
				{else}
					{assign var=thisPageClass value=''}
				{/if}
				{if $prefs.flaggedrev_approval neq 'y' or ! $revision_approval or $lastVersion eq $revision_displayed}
					{button href="tiki-editpage.php?page="|cat:$thisPageName|cat:$thisPageRefId _class=$thisPageClass _text="{tr}Edit this page{/tr}"}
				{elseif $tiki_p_wiki_view_latest eq 'y'}
					<span class="button">{self_link latest=1}{tr}View latest version before editing{/tr}{/self_link}</span>
				{/if}
			{/if}

			{if $prefs.feature_source eq 'y' and $tiki_p_wiki_view_source eq 'y'}
				{button href="tiki-pagehistory.php?page=$thispage&amp;source=0" _text="{tr}Source{/tr}"}
			{/if}

			{if $page|lower ne 'sandbox'}
				{if $tiki_p_remove eq 'y' && $editable}
					{button href="tiki-removepage.php?page=$thispage&amp;version=last" _text="{tr}Remove{/tr}"}
				{/if}

				{if $tiki_p_rename eq 'y' && $editable}
					{if isset($beingStaged) and $beingStaged eq 'y'}
						{button href="tiki-rename_page.php?page=$thisapprovedPageName" _text="{tr}Rename{/tr}"}
					{else}
						{button href="tiki-rename_page.php?page=$thispage" _text="{tr}Rename{/tr}"}
					{/if}
				{/if}

				{if $prefs.feature_wiki_usrlock eq 'y' and ( $tiki_p_admin_wiki eq 'y' or (isset($user) and $user and $user eq $page_user and $tiki_p_lock eq 'y') )}
					{if $lock}
						{button href="tiki-index.php?page=$thispage&amp;action=unlock" _text="{tr}Unlock{/tr}"}
					{else}
						{button href="tiki-index.php?page=$thispage&amp;action=lock" _text="{tr}Lock{/tr}"}
					{/if}
				{/if}

				{if $tiki_p_admin_wiki eq 'y' or $tiki_p_assign_perm_wiki_page eq 'y'}
					{button href="tiki-objectpermissions.php?objectId=$thispage&amp;objectName=$thispage&amp;objectType=wiki+page&amp;permType=wiki"	_text="{tr}Permissions{/tr}"}
				{/if}

				{if $prefs.feature_history eq 'y' and $tiki_p_wiki_view_history eq 'y'}
					{button href="tiki-pagehistory.php?page=$thispage" _text="{tr}History{/tr}"}
				{/if}

				{if $prefs.feature_page_contribution eq 'y' and $tiki_p_page_contribution_view eq 'y'}
					{button href="tiki-page_contribution.php?page=$thispage" _text="{tr}Contributions by author{/tr}"}
				{/if}
			{/if}

			{if $prefs.feature_likePages eq 'y' and $tiki_p_wiki_view_similar eq 'y'}
				{button href="tiki-likepages.php?page=$thispage" _text="{tr}Similar{/tr}"}
			{/if}

			{if $prefs.feature_wiki_undo eq 'y' and $canundo eq 'y'}
				{button href="tiki-index.php?page=$thispage&amp;undo=1" _text="{tr}Undo{/tr}"}
			{/if}

			{if $prefs.feature_wiki_make_structure eq 'y' and $tiki_p_edit_structures eq 'y' and $editable and $structure eq 'n' and count($showstructs) eq 0}
				{button href="tiki-index.php?page=$thispage&amp;convertstructure=1" _text="{tr}Make Structure{/tr}"}
			{/if}

			{if $prefs.wiki_uses_slides eq 'y'}
				{if $show_slideshow eq 'y'}
					{button href="tiki-slideshow.php?page=$thispage" _text="{tr}Slideshow{/tr}"}
				{elseif $structure eq 'y'}
					{assign var=thispage_info value=$page_info.page_ref_id}
					{button href="tiki-slideshow2.php?page_ref_id=$thispage_info" _text="{tr}Slideshow{/tr}"}
				{/if}
			{/if}

			{if $prefs.feature_wiki_export eq 'y' and ( $tiki_p_admin_wiki eq 'y' or $tiki_p_export_wiki eq 'y' )}
				{button href="tiki-export_wiki_pages.php?page=$thispage" _text="{tr}Export{/tr}"}
			{/if}

			{if $prefs.feature_wiki_discuss eq 'y' && $show_page eq 'y' && $beingStaged ne 'y' && $tiki_p_forum_post eq 'y'}
				{capture name='wiki_discussion_string'}{include file='wiki-discussion.tpl'}{/capture}
				{assign var=thiswiki_discussion_string value=$smarty.capture.wiki_discussion_string|escape:"url"}
				{button href="tiki-view_forum.php?forumId=`$prefs.wiki_forum_id`&amp;comments_postComment=post&amp;comments_title=$thispage&amp;comments_data=$thiswiki_discussion_string%3A+%5Btiki-index.php%3Fpage=$thispage%7C$thispage%5D&amp;comment_topictype=n" _text="{tr}Discuss{/tr}"}
			{/if}

			{if $show_page == 'y'} {* Show this buttons only if page view mode *}

				{* don't show comments if feature disabled or not enough rights *}
				{if $prefs.feature_wiki_comments == 'y'
					&& $comments_allowed_on_page == 'y'
					&& $tiki_p_wiki_view_comments == 'y'
					&& (($tiki_p_read_comments == 'y'
					&& $comments_cant != 0)
					|| $tiki_p_post_comments == 'y'
					||$tiki_p_edit_comments == 'y')}
					{assign var=pagemd5 value=$page|@md5}
					{include file='comments_button.tpl'}
				{/if}

				{* don't show attachments button if feature disabled or no corresponding rights or no attached files and r/o*}

				{if $prefs.feature_wiki_attachments == 'y'
					&& (
						$tiki_p_wiki_view_attachments == 'y'
						&& count($atts) > 0
						|| $tiki_p_wiki_attach_files == 'y'
						|| $tiki_p_wiki_admin_attachments == 'y')
				}
					{if $atts|@count gt 0}
						{assign var=thisbuttonclass value='highlight'}
					{else}
						{assign var=thisbuttonclass value=''}
					{/if}
					{capture assign=thistext}{strip}
						{if $atts|@count == 0 || $tiki_p_wiki_attach_files == 'y' && $tiki_p_wiki_view_attachments == 'n' && $tiki_p_wiki_admin_attachments == 'n'}
							{tr}Attach File{/tr}
						{elseif $atts|@count == 1}
							{tr}1 File Attached{/tr}
						{else}
							{tr}{$atts|@count} files attached{/tr}
						{/if}
					{/strip}{/capture}
					{button href="#attachments" _flip_id="attzone$pagemd5" _class=$thisbuttonclass _text=$thistext _flip_default_open=$prefs.w_displayed_default}
				{/if}{* attachments *}

				{if $prefs.feature_multilingual eq 'y' and ($tiki_p_edit eq 'y' or ((!isset($user) or !$user) and $prefs.wiki_encourage_contribution eq 'y')) and !$lock}
					{if $beingStaged == 'y'}
						{button href="tiki-edit_translation.php?page=$thisapprovedPageName" _text="{tr}Translate{/tr}"}
					{else}
						{button href="tiki-edit_translation.php?page=$thispage" _text="{tr}Translate{/tr}"}
					{/if}
				{/if}

				{if $tiki_p_admin_wiki eq 'y' && $prefs.wiki_keywords eq 'y'}
					{button href="tiki-admin_keywords.php" page=$page _text="{tr}Keywords{/tr}"}
				{/if}
				{if (isset($user) and $user) and (isset($tiki_p_create_bookmarks) and $tiki_p_create_bookmarks eq 'y') and $prefs.feature_user_bookmarks eq 'y'}
					{button _script="tiki-user_bookmarks.php" urlname=$page urlurl=$page|sefurl addurl="Add" _text="{tr}Bookmark{/tr}" _auto_args="urlname,urlurl,addurl"}
				{/if}
			{/if}
		{/if}
	{/capture}

	{if $page_bar neq ''}
		<div class="clearfix" id="page-bar">
			{$page_bar}
		</div>
	{/if}

	{if $wiki_extras eq 'y' && $prefs.feature_wiki_attachments eq 'y' and $tiki_p_wiki_view_attachments eq 'y'}
		<a name="attachments"></a>
		{if $prefs.feature_use_fgal_for_wiki_attachments eq 'y'}
			{attachments _id=$page _type='wiki page'}
		{else}
			{include file='attachments.tpl'}
		{/if}
	{/if}

	{if $prefs.feature_wiki_comments eq 'y' and $tiki_p_wiki_view_comments == 'y' and $edit_page ne 'y' and $comments_allowed_on_page == 'y'}
		<a name="comments"></a>
		{include file='comments.tpl'}
	{/if}

	{/strip}
{/if}
