{* $Id$ *}

{if $prefs.feature_page_title eq 'y'}<h1><a  href="tiki-index.php?page={$page|escape:"url"}" class="pagetitle">
  {if $structure eq 'y' and $page_info.page_alias ne ''}
    {$page_info.page_alias}
  {else}
    {$page}
  {/if}</a>
  {if $lock}
    <img src="pics/icons/lock.png" height="19" width="19" alt="{tr}Locked{/tr}" title="{tr}Locked by{/tr} {$page_user}" />
  {/if}
  </h1>
{/if}
{if $prefs.feature_wiki_pageid eq 'y'}
	<small><a class="link" href="tiki-index.php?page_id={$page_id}">{tr}page id{/tr}: {$page_id}</a></small>
{/if}

<table class="wikitopline">
  <tr>
    {if $prefs.feature_wiki_description eq 'y' or $cached_page eq 'y'}
      <td>
        {if $prefs.feature_wiki_description eq 'y'}<small>{$description}</small>{/if}
        {if $cached_page eq 'y'}<small>({tr}Cached{/tr})</small>{/if}
      </td>
    {/if}
  {if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.feature_categorypath eq 'y'}
    <td align="right">{$display_catpath}</td>
    </tr><tr><td>&nbsp;</td>
  {/if}
  {if $print_page ne 'y'}
	<td style="text-align:left;">
	{if !$lock and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox') and $beingEdited ne 'y'}
	  <a title="{tr}Edit{/tr}" href="tiki-editpage.php?page={$page|escape:"url"}"><img src="pics/icons/page_edit.png" border="0" width="16" height="16" alt='{tr}Edit{/tr}' /></a>
	{/if}
	{if $prefs.feature_morcego eq "y" && $prefs.wiki_feature_3d eq "y"}
		<a title="{tr}3d browser{/tr}" href="javascript:wiki3d_open('{$page|escape}',{$prefs.wiki_3d_width}, {$prefs.wiki_3d_height})"><img src="pics/icons/wiki3d.png" border="0" width="13" height="16" alt='{tr}3d browser{/tr}' /></a>
	{/if}
	{if $cached_page eq 'y'}
	  <a title="{tr}Refresh{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;refresh=1"><img src="pics/icons/arrow_refresh.png" border="0" height="16" width="16" alt='{tr}Refresh{/tr}' /></a>
	{/if}
	{if $prefs.feature_wiki_print eq 'y'}
	<a title="{tr}Print{/tr}" href="tiki-print.php?page={$page|escape:"url"}"><img src="pics/icons/printer.png" border="0"  width="16" height="16" alt='{tr}Print{/tr}' /></a>
	{/if}
	{*
	{if $prefs.feature_wiki_pdf eq 'y'}
	  <a title="{tr}Create pdf{/tr}" href="tiki-config_pdf.php?{if $home_info && $home_info.page_ref_id}page_ref_id={$home_info.page_ref_id}{else}page={$page|escape:"url"}{/if}"><img src="pics/icons/page_white_acrobat.png" border="0"  width="16" height="16" alt='{tr}pdf{/tr}' /></a>
	{/if}
	*}
	{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
	  <a title="{tr}Save to notepad{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;savenotepad=1"><img src="pics/icons/disk.png" border="0"  width="16" height="16" alt='{tr}Save{/tr}' /></a>
	{/if}
	{if $user and $prefs.feature_user_watches eq 'y'}
	  {if $user_watching_page eq 'n'}
			<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=add">{html_image file='pics/icons/eye.png' border='0' alt="{tr}Monitor this Page{/tr}" title="{tr}Monitor this Page{/tr}"}</a>
	  {else}
			<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=remove">{html_image file='pics/icons/no_eye.png' border='0' alt="{tr}Stop Monitoring this Page{/tr}" title="{tr}Stop Monitoring this Page{/tr}"}</a>
	  {/if}
	{/if}
    </td>

	{if $prefs.feature_backlinks eq 'y' and $backlinks}
      <td style="text-align:right;">
      <form action="tiki-index.php" method="get">
	  <select name="page" onchange="page.form.submit()">
	    <option value="{$page}">{tr}Backlinks{/tr}...</option>
		{section name=back loop=$backlinks}
		  <option value="{$backlinks[back].fromPage}">{$backlinks[back].fromPage}</option>
		{/section}
	  </select>
      </form>
      </td>
	{/if}
	{if count($showstructs) > 1}
  	  <td style="text-align:right;">
      <form action="tiki-index.php" method="post">
	  <select name="page_ref_id" onchange="page_ref_id.form.submit()">
	    <option>{tr}Structures{/tr}...</option>
		{section name=struct loop=$showstructs}
		  <option value="{$showstructs[struct].req_page_ref_id}">
{if $showstructs[struct].page_alias} 
{$showstructs[struct].page_alias}
{else}
{$showstructs[struct].pageName}
{/if}</option>
		{/section}
	  </select>
      </form>
      </td>
	{/if}
	</td>
  {else}
    <td>&nbsp;</td>
  {/if}
  {if $prefs.feature_multilingual == 'y'}{include file="translated-lang.tpl" td='y'}{/if}
</tr>
</table>

<div class="wikitext">
{if $structure eq 'y'}
<div class="tocnav">
<table>
<tr>
  <td>
    {if $prev_info and $prev_info.page_ref_id}
		<a href="tiki-index.php?page_ref_id={$prev_info.page_ref_id}"><img src="pics/icons/resultset_previous.png" border="0" height="11" width="8" alt='{tr}Previous page{/tr}' 
   			{if $prev_info.page_alias}
   				title='{$prev_info.page_alias}'
   			{else}
   				title='{$prev_info.pageName}'
   			{/if}/></a>{else}<img src="img/icons2/8.gif" alt="" border="0" height="1" width="8" />{/if}
	{if $parent_info}
   	<a href="tiki-index.php?page_ref_id={$parent_info.page_ref_id}"><img src="pics/icons/resultset_up.png" border="0" height="11" width="13" alt='{tr}Parent page{/tr}' 
        {if $parent_info.page_alias}
   	      title='{$parent_info.page_alias}'
        {else}
   	      title='{$parent_info.pageName}'
        {/if}/></a>{else}<img src="img/icons2/8.gif" alt="" border="0" height="1" width="8" />{/if}
   	{if $next_info and $next_info.page_ref_id}
      <a href="tiki-index.php?page_ref_id={$next_info.page_ref_id}"><img src="pics/icons/resultset_next.png" height="11" width="8" border="0" alt='{tr}Next page{/tr}' 
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
      {if $structure_path[ix].parent_id}&nbsp;{$prefs.site_crumb_seper}&nbsp;{/if}
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
{if $prefs.feature_wiki_ratings eq 'y'}{include file="poll.tpl"}{/if}
{$parsed}
{if $pages > 1}
	<br />
	<div align="center">
		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$first_page}"><img src="pics/icons/resultset_first.png" border="0" height="11" width="27" alt='{tr}First page{/tr}' title='{tr}First page{/tr}' /></a>

		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$prev_page}"><img src="pics/icons/resultset_previous.png" border="0" height="11" width="8" alt='{tr}Previous page{/tr}' title='{tr}Previous page{/tr}' /></a>

		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>

		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$next_page}"><img src="pics/icons/resultset_next.png" border="0" height="11" width="8" alt='{tr}Next page{/tr}' title='{tr}Next page{/tr}' /></a>


		<a href="tiki-index.php?{if $page_info}page_ref_id={$page_info.page_ref_id}{else}page={$page|escape:"url"}{/if}&amp;pagenum={$last_page}">{html_image file='pics/icons/resultset_last.png' border='0' alt='{tr}Last page{/tr}' title='{tr}Last page{/tr}'}</a>
	</div>
{/if}
</div> {* End of main wiki page *}

{if $has_footnote eq 'y'}
<div class="wikitext">
{$footnote}
</div>
{/if}

{if isset($prefs.wiki_authors_style) && $prefs.wiki_authors_style eq 'business'}
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
{elseif isset($prefs.wiki_authors_style) &&  $prefs.wiki_authors_style eq 'collaborative'}
<p class="editdate">
  {tr}Contributors to this page{/tr}: {$lastUser|userlink}
  {section name=author loop=$contributors}
   {if !$smarty.section.author.last},
   {else} {tr}and{/tr}
   {/if}
   {$contributors[author]|userlink}
  {/section}.<br />
  {tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}.
</p>
{elseif isset($prefs.wiki_authors_style) &&  $prefs.wiki_authors_style eq 'none'}
{else}
<p class="editdate">
  {tr}Created by:{/tr} {$creator|userlink},
  {tr}Last Modification:{/tr} {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser|userlink}
</p>
{/if}

{if $prefs.wiki_feature_copyrights  eq 'y' and $prefs.wikiLicensePage}
  {if $prefs.wikiLicensePage == $page}
    {if $tiki_p_edit_copyrights eq 'y'}
      <p class="editdate">{tr}To edit the copyright notices{/tr} <a href="copyrights.php?page={$copyrightpage}">{tr}Click Here{/tr}</a>.</p>
    {/if}
  {else}
    <p class="editdate">{tr}The content on this page is licensed under the terms of the{/tr} <a href="tiki-index.php?page={$prefs.wikiLicensePage}&amp;copyrightpage={$page|escape:"url"}">{$prefs.wikiLicensePage}</a>.</p>
  {/if}
{/if}

{if $print_page eq 'y'}
  <div class="editdate" align="center"><p>
    {tr}The original document is available at{/tr} {$urlprefix}tiki-index.php?page={$page|escape:"url"}
  </p></div>
{/if}

{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.feature_categoryobjects eq 'y'}
{$display_catobjects}
{/if}

{if $print_page ne 'y'}
{include file=tiki-page_bar.tpl}
{/if}
