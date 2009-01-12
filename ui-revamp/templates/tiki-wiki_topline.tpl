<div class="wikitopline" style="clear: both;">
	<div class="content">
		{if !$hide_page_header}
		<div class="wikiinfo" style="float: left">
{if $prefs.feature_wiki_pageid eq 'y' and $print_page ne 'y'}
			<small><a class="link" href="tiki-index.php?page_id={$page_id}">{tr}page id{/tr}: {$page_id}</a></small>
{/if}

{breadcrumbs type="desc" loc="page" crumbs=$crumbs}

{if $cached_page eq 'y'}<small>({tr}Cached{/tr})</small>{/if}
{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.feature_categorypath eq 'y'}
	{$display_catpath}
{/if}
		</div>

{include file='tiki-pagecontrols.tpl' controls=$object_page_controls}

{* Superseeded by page controls? TODO : Verify and clean-up
{if $print_page ne 'y'}
		<div class="wikiactions" style="float: right; padding-left:10px; white-space: nowrap">
			<div class="icons" style="float: left;">

	{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
		{popup_link block=page_group_watch}
			{icon _id=eye alt='{tr}Show Group Watches on page{/tr}'}
		{/popup_link}
		<div id="page_group_watch" class="popup-group-watch">
			{foreach from=$grouplist item=g}
				<div>
					{if ! in_array( $g, $page_group_watches )}
						<a href="tiki-index.php?page={$page|escape:'url'}&amp;watch_group={$g|escape:'url'}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=add{if $structure eq 'y'}&amp;structure={$home_info.pageName|escape:'url'}{/if}" class="icon">
							{icon _id=eye alt='{tr}Enable Page Monitoring for Group{/tr}'}
						</a>

						{$g|escape}
					{else}
						<a href="tiki-index.php?page={$page|escape:'url'}&amp;watch_group={$g|escape:'url'}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=remove{if $structure eq 'y'}&amp;structure={$home_info.pageName|escape:'url'}{/if}" class="icon">
							{icon _id=no_eye alt='{tr}Disable Page Monitoring for Group{/tr}'}
						</a>

						{$g|escape}
					{/if}
				</div>
			{/foreach}
		</div>

		{if $structure == 'y'}
			{popup_link block=structure_group_watch}
				{icon _id=eye_arrow_down alt='{tr}Show Group Watches on structure{/tr}'}
			{/popup_link}
			<div id="structure_group_watch" class="popup-group-watch">
				{foreach from=$grouplist item=g}
					<div>
						{if ! in_array( $g, $structure_group_watches )}
							<a href="tiki-index.php?page={$page|escape:'url'}&amp;watch_group={$g|escape:'url'}&amp;watch_event=structure_changed&amp;watch_object={$page_info.page_ref_id|escape:"url"}&amp;watch_action=add_desc{if $structure eq 'y'}&amp;structure={$home_info.pageName|escape:'url'}{/if}" class="icon">
								{icon _id=eye_arrow_down alt='{tr}Enable Sub-Structure Monitoring for Group{/tr}'}
							</a>

							{$g|escape}
						{else}
							<a href="tiki-index.php?page={$page|escape:'url'}&amp;watch_group={$g|escape:'url'}&amp;watch_event=structure_changed&amp;watch_object={$page_info.page_ref_id|escape:"url"}&amp;watch_action=remove_desc{if $structure eq 'y'}&amp;structure={$home_info.pageName|escape:'url'}{/if}" class="icon">
								{icon _id=no_eye_arrow_down alt='{tr}Disable Sub-Structure Monitoring for Group{/tr}'}
							</a>

							{$g|escape}
						{/if}
					</div>
				{/foreach}
			</div>
		{/if}
	{/if}
	{if $structure == 'y' and count($showstructs) > 1 or $structure eq 'n' and count($showstructs) ne 0 }
			<form action="tiki-index.php" method="post" style="float: left">
				<select name="page_ref_id" onchange="page_ref_id.form.submit()">
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

	{if $prefs.feature_multilingual == 'y' && $prefs.show_available_translations eq 'y'}
			<div style="float: left">
		{include file="translated-lang.tpl" td='n'}
			</div>
	{/if}
		</div>
		<br class="clear" style="clear: both" />
{/if}
*}
{/if} {*hide_page_header*}
	</div> {* div.content *}
</div> {* div.wikitopline *}
