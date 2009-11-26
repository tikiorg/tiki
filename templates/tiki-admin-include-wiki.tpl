{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Use the 'Quick Edit' module to easily create or edit wiki pages.{/tr} {tr}Select <a class="rbox-link" href="tiki-admin_modules.php">Admin &gt; Modules</a> to add this (or other) modules.{/tr}
{/remarksbox}

<form action="tiki-admin.php?page=wiki" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="wikisetprefs" value="{tr}Change preferences{/tr}" />
	</div>

	{tabset name="admin_wiki"}
		{tab name="{tr}General Preferences{/tr}"}
			{preference name=wikiHomePage}
			{preference name=wiki_page_regex}

			<fieldset>
				<legend>{tr}Page display{/tr}</legend>
				{preference name=feature_wiki_description label="{tr}Description{/tr}"}
				{preference name=feature_page_title label="{tr}Title{/tr}"}
				{preference name=feature_wiki_pageid label="{tr}Page ID{/tr}"}
				{preference name=wiki_show_version label="{tr}Page version{/tr}"}
				{preference name=wiki_pagename_strip}
				{preference name=wiki_badchar_prevent}
				{preference name=wiki_authors_style label="{tr}List authors{/tr}"}

				<div class="adminoptionboxchild">
					{preference name=wiki_authors_style_by_page label="{tr}Allow override per page{/tr}"}
				</div>

				{preference name=wiki_actions_bar}
				{preference name=wiki_page_navigation_bar}
				{preference name=wiki_topline_position}
				{preference name=page_bar_position}
				{preference name=wiki_encourage_contribution}
			</fieldset>

			<fieldset>
				<legend>{tr}Edit{/tr}</legend>

				{preference name=wiki_spellcheck}
				{preference name=feature_wiki_templates}
				{preference name=feature_warn_on_edit}
				{preference name=warn_on_edit_time}
				{preference name=feature_wiki_undo}
				{preference name=feature_wiki_save_draft}
				{preference name=feature_wiki_footnotes}
				{preference name=feature_wiki_allowhtml}
				{preference name=wiki_timeout_warning}

				{preference name=wiki_edit_section}
				<div class="adminoptionboxchild" id="wiki_edit_section_childcontainer">
					{preference name=wiki_edit_section_level}
				</div>

				{preference name=wiki_edit_icons_toggle}
				{preference name=wiki_edit_minor}
				<div class="adminoptionboxchild" id="wiki_edit_minor_childcontainer">
					{remarksbox type=note title=Note}{tr}Minor edits do not flag new content for translation and do not send watch notifications.{/tr}.
						<br />
						{tr}Only user groups granted the tiki_p_minor permission (and admins) will be able to save minor edits when this is enabled.{/tr}
						<a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Registered" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
					{/remarksbox}
				</div>

				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="feature_wiki_mandatory_category">{tr}Force and limit categorization to within subtree of:{/tr}</label>
						<select name="feature_wiki_mandatory_category" id="feature_wiki_mandatory_category">
							<option value="-1" {if $prefs.feature_wiki_mandatory_category eq -1 or $prefs.feature_wiki_mandatory_category eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
							<option value="0" {if $prefs.feature_wiki_mandatory_category eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
							{section name=ix loop=$catree}
								<option value="{$catree[ix].categId|escape}" {if $catree[ix].categId eq $prefs.feature_wiki_mandatory_category}selected="selected"{/if}>{if $catree[ix].categpath}{$catree[ix].categpath|escape}{else}{$catree[ix].name|escape}{/if}</option>
							{/section}
						</select>
						{if $prefs.feature_categories ne 'y'}
							<br />
							{icon _id=information}{tr}Categories are disabled.{/tr} 
							<a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.
						{/if}
					</div>
				</div>

				{preference name=feature_actionlog_bytes}
			</fieldset>
		{/tab}

		{tab name="{tr}Features{/tr}"}
			<input type="hidden" name="wikifeatures" />    	
			{preference name=feature_sandbox}
			
			{preference name=feature_wiki_comments}
			<div class="adminoptionboxchild" id="feature_wiki_comments_childcontainer">
				<a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
				{preference name=wiki_comments_displayed_default}
				{preference name=wiki_comments_notitle}
				{preference name=wiki_comments_per_page}
				{preference name=wiki_comments_default_ordering}
				{preference name=wiki_comments_allow_per_page}

			</div>

			{preference name=feature_wiki_attachments}
			<div class="adminoptionboxchild" id="feature_wiki_attachments_childcontainer">
				<a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
					{preference name=w_displayed_default}
					{preference name=w_use_db}
					<div class="adminoptionboxchild">
						{preference name=w_use_dir}
						{button href="tiki-admin.php?page=wikiatt" _text="{tr}Manage attachments{/tr}"}
					</div>
			</div>

			{preference name=feature_dump}
			<div class="adminoptionboxchild" id="feature_dump_childcontainer">
				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="tagname">{tr}Tag for current wiki{/tr}:</label>
						<input maxlength="20" size="20" type="text" name="tagname" id="tagname" />
						<input type="submit" name="createtag" value="{tr}Create{/tr}" />
					</div>
				</div>
				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="restoretag">{tr}Restore wiki to tag{/tr}:</label>
						<select name="tagname" id="restoretag"{if $tags|@count eq '0'} disabled="disabled"{/if}>
							{section name=sel loop=$tags}
								<option value="{$tags[sel]|escape}">{$tags[sel]}</option>
							{sectionelse}
								<option value=''>{tr}None{/tr}</option>
							{/section}
						</select>
						<input type="submit" name="restoretag" value="{tr}Restore{/tr}"{if $tags|@count eq '0'} disabled="disabled"{/if} />
					</div>
				</div>
				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="removetag">{tr}Remove a tag{/tr}:</label> 
						<select name="tagname" id="removetag"{if $tags|@count eq '0'} disabled="disabled"{/if}>
							{section name=sel loop=$tags}
								<option value="{$tags[sel]|escape}">{$tags[sel]}</option>
							{sectionelse}
								<option value=''>{tr}None{/tr}</option>
							{/section}
						</select>
						<input type="submit" name="removetag" value="{tr}Remove{/tr}"{if $tags|@count eq '0'} disabled="disabled"{/if} />
					</div>
				</div>

				{button href="tiki-admin.php?page=wiki&amp;dump=1" _text="{tr}Generate dump{/tr}"}
				{if $tikidomain}
					{button href="dump/$tikidomain/new.tar" _text="{tr}Download last dump{/tr}"}
				{else}
					{button href="dump/new.tar" _text="{tr}Download last dump{/tr}"}
				{/if}
			</div>

		{preference name=feature_wiki_pictures}
		<div class="adminoptionboxchild" id="feature_wiki_pictures_childcontainer">
			<a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
			{preference name=feature_filegals_manager}
			{button href="tiki-admin.php?page=wiki&amp;rmvunusedpic=1" _text="{tr}Remove unused pictures{/tr}"}
		</div>

		{preference name=feature_wiki_export}
		<div class="adminoptionboxchild" id="feature_wiki_export_childcontainer">
			<a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
			{button href="tiki-export_wiki_pages.php" _text="{tr}Export Wiki Pages{/tr}"}
		</div>
    
		{preference name=feature_wikiwords}
		<div class="adminoptionboxchild" id="feature_wikiwords_childcontainer">
			{preference name=feature_wikiwords_usedash}
			{preference name=feature_wiki_plurals}
		</div>

		{preference name=feature_history}
		<div class="adminoptionboxchild" id="feature_history_childcontainer">
			<a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
			{preference name=maxVersions}
			{preference name=keep_versions}
			{preference name=feature_wiki_history_ip}
			{preference name=default_wiki_diff_style}
			{preference name=feature_wiki_history_full}
		</div>

		{preference name=feature_wiki_discuss}
		<div class="adminoptionboxchild" id="feature_wiki_discuss_childcontainer">
			<div class="adminoptionboxlabel">
				<label for="wiki_forum_id">{tr}Forum for discussion:{/tr}</label>
				{if $prefs.feature_forums eq 'y'} 
					<a class="link" href="tiki-assignpermission.php?level=forum" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
				{/if}
				<select id="wiki_forum_id" name="wiki_forum_id"{if $prefs.feature_forums ne 'y' or !$all_forums} disabled="disabled"{/if}>
					{if $all_forums}
						{section name=ix loop=$all_forums}
							<option value="{$all_forums[ix].forumId|escape}" {if $all_forums[ix].forumId eq $prefs.wiki_forum_id}selected="selected"{/if}>{$all_forums[ix].name}</option>
						{/section}
					{else}    
						<option value="">{tr}None{/tr}</option>
					{/if}
				</select>
			</div>
		</div>

		{preference name=feature_source}
		<div class="adminoptionboxchild" id="feature_source_childcontainer">
			<a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
		</div>

		{preference name=feature_wiki_ratings}
		<div class="adminoptionboxchild" id="feature_wiki_ratings_childcontainer">
			<a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
		</div>

		{preference name=feature_backlinks}
		<div class="adminoptionboxchild" id="feature_backlinks_childcontainer">
			<a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
		</div>

		{preference name=feature_semantic}
		{preference name=feature_likePages}
		{preference name=feature_wiki_rankings}

		{preference name=feature_wiki_structure}
		<div class="adminoptionboxchild" id="feature_wiki_structure_childcontainer">
			{preference name=feature_wiki_open_as_structure}
			{preference name=feature_wiki_make_structure}
			{preference name=feature_wiki_categorize_structure}
			{preference name=feature_create_webhelp}
			{preference name=page_n_times_in_a_structure}
		</div>

		{preference name=feature_wiki_import_html}
		{preference name=feature_wiki_import_page}
		{preference name=wiki_uses_slides}
		{preference name=feature_wiki_1like_redirection}
		{preference name=feature_wiki_userpage}
		<div class="adminoptionboxchild" id="feature_wiki_userpage_childcontainer">
			{preference name=feature_wiki_userpage_prefix}
		</div>

		{preference name=feature_wiki_usrlock}
		<div class="adminoptionboxchild" id="feature_wiki_usrlock_childcontainer">
			<a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
		</div>

		{preference name=wiki_creator_admin}
		{preference name=feature_wiki_print}
		<div class="adminoptionboxchild" id="feature_wiki_print_childcontainer">
			{preference name=feature_wiki_multiprint}
		</div>

		<div class="adminoptionbox">
			<fieldset>
				<legend>{tr}Wiki watch{/tr}{if $prefs.feature_help eq 'y'} {help url="Watch"}{/if}</legend>
				{if $prefs.feature_user_watches ne 'y'}
					<div class="adminoptionbox">
						{icon _id=information} {tr}Feature disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.
					</div>
				{else}
					<input type="hidden" name="wikisetwatch" />
					{preference name=wiki_watch_author}
					{preference name=wiki_watch_editor}
					{preference name=wiki_watch_comments}
					{preference name=wiki_watch_minor}
				{/if}
			</fieldset>
		</div>
	{/tab}

	{tab name="{tr}Staging &amp; Approval{/tr}"}
		<input type="hidden" name="wikiapprovalprefs" />    
			{preference name=feature_wikiapproval}
			<div class="adminoptionboxchild" id="feature_wikiapproval_childcontainer">
				{preference name=wikiapproval_block_editapproved}
				{preference name=wikiapproval_delete_staging}

					<div class="adminoptionbox">
						<div class="adminoptionlabel">
							<label for="wikiapproval_master_group">{tr}If not in the group, edit is always redirected to the staging page edit:{/tr}</label>
							<select name="wikiapproval_master_group" id="wikiapproval_master_group">
								<option value=""{if $prefs.wikiapproval_master_group eq ''} selected="selected"{/if}></option>
								{foreach from=$all_groups item=g}
									<option value="{$g|escape}"{if $prefs.wikiapproval_master_group eq $g} selected="selected"{/if}>{$g|escape}</option>
								{/foreach}
							</select>
						</div>
					</div>

					<fieldset>
						<legend>{tr}Page name{/tr}</legend>
						{preference name=wikiapproval_prefix}
						{preference name=wikiapproval_hideprefix}
					</fieldset>

					<fieldset>
						<legend>{tr}Category{/tr}</legend>
						<div class="adminoptionbox">
							{if $prefs.feature_categories ne 'y'}
								<br />
								{icon _id=information}{tr}Categories are disabled.{/tr} <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.
							{/if}
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="wikiapproval_staging_category">{tr}Staging{/tr}:</label>
								<select id="wikiapproval_staging_category" name="wikiapproval_staging_category">
									<option value="0" {if $prefs.feature_wikiapproval_staging_category eq 0}selected="selected"{/if}>{tr}None{/tr}</option>
									{section name=ix loop=$catree}	
										<option value="{$catree[ix].categId|escape}" {if $prefs.wikiapproval_staging_category eq $catree[ix].categId}selected="selected"{/if}>{if $catree[ix].categpath}{$catree[ix].categpath}{else}{$catree[ix].name}{/if}</option>
									{/section}	
								</select>
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="wikiapproval_approved_category">{tr}Approved{/tr} {tr}(mandatory for feature to work){/tr}:</label>
								<select name="wikiapproval_approved_category" id="wikiapproval_approved_category">
									<option value="0" {if $prefs.feature_wikiapproval_approved_category eq 0}selected="selected"{/if}>{tr}None{/tr}</option>
									{section name=ix loop=$catree}	
										<option value="{$catree[ix].categId|escape}" {if $prefs.wikiapproval_approved_category eq $catree[ix].categId}selected="selected"{/if}>{if $catree[ix].categpath}{$catree[ix].categpath}{else}{$catree[ix].name}{/if}</option>
									{/section}	
								</select>
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="wikiapproval_outofsync_category">{tr}Out-of-sync{/tr}:</label>
								<select name="wikiapproval_outofsync_category" id="wikiapproval_outofsync_category">
									<option value="0" {if $prefs.feature_wikiapproval_outofsync_category eq 0}selected="selected"{/if}>{tr}None{/tr}</option>
									{section name=ix loop=$catree}	
										<option value="{$catree[ix].categId|escape}" {if $prefs.wikiapproval_outofsync_category eq $catree[ix].categId}selected="selected"{/if}>{if $catree[ix].categpath}{$catree[ix].categpath}{else}{$catree[ix].name}{/if}</option>
									{/section}	
								</select>
							</div>
						</div>
						{preference name=wikiapproval_sync_categories}
					</fieldset>

					<fieldset>
						<legend>{tr}Freetags{/tr}</legend>
						<div class="adminoptionbox">
							{if $prefs.feature_freetags ne 'y'}
								<br />
								{icon _id=information}{tr}Freetags are disabled.{/tr} <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.
							{/if}
						</div>
						{preference name=wikiapproval_update_freetags}
						{preference name=wikiapproval_combine_freetags}
					</fieldset>
			</div>

	{/tab}

	{tab name="{tr}Page Listings{/tr}"}
		<input type="hidden" name="wikilistprefs" />	  
		{preference name=feature_listPages}
		{preference name=feature_lastChanges}
		{preference name=feature_listorphanPages}
		{preference name=feature_listorphanStructure}

		<div class="adminoptionbox">
			<fieldset>
				<legend>{tr}Configuration{/tr}</legend>
				<div class="adminoptionbox">
					{tr}Select which items to display when listing pages{/tr}:
				</div>
				{preference name=wiki_list_sortorder}
				<div class="adminoptionboxchild">
					{preference name=wiki_list_sortdirection}
				</div>
				{preference name=wiki_list_id}
				
				{preference name=wiki_list_name}
				<div class="adminoptionboxchild" id="wiki_list_name_childcontainer">
					{preference name=wiki_list_name_len}
				</div>

				{preference name=wiki_list_hits}
				{preference name=wiki_list_lastmodif}
				{preference name=wiki_list_creator}
				{preference name=wiki_list_user}
				{preference name=wiki_list_lastver}
				{preference name=wiki_list_comment}
				<div class="adminoptionboxchild" id="wiki_list_comment_childcontainer">
					{preference name=wiki_list_comment_len}
				</div>
				{preference name=wiki_list_description}
				<div class="adminoptionboxchild" id="wiki_list_description_childcontainer">
					{preference name=wiki_list_description_len}
				</div>

				{preference name=wiki_list_status}
				{preference name=wiki_list_versions}
				{preference name=wiki_list_links}
				{preference name=wiki_list_backlinks}
				{preference name=wiki_list_size}
				{preference name=wiki_list_language}
				{preference name=wiki_list_categories}
				{preference name=wiki_list_categories_path}
			</fieldset>  
		</div>
	{/tab}
		
	{if $prefs.feature_morcego eq 'y'}
		{tab name="{tr}Wiki 3D Browser Configuration{/tr}"}
			<fieldset>
				<legend>{tr}General{/tr}</legend>
				{preference name=wiki_feature_3d}
				{preference name=wiki_3d_autoload}
				{preference name=wiki_3d_width}
				{preference name=wiki_3d_height}
			</fieldset>
			<fieldset>
				<legend>{tr}Graph appearance{/tr}</legend>
				{preference name=wiki_3d_navigation_depth}
				{preference name=wiki_3d_node_size}
				{preference name=wiki_3d_text_size}
				{preference name=wiki_3d_spring_size}
				{preference name=wiki_3d_existing_page_color}
				{preference name=wiki_3d_missing_page_color}
			</fieldset>
			<fieldset>
				<legend>{tr}Camera settings{/tr}</legend>
				{preference name=wiki_3d_adjust_camera}
				{preference name=wiki_3d_camera_distance}
				{preference name=wiki_3d_fov}
				{preference name=wiki_3d_feed_animation_interval}
			</fieldset>
			<fieldset>
				<legend>{tr}Physics engine{/tr}</legend>
				{preference name=wiki_3d_friction_constant}
				{preference name=wiki_3d_elastic_constant}
				{preference name=wiki_3d_eletrostatic_constant}
				{preference name=wiki_3d_node_mass}
				{preference name=wiki_3d_node_charge}
			</fieldset>
		{/tab}
	{/if}

	{tab name="{tr}Screencasts{/tr}"}
		{preference name=feature_wiki_screencasts}
		{preference name=feature_wiki_screencasts_upload_type label="{tr}Upload Type{/tr}"}
		{preference name=feature_wiki_screencasts_max_size label="{tr}Maximum size{/tr}"}
		{preference name=feature_wiki_screencasts_base label="{tr}Data location{/tr}"}
		{preference name=feature_wiki_screencasts_httpbase label="{tr}HTTP Prefix{/tr}"}
		{preference name=feature_wiki_screencasts_user label="{tr}WebDav username{/tr}"}
		{preference name=feature_wiki_screencasts_pass label="{tr}WebDav password{/tr}"}
	{/tab}
{/tabset}

<div class="heading input_submit_container" style="text-align: center">
	<input type="submit" name="wikisetprefs" value="{tr}Change preferences{/tr}" />
</div>
</form>



