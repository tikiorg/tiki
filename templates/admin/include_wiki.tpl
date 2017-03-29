{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Use the 'Quick Edit' module to easily create or edit wiki pages.{/tr} <a class="btn btn-default" href="tiki-admin_modules.php">{icon name="module"} {tr}Modules{/tr}</a>
{/remarksbox}
<form class="form-horizontal" action="tiki-admin.php?page=wiki" method="post">
	{include file='access/include_ticket.tpl'}
	<div class="heading input_submit_container text-right">
	</div>
	<div class="t_navbar margin-bottom-md clearfix">
		{button _icon_name='admin_wiki' _text="{tr}Pages{/tr}" _type="link" _class='btn btn-link' _script='tiki-listpages.php' _title="{tr}List wiki pages{/tr}"}
		{if $prefs.feature_wiki_structure eq "y" and $tiki_p_view eq "y"}
			{button _icon_name='structure' _text="{tr}Structures{/tr}"  _type="link" _class='btn btn-link' _script='tiki-admin_structures.php' _title="{tr}List structures{/tr}"}
		{/if}
		<div class="pull-right">
			<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply changes{/tr}" value="{tr}Apply{/tr}">
		</div>
	</div>
	{tabset name="admin_wiki"}
		{tab name="{tr}General Preferences{/tr}"}
			<br>
			<fieldset>
				<legend>{tr}Activate the feature{/tr}</legend>
				{preference name=feature_wiki visible="always"}
				{preference name=wiki_url_scheme}
			</fieldset>
			<fieldset class="table">
				<legend>{tr}Plugins{/tr}</legend>
				{preference name=wikiplugin_author}
				{preference name=wikiplugin_include}
				{preference name=wikiplugin_transclude}
				{preference name=wikiplugin_randominclude}
				{preference name=wikiplugin_slideshow}
				{preference name=wikiplugin_attach}
				{preference name=wikiplugin_backlinks}
				{preference name=wikiplugin_listpages}
				{preference name=wikiplugin_showpages}
				{preference name=wikiplugin_titlesearch}
				{preference name=wikiplugin_wantedpages}
			</fieldset>
			{preference name=wikiHomePage}
			<fieldset>
				<legend>{tr}Page display{/tr}</legend>
				{preference name=feature_wiki_description label="{tr}Description{/tr}"}
				{preference name=feature_page_title label="{tr}Display page name as page title{/tr}"}
				{preference name=wiki_page_name_above label="{tr}Display page name above page{/tr}"}
				{preference name=wiki_page_name_inside label="{tr}Display page name inside page content{/tr}"}
				{preference name=feature_wiki_pageid label="{tr}Page ID{/tr}"}
				{preference name=wiki_show_version label="{tr}Page version{/tr}"}
				{preference name=wiki_authors_style label="{tr}List authors{/tr}"}
				<div class="adminoptionbox wiki_authors_style_childcontainer classic business collaborative lastmodif">
					{preference name=wiki_authors_style_by_page label="{tr}Allow override per page{/tr}"}
				</div>
				{preference name=feature_wiki_show_hide_before}
				{preference name=wiki_actions_bar}
				{preference name=wiki_page_navigation_bar}
				{preference name=wiki_topline_position}
				{preference name=page_bar_position}
				{preference name=wiki_encourage_contribution}
				{preference name=wiki_page_hide_title}
				{preference name=wiki_heading_links}
			</fieldset>
			<fieldset>
				<legend>{tr}Automatic Table of Contents{/tr}</legend>
				{preference name=wiki_auto_toc}
				<div class="adminoptionbox clearfix" id="wiki_auto_toc_childcontainer">
					{preference name=wiki_inline_auto_toc}
					{preference name=wiki_toc_pos}
					{preference name=wiki_toc_offset}
				</div>
			</fieldset>
			<fieldset>
				<legend>{tr}Page name{/tr}</legend>
				{preference name=wiki_page_regex}
				{preference name=wiki_badchar_prevent}
				{preference name=wiki_pagename_strip}
			</fieldset>
			<fieldset>
				<legend>{tr}Editing{/tr}</legend>
				{preference name=feature_wiki_templates}
				<div class="adminoptionboxchild" id="feature_wiki_templates_childcontainer">
					{preference name=lock_content_templates}
				</div>
				{preference name=feature_warn_on_edit}
				{preference name=warn_on_edit_time}
				{preference name=feature_wiki_undo}
				{preference name=feature_wiki_footnotes}
				{preference name=feature_wiki_allowhtml}
				{preference name=feature_wysiwyg}
				{preference name=wiki_timeout_warning}
				{preference name=wiki_edit_icons_toggle}
				{preference name=wiki_edit_section}
				<div class="adminoptionboxchild" id="wiki_edit_section_childcontainer">
					{preference name=wiki_edit_section_level}
				</div>
				{preference name=wiki_edit_minor}
				{preference name=feature_wiki_mandatory_category}
				{preference name=feature_actionlog_bytes}
				{preference name=wiki_mandatory_edit_summary}
				{preference name=wiki_freetags_edit_position}
			</fieldset>
			<fieldset>
				<legend>
					{tr}Sharing on social networks{/tr}{help url="Social+Networks#Using+ShareThis"}
				</legend>
				{preference name=feature_wiki_sharethis}
				<div class="adminoptionboxchild" id="feature_wiki_sharethis_childcontainer">
					{preference name=blog_sharethis_publisher}
					{preference name=wiki_sharethis_encourage}
				</div>
			</fieldset>
		{/tab}
		{tab name="{tr}Features{/tr}"}
			<br>
			{preference name=feature_sandbox}
			{preference name=feature_references}
			{preference name=wiki_pagination}
			<div class="adminoptionboxchild" id="wiki_pagination_childcontainer">
				{preference name=wiki_page_separator}
			</div>
			{preference name=feature_wiki_export}
			<div class="adminoptionboxchild col-md-8 col-sm-offset-4" id="feature_wiki_export_childcontainer">
				{permission_link mode=button permType=wiki textFilter=export showDisabled=y label="{tr}Export permissions{/tr}"}
				{permission_link mode=button permType=wiki textFilter=picture showDisabled=y label="{tr}Picture permissions{/tr}"}
				{button href="tiki-export_wiki_pages.php" _text="{tr}Export Wiki Pages{/tr}"}
			</div>
			{preference name=feature_wikiwords}
			<div class="adminoptionboxchild" id="feature_wikiwords_childcontainer">
				{preference name=feature_wikiwords_usedash}
				{preference name=feature_wiki_plurals}
			</div>
			{preference name=feature_history}
			<div class="adminoptionboxchild" id="feature_history_childcontainer">
				<div class="col-sm-offset-4 col-sm-8" style="margin-bottom:10px">
					{permission_link mode=button permType=wiki textFilter=history showDisabled=y}
				</div>
				{preference name=maxVersions}
				{preference name=keep_versions}
				{preference name=feature_wiki_history_ip}
				{preference name=default_wiki_diff_style}
				{preference name=feature_wiki_history_full}
				{preference name=feature_page_contribution}
			</div>
			{preference name=feature_wiki_discuss}
			<div class="adminoptionboxchild" id="feature_wiki_discuss_childcontainer">
				{preference name=wiki_forum_id}
				<div class="col-sm-offset-4 col-sm-8" style="margin-bottom:10px">
					{permission_link mode=button permType=forums}
				</div>
				{preference name=wiki_discuss_visibility}
			</div>
			{preference name=feature_source}
			<div class="adminoptionboxchild col-md-8 col-sm-offset-4" id="feature_source_childcontainer">
				{permission_link mode=button permType=wiki textFilter=source showDisabled=y}
			</div>
			{preference name=feature_wiki_ratings}
			<div class="adminoptionboxchild col-sm-offset-4 col-sm-8" id="feature_wiki_ratings_childcontainer">
				{permission_link mode=button permType=wiki textFilter=ratings showDisabled=y}
			</div>
			{preference name=wiki_simple_ratings}
			<div class="adminoptionboxchild" id="wiki_simple_ratings_childcontainer">
				{preference name=wiki_simple_ratings_options}
			</div>
			{preference name=feature_backlinks}
			<div class="adminoptionboxchild" id="feature_backlinks_childcontainer">
				{preference name=wiki_backlinks_name_len}
				<div class="col-sm-offset-4 col-md-8">
					{permission_link mode=button permType=wiki textFilter=backlinks showDisabled=y}
				</div>
			</div>
			{preference name=feature_semantic}
			{preference name=wiki_prefixalias_tokens}
			{preference name=feature_likePages}
			<div class="adminoptionboxchild" id="wiki_likepages_samelang_only">
				{preference name=wiki_likepages_samelang_only}
			</div>
			{preference name=feature_wiki_1like_redirection}
			{preference name=feature_wiki_pagealias}
			{preference name=wiki_pagealias_tokens}
			{preference name=feature_wiki_rankings}
			{preference name=feature_wiki_import_html}
			{preference name=feature_wiki_import_page}
			{preference name=feature_wiki_use_date}
			<div class="adminoptionboxchild" id="feature_wiki_use_date_links">
				{preference name=feature_wiki_use_date_links}
			</div>
			{preference name=wiki_uses_slides}
			{preference name=feature_wiki_userpage}
			<div class="adminoptionboxchild" id="feature_wiki_userpage_childcontainer">
				{preference name=feature_wiki_userpage_prefix}
			</div>
			{preference name=feature_wiki_usrlock}
			<div class="adminoptionboxchild col-sm-8 col-sm-offset-4" id="feature_wiki_usrlock_childcontainer">
				{permission_link mode=button permType=wiki textFilter=lock showDisabled=y}
			</div>
			{preference name=wiki_creator_admin}
			{preference name=feature_wiki_mindmap}
			{preference name=wiki_feature_copyrights}
			<div class="adminoptionbox clearfix">
				<fieldset>
					<legend>{tr}Attachments{/tr}</legend>
					{preference name=feature_wiki_attachments}
					<div class="adminoptionboxchild" id="feature_wiki_attachments_childcontainer">
						{preference name=w_displayed_default}
						{preference name=w_use_db}
						<div class="adminoptionboxchild w_use_db_childcontainer n">
							{preference name=w_use_dir}
						</div>
						{if !empty($prefs.w_use_dir)}
							{tr}If you change storage, it is better to move all the files for easy backup...{/tr}
							{button href="tiki-admin.php?page=wikiatt&all2db=1" _text="{tr}Change all to db{/tr}"}
							{button href="tiki-admin.php?page=wikiatt&all2file=1" _text="{tr}Change all to file{/tr}"}
						{/if}
					</div>
					{preference name=feature_wiki_pictures}
					<div class="adminoptionboxchild" id="feature_wiki_pictures_childcontainer">
						<div class="col-sm-offset-4 col-sm-8">
							{permission_link mode=button permType=wiki textFilter=picture showDisabled=y}
						</div>
						{preference name=feature_filegals_manager}
						<div class="col-sm-offset-4 col-sm-8">
							{button href="tiki-admin.php?page=wiki&amp;rmvunusedpic=1" _text="{tr}Remove unused pictures{/tr}"}
							{button href="tiki-admin.php?page=wiki&amp;moveWikiUp=1" _text="{tr}Move images from wiki_up to the home file gallery{/tr}"}
							<span class="help-block">
								{tr}If you use these buttons please make sure to have a backup of the database and the directory wiki_up{/tr}
							</span>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="adminoptionbox clearfix">
				<fieldset>
					<legend>{tr}Comments{/tr}</legend>
					{preference name=feature_wiki_comments}
					<div class="adminoptionboxchild" id="feature_wiki_comments_childcontainer">
						{preference name=wiki_comments_displayed_default}
						{preference name=wiki_comments_form_displayed_default}
						{preference name=wiki_comments_per_page}
						{preference name=wiki_comments_default_ordering}
						{preference name=wiki_comments_allow_per_page}
						{preference name=wiki_watch_comments}
					</div>
				</fieldset>
			</div>
			<div class="adminoptionbox clearfix">
				<fieldset>
					<legend>{tr}Structures{/tr}{help url="Structures"}</legend>
					{preference name=feature_wiki_structure}
					<div class="adminoptionboxchild" id="feature_wiki_structure_childcontainer">
						{preference name=feature_wiki_open_as_structure}
						{preference name=feature_wiki_make_structure}
						{preference name=feature_wiki_categorize_structure}
						{preference name=lock_wiki_structures}
						{preference name=feature_create_webhelp}
						{preference name=feature_wiki_structure_drilldownmenu}
						{preference name=page_n_times_in_a_structure}
						{preference name=feature_listorphanStructure}
						{preference name=feature_wiki_no_inherit_perms_structure}
						{preference name=wiki_structure_bar_position}
						{preference name=wikiplugin_toc}
					</div>
				</fieldset>
			</div>
			<div class="adminoptionbox clearfix">
				<fieldset>
					<legend>{tr}Wiki watch{/tr}{help url="Watch"}</legend>
					{preference name=wiki_watch_author}
					{preference name=wiki_watch_editor}
					{preference name=wiki_watch_comments}
					{preference name=wiki_watch_minor}
				</fieldset>
			</div>
			{preference name=wiki_keywords}
			{preference name=geo_locate_wiki}
			<fieldset>
				<legend>{tr}Namespaces{/tr}{help url="Namespaces"}</legend>
				{preference name=namespace_enabled}
				<div class="adminoptionboxchild" id="namespace_enabled_childcontainer">
					<div class="col-sm-offset-4 colsm-8">
						{tr}The namespace separator should not{/tr}
						<ul>
							<li>{tr}contain any of the characters not allowed in wiki page names, typically{/tr} /?#[]@$&amp;+;=&lt;&gt;</li>
							<li>{tr}conflict with wiki syntax tagging{/tr}</li>
						</ul>
					</div>
					{preference name=namespace_separator}
					{preference name=namespace_indicator_in_structure}
					{preference name=namespace_indicator_in_page_title}
					<div class="col-sm-offset-4 colsm-8">
						<p><strong>{tr}Settings that may be affected by the namespace separator{/tr}</strong></p>
						{tr}To use :: as a separator, you should also use ::: as the wiki center tag syntax{/tr}.<br/>
						{tr}Note: a conversion of :: to ::: for existing pages must be done manually.{/tr}<br/>
					</div>
					{preference name=feature_use_three_colon_centertag}
					<div class="col-sm-offset-4 colsm-8">
						{tr}If the page name display stripper conflicts with the namespace separator, the namespace is used and the page name display is not stripped.{/tr}
					</div>
					{preference name=wiki_pagename_strip}
					{preference name=namespace_force_links}
				</div>
			</fieldset>
		{/tab}
		{tab name="{tr}Flagged Revision{/tr}"}
			<br>
			<fieldset>
				<legend>{tr}Revision Approval{/tr}</legend>
				{preference name=flaggedrev_approval}
				<div id="flaggedrev_approval_childcontainer">
					{preference name=flaggedrev_approval_categories}
				</div>
			</fieldset>
		{/tab}
		{tab name="{tr}Page Listings{/tr}"}
			<br>
			{preference name=feature_listPages}
			{preference name=feature_lastChanges}
			{preference name=feature_listorphanPages}
			{preference name=feature_listorphanStructure}
			{preference name=gmap_page_list}
			<div class="adminoptionbox clearfix">
				<fieldset>
					<legend>{tr}Configuration{/tr}</legend>
					<div class="adminoptionbox clearfix">
						{tr}Select which items to display when listing pages:{/tr}
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
					{preference name=wiki_list_rating}
				</fieldset>
			</div>
		{/tab}
		{tab name="{tr}Tools{/tr}"}
			<br>

				<div>
					<h4>Database Dumps & Restores</h4>

					Create database archives of wiki pages for restoring at a later date.<br>
					<label for="tagname">{tr}Name for Dump:{/tr}</label>
					<input maxlength="20" size="20" type="text" name="newtagname" id="newtagname">
					<input type="submit" class="btn btn-default btn-sm" name="createtag" value="{tr}Create Database Dump{/tr}"><br>

					<label for="databasetag">{tr}Wiki Database:{/tr}</label>
					<select name="tagname" {if $tags|@count eq '0'} disabled="disabled"{/if}>
						{section name=sel loop=$tags}
							<option value="{$tags[sel]|escape}">{$tags[sel]}</option>
							{sectionelse}
							<option value=''>{tr}None{/tr}</option>
						{/section}
					</select>
					<input type="submit" class="btn btn-default btn-sm" name="restoretag" value="{tr}Restore{/tr}"{if $tags|@count eq '0'} disabled="disabled"{/if}>
					<input type="submit" class="btn btn-default btn-sm" name="removetag" value="{tr}Remove{/tr}"{if $tags|@count eq '0'} disabled="disabled"{/if}>
					<h4>{tr}Dump Files{/tr}</h4>
					{tr}Dump files archive wiki pages for various usages such as off-line browsing or distribution on optical disks.{/tr}
					<h5>{tr}Warnings{/tr}</h5>
					<ul>
						<li>{tr}The HTML files generated may refer to files not included in the dump.{/tr} {tr}Dumps do not include files attached to wiki pages.{/tr}</li>
						{if $isDump}<li>{tr}Dumping will overwrite the preexisting dump.{/tr}</li>{/if}
					</ul>
					<br><br>

					<input type="submit" class="btn btn-default btn-sm" name="createdump" value="{tr}Create Dump File{/tr}">
					<input type="submit" class="btn btn-default btn-sm" name="downloaddump" value="{tr}Download Dump File{/tr}" {if !$isDump} disabled="disabled"{/if}>
					<input type="submit" class="btn btn-default btn-sm" name="removedump" data-target="_blank" value="{tr}Remove Dump File{/tr}" {if !$isDump} disabled="disabled"{/if}>
				</div>
			<br>
			<h4><a href="tiki-search_replace.php">{tr}Mass search and replace{/tr}</a></h4>

			<h4><a href="tiki-report_direct_object_perms.php">{tr}Report wiki pages with direct object permissions{/tr}</a></h4>
		{/tab}
	{/tabset}
	<div class="t_navbar margin-bottom-md text-center">
		<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply changes{/tr}" value="{tr}Apply{/tr}">
	</div>
</form>
