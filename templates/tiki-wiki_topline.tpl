{* $Id$ *}
<div class="wikitopline clearfix" style="clear: both;">
	<div class="content">
		{if !isset($hide_page_header) or !$hide_page_header}
			<div class="wikiinfo pull-left">
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
	{if $prefs.javascript_enabled != 'y'}
		{$js = 'n'}
	{else}
		{$js = 'y'}
	{/if}
	<div class="wikiactions_wrapper clearfix">
		<div class="wikiactions icons btn-group pull-right">
			<div class="btn-group">
				{* languages and structures are separate dropdowns*}
				{if $prefs.feature_multilingual eq 'y' && $prefs.show_available_translations eq 'y' && $machine_translate_to_lang eq ''}
					<!--span class="btn-i18n" -->
					{include file='translated-lang.tpl' object_type='wiki page'}
					<!--/span -->
				{/if}

				{* if we want a ShareThis icon and we want it displayed prominently *}
				{if $prefs.feature_wiki_sharethis eq "y" and $prefs.wiki_sharethis_encourage eq "y"}
					{* Similar as in the blogs except there can be only one per page, so it is simpler *}
					<div class="btn-group">
						{literal}
						<script type="text/javascript">
							//Create your sharelet with desired properties and set button element to false
							var object = SHARETHIS.addEntry({ title:'{/literal}{$page|escape:"url"}{literal}'}, {button:false});
							//Output your customized button
							document.write('<a class="btn btn-link tips" id="share" href="#"{/literal} title="{tr}ShareThis{/tr}">{icon name="sharethis"}{literal}</a>');
							//Tie customized button to ShareThis button functionality.
							var element = document.getElementById("share");
							object.attachButton(element);
						</script>
						{/literal}
					</div>
				{/if}

				{if $prefs.feature_backlinks eq 'y' and $backlinks|default:null and $tiki_p_view_backlink eq 'y'}
					<div class="btn-group backlinks">
						{if $js == 'n'}<ul class="cssmenu_horiz"><li>{/if}
						<a role="button" data-toggle="dropdown" data-hover="dropdown" class="btn btn-link dropdown-toggle">
							{icon name="backlink"}
						</a>
						<ul class="dropdown-menu" role="menu">
							<li class="dropdown-title">
								{tr}Backlinks{/tr}
							</li>
							<li class="divider"></li>
							<li role="presentation">
								{section name=back loop=$backlinks}
									<a role="menuitem" tabindex="-1" href="{$backlinks[back].fromPage|sefurl:'wiki'}" title="{$backlinks[back].fromPage|escape}">
										{if $prefs.wiki_backlinks_name_len ge '1'}{$backlinks[back].fromPage|truncate:$prefs.wiki_backlinks_name_len:"...":true|escape}{else}{$backlinks[back].fromPage|escape}{/if}
									</a>
								{/section}
							</li>
						</ul>
						{if $js == 'n'}</li></ul>{/if}
					</div>
				{/if}
				{if $structure eq 'y' or ( $structure eq 'n' and count($showstructs) neq 0 )}
					<div class="btn-group structures">
						{if $js == 'n'}<ul class="cssmenu_horiz"><li>{/if}
						<a class="btn btn-link dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
							{icon name="structure"}
						</a>
						<ul class="dropdown-menu dropdown-menu-right" role="menu">
							<li class="dropdown-title">
								{tr}Structures{/tr}
							</li>
							<li class="divider"></li>
							<li role="presentation">
								{section name=struct loop=$showstructs}
									<a href="tiki-index.php?page={$page}&amp;structure={$showstructs[struct].pageName|escape}" {if isset($structure_path[0].pageName) and $showstructs[struct].pageName eq $structure_path[0].pageName} title="Current structure: {$showstructs[struct].pageName|escape}" class="selected tips" {else} class="tips" title="{tr}Show structure{/tr}: {$showstructs[struct].pageName|escape}"{/if}>
										{if $showstructs[struct].page_alias}
											{$showstructs[struct].page_alias}
										{else}
											{$showstructs[struct].pageName}
										{/if}
									</a>
								{/section}
							</li>
							{if isset($structure_path) && $showstructs[struct].pageName neq $structure_path[0].pageName and $prefs.feature_wiki_open_as_structure neq 'y'}
								<li role="presentation" class="divider"></li>
								<li role="presentation">
									<a href="tiki-index.php?page={$page|escape:url}" class="tips" title=":{tr}Hide structure bar and any toc{/tr}">
										{tr}Hide structure{/tr}
									</a>
								</li>
							{/if}
						</ul>
						{if $js == 'n'}</li></ul>{/if}
					</div>
				{/if}

				{* all single-action icons under one dropdown*}
				{assign var="hasPageAction" value="0"}
				{capture name="pageActions"}
					{if $js == 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a class="btn btn-link" data-toggle="dropdown" data-hover="dropdown" href="#">
						{icon name='menu-extra'}
					</a>
					<ul class="dropdown-menu dropdown-menu-right">
						<li class="dropdown-title">
							{tr}Page actions{/tr}
						</li>
						<li class="divider"></li>
						<li>
							{if $pdf_export eq 'y'}
								<a href="tiki-print.php?{query display="pdf" page=$page}">
									{icon name="pdf"} {tr}PDF{/tr}
									{assign var="hasPageAction" value="1"}
								</a>
							{/if}
						</li>
						{if !($prefs.flaggedrev_approval neq 'y' or ! $revision_approval or $lastVersion eq $revision_displayed)}
							{jq}
								$(".editplugin, .icon_edit_section").hide();
							{/jq}
						{/if}
						{if $prefs.flaggedrev_approval neq 'y' or ! $revision_approval or $lastVersion eq $revision_displayed}
							{if $editable and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox') and $beingEdited ne 'y' and $machine_translate_to_lang eq ''}
								<li>
									<a {ajax_href template="tiki-editpage.tpl"}tiki-editpage.php?page={$page|escape:"url"}{if !empty($page_ref_id) and (empty($needsStaging) or $needsStaging neq 'y')}&amp;page_ref_id={$page_ref_id}{/if}{/ajax_href}>
										{icon name="edit"} {tr}Edit{/tr}
										{assign var="hasPageAction" value="1"}
									</a>
								</li>
								{if $prefs.wiki_edit_icons_toggle eq 'y' and ($prefs.wiki_edit_plugin eq 'y' or $prefs.wiki_edit_section eq 'y')}
									{jq}
										$("#wiki_plugin_edit_view").click( function () {
										var $icon = $("#wiki_plugin_edit_view span");
										if (! $icon.hasClass("highlight")) {
										$(".editplugin, .icon_edit_section").show();
										$icon.addClass("highlight");
										setCookieBrowser("wiki_plugin_edit_view", true);
										} else {
										$(".editplugin, .icon_edit_section").hide();
										$icon.removeClass("highlight");
										deleteCookie("wiki_plugin_edit_view");
										}
										return false;
										});
										if (!getCookie("wiki_plugin_edit_view")) {$(".editplugin, .icon_edit_section").hide(); } else { $("#wiki_plugin_edit_view").click(); }
									{/jq}
									<li>
										<a href="#" id="wiki_plugin_edit_view">
											{icon name='plugin'} {tr}Edit icons{/tr}
											{assign var="hasPageAction" value="1"}
										</a>
									</li>
								{/if}
							{/if}
							{if ($tiki_p_edit eq 'y' or $tiki_p_edit_inline eq 'y' or $page|lower eq 'sandbox') and $beingEdited ne 'y' and $machine_translate_to_lang eq ''}
								{if $prefs.wysiwyg_inline_editing eq 'y' and $prefs.feature_wysiwyg eq 'y'}
									{jq}
										$("#wysiwyg_inline_edit").click( function () {
										var $icon = $("#wysiwyg_inline_edit span");
										if (! $icon.hasClass("highlight")) {
										if (enableWysiwygInlineEditing()) {
										$icon.addClass("highlight");
										}
										} else {
										if (disableWyiswygInlineEditing()) {
										$icon.removeClass("highlight");
										}
										}
										return false;
										});
										if (getCookie("wysiwyg_inline_edit", "preview")) { $("#wysiwyg_inline_edit").click(); }
									{/jq}
									<li>
										<a href="#" id="wysiwyg_inline_edit">
											{icon name='edit'} {tr}Inline edit{/tr}
											{assign var="hasPageAction" value="1"}
										</a>
									</li>
								{/if}
							{/if}
						{/if}
						{if $cached_page eq 'y'}
							<li>
								<a href="{$page|sefurl:'wiki':'with_next'}refresh=1">
									{icon name="refresh"} {tr}Refresh{/tr}
									{assign var="hasPageAction" value="1"}
								</a>
							</li>
						{/if}
						{if $prefs.feature_wiki_print eq 'y'}
							<li>
								<a href="tiki-print.php?{query _keepall='y' page=$page}">
									{icon name="print"} {tr}Print{/tr}
									{assign var="hasPageAction" value="1"}
								</a>
							</li>
						{/if}
						{if $prefs.feature_share eq 'y' && $tiki_p_share eq 'y'}
							<li>
								<a href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}">
									{icon name="share"} {tr}Share{/tr}
									{assign var="hasPageAction" value="1"}
								</a>
							</li>
						{/if}
						{if $prefs.feature_tell_a_friend eq 'y' && $tiki_p_tell_a_friend eq 'y'}
							<li>
								<a href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">
									{icon name="envelope"} {tr}Send link{/tr}
									{assign var="hasPageAction" value="1"}
								</a>
							</li>
						{/if}
						{* if we want a ShareThis icon and we show it under the single-action icons dropdown singl-click *}
						{if $prefs.feature_wiki_sharethis eq "y" and $prefs.wiki_sharethis_encourage neq "y"}
							{* Similar as in the blogs except there can be only one per page, so it is simpler *}
							<li>
								{literal}
								<script type="text/javascript">
									//Create your sharelet with desired properties and set button element to false
									var object = SHARETHIS.addEntry({ title:'{/literal}{$page|escape:"url"}{literal}'}, {button:false});
									//Output your customized button
									document.write('<a id="share" href="#"{/literal} title="{tr}ShareThis{/tr}">{icon name="sharethis"} {tr}ShareThis{/tr}{literal}</a>');
									//Tie customized button to ShareThis button functionality.
									var element = document.getElementById("share");
									object.attachButton(element);
								</script>
								{/literal}
							</li>
						{/if}
						{if !empty($user) and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
							<li>
								<a href="tiki-index.php?page={$page|escape:"url"}&amp;savenotepad=1{if !empty($page_ref_id)}&amp;page_ref_id={$page_ref_id}{/if}">
									{icon name="notepad"} {tr}Save to notepad{/tr}
									{assign var="hasPageAction" value="1"}
								</a>
							</li>
						{/if}
						{monitor_link type="wiki page" object=$page class="" linktext="{tr}Notification{/tr}" tag="li"}
						{if !empty($user) and $prefs.feature_user_watches eq 'y'}
							{if $user_watching_page eq 'n'}
								<li>
									<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=add{if $structure eq 'y'}&amp;structure={$home_info.pageName|escape:'url'}{/if}" class="icon">
										{icon name="watch"} {tr}Monitor page{/tr}
										{assign var="hasPageAction" value="1"}
									</a>
								</li>
							{else}
								<li>
									<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=remove{if $structure eq 'y'}&amp;structure={$home_info.pageName|escape:'url'}{/if}" class="icon">
										{icon name="stop-watching"} {tr}Stop monitoring page{/tr}
										{assign var="hasPageAction" value="1"}
									</a>
								</li>
							{/if}
							{if $structure eq 'y' and $tiki_p_watch_structure eq 'y'}
								{if $user_watching_structure ne 'y'}
									<li>
										<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=structure_changed&amp;watch_object={$page_info.page_ref_id}&amp;watch_action=add_desc&amp;structure={$home_info.pageName|escape:'url'}">
											{icon name="watch"} {tr}Monitor sub-structure{/tr}
											{assign var="hasPageAction" value="1"}
										</a>
									</li>
								{else}
									<li>
										<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=structure_changed&amp;watch_object={$page_info.page_ref_id}&amp;watch_action=remove_desc&amp;structure={$home_info.pageName|escape:'url'}">
											{icon name="stop-watching"} {tr}Stop monitoring sub-structure{/tr}
											{assign var="hasPageAction" value="1"}
										</a>
									</li>
								{/if}
							{/if}
						{/if}
						{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
							<li>
								<a href="tiki-object_watches.php?objectId={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;objectType=wiki+page&amp;objectName={$page|escape:"url"}&amp;objectHref={'tiki-index.php?page='|cat:$page|escape:"url"}" class="icon">
									{icon name="watch-group"} {tr}Group monitor{/tr}
									{assign var="hasPageAction" value="1"}
								</a>
							</li>
							{if $structure eq 'y'}
								<li>
									<a href="tiki-object_watches.php?objectId={$page_info.page_ref_id|escape:"url"}&amp;watch_event=structure_changed&amp;objectType=structure&amp;objectName={$page|escape:"url"}&amp;objectHref={'tiki-index.php?page_ref_id='|cat:$page_ref_id|escape:"url"}" class="icon">
										{icon name="watch-group"} {tr}Group monitor structure{/tr}
										{assign var="hasPageAction" value="1"}
									</a>
								</li>
							{/if}
						{/if}
						{if $user and $prefs.user_favorites eq 'y'}
							<li>
								{favorite type="wiki page" object=$page button_classes="icon"}
								{assign var="hasPageAction" value="1"}
							</li>
						{/if}
					</ul>
					{if $js == 'n'}</li></ul>{/if}
				{/capture}
				{if $hasPageAction eq '1'}
					<div class="btn-group page_actions">
						{$smarty.capture.pageActions}
					</div>
				{/if}
			</div>
		</div> {* END of wikiactions *}
	</div>
{/if}
