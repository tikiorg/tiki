<div class="wikitopline clearfix" style="clear: both;">
	<div class="content">
		{if !isset($hide_page_header) or !$hide_page_header}
		<div class="wikiinfo" style="float: left">

{if $prefs.wiki_page_name_above eq 'y' and $print_page ne 'y'}
    <a href="javascript:self.location=self.location;" class="titletop" title="{tr}refresh{/tr}">{$page}</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{* The hard-coded spaces help selecting the page name for inclusion in a wiki link *}
{/if}

{if $prefs.feature_wiki_pageid eq 'y' and $print_page ne 'y'}
			<small><a class="link" href="tiki-index.php?page_id={$page_id}">{tr}page id:{/tr} {$page_id}</a></small>
{/if}

{breadcrumbs type="desc" loc="page" crumbs=$crumbs}

{if $cached_page eq 'y'}<span class="cachedStatus">({tr}Cached{/tr})</span>{/if}
{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.feature_categorypath eq 'y' and $tiki_p_view_category eq 'y'}
	{$display_catpath}
{/if}
		</div>

{if not $versioned}
	{if $print_page ne 'y'}
			<div class="wikiactions" style="float: right; padding-left:10px; white-space: nowrap">
				<div class="icons" style="float: left;">
		{if $pdf_export eq 'y'}
					<a href="tiki-print.php?{query display="pdf" page=$page}" title="{tr}PDF{/tr}">{icon _id='page_white_acrobat' alt="{tr}PDF{/tr}"}</a>
		{/if}
		{if $prefs.flaggedrev_approval neq 'y' or ! $revision_approval or $lastVersion eq $revision_displayed}
			{if $editable and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox') and $beingEdited ne 'y' and $machine_translate_to_lang eq '' or $canEditStaging eq 'y'}
				<a title="{tr}Edit this page{/tr}" {ajax_href template="tiki-editpage.tpl"}tiki-editpage.php?page={if $needsStaging eq 'y'}{$stagingPageName|escape:"url"}{else}{$page|escape:"url"}{/if}{if !empty($page_ref_id) and $needsStaging neq 'y'}&amp;page_ref_id={$page_ref_id}{/if}{/ajax_href}>{icon _id='page_edit' alt="{tr}Edit this page{/tr}"}</a>
				{if $prefs.wiki_edit_icons_toggle eq 'y' and ($prefs.wiki_edit_plugin eq 'y' or $prefs.wiki_edit_section eq 'y')}
					{jq}
					$("#wiki_plugin_edit_view").click( function () {
						var src = $("#wiki_plugin_edit_view img").attr("src");
						if (src.indexOf("wiki_plugin_edit_view") > -1) {
							$(".editplugin, .icon_edit_section").show();
							$("#wiki_plugin_edit_view img").attr("src", src.replace("wiki_plugin_edit_view", "wiki_plugin_edit_hide"));
							setCookieBrowser("wiki_plugin_edit_view", true);
						} else {
							$(".editplugin, .icon_edit_section").hide();
							$("#wiki_plugin_edit_view img").attr("src", src.replace("wiki_plugin_edit_hide", "wiki_plugin_edit_view"));
							deleteCookie("wiki_plugin_edit_view");
						}
						return false;
					});
					if (!getCookie("wiki_plugin_edit_view")) {$(".editplugin, .icon_edit_section").hide(); } else { $("#wiki_plugin_edit_view").click(); }
					{/jq}
				<a title="{tr}View edit icons{/tr}" href="#" id="wiki_plugin_edit_view">{icon _id='wiki_plugin_edit_view' title="{tr}View edit icons{/tr}"}</a>
				{/if}
			{/if}
		{/if}
		{if $prefs.feature_morcego eq 'y' && $prefs.wiki_feature_3d eq 'y'}
					<a title="{tr}3d browser{/tr}" href="javascript:wiki3d_open('{$page|escape}',{$prefs.wiki_3d_width}, {$prefs.wiki_3d_height})">{icon _id='wiki3d' alt="{tr}3d browser{/tr}"}</a>
		{/if}
		{if $cached_page eq 'y'}
					<a title="{tr}Refresh{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;refresh=1">{icon _id='arrow_refresh'}</a>
		{/if}
		{if $prefs.feature_wiki_print eq 'y'}
					<a title="{tr}Print{/tr}" href="tiki-print.php?{if !empty($page_ref_id)}page_ref_id={$page_ref_id}&amp;{/if}page={$page|escape:"url"}">{icon _id='printer' alt="{tr}Print{/tr}"}</a>
		{/if}

		{if $prefs.feature_share eq 'y' && $tiki_p_share eq 'y'}
					<a title="{tr}Share this page{/tr}" href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{icon _id='share_link' alt="{tr}Share this page{/tr}"}</a>
		{/if}
		{if $prefs.feature_tell_a_friend eq 'y' && $tiki_p_tell_a_friend eq 'y'}
					<a title="{tr}Send a link{/tr}" href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{icon _id='email_link' alt="{tr}Send a link{/tr}"}</a>
		{/if}
		{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
					<a title="{tr}Save to notepad{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;savenotepad=1{if !empty($page_ref_id)}&amp;page_ref_id={$page_ref_id}{/if}">{icon _id='disk' alt="{tr}Save to notepad{/tr}"}</a>
		{/if}
		{if $user and $prefs.feature_user_watches eq 'y'}
			{if $user_watching_page eq 'n'}
					<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=add{if $structure eq 'y'}&amp;structure={$home_info.pageName|escape:'url'}{/if}" class="icon">{icon _id='eye' alt="{tr}Page is NOT being monitored. Click icon to START monitoring.{/tr}"}</a>
			{else}
					<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=remove{if $structure eq 'y'}&amp;structure={$home_info.pageName|escape:'url'}{/if}" class="icon">{icon _id='no_eye' alt="{tr}Page IS being monitored. Click icon to STOP monitoring.{/tr}"}</a>
			{/if}
			{if $structure eq 'y' and $tiki_p_watch_structure eq 'y'}
				{if $user_watching_structure ne 'y'}
					<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=structure_changed&amp;watch_object={$page_info.page_ref_id}&amp;watch_action=add_desc&amp;structure={$home_info.pageName|escape:'url'}">{icon _id='eye_arrow_down' alt="{tr}Monitor the Sub-Structure{/tr}"}</a>
				{else}
					<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=structure_changed&amp;watch_object={$page_info.page_ref_id}&amp;watch_action=remove_desc&amp;structure={$home_info.pageName|escape:'url'}">{icon _id='no_eye_arrow_down' alt="{tr}Stop Monitoring the Sub-Structure{/tr}"}</a>
				{/if}
			{/if}
		{/if}
		{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
			<a href="tiki-object_watches.php?objectId={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;objectType=wiki+page&amp;objectName={$page|escape:"url"}&amp;objectHref={'tiki-index.php?page='|cat:$page|escape:"url"}" class="icon">{icon _id='eye_group' alt="{tr}Group Monitor{/tr}"}</a>

			{if $structure eq 'y'}
				<a href="tiki-object_watches.php?objectId={$page_info.page_ref_id|escape:"url"}&amp;watch_event=structure_changed&amp;objectType=structure&amp;objectName={$page|escape:"url"}&amp;objectHref={'tiki-index.php?page_ref_id='|cat:$page_ref_id|escape:"url"}" class="icon">{icon _id=eye_group_arrow_down alt="{tr}Group Monitor on Structure{/tr}"}</a>
			{/if}
		{/if}
				</div><!-- END of icons -->

		{if $prefs.feature_backlinks eq 'y' and $backlinks and $tiki_p_view_backlink eq 'y'}
				<form action="tiki-index.php" method="get" style="display: block; float: left">
					<select name="page" onchange="this.form.submit()" title="{tr}Backlinks{/tr}">
						<option>{tr}Backlinks{/tr}...</option>
			{section name=back loop=$backlinks}
						<option value="{$backlinks[back].fromPage}" title="{$backlinks[back].fromPage}">
						{if $prefs.wiki_backlinks_name_len ge '1'}{$backlinks[back].fromPage|truncate:$prefs.wiki_backlinks_name_len:"...":true}{else}{$backlinks[back].fromPage}{/if}
						</option>
			{/section}
					</select>
				</form>
		{/if}

		{if ( $structure eq 'y' and count($showstructs) gt 1 )
				or 
				( $structure eq 'n' and count($showstructs) neq 0 )}
				<form action="tiki-index.php" method="post" style="float: left">
					<select name="page_ref_id" onchange="this.form.submit()">
						<option>{tr}Structures{/tr}...</option>
			{section name=struct loop=$showstructs}
						<option value="{$showstructs[struct].req_page_ref_id}" {if $showstructs[struct].pageName eq $structure_path[0].pageName}selected="selected"{/if}>
			{if $showstructs[struct].page_alias}
				{$showstructs[struct].page_alias}
			{else}
				{$showstructs[struct].pageName}
			{/if}
						</option>
			{/section}
					</select>
				</form>
		{/if}

		{if $prefs.feature_multilingual eq 'y' && $prefs.show_available_translations eq 'y' && $machine_translate_to_lang eq ''}
				<div style="float: left">
			{include file='translated-lang.tpl' td='n'}
				</div>
		{/if}
			</div>
	{/if} {* <-- end of if $print_page ne 'y' *}
{/if}
{/if} {*hide_page_header*}
	</div> {* div.content *}
</div> {* div.wikitopline *}
