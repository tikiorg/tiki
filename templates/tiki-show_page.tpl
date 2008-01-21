{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-show_page.tpl,v 1.129.2.10 2008-01-21 09:47:16 nyloth Exp $ *} 
{if $prefs.feature_ajax == 'y'}
  <script language="JavaScript" src="lib/wiki/wiki-ajax.js"></script>
{/if}

{breadcrumbs type="trail" loc="page" crumbs=$crumbs}
{if $prefs.feature_page_title eq 'y'}
{breadcrumbs type="pagetitle" loc="page" crumbs=$crumbs}
{/if}

{if $beingStaged eq 'y'}
<div class="tocnav">
{tr}This is the staging copy of{/tr} <a class="link" href="tiki-index.php?page={$approvedPageName|escape:'url'}">{tr}the approved version of this page.{/tr}</a>
{if $outOfSync eq 'y'}
	{if $canApproveStaging == 'y'}
	{if $lastSyncVersion}<a class="link" href="tiki-pagehistory.php?page={$page|escape:'url'}&amp;diff2={$lastSyncVersion}">{tr}View changes since last approval.{/tr}</a>
	{else}{tr}Viewing of changes since last approval is possible only after first approval.{/tr}{/if}
	<a class="link" href="tiki-approve_staging_page.php?page={$page|escape:'url'}">{tr}Approve changes.{/tr}</a>
	{else}
	{tr}Latest changes will be synchronized after approval.{/tr}
	{/if}
{/if}
</div>
{/if}
{if $needsFirstApproval == 'y' and $canApproveStaging == 'y'}
<div class="tocnav">
{tr}This is a new staging page that has not been approved before. Edit and manually move it to the category for approved pages to approve it for the first time.{/tr}
</div>
{/if}

<div class="wikitopline">
<table><tr>
<td style="vertical-align:top;">
{if $prefs.feature_wiki_pageid eq 'y'}
	<small><a class="link" href="tiki-index.php?page_id={$page_id}">{tr}page id{/tr}: {$page_id}</a></small>
{/if}
{breadcrumbs type="desc" loc="page" crumbs=$crumbs}
{if $cached_page eq 'y'}<small>({tr}Cached{/tr})</small>{/if}
</td>
{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.feature_categorypath eq 'y'}
	<td style="vertical-align:top;">{$display_catpath}</td>
{/if}
{if $print_page ne 'y'}
	<td style="vertical-align:top;text-align:right;padding-left:10px;wrap:nowrap">
	{if $pdf_export eq 'y'}<a href="tiki-print.php?{query display="pdf"}" title="{tr}PDF{/tr}"><img width="16" height="16" border="0" alt="{tr}PDF{/tr}" src="pics/icons/page_white_acrobat.png"/></a>{/if}
	{if $editable and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox') and $beingEdited ne 'y' or $canEditStaging eq 'y'}
		<a title="{tr}Edit{/tr}" {ajax_href template="tiki-editpage.tpl" htmlelement="tiki-center"}tiki-editpage.php?page={if $needsStaging eq 'y'}{$stagingPageName|escape:"url"}{else}{$page|escape:"url"}{/if}{if !empty($page_ref_id) and $needsStaging neq 'y'}&amp;page_ref_id={$page_ref_id}{/if}{/ajax_href}><img src="pics/icons/page_edit.png" border="0" width="16" height="16" alt="{tr}Edit{/tr}" /></a>
	{/if}       
	{if $prefs.feature_morcego eq 'y' && $prefs.wiki_feature_3d eq 'y'}
		<a title="{tr}3d browser{/tr}" href="javascript:wiki3d_open('{$page|escape}',{$prefs.wiki_3d_width}, {$prefs.wiki_3d_height})"><img src="pics/icons/wiki3d.png" border="0" width="16" height="16" alt="{tr}3d browser{/tr}" /></a>
	{/if}
	{if $cached_page eq 'y'}
		<a title="{tr}Refresh{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;refresh=1"><img src="pics/icons/arrow_refresh.png" border="0" height="16" width="16" alt="{tr}Refresh{/tr}" /></a>
	{/if}
	{if $prefs.feature_wiki_print eq 'y'}
	<a title="{tr}Print{/tr}" href="tiki-print.php?page={$page|escape:"url"}"><img src="pics/icons/printer.png" border="0" width="16" height="16" alt="{tr}Print{/tr}" /></a>
	{/if}

        {if $prefs.feature_tell_a_friend eq 'y' && $tiki_p_tell_a_friend eq 'y'}
          <a title="{tr}Send a link{/tr}" href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">
            <img src="pics/icons/email_link.png" border="0" width="16" height="16" alt="{tr}Send a link{/tr}" />
          </a>
        {/if}
{*	
        {if $prefs.feature_wiki_pdf eq 'y'}
		<a title="{tr}Create pdf{/tr}" href="tiki-config_pdf.php?{if $home_info && $home_info.page_ref_id}page_ref_id={$home_info.page_ref_id}{else}page={$page|escape:"url"}{/if}"><img src="pics/icons/page_white_acrobat.png" border="0" width="16" height="16" alt="{tr}pdf{/tr}" /></a>
	{/if}
*}
	{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
		<a title="{tr}Save to notepad{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;savenotepad=1{if !empty($page_ref_id)}&amp;page_ref_id={$page_ref_id}{/if}"><img src="pics/icons/disk.png" border="0" width="16" height="16" alt="{tr}Save{/tr}" /></a>
	{/if}
	{if $user and $prefs.feature_user_watches eq 'y'}
		{if $user_watching_page eq 'n'}
			<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=add{if $structure eq 'y'}&amp;structure={$home_info.pageName|escape:'url'}{/if}">{html_image file='pics/icons/eye.png' border='0' alt='{tr}Monitor this Page{/tr}' title='{tr}Monitor this Page{/tr}'}</a>
		{else}
			<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=remove{if $structure eq 'y'}&amp;structure={$home_info.pageName|escape:'url'}{/if}">{html_image file='pics/icons/no_eye.png' border='0' alt='{tr}Stop Monitoring this Page{/tr}' title='{tr}Stop Monitoring this Page{/tr}'}</a>
		{/if}
		{if $structure == 'y' and $tiki_p_watch_structure eq 'y'}
			{if $user_watching_structure ne 'y'}
				<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=structure_changed&amp;watch_object={$page_info.page_ref_id}&amp;watch_action=add_desc&amp;structure={$home_info.pageName|escape:'url'}">{html_image file='pics/icons/eye_arrow_down.png' border='0' alt='{tr}Monitor the Sub-Structure{/tr}' title='{tr}Monitor the Sub-Structure{/tr}'}</a>
			{else}
				<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=structure_changed&amp;watch_object={$page_info.page_ref_id}&amp;watch_action=remove_desc&amp;structure={$home_info.pageName|escape:'url'}">{html_image file='pics/icons/no_eye_arrow_down.png' border='0' alt='{tr}Stop Monitoring the Sub-Structure{/tr}' title='{tr}Stop Monitoring the Sub-Structure{/tr}'}</a>
			{/if}
		{/if}
	{/if}
	</td>

	{if $prefs.feature_backlinks eq 'y' and $backlinks}
		<td style="vertical-align:top;text-align:right;width:42px;">
		<form action="tiki-index.php" method="get">
		<select name="page" onchange="page.form.submit()">
		<option>{tr}Backlinks{/tr}...</option>
		{section name=back loop=$backlinks}
		<option value="{$backlinks[back].fromPage}">{$backlinks[back].fromPage}</option>
		{/section}
		</select>
		</form>
		</td>
	{/if}

	{if $structure == 'y' and count($showstructs) > 1 or $structure eq 'n' and count($showstructs) ne 0 }
		<td style="vertical-align:top;text-align:right;width:42px;">
		<form action="tiki-index.php" method="post">
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
		</td>
	{/if}

	{if $prefs.feature_multilingual == 'y'}
		{include file="translated-lang.tpl" td='y'}
	{/if}

{/if}{* <-- end of if $print_page ne 'y' *}
</tr></table>
</div>

<div class="navbar" align="right">
    {if $user and $prefs.feature_user_watches eq 'y'}
        {if $category_watched eq 'y'}
            {tr}Watched by categories{/tr}:
            {section name=i loop=$watching_categories}
			    <a href="tiki-browse_categories?parentId={$watching_categories[i].categId}">{$watching_categories[i].name}</a>&nbsp;
            {/section}
        {/if}			
    {/if}
</div>

{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y' and isset($freetags.data[0])}
{include file="freetag_list.tpl"}
{/if}

{if $pages > 1 and $prefs.wiki_page_navigation_bar neq 'bottom'}
	<div align="center">
		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$first_page}"><img src="pics/icons/resultset_first.png" border="0" height="16" width="16" alt="{tr}First page{/tr}" title="{tr}First page{/tr}" /></a>

		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$prev_page}"><img src="pics/icons/resultset_previous.png" border="0" height="16" width="16" alt="{tr}Previous page{/tr}" title="{tr}Previous page{/tr}" /></a>

		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>

		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$next_page}"><img src="pics/icons/resultset_next.png" border="0" height="16" width="16" alt="{tr}Next page{/tr}" title="{tr}Next page{/tr}" /></a>


		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$last_page}"><img src="pics/icons/resultset_last.png" border="0" height="16" width="16" alt="{tr}Last page{/tr}" title="{tr}Last page{/tr}" /></a>
	</div>
{/if}

<div class="wikitext">
{if $structure eq 'y'}
<div class="tocnav">
<table>
<tr>
  <td>
    {if $prev_info and $prev_info.page_ref_id}
		<a href="tiki-index.php?page={$prev_info.pageName|escape:'url'}&amp;structure={$home_info.pageName|escape:'url'}"><img src="pics/icons/resultset_previous.png" border="0" height="16" width="16" alt="{tr}Previous page{/tr}" 
   			{if $prev_info.page_alias}
   				title='{$prev_info.page_alias}'
   			{else}
   				title='{$prev_info.pageName}'
   			{/if}/></a>{else}<img src="img/icons2/8.gif" alt="" border="0" height="1" width="8" />{/if}
	{if $parent_info}
   	<a href="tiki-index.php?page={$parent_info.pageName|escape:'url'}&amp;structure={$home_info.pageName|escape:'url'}"><img src="pics/icons/resultset_up.png" border="0" height="16" width="16" alt="{tr}Parent page{/tr}" 
        {if $parent_info.page_alias}
   	      title='{$parent_info.page_alias}'
        {else}
   	      title='{$parent_info.pageName}'
        {/if}/></a>{else}<img src="img/icons2/8.gif" alt="" border="0" height="1" width="8" />{/if}
   	{if $next_info and $next_info.page_ref_id}
      <a href="tiki-index.php?page={$next_info.pageName|escape:'url'}&amp;structure={$home_info.pageName|escape:'url'}"><img src="pics/icons/resultset_next.png" height="16" width="16" border="0" alt="{tr}Next page{/tr}" 
		  {if $next_info.page_alias}
			  title='{$next_info.page_alias}'
		  {else}
			  title='{$next_info.pageName}'
		  {/if}/></a>{else}<img src="img/icons2/8.gif" alt="" border="0" height="1" width="8" />
	{/if}
	{if $home_info}
   	<a href="tiki-index.php?page={$home_info.pageName|escape:'url'}&amp;structure={$home_info.pageName|escape:'url'}"><img src="pics/icons/house.png" border="0" height="16" width="16" alt="{tr}TOC{/tr}" 
		  {if $home_info.page_alias}
			  title='{$home_info.page_alias}'
		  {else}
			  title='{$home_info.pageName}'
		  {/if}/></a>{/if}
  </td>
  <td>
{if $tiki_p_edit_structures and $tiki_p_edit_structures eq 'y' and $struct_editable eq 'y'}
    <form action="tiki-editpage.php" method="post">
      <input type="hidden" name="current_page_id" value="{$page_info.page_ref_id}" />
      <input type="text" name="page" />
      {* Cannot add peers to head of structure *}
      {if $page_info and !$parent_info }
      <input type="hidden" name="add_child" value="checked" /> 
      {else}
      <input type="checkbox" name="add_child" /> {tr}Child{/tr}
      {/if}      
      <input type="submit" name="insert_into_struct" value="{tr}Add Page{/tr}" />
    </form>
{/if}
  </td>
</tr>
<tr>
  <td colspan="2">
  	<a href="tiki-edit_structure.php?page_ref_id={$home_info.page_ref_id}"><img src='pics/icons/chart_organisation.png' alt="{tr}Structure{/tr}" title="{tr}Structure{/tr}" border='0' width='16' height='16' /></a>&nbsp;&nbsp;
	({$cur_pos})&nbsp;&nbsp;	
    {section loop=$structure_path name=ix}
      {if $structure_path[ix].parent_id}&nbsp;{$prefs.site_crumb_seper}&nbsp;{/if}
	  <a href="tiki-index.php?page={$structure_path[ix].pageName|escape:'url'}&amp;structure={$home_info.pageName|escape:'url'}">
      {if $structure_path[ix].page_alias}
        {$structure_path[ix].page_alias}
	  {else}
        {$structure_path[ix].pageName}
	  {/if}
	  </a>
	{/section}
  </td>
</tr>
</table>
</div>
{/if}
{if $prefs.feature_wiki_ratings eq 'y'}{include file="poll.tpl"}{/if}
{$parsed}
{if $pages > 1 and $prefs.wiki_page_navigation_bar neq 'top'}
	<br />
	<div align="center">
		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$first_page}"><img src="pics/icons/resultset_first.png" border="0" height="16" width="16" alt="{tr}First page{/tr}" title="{tr}First page{/tr}" /></a>

		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$prev_page}"><img src="pics/icons/resultset_previous.png" border="0" height="16" width="16" alt="{tr}Previous page{/tr}" title="{tr}Previous page{/tr}" /></a>

		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>

		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$next_page}"><img src="pics/icons/resultset_next.png" border="0" height="16" width="16" alt="{tr}Next page{/tr}" title="{tr}Next page{/tr}" /></a>


		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$last_page}"><img src="pics/icons/resultset_last.png" border="0" height="16" width="16" alt="{tr}Last page{/tr}" title="{tr}Last page{/tr}" /></a>
	</div>
{/if}
</div> {* End of main wiki page *}

{if $has_footnote eq 'y'}<div class="wikitext" id="wikifootnote">{$footnote}</div>{/if}

<p class="editdate"> {* begining editdate *}
{if isset($prefs.wiki_authors_style) && $prefs.wiki_authors_style eq 'business'}
  {tr}Last edited by{/tr} {$lastUser|userlink}
  {section name=author loop=$contributors}
   {if $smarty.section.author.first}, {tr}based on work by{/tr}
   {else}
    {if !$smarty.section.author.last},
    {else} {tr}and{/tr}
    {/if}
   {/if}
   {$contributors[author]|userlink}
  {/section}.<br />                                         
  {tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}. {if $prefs.wiki_show_version eq 'y'}({tr}Version{/tr} {$lastVersion}){/if}
{elseif isset($prefs.wiki_authors_style) &&  $prefs.wiki_authors_style eq 'collaborative'}
<br />
  {tr}Contributors to this page{/tr}: {$lastUser|userlink}
  {section name=author loop=$contributors}
   {if !$smarty.section.author.last},
   {else} {tr}and{/tr}
   {/if}
   {$contributors[author]|userlink}
  {/section}.<br />
  {tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser|userlink}. {if $prefs.wiki_show_version eq 'y'}({tr}Version{/tr} {$lastVersion}){/if}
{elseif isset($prefs.wiki_authors_style) &&  $prefs.wiki_authors_style eq 'none'}
{else}
<br />
  {tr}Created by{/tr}: {$creator|userlink}.
  {tr}Last Modification{/tr}: {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser|userlink}. {if $prefs.wiki_show_version eq 'y'}({tr}Version{/tr} {$lastVersion}){/if}
{/if}

{if $prefs.wiki_feature_copyrights  eq 'y' and $prefs.wikiLicensePage}
  {if $prefs.wikiLicensePage == $page}
    {if $tiki_p_edit_copyrights eq 'y'}
      <br />
      {tr}To edit the copyright notices{/tr} <a href="copyrights.php?page={$copyrightpage}">{tr}Click Here{/tr}</a>.
    {/if}
  {else}
    <br />
    {tr}The content on this page is licensed under the terms of the{/tr} <a href="tiki-index.php?page={$prefs.wikiLicensePage}&amp;copyrightpage={$page|escape:"url"}">{$prefs.wikiLicensePage}</a>.
  {/if}
{/if}

{if $print_page eq 'y'}
    <br />
    {tr}The original document is available at{/tr} <a href="{$base_url}tiki-index.php?page={$page|escape:"url"}">{$base_url}tiki-index.php?page={$page|escape:"url"}</a>
{/if}

</p> {* end editdate *}

{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.feature_categoryobjects eq 'y' and $tiki_p_view_categories eq 'y'}
<div class="catblock">{$display_catobjects}</div>
{/if}

{if $print_page ne 'y'}
{include file=tiki-page_bar.tpl}
{/if}
