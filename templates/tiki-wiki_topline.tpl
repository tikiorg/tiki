<div class="wikitopline clearfix" style="clear: both;">
	<div class="content">
		{if !isset($hide_page_header) or !$hide_page_header}
			<div class="wikiinfo" style="float: left">
				{if $prefs.wiki_page_name_above eq 'y' and $print_page ne 'y'}
				    <a href="tiki-index.php?page={$page|escape:"url"}" class="titletop" title="{tr}refresh{/tr}">{$page|escape}</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{* The hard-coded spaces help selecting the page name for inclusion in a wiki link *}
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
		{/if} {*hide_page_header*}
	</div> {* div.content *}
</div> {* div.wikitopline *}

{if !isset($versioned) and $print_page ne 'y' and (!isset($hide_page_header) or !$hide_page_header)}
<div class="clearfix">
	<div class="wikiactions icons btn-group pull-right">
			{if $pdf_export eq 'y'}
				<a class="btn btn-default" href="tiki-print.php?{query display="pdf" page=$page}" title="{tr}PDF{/tr}">{glyph name='print' alt="{tr}PDF{/tr}"}</a>
			{/if}
			{if $prefs.flaggedrev_approval neq 'y' or ! $revision_approval or $lastVersion eq $revision_displayed}
				{if $editable and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox') and $beingEdited ne 'y' and $machine_translate_to_lang eq ''}
					<a class="btn btn-default" title="{tr}Edit this page{/tr}" {ajax_href template="tiki-editpage.tpl"}tiki-editpage.php?page={$page|escape:"url"}{if !empty($page_ref_id) and $needsStaging neq 'y'}&amp;page_ref_id={$page_ref_id}{/if}{/ajax_href}>{glyph name='edit'  title="{tr}Edit this page{/tr}"}</a>
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
						<a class="btn btn-default" title="{tr}View edit icons{/tr}" href="#" id="wiki_plugin_edit_view">{icon _id='wiki_plugin_edit_view' title="{tr}View edit icons{/tr}"}</a>
					{/if}
				{/if}
				{if ($tiki_p_edit eq 'y' or $tiki_p_edit_inline eq 'y' or $page|lower eq 'sandbox') and $beingEdited ne 'y' and $machine_translate_to_lang eq ''}
					{if $prefs.wysiwyg_inline_editing eq 'y' and $prefs.feature_wysiwyg eq 'y'}
						{jq}
							$("#wysiwyg_inline_edit").click( function () {
								var src = $("#wysiwyg_inline_edit img").attr("src");
								if (src.indexOf("page.png") > -1) {
									if (enableWysiwygInlineEditing()) {
										$("#wysiwyg_inline_edit img").attr("src", src.replace("page.png", "page_lightning.png"));
									}
								} else {
									if (disableWyiswygInlineEditing()) {
										$("#wysiwyg_inline_edit img").attr("src", src.replace("page_lightning.png", "page.png"));
									}
								}
								return false;
							});
							if (getCookie("wysiwyg_inline_edit", "preview")) { $("#wysiwyg_inline_edit").click(); }
						{/jq}
						<a class="btn btn-default" title="{tr}Inline Edit{/tr}" href="#" id="wysiwyg_inline_edit">{glyph name='edit' title="{tr}Inline Edit{/tr}"}</a>
					{/if}
				{/if}
			{/if}
			{if $prefs.feature_morcego eq 'y' && $prefs.wiki_feature_3d eq 'y'}
				<a class="btn btn-default" title="{tr}3d browser{/tr}" href="javascript:wiki3d_open('{$page|escape}',{$prefs.wiki_3d_width}, {$prefs.wiki_3d_height})">{icon _id='wiki3d' alt="{tr}3d browser{/tr}"}</a>
			{/if}
			{if $cached_page eq 'y'}
				<a class="btn btn-default" title="{tr}Refresh{/tr}" href="{$page|sefurl:'wiki':'with_next'}refresh=1">{glyph name='refresh'  title="{tr}Refresh{/tr}"}</a>
			{/if}
			{if $prefs.feature_wiki_print eq 'y'}
				<a class="btn btn-default" title="{tr}Print{/tr}" href="tiki-print.php?{query _keepall='y'}">{glyph name='print'  title="{tr}Print{/tr}"}</a>
			{/if}
	
			{if $prefs.feature_share eq 'y' && $tiki_p_share eq 'y'}
				<a class="btn btn-default" title="{tr}Share this page{/tr}" href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}{icon name="share"}</a>
			{/if}
			{if $prefs.feature_tell_a_friend eq 'y' && $tiki_p_tell_a_friend eq 'y'}
				<a class="btn btn-default" title="{tr}Send a link{/tr}" href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{glyph name='enveloppe' alt="{tr}Send a link{/tr}"}</a>
			{/if}
			{if !empty($user) and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
				<a class="btn btn-default" title="{tr}Save to notepad{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;savenotepad=1{if !empty($page_ref_id)}&amp;page_ref_id={$page_ref_id}{/if}">{glyph name='bookmark' alt="{tr}Save to notepad{/tr}"}</a>
			{/if}
			{monitor_link type="wiki page" object=$page}
			{if !empty($user) and $prefs.feature_user_watches eq 'y'}
				{if $user_watching_page eq 'n'}
					<a class="btn btn-default" href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=add{if $structure eq 'y'}&amp;structure={$home_info.pageName|escape:'url'}{/if}" class="icon" title="{tr}Page is NOT being monitored. Click icon to START monitoring.{/tr}">{icon name="watch"}</a>
				{else}
					<a class="btn btn-default" href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=remove{if $structure eq 'y'}&amp;structure={$home_info.pageName|escape:'url'}{/if}" class="icon" title="{tr}Page IS being monitored. Click icon to STOP monitoring.{/tr}">{icon name="stop-watching"}</a>
				{/if}
				{if $structure eq 'y' and $tiki_p_watch_structure eq 'y'}
					{if $user_watching_structure ne 'y'}
						<a class="btn btn-default" href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=structure_changed&amp;watch_object={$page_info.page_ref_id}&amp;watch_action=add_desc&amp;structure={$home_info.pageName|escape:'url'}">{icon _id='eye_arrow_down' alt="{tr}Monitor the Sub-Structure{/tr}"}</a>
					{else}
						<a class="btn btn-default" href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=structure_changed&amp;watch_object={$page_info.page_ref_id}&amp;watch_action=remove_desc&amp;structure={$home_info.pageName|escape:'url'}">{icon _id='no_eye_arrow_down' alt="{tr}Stop Monitoring the Sub-Structure{/tr}"}</a>
					{/if}
				{/if}
			{/if}
			{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
				<a class="btn btn-default" href="tiki-object_watches.php?objectId={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;objectType=wiki+page&amp;objectName={$page|escape:"url"}&amp;objectHref={'tiki-index.php?page='|cat:$page|escape:"url"}" class="icon" title="{tr}Group Monitor{/tr}">{icon name="watch-group"}</a>
	
				{if $structure eq 'y'}
					<a class="btn btn-default" href="tiki-object_watches.php?objectId={$page_info.page_ref_id|escape:"url"}&amp;watch_event=structure_changed&amp;objectType=structure&amp;objectName={$page|escape:"url"}&amp;objectHref={'tiki-index.php?page_ref_id='|cat:$page_ref_id|escape:"url"}" class="icon">{icon _id=eye_group_arrow_down alt="{tr}Group Monitor on Structure{/tr}"}</a>
				{/if}
			{/if}
			{if $prefs.user_favorites eq 'y'}
				{favorite type="wiki page" object=$page}
			{/if}
			{if $prefs.feature_multilingual eq 'y' && $prefs.show_available_translations eq 'y' && $machine_translate_to_lang eq ''}
				<!--span class="btn-i18n" -->
				{include file='translated-lang.tpl' object_type='wiki page'}
				<!--/span -->
			{/if}
			
			{if $prefs.feature_backlinks eq 'y' and $backlinks and $tiki_p_view_backlink eq 'y'}
				<div class="btn-group backlinks">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						{icon _id=arrow_turn_left title="{tr}Backlinks{/tr}" class="icon"}
					</button>
					<ul class="dropdown-menu" role="menu">
						<li role="presentation">
							{section name=back loop=$backlinks}
								<a role="menuitem" tabindex="-1" href="{$backlinks[back].fromPage|sefurl:'wiki'}" title="{$backlinks[back].fromPage|escape}">
									{if $prefs.wiki_backlinks_name_len ge '1'}{$backlinks[back].fromPage|truncate:$prefs.wiki_backlinks_name_len:"...":true|escape}{else}{$backlinks[back].fromPage|escape}{/if}
								</a>
							{/section}
						</li>
					</ul>
				</div>
			{/if}
			{if $structure eq 'y' or ( $structure eq 'n' and count($showstructs) neq 0 )}
				<div class="btn-group structures">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						{icon _id=chart_organisation title="{tr}Structures{/tr}" class="icon"}							
					</button>
					<ul class="structure_poppedup dropdown-menu" role="menu">
						<li role="presentation">
							{section name=struct loop=$showstructs}
								<a href="tiki-index.php?page={$page}&structure={$showstructs[struct].pageName|escape}" {if $showstructs[struct].pageName eq $structure_path[0].pageName} title="Current structure: {$showstructs[struct].pageName|escape}" class="selected" {else} title="Show structure: {$showstructs[struct].pageName|escape}"{/if}>
									{if $showstructs[struct].page_alias}														
										{$showstructs[struct].page_alias}
									{else}
										{$showstructs[struct].pageName}
									{/if}
								</a>
							{/section}
							{if $showstructs[struct].pageName neq $structure_path[0].pageName}
								<li role="presentation" class="divider"></li>
								<li role="presentation">
									<a href="tiki-index.php?page={$page|escape:url}" title="{tr}Hide structure bar{/tr}">
										{tr}Hide structure{/tr}
									</a>
								</li>
							{/if}	
						</li>
					</ul>
				</div>
			{/if}
	</div> {* END of wikiactions *}
</div>
{/if}
