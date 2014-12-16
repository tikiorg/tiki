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
	<div class="wikiactions_wrapper clearfix">
		<div class="wikiactions icons btn-group pull-right">
			{if $pdf_export eq 'y'}
				<a href="tiki-print.php?{query display="pdf" page=$page}">
					{icon name="pdf" class="btn btn-link btn-sm tikihelp" title=":{tr}PDF{/tr}"}
				</a>
			{/if}

			{if $prefs.flaggedrev_approval neq 'y' or ! $revision_approval or $lastVersion eq $revision_displayed}
				{if $editable and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox') and $beingEdited ne 'y' and $machine_translate_to_lang eq ''}
					<a {ajax_href template="tiki-editpage.tpl"}tiki-editpage.php?page={$page|escape:"url"}{if !empty($page_ref_id) and $needsStaging neq 'y'}&amp;page_ref_id={$page_ref_id}{/if}{/ajax_href}>
						{icon name="edit" class="btn btn-link btn-sm tikihelp" title=":{tr}Edit this page{/tr}"}
					</a>
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
						<a href="#" id="wiki_plugin_edit_view">
							{icon _id='wiki_plugin_edit_view' class="btn btn-link btn-sm tikihelp" title=":{tr}View edit icons{/tr}" }
						</a>
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
						<a href="#" id="wysiwyg_inline_edit">
							{icon name='edit' class="btn btn-link btn-sm tikihelp" title=":{tr}Inline edit{/tr}"}
						</a>
					{/if}
				{/if}
			{/if}

			{if $prefs.feature_morcego eq 'y' && $prefs.wiki_feature_3d eq 'y'}
				<a class="btn btn-link btn-sm tikihelp" title=":{tr}3d browser{/tr}" href="javascript:wiki3d_open('{$page|escape}',{$prefs.wiki_3d_width}, {$prefs.wiki_3d_height})">{icon name="wiki3d"}</a>
			{/if}
			{if $cached_page eq 'y'}
				<a href="{$page|sefurl:'wiki':'with_next'}refresh=1">
					{icon name="refresh" class="btn btn-link btn-sm tikihelp" title=":{tr}Refresh{/tr}"}
				</a>
			{/if}
			{if $prefs.feature_wiki_print eq 'y'}
				<a href="tiki-print.php?{query _keepall='y'}">
					{icon name="print" class="btn btn-link btn-sm tikihelp" title=":{tr}Print{/tr}" }
				</a>
			{/if}

			{if $prefs.feature_share eq 'y' && $tiki_p_share eq 'y'}
				<a href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}">
					{icon name="share" class="btn btn-link btn-sm tikihelp" title=":{tr}Share this page{/tr}" }
				</a>
			{/if}
			{if $prefs.feature_tell_a_friend eq 'y' && $tiki_p_tell_a_friend eq 'y'}
				<a href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">
					{icon name="envelope" class="btn btn-link btn-sm tikihelp" title=":{tr}Send a link{/tr}"}
				</a>
			{/if}
			{if !empty($user) and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
				<a href="tiki-index.php?page={$page|escape:"url"}&amp;savenotepad=1{if !empty($page_ref_id)}&amp;page_ref_id={$page_ref_id}{/if}">
					{icon name="notepad" class="btn btn-link btn-sm tikihelp" title=":{tr}Save to notepad{/tr}"}
				</a>
			{/if}

			{monitor_link type="wiki page" object=$page}
			{if !empty($user) and $prefs.feature_user_watches eq 'y'}
				{if $user_watching_page eq 'n'}
					<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=add{if $structure eq 'y'}&amp;structure={$home_info.pageName|escape:'url'}{/if}" class="icon">
						{icon name="watch" class="btn btn-link btn-sm tikihelp" title="{tr}Page is NOT being monitored:Click icon to START monitoring.{/tr}"}
					</a>
				{else}
					<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=remove{if $structure eq 'y'}&amp;structure={$home_info.pageName|escape:'url'}{/if}" class="icon">
						{icon name="stop-watching" class="btn btn-link btn-sm tikihelp" title="{tr}Page IS being monitored:Click icon to STOP monitoring.{/tr}"}
					</a>
				{/if}
				{if $structure eq 'y' and $tiki_p_watch_structure eq 'y'}
					{if $user_watching_structure ne 'y'}
						<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=structure_changed&amp;watch_object={$page_info.page_ref_id}&amp;watch_action=add_desc&amp;structure={$home_info.pageName|escape:'url'}">
							{icon name="watch" class="btn btn-link btn-sm tikihelp" title=":{tr}Monitor the sub-structure{/tr}"}
						</a>
					{else}
						<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=structure_changed&amp;watch_object={$page_info.page_ref_id}&amp;watch_action=remove_desc&amp;structure={$home_info.pageName|escape:'url'}">
							{icon name="stop-watching" class="btn btn-link btn-sm tikihelp" title=":{tr}Stop Monitoring the sub-structure{/tr}"}
						</a>
					{/if}
				{/if}
			{/if}

			{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
				<a href="tiki-object_watches.php?objectId={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;objectType=wiki+page&amp;objectName={$page|escape:"url"}&amp;objectHref={'tiki-index.php?page='|cat:$page|escape:"url"}" class="icon">
					{icon name="watch-group" class="btn btn-link btn-sm tikihelp" title=":{tr}Group monitor{/tr}"}
				</a>
				{if $structure eq 'y'}
					<a href="tiki-object_watches.php?objectId={$page_info.page_ref_id|escape:"url"}&amp;watch_event=structure_changed&amp;objectType=structure&amp;objectName={$page|escape:"url"}&amp;objectHref={'tiki-index.php?page_ref_id='|cat:$page_ref_id|escape:"url"}" class="icon">
						{icon name="watch-group" class="btn btn-link btn-sm tikihelp" title=":{tr}Group monitor on structure{/tr}"}
					</a>
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
					<a data-toggle="dropdown">
						{icon name="backlink" class="btn btn-link btn-sm dropdown-toggle tikihelp" title=":{tr}Backlinks{/tr}"}
					</a>
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
					<a class="dropdown-toggle" data-toggle="dropdown">
						{icon name="structure" title=":{tr}Structures{/tr}" class="btn btn-link btn-sm icon tikihelp"}
					</a>
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
