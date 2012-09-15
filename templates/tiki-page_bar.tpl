{* $Id$ *}
{if !isset($versioned) or not $versioned}
	{strip}
		{capture assign=page_bar}
			{favorite type="wiki page" object=$page}

			{if $edit_page neq 'y'}
				{* Check that page is not locked and edit permission granted. SandBox can be edited w/o perm *}
				{if ((isset($editable) and $editable) and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox')
					or (!$user and $prefs.wiki_encourage_contribution eq 'y')) or $tiki_p_admin_wiki eq 'y'}
					{if isset($beingEdited) and $beingEdited eq 'y'}
						{assign var=thisPageClass value='+highlight'}
					{else}
						{assign var=thisPageClass value=''}
					{/if}
					{if $prefs.flaggedrev_approval neq 'y' or ! $revision_approval or $lastVersion eq $revision_displayed}
						{if isset($page_ref_id)}
							{button _keepall='y' href="tiki-editpage.php" page=$page page_ref_id=$page_ref_id _class=$thisPageClass _text="{tr}Edit this page{/tr}"}
						{else}
							{button _keepall='y' href="tiki-editpage.php" page=$page _class=$thisPageClass _text="{tr}Edit this page{/tr}"}
						{/if}
					{elseif $tiki_p_wiki_view_latest eq 'y'}
						<span class="button">
							{self_link latest=1}
								{tr}View latest version before editing{/tr}
							{/self_link}
						</span>
					{/if}
				{/if}

				{if $prefs.feature_source eq 'y' and $tiki_p_wiki_view_source eq 'y'}
					{button _keepall='y' href="tiki-pagehistory.php" page=$page source="0" _text="{tr}Source{/tr}"}
				{/if}

				{if $page|lower ne 'sandbox'}
					{if $tiki_p_remove eq 'y' && (isset($editable) and $editable)}
						{button _keepall='y' href="tiki-removepage.php" page=$page version="last" _text="{tr}Remove{/tr}"}
					{/if}

					{if $tiki_p_rename eq 'y' && (isset($editable) and $editable)}
						{button _keepall='y' href="tiki-rename_page.php" page=$page _text="{tr}Rename{/tr}"}
					{/if}

					{if $prefs.feature_wiki_usrlock eq 'y' and $user and $tiki_p_lock eq 'y'}
						{if !$lock}
							{button _keepall='y' href="tiki-index.php" page=$page action="lock" _text="{tr}Lock{/tr}"}
						{elseif $tiki_p_admin_wiki eq 'y' or $user eq $page_user}
							{button _keepall='y' href="tiki-index.php" page=$page action="unlock" _text="{tr}Unlock{/tr}"}
						{/if}
					{/if}

					{if $tiki_p_admin_wiki eq 'y' or $tiki_p_assign_perm_wiki_page eq 'y'}
						{button _keepall='y' href="tiki-objectpermissions.php" objectId=$page objectName=$page objectType="wiki+page" permType="wiki" _text="{tr}Permissions{/tr}"}
					{/if}

					{if $prefs.feature_history eq 'y' and $tiki_p_wiki_view_history eq 'y'}
						{button _keepall='y' href="tiki-pagehistory.php" page=$page _text="{tr}History{/tr}"}
					{/if}

					{if $prefs.feature_page_contribution eq 'y' and $tiki_p_page_contribution_view eq 'y'}
						{button _keepall='y' href="tiki-page_contribution.php" page=$page _text="{tr}Contributions by author{/tr}"}
					{/if}
				{/if}

				{if $prefs.feature_likePages eq 'y' and $tiki_p_wiki_view_similar eq 'y'}
					{button _keepall='y' href="tiki-likepages.php" page=$page _text="{tr}Similar{/tr}"}
				{/if}

				{if $prefs.feature_wiki_undo eq 'y' and $canundo eq 'y'}
					{button _keepall='y' href="tiki-index.php" page=$page undo="1" _text="{tr}Undo{/tr}"}
				{/if}

				{if $prefs.feature_wiki_make_structure eq 'y' and $tiki_p_edit_structures eq 'y' and (isset($editable)
					and $editable) and $structure eq 'n' and count($showstructs) eq 0}
					{button _keepall='y' href="tiki-index.php" page=$page convertstructure="1" _text="{tr}Make Structure{/tr}"}
				{/if}

				{if $prefs.wiki_uses_slides eq 'y'}
					{if $show_slideshow eq 'y'}
						{button _keepall='y' href="tiki-slideshow.php" page=$page _text="{tr}Slideshow{/tr}"}
					{elseif $structure eq 'y'}
						{button _keepall='y' href="tiki-slideshow2.php" page_ref_id=$page_info.page_ref_id _text="{tr}Slideshow{/tr}"}
					{/if}
				{/if}

				{if $prefs.feature_wiki_export eq 'y' and ( $tiki_p_admin_wiki eq 'y' or $tiki_p_export_wiki eq 'y' )}
					{button _keepall='y' href="tiki-export_wiki_pages.php" page=$page _text="{tr}Export{/tr}"}
				{/if}

				{if $prefs.feature_wiki_discuss eq 'y' && $show_page eq 'y' && $tiki_p_forum_post eq 'y'}
					{capture assign=wiki_discussion_string}
						{include file='wiki-discussion.tpl'} [tiki-index.php?page={$page|escape:url}|{$page}]
					{/capture}
					{button _keepall='y' href="tiki-view_forum.php" forumId=$prefs.wiki_forum_id comments_postComment="post" comments_title=$page comments_data=$wiki_discussion_string comment_topictype="n" _text="{tr}Discuss{/tr}"}
				{/if}

				{if isset($show_page) and $show_page eq 'y'}

					{* don't show comments if feature disabled or not enough rights *}

					{if $prefs.feature_wiki_comments eq 'y'
						&& ($prefs.wiki_comments_allow_per_page neq 'y' or $info.comments_enabled eq 'y')
						&& $tiki_p_wiki_view_comments eq 'y'
						&& $tiki_p_read_comments eq 'y'}
						<span class="button">
							<a id="comment-toggle" href="{service controller=comment action=list type="wiki page" objectId=$page}#comment-container">
								{tr}Comments{/tr}
							</a>
						</span>
						{jq}
							$('#comment-toggle').comment_toggle();
						{/jq}
					{/if}

					{* don't show attachments button if feature disabled or no corresponding rights or no attached files and r/o*}

					{if $prefs.feature_wiki_attachments == 'y'
						&& (
							$tiki_p_wiki_view_attachments == 'y'
							&& (isset($atts) && $atts|@count gt 0)
							|| $tiki_p_wiki_attach_files == 'y'
							|| $tiki_p_wiki_admin_attachments == 'y')}
						{if isset($atts) and $atts|@count gt 0}
							{assign var=thisbuttonclass value='highlight'}
						{else}
							{assign var=thisbuttonclass value=''}
						{/if}
						{capture assign=thistext}
							{strip}
								{if (!isset($atts) or $atts|@count == 0) || $tiki_p_wiki_attach_files == 'y'
									&& $tiki_p_wiki_view_attachments == 'n' && $tiki_p_wiki_admin_attachments == 'n'}
									{tr}Attach File{/tr}
								{elseif isset($atts) and $atts|@count == 1}
									{tr}1 File Attached{/tr}
								{else}
									{tr}{$atts|@count} files attached{/tr}
								{/if}
							{/strip}
						{/capture}
						{if (isset($atts) and $atts|@count gt 0) || $editable}
							{button href="#attachments" _flip_id="attzone{if isset($pagemd5)}{$pagemd5}{/if}" _class=$thisbuttonclass _text=$thistext _flip_default_open=$prefs.w_displayed_default}
						{/if}
					{/if}{* attachments *}

					{if $prefs.feature_multilingual eq 'y' and ($tiki_p_edit eq 'y'
						or (!$user and $prefs.wiki_encourage_contribution eq 'y')) and !$lock}
						{button _keepall='y' href="tiki-edit_translation.php" page=$page _text="{tr}Translate{/tr}"}
					{/if}

					{if $tiki_p_admin_wiki eq 'y' && $prefs.wiki_keywords eq 'y'}
						{button _keepall='y' href="tiki-admin_keywords.php" page=$page _text="{tr}Keywords{/tr}"}
					{/if}
					{if $user and (isset($tiki_p_create_bookmarks) and $tiki_p_create_bookmarks eq 'y') and $prefs.feature_user_bookmarks eq 'y'}
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

		{if $prefs.feature_wiki_comments eq 'y' and $tiki_p_wiki_view_comments == 'y' and $edit_page ne 'y'}
			<div id="comment-container"></div>
		{/if}

	{/strip}
{/if}
