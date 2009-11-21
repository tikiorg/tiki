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
			<a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
			
			<div class="adminoptionboxchild" id="feature_wiki_comments_childcontainer">
				{preference name=wiki_comments_displayed_default}
				{preference name=wiki_comments_notitle}
				{preference name=wiki_comments_per_page}
				{preference name=wiki_comments_default_ordering}

			</div>


			<div class="adminoptionbox">
				<div class="adminoption">
					<input type="checkbox" id="feature_wiki_attachments" name="feature_wiki_attachments" {if $prefs.feature_wiki_attachments eq 'y'}checked="checked" {/if}onclick="flip('useattachments');" />
				</div>
				<div class="adminoptionlabel">
					<label for="feature_wiki_attachments">{tr}Attachments{/tr}</label>
					{if $prefs.feature_help eq 'y'}
						{help url="Attachments"}
					{/if} 
					<a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
				</div>
				<input type="hidden" name="wikiattprefs" />
				<div class="adminoptionboxchild" id="useattachments" style="display:{if $prefs.feature_wiki_attachments eq 'y'}block{else}none{/if};">
					<div class="adminoptionbox">
						<div class="adminoption">
							<input type="checkbox" id="w_displayed_default" name="w_displayed_default" {if $prefs.w_displayed_default eq 'y'} checked="checked"{/if} /> 
						</div>
						<div class="adminoptionlabel">
							<label for='w_displayed_default'>{tr}Display by default{/tr}.</label>
						</div>
					</div>
		
					<div class="adminoptionbox">
						<div class="adminoptionlabel">
							<input type="radio" id="w_use_db_1" name="w_use_db" value="y" {if $prefs.w_use_db eq 'y'}checked="checked"{/if} onclick="flip('directorypath');" />
							<label for="w_use_db_1">{tr}Store in database{/tr}.</label>
						</div>
						<div class="adminoptionlabel">
							<input type="radio" id="w_use_db_2" name="w_use_db" value="n" {if $prefs.w_use_db eq 'n'}checked="checked"{/if} onclick="flip('directorypath');" />
							<label for="w_use_db_2">{tr}Store in directory{/tr}.</label>
						</div>
						<div class="adminoptionboxchild" id="directorypath" style="display:{if $prefs.w_use_db eq 'n'}block{else}none{/if};">
							<div class="adminoptionlabel">
								<label for="w_use_dir">{tr}Path:{/tr}</label>
								<input type="text" name="w_use_dir" value="{$prefs.w_use_dir}" id="w_use_dir" />
							</div>
						</div>
					</div>

					<div class="adminoptionbox">
						<div class="adminoptionlabel">
							<a class="button" href="tiki-admin.php?page=wikiatt">{tr}Manage attachments{/tr}</a>
						</div>
					</div>
				</div>		
			</div>

			<div class="adminoptionbox">
				<div class="adminoption">
					<input type="checkbox" id="feature_dump" name="feature_dump" {if $prefs.feature_dump eq 'y'}checked="checked"{/if} onclick="flip('usedumps');" />
				</div>
				<div class="adminoptionlabel">
					<label for="feature_dump">{tr}Dumps{/tr}</label>
				</div>
				<div class="adminoptionboxchild" id="usedumps" style="display:{if $prefs.feature_dump eq 'y'}block{else}none{/if};">
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

					<div class="adminoptionbox">
						<div class="adminoptionlabel">
							<a class="button" href="tiki-admin.php?page=wiki&amp;dump=1">{tr}Generate dump{/tr}</a>
						</div>
					</div>
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><a class="button" href="dump/{if $tikidomain}{$tikidomain}/{/if}new.tar">{tr}Download last dump{/tr}</a>
					</div>
				</div>
			</div>	
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
		<div class="adminoptionbox">
			<div class="adminoption">
				<input type="checkbox" id="feature_wikiapproval" onclick="flip('usestaging');" name="feature_wikiapproval" {if $prefs.feature_wikiapproval eq 'y'}checked="checked" {/if}/>
			</div>
			<div class="adminoptionlabel">
				<label for="feature_wikiapproval">{tr}Use wiki page staging and approval{/tr}.{if $prefs.feature_help eq 'y'} {help url="Wiki+Page+Staging+and+Approval"}{/if}</label>
				<div id="usestaging" style="display:{if $prefs.feature_wikiapproval eq 'y'}block{else}none{/if};">
					<div class="adminoptionbox">
						<div class="adminoption">
							<input type="checkbox" id="wikiapproval_block_editapproved" name="wikiapproval_block_editapproved" {if $prefs.wikiapproval_block_editapproved eq 'y'}checked="checked"{/if}/>
						</div>
						<div class="adminoptionlabel">
							<label for="wikiapproval_block_editapproved">{tr}Force bounce of editing of approved pages to staging{/tr}.</label>
						</div>
					</div>
 
					<div class="adminoptionbox">
						<div class="adminoption">
							<input type="checkbox" id="wikiapproval_delete_staging"  name="wikiapproval_delete_staging" {if $prefs.wikiapproval_delete_staging eq 'y'}checked="checked"{/if}/>
						</div>
						<div class="adminoptionlabel">
							<label for="wikiapproval_delete_staging">{tr}Delete staging pages at approval{/tr}.</label>
						</div>
					</div>

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
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="wikiapproval_prefix">{tr}Unique page name prefix to indicate staging copy:{/tr}  <input id="wikiapproval_prefix" type="text" name="wikiapproval_prefix" value="{if $prefs.wikiapproval_prefix}{$prefs.wikiapproval_prefix|escape}{else}*{/if}" /></label>
							</div>
						</div>	

						<div class="adminoptionboxchild">
							<div class="adminoption">
								<input type="checkbox" id="wikiapproval_hideprefix" name="wikiapproval_hideprefix" {if $prefs.wikiapproval_hideprefix eq 'y'}checked="checked"{/if}/>
							</div>
							<div class="adminoptionlabel">
								<label for="wikiapproval_hideprefix">{tr}Hide page name prefix{/tr}.</label>
							</div>
						</div>
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
						<div class="adminoptionbox">
							<div class="adminoption">
								<input type="checkbox" id="wikiapproval_sync_categories" name="wikiapproval_sync_categories" {if $prefs.wikiapproval_sync_categories eq 'y'}checked="checked"{/if}/>
							</div>
							<div class="adminoptionlabel">
								<label for="wikiapproval_sync_categories">{tr}Categorize approved pages with categories of staging copy on approval{/tr}.</label>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>{tr}Freetags{/tr}</legend>
						<div class="adminoptionbox">
							{if $prefs.feature_freetags ne 'y'}
								<br />
								{icon _id=information}{tr}Freetags are disabled.{/tr} <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.
							{/if}
						</div>

						<div class="adminoptionbox">
							<div class="adminoption">
								<input type="checkbox" id="wikiapproval_update_freetags" name="wikiapproval_update_freetags" {if $prefs.wikiapproval_update_freetags eq 'y'}checked="checked"{/if}/>
							</div>
							<div class="adminoptionlabel">
								<label for="wikiapproval_update_freetags">{tr}Replace freetags with that of staging pages, on approval{/tr}.</label>
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoption">
								<input type="checkbox" id="wikiapproval_combine_freetags" name="wikiapproval_combine_freetags" {if $prefs.wikiapproval_combine_freetags eq 'y'}checked="checked"{/if}/>
							</div>
							<div class="adminoptionlabel">
								<label for="wikiapproval_combine_freetags">{tr}Add new freetags of approved copy (into tags field) when editing staging pages{/tr}.</label>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
		</div>
	{/tab}

	{tab name="{tr}Page Listings{/tr}"}
		<input type="hidden" name="wikilistprefs" />	  
		{preference name=feature_listPages}
		{preference name=feature_lastChanges}
		{preference name=feature_listorphanPages}

		<div class="adminoptionbox">
			<div class="adminoption">
				<input type="checkbox" id='feature_listorphanStructure' name="feature_listorphanStructure" {if $prefs.feature_listorphanStructure eq 'y'}checked="checked"{/if}/>
			</div>
			<div class="adminoptionlabel">
				<label for="feature_listorphanStructure">{tr}Pages not in structure{/tr} </label>
			</div>
		</div>	 

		<div class="adminoptionbox">
			<fieldset>
				<legend>{tr}Configuration{/tr}</legend>
				<div class="adminoptionbox">
					{tr}Select which items to display when listing pages{/tr}:
				</div>
				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="wiki_list_sortorder">{tr}Default sort order:{/tr}</label>
						<select name="wiki_list_sortorder" id="wiki_list_sortorder">
							{foreach from=$options_sortorder key=key item=item}
								<option value="{$item}" {if $prefs.wiki_list_sortorder eq $item} selected="selected"{/if}>{$key}</option>
							{/foreach}
						</select>
					</div>
					<div class="adminoptionboxchild">
						<div class="adminoptionlabel">
							<input type="radio" id="wiki_list_sortdirection" name="wiki_list_sortdirection" value="desc" {if $prefs.wiki_list_sortdirection eq 'desc'}checked="checked"{/if} />
							<label for="wiki_list_sortdirection">{tr}Descending{/tr}</label>
						</div>
						<div class="adminoptionlabel">
							<input type="radio" name="wiki_list_sortdirection" id="wiki_list_sortdirection2" value="asc" {if $prefs.wiki_list_sortdirection eq 'asc'}checked="checked"{/if} />
							<label for="wiki_list_sortdirection2">{tr}Ascending{/tr}</label>
						</div>
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_id" name="wiki_list_id" {if $prefs.wiki_list_id eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_id">{tr}Page ID{/tr} </label>
					</div>
				</div>	 
				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_name" name="wiki_list_name" {if $prefs.wiki_list_name eq 'y'}checked="checked"{/if} onclick="flip('namelength');" />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_name">{tr}Name{/tr} </label>
					</div>
					<div class="adminoptionboxchild" id="namelength" style="display:{if $prefs.wiki_list_name eq 'y'}block{else}none{/if};">
						<div class="adminoptionlabel">
							{tr}Name length:{/tr} <input type="text" name="wiki_list_name_len" value="{$prefs.wiki_list_name_len}" size="3" />
						</div>
					</div>
				</div>	 

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_hits" name="wiki_list_hits" {if $prefs.wiki_list_hits eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_hits">{tr}Hits{/tr}</label>
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_lastmodif" name="wiki_list_lastmodif" {if $prefs.wiki_list_lastmodif eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_lastmodif">{tr}Last modification date{/tr}</label>
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_creator" name="wiki_list_creator" {if $prefs.wiki_list_creator eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_creator">{tr}Creator{/tr}</label>
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_user" name="wiki_list_user" {if $prefs.wiki_list_user eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_user">{tr}Last modified by{/tr}</label>
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_lastver" name="wiki_list_lastver" {if $prefs.wiki_list_lastver eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_lastver">{tr}Version{/tr}</label>
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" onclick="flip('commentlength');" id="wiki_list_comment" name="wiki_list_comment" {if $prefs.wiki_list_comment eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_comment">{tr}Edit comments{/tr}</label>
					</div>
					<div class="adminoptionboxchild" id="commentlength" style="display:{if $prefs.wiki_list_comment eq 'y'}block{else}none{/if}">
						{tr}Edit Comments length:{/tr}
						<input type="text" name="wiki_list_comment_len" value="{$prefs.wiki_list_comment_len}" size="3" />
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_description" name="wiki_list_description" {if $prefs.wiki_list_description eq 'y'}checked="checked" {/if} onclick="flip('descriptionlength');" />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_description">{tr}Description{/tr}</label>
					</div>
					<div class="adminoptionboxchild" id="descriptionlength" style="display:{if $prefs.wiki_list_description eq 'y'}block{else}none{/if};">
						<label for="wiki_list_description_len">{tr}Description length:{/tr} </label>
						<input type="text" name="wiki_list_description_len" value="{$prefs.wiki_list_description_len}" size="3" id="wiki_list_description_len" />
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_status" name="wiki_list_status" {if $prefs.wiki_list_status eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_status">{tr}Status{/tr}</label>
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_versions" name="wiki_list_versions" {if $prefs.wiki_list_versions eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_versions">{tr}Versions{/tr}</label>
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_links" name="wiki_list_links" {if $prefs.wiki_list_links eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_links">{tr}Links{/tr}</label>
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_backlinks" name="wiki_list_backlinks" {if $prefs.wiki_list_backlinks eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_backlinks">{tr}Backlinks{/tr}</label>
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_size" name="wiki_list_size" {if $prefs.wiki_list_size eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_size">{tr}Size{/tr}</label>
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_language" name="wiki_list_language" {if $prefs.wiki_list_language eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_language">{tr}Language{/tr}</label>
						{if $prefs.feature_multilingual ne 'y'}
							<br />
							{icon _id=information}{tr}Feature is disabled.{/tr} <a href="tiki-admin.php?page=i18n" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.
						{/if}
					</div>	
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_categories" name="wiki_list_categories" {if $prefs.wiki_list_categories eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_categories">{tr}Categories{/tr}</label>
						{if $prefs.feature_categories ne 'y'}
							<br />
							{icon _id=information}{tr}Categories are disabled.{/tr} <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.
						{/if}
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="wiki_list_categories_path" name="wiki_list_categories_path" {if $prefs.wiki_list_categories_path eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="wiki_list_categories_path">{tr}Categories path{/tr}</label>
						{if $prefs.feature_categories ne 'y'}
							<br />
							{icon _id=information}{tr}Categories are disabled.{/tr} <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.
						{/if}
					</div>
				</div>
			</fieldset>  
		</div>
	{/tab}
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



