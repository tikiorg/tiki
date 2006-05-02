{if $show_page_bar !== 'n'}
{include file="tiki-page_bar_tabs.tpl"}
{/if}

{breadcrumbs type="trail" loc="page" crumbs=$crumbs}
{if $feature_page_title eq 'y'}
{breadcrumbs type="pagetitle" loc="page" crumbs=$crumbs}
{/if}

{if $feature_wiki_pageid eq 'y'}
	<small><a class="link" href="tiki-index.php?page_id={$page_id}">{tr}page id{/tr}: {$page_id}</a></small>
{/if}

{if $is_categorized eq 'y' and $feature_categories eq 'y' and $feature_categorypath eq 'y'}
<div style="float:right; margin-top: -19px; margin-right: 20px; font-size: 93%;"> {*catpath moved up to above pagetitle*}
{tr}Category{/tr}: {$display_catpath}
</div>
{/if}

<div>
{if $feature_wiki_description eq 'y'}
<small>{$description}</small>
{/if}
{if $cached_page eq 'y'}
<small>(cached)</small>
{/if}
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
		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$first_page}"><img src='img/icons2/nav_first.gif' border='0' alt='{tr}First page{/tr}' title='{tr}First page{/tr}' /></a>

		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$prev_page}"><img src='img/icons2/nav_dot_right.gif' border='0' alt='{tr}Previous page{/tr}' title='{tr}Previous page{/tr}' /></a>

		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>

		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$next_page}"><img src='img/icons2/nav_dot_left.gif' border='0' alt='{tr}Next page{/tr}' title='{tr}Next page{/tr}' /></a>


		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$last_page}">{html_image file='img/icons2/nav_last.gif' border='0' alt='{tr}Last page{/tr}' title='{tr}Last page{/tr}'}</a>
	</div>
{/if}
</div>

{if $has_footnote eq 'y'}
<div class="wikitext">
{$footnote}
</div>
{/if}
<br style="clear:both" />
{if $wiki_extras eq 'y' && $feature_wiki_attachments eq 'y' and $tiki_p_wiki_view_attachments eq 'y'}
{include file=attachments.tpl}
{/if}

{if $feature_wiki_comments eq 'y' and $tiki_p_wiki_view_comments eq 'y'}
{include file=comments.tpl}
{/if}

{if $print_page eq 'y'}
<div class="editdate" align="center">
<p>{tr}The original document is available at{/tr} {$urlprefix}tiki-index.php?page={$page|escape:"url"}
</p>
</div>
{/if}
{if $is_categorized eq 'y' and $feature_categories eq 'y' and $feature_categoryobjects eq 'y'}
<div class="catblock">{$display_catobjects}</div>
{/if}
{* For tikipedia wiki page footer 
{include file="tiki-bot_bar_wiki.tpl"}*}
