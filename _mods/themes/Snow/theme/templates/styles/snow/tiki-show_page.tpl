{* $Header: /cvsroot/tikiwiki/_mods/themes/Snow/theme/templates/styles/snow/tiki-show_page.tpl,v 1.1 2006-04-06 04:35:00 chibaguy Exp $ *}

{breadcrumbs type="trail" loc="page" crumbs=$crumbs}
{if $feature_page_title eq 'y'}
<div id="pagetitle-container">
{breadcrumbs type="pagetitle" loc="page" crumbs=$crumbs}
</div>
{/if}

<div class="wikitopline">
<table><tr>
<td style="vertical-align:top;">
{if $feature_wiki_pageid eq 'y'}
	<small><a class="link" href="tiki-index.php?page_id={$page_id}">{tr}page id{/tr}: {$page_id}</a></small>
{/if}
{breadcrumbs type="desc" loc="page" crumbs=$crumbs}
{if $cached_page eq 'y'}<small>({tr}cached{/tr})</small>{/if}
</td>
{if $is_categorized eq 'y' and $feature_categories eq 'y' and $feature_categorypath eq 'y'}
	<td style="vertical-align:top;width:100px;">{$display_catpath}</td>
{/if}
{if $print_page ne 'y'}
	<td style="text-align:right;width:142px;wrap:nowrap">
	{if $editable and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox') and $beingEdited ne 'y'}
		<a title="{tr}edit{/tr}" href="tiki-editpage.php?page={$page|escape:"url"}"><img src="img/icons/edit.gif" border="0"  width="20" height="16" alt="{tr}edit{/tr}" /></a>
	{/if}       
	{if $wiki_feature_3d eq 'y'}
		<a title="{tr}3d browser{/tr}" href="javascript:wiki3d_open('{$page|escape}',{$wiki_3d_width}, {$wiki_3d_height})"><img src="img/icons/ico_wiki3d.gif" border="0" width="13" height="16" alt="{tr}3d browser{/tr}" /></a>
	{/if}
	{if $cached_page eq 'y'}
		<a title="{tr}refresh{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;refresh=1"><img src="img/icons/ico_redo.gif" border="0" height="16" width="16"  alt="{tr}refresh{/tr}" /></a>
	{/if}
	{if $feature_wiki_print eq 'y'}
	<a title="{tr}print{/tr}" href="tiki-print.php?page={$page|escape:"url"}"><img src="img/icons/ico_print.gif" border="0"  width="16" height="16" alt="{tr}print{/tr}" /></a>
	{/if}

	{if $feature_wiki_pdf eq 'y'}
		<a title="{tr}create pdf{/tr}" href="tiki-config_pdf.php?{if $home_info && $home_info.page_ref_id}page_ref_id={$home_info.page_ref_id}{else}page={$page|escape:"url"}{/if}"><img src="img/icons/ico_pdf.gif" border="0"  width="16" height="16" alt="{tr}pdf{/tr}"></a>
	{/if}
	{if $user and $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
		<a title="{tr}Save to notepad{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;savenotepad=1"><img src="img/icons/ico_save.gif" border="0"  width="16" height="16" alt="{tr}save{/tr}" /></a>
	{/if}
	{if $user and $feature_user_watches eq 'y'}
		{if $user_watching_page eq 'n'}
			<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=add">{html_image file='img/icons/icon_watch.png' border='0' alt="{tr}monitor this page{/tr}" title="{tr}monitor this page{/tr}"}</a>
		{else}
			<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=remove">{html_image file='img/icons/icon_unwatch.png' border='0' alt="{tr}stop monitoring this page{/tr}" title="{tr}stop monitoring this page{/tr}"}</a>
		{/if}
	{/if}
	</td>

	{if $feature_backlinks eq 'y' and $backlinks}
		<td style="text-align:right;width:42px;">
		<form action="tiki-index.php" method="get">
		<select name="page" onchange="page.form.submit()">
		<option>{tr}backlinks{/tr}...</option>
		{section name=back loop=$backlinks}
		<option value="{$backlinks[back].fromPage}">{$backlinks[back].fromPage}</option>
		{/section}
		</select>
		</form>
		</td>
	{/if}

	{if !$page_ref_id and count($showstructs) ne 0}
		<td style="text-align:right;width:42px;">
		<form action="tiki-index.php" method="post">
		<select name="page_ref_id" onchange="page_ref_id.form.submit()">
		<option>{tr}Structures{/tr}...</option>
		{section name=struct loop=$showstructs}
		<option value="{$showstructs[struct].req_page_ref_id}">
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

	{if $feature_multilingual == 'y'}
		{include file="translated-lang.tpl" td='y'}
	{/if}

{/if}{* <-- end of if $print_page ne 'y' *}
</tr></table>
</div>

<div class="wikitext">
{if $structure eq 'y'}
<div class="tocnav">
<table>
<tr>
  <td>
    {if $prev_info and $prev_info.page_ref_id}
		<a href="tiki-index.php?page_ref_id={$prev_info.page_ref_id}"><img src="img/icons2/nav_dot_right.gif" border="0" height="11" width="8" alt="{tr}Previous page{/tr}" 
   			{if $prev_info.page_alias}
   				title='{$prev_info.page_alias}'
   			{else}
   				title='{$prev_info.pageName}'
   			{/if}/></a>{else}<img src="img/icons2/8.gif" alt="" border="0" height="1" width="8" />{/if}
	{if $parent_info}
   	<a href="tiki-index.php?page_ref_id={$parent_info.page_ref_id}"><img src="img/icons2/nav_home.gif" border="0" height="11" width="13" alt="{tr}Parent page{/tr}" 
        {if $parent_info.page_alias}
   	      title='{$parent_info.page_alias}'
        {else}
   	      title='{$parent_info.pageName}'
        {/if}/></a>{else}<img src="img/icons2/8.gif" alt="" border="0" height="1" width="8" />{/if}
   	{if $next_info and $next_info.page_ref_id}
      <a href="tiki-index.php?page_ref_id={$next_info.page_ref_id}"><img src="img/icons2/nav_dot_left.gif" height="11" width="8" border="0" alt="{tr}Next page{/tr}" 
		  {if $next_info.page_alias}
			  title='{$next_info.page_alias}'
		  {else}
			  title='{$next_info.pageName}'
		  {/if}/></a>{else}<img src="img/icons2/8.gif" alt="" border="0" height="1" width="8" />
	{/if}
	{if $home_info}
   	<a href="tiki-index.php?page_ref_id={$home_info.page_ref_id}"><img src="img/icons2/home.gif" border="0" height="16" width="16" alt="TOC" 
		  {if $home_info.page_alias}
			  title='{$home_info.page_alias}'
		  {else}
			  title='{$home_info.pageName}'
		  {/if}/></a>{/if}
  </td>
  <td>
{if $tiki_p_edit_structures and $tiki_p_edit_structures eq 'y' }
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
    {section loop=$structure_path name=ix}
      {if $structure_path[ix].parent_id}&nbsp;{$site_crumb_seper}&nbsp;{/if}
	  <a href="tiki-index.php?page_ref_id={$structure_path[ix].page_ref_id}">
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
{if $feature_wiki_ratings eq 'y'}{include file="poll.tpl"}{/if}
{$parsed}
{if $pages > 1}
	<br />
	<div align="center">
		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$first_page}"><img src="img/icons2/nav_first.gif" border="0" height="11" width="27" alt="{tr}First page{/tr}" title="{tr}First page{/tr}" /></a>

		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$prev_page}"><img src="img/icons2/nav_dot_right.gif" border="0" height="11" width="8" alt="{tr}Previous page{/tr}" title="{tr}Previous page{/tr}" /></a>

		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>

		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$next_page}"><img src="img/icons2/nav_dot_left.gif" border="0" height="11" width="8" alt="{tr}Next page{/tr}" title="{tr}Next page{/tr}" /></a>


		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$last_page}"><img src="img/icons2/nav_last.gif" border="0" height="11" width="27" alt="{tr}Last page{/tr}" title="{tr}Last page{/tr}" /></a>
	</div>
{/if}
</div> {* End of main wiki page *}

{if $has_footnote eq 'y'}<div class="wikitext wikifootnote">{$footnote}</div>{/if}

{if isset($wiki_authors_style) && $wiki_authors_style eq 'business'}
<p class="editdate">
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
  {tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}.
</p>
{elseif isset($wiki_authors_style) &&  $wiki_authors_style eq 'collaborative'}
<p class="editdate">
  {tr}Contributors to this page{/tr}: {$lastUser|userlink}
  {section name=author loop=$contributors}
   {if !$smarty.section.author.last},
   {else} {tr}and{/tr}
   {/if}
   {$contributors[author]|userlink}
  {/section}.<br />
  {tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser|userlink}.
</p>
{elseif isset($wiki_authors_style) &&  $wiki_authors_style eq 'none'}
{else}
<p class="editdate">
  {tr}Created by{/tr}: {$creator|userlink}
  {tr}last modification{/tr}: {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser|userlink}
</p>
{/if}

{if $wiki_feature_copyrights  eq 'y' and $wikiLicensePage}
  {if $wikiLicensePage == $page}
    {if $tiki_p_edit_copyrights eq 'y'}
      <p class="editdate">{tr}To edit the copyright notices{/tr} <a href="copyrights.php?page={$copyrightpage}">{tr}click here{/tr}</a>.</p>
    {/if}
  {else}
    <p class="editdate">{tr}The content on this page is licensed under the terms of the{/tr} <a href="tiki-index.php?page={$wikiLicensePage}&amp;copyrightpage={$page|escape:"url"}">{$wikiLicensePage}</a>.</p>
  {/if}
{/if}

{if $print_page eq 'y'}
  <div class="editdate" align="center"><p>
    {tr}The original document is available at{/tr} {$urlprefix}tiki-index.php?page={$page|escape:"url"}
  </p></div>
{/if}

{if $is_categorized eq 'y' and $feature_categories eq 'y' and $feature_categoryobjects eq 'y'}
<div class="catblock">{$display_catobjects}</div>
{/if}
