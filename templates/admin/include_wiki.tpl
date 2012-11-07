{* $Id$ *}

{if !empty($feedbacksWikiUp) || !empty($moveWikiUp)}
	{remarksbox type="feedback" title="{tr}Feedback{/tr}"}
	{if !empty($feedbacksWikiUp)}
		{tr}The following pages were modified:{/tr}
		<ul>
		{foreach from=$feedbacksWikiUp item=f}
			<li>{$f|escape}</li>
		{/foreach}
		</ul>
	{else}
		{tr}Nothing was changed (no images in wiki_up were found in Wiki pages).{/tr}
	{/if}
	{/remarksbox}
{elseif !empty($moveWikiUp)}
{/if}
{if !empty($errorsWikiUp)}
	{remarksbox type="errors" title="{tr}Errors{/tr}"}
		<ul>
		{foreach from=$errorsWikiUp item=f}
			<li>{$f|escape}</li>
		{/foreach}
		</ul>
	{/remarksbox}
{/if}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Use the 'Quick Edit' module to easily create or edit wiki pages.{/tr} {tr}Select <a class="rbox-link" href="tiki-admin_modules.php">Admin &gt; Modules</a> to add this (or other) modules.{/tr}
{/remarksbox}

<form action="tiki-admin.php?page=wiki" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="wikisetprefs" value="{tr}Change preferences{/tr}" />
	</div>

	{tabset name="admin_wiki"}
		{tab name="{tr}General Preferences{/tr}"}
		
			<fieldset class="admin">
				<legend>{tr}Activate the feature{/tr}</legend>
				{preference name=feature_wiki visible="always"}
			</fieldset>

			<fieldset class="admin">
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
				{preference name=feature_wiki_pageid label="{tr}Page ID{/tr}"}
				{preference name=wiki_show_version label="{tr}Page version{/tr}"}
				{preference name=wiki_authors_style label="{tr}List authors{/tr}"}

				<div class="adminoptionboxchild">
					{preference name=wiki_authors_style_by_page label="{tr}Allow override per page{/tr}"}
				</div>

				{preference name=feature_wiki_show_hide_before}
				{preference name=wiki_actions_bar}
				{preference name=wiki_page_navigation_bar}
				{preference name=wiki_topline_position}
				{preference name=page_bar_position}
				{preference name=wiki_encourage_contribution}
			</fieldset>

			<fieldset>
				<legend>{tr}Page name{/tr}</legend>

				{preference name=wiki_page_regex}
				{preference name=wiki_badchar_prevent}
				{preference name=wiki_pagename_strip}
			</fieldset>
			
			<fieldset>
				<legend>{tr}Edit{/tr}</legend>

				{preference name=feature_wiki_templates}
				{preference name=feature_warn_on_edit}
				{preference name=warn_on_edit_time}
				{preference name=feature_wiki_undo}
				{preference name=feature_wiki_footnotes}
				{preference name=feature_wiki_allowhtml}
				{preference name=feature_wysiwyg}				
				{preference name=wiki_timeout_warning}

				{preference name=wiki_edit_section}
				<div class="adminoptionboxchild" id="wiki_edit_section_childcontainer">
					{preference name=wiki_edit_section_level}
				</div>

				{preference name=wiki_edit_icons_toggle}
				{preference name=wiki_edit_minor}
				<div class="adminoptionboxchild" id="wiki_edit_minor_childcontainer">
					{remarksbox type=note title="{tr}Note{/tr}"}{tr}Minor edits do not flag new content for translation and do not send watch notifications (unless "Watch minor edits" is enabled).{/tr}
						<br />
						{tr}Only user groups granted the tiki_p_minor permission (and admins) will be able to save minor edits when this is enabled.{/tr}
						<a class="link" href="tiki-objectpermissions.php?permType=wiki&amp;textFilter=minor&amp;show_disabled_features=y" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
					{/remarksbox}
				</div>
				{preference name=feature_wiki_mandatory_category}
				{preference name=feature_actionlog_bytes}
				{preference name=wiki_mandatory_edit_summary}
			</fieldset>
		{/tab}

		{tab name="{tr}Features{/tr}"}
			<input type="hidden" name="wikifeatures" />    	

			{preference name=feature_sandbox}
			{preference name=feature_references}
			{preference name=feature_wiki_comments}
			
			{preference name=feature_wiki_attachments}
			<div class="adminoptionboxchild" id="feature_wiki_attachments_childcontainer">
					{preference name=w_displayed_default}
					{preference name=w_use_db}
					<div class="adminoptionboxchild">
						{preference name=w_use_dir}
					</div>
					{if !empty($prefs.w_use_dir)}
						{tr}If you change storage, it is better to move all the files for easy backup...{/tr}
						{button href="tiki-admin.php?page=wikiatt&all2db=1" _text="{tr}Change all to db{/tr}"}
						{button href="tiki-admin.php?page=wikiatt&all2file=1" _text="{tr}Change all to file{/tr}"}
					{/if}
			</div>

			{preference name=feature_dump}
			<div class="adminoptionboxchild" id="feature_dump_childcontainer">
				<div class="adminoptionbox clearfix">
					<div class="adminoptionlabel">
						<label for="tagname">{tr}Tag for current wiki:{/tr}</label>
						<input maxlength="20" size="20" type="text" name="tagname" id="tagname" />
						<input type="submit" name="createtag" value="{tr}Create{/tr}" />
					</div>
				</div>
				<div class="adminoptionbox clearfix">
					<div class="adminoptionlabel">
						<label for="restoretag">{tr}Restore wiki to tag:{/tr}</label>
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
				<div class="adminoptionbox clearfix">
					<div class="adminoptionlabel">
						<label for="removetag">{tr}Remove a tag:{/tr}</label> 
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
			<a class="link" href="tiki-objectpermissions.php?permType=wiki&amp;textFilter=picture&amp;show_disabled_features=y" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
			{preference name=feature_filegals_manager}
			{button href="tiki-admin.php?page=wiki&amp;rmvunusedpic=1" _text="{tr}Remove unused pictures{/tr}"}
			{button href="tiki-admin.php?page=wiki&amp;moveWikiUp=1" _text="{tr}Move images from wiki_up to the home file gallery{/tr}"}
			<br /><em>{tr}If you use these buttons please make sure to have a backup of the database and the directory wiki_up{/tr}</em>
		</div>

		{preference name=feature_wiki_export}
		<div class="adminoptionboxchild" id="feature_wiki_export_childcontainer">
			<a class="link" href="tiki-objectpermissions.php?permType=wiki&amp;textFilter=export&amp;show_disabled_features=y" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
			{button href="tiki-export_wiki_pages.php" _text="{tr}Export Wiki Pages{/tr}"}
		</div>
    
		{preference name=feature_wikiwords}
		<div class="adminoptionboxchild" id="feature_wikiwords_childcontainer">
			{preference name=feature_wikiwords_usedash}
			{preference name=feature_wiki_plurals}
		</div>

		{preference name=feature_history}
		<div class="adminoptionboxchild" id="feature_history_childcontainer">
			<a class="link" href="tiki-objectpermissions.php?permType=wiki&amp;textFilter=history&amp;show_disabled_features=y" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
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
			<a class="link" href="tiki-objectpermissions.php?permType=forums" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
		</div>

		{preference name=feature_source}
		<div class="adminoptionboxchild" id="feature_source_childcontainer">
			<a class="link" href="tiki-objectpermissions.php?permType=wiki&amp;textFilter=source&amp;show_disabled_features=y" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
		</div>

		{preference name=feature_wiki_ratings}
		<div class="adminoptionboxchild" id="feature_wiki_ratings_childcontainer">
			<a class="link" href="tiki-objectpermissions.php?permType=wiki&amp;textFilter=ratings&amp;show_disabled_features=y" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
		</div>
		{preference name=wiki_simple_ratings}
		<div class="adminoptionboxchild" id="wiki_simple_ratings_childcontainer">
			{preference name=wiki_simple_ratings_options}
		</div>

		{preference name=feature_backlinks}
		<div class="adminoptionboxchild" id="feature_backlinks_childcontainer">
			{preference name=wiki_backlinks_name_len}
			<a class="link" href="tiki-objectpermissions.php?permType=wiki&amp;textFilter=backlinks&amp;show_disabled_features=y" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
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

		{preference name=feature_wiki_structure}
		<div class="adminoptionboxchild" id="feature_wiki_structure_childcontainer">
			{preference name=feature_wiki_open_as_structure}
			{preference name=feature_wiki_make_structure}
			{preference name=feature_wiki_categorize_structure}
			{preference name=feature_create_webhelp}
			{preference name=feature_wiki_structure_drilldownmenu}
			{preference name=page_n_times_in_a_structure}
			{preference name=feature_wiki_no_inherit_perms_structure}
			{preference name=wiki_structure_bar_position}
			{preference name=wikiplugin_toc}
			{preference name=namespace_indicator_in_structure}
		</div>

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
		<div class="adminoptionboxchild" id="feature_wiki_usrlock_childcontainer">
			<a class="link" href="tiki-objectpermissions.php?permType=wiki&amp;textFilter=lock&amp;show_disabled_features=y" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
		</div>

		{preference name=wiki_creator_admin}
		{preference name=feature_wiki_print}
		<div class="adminoptionboxchild" id="feature_wiki_print_childcontainer">
			{preference name=feature_wiki_multiprint}
		</div>
		{preference name=feature_print_indexed}
		
		{preference name=feature_wiki_mindmap}
		{preference name=feature_morcego}

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

		{preference name=namespace_enabled}
		<div class="adminoptionboxchild" id="namespace_enabled_childcontainer">
		{preference name=namespace_separator}
		</div>
	{/tab}

	{tab name="{tr}Flagged Revision{/tr}"}
		<fieldset>
			<legend>{tr}Revision Approval{/tr}</legend>

			{preference name=flaggedrev_approval}
			<div id="flaggedrev_approval_childcontainer">
				{preference name=flaggedrev_approval_categories}
			</div>
		</fieldset>
	{/tab}

	{tab name="{tr}Page Listings{/tr}"}
		<input type="hidden" name="wikilistprefs" />	  
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
	
	{tab name="{tr}Tools{/tr}"}
		<a href="tiki-search_replace.php">{tr}Mass search and replace{/tr}</a><br />
		<a href="tiki-report_direct_object_perms.php">{tr}Report wiki pages with direct object permissions{/tr}</a><br />
	{/tab}
{/tabset}

<div class="heading input_submit_container" style="text-align: center">
	<input type="submit" name="wikisetprefs" value="{tr}Change preferences{/tr}" />
</div>
</form>
