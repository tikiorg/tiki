{* $Header: /cvsroot/tikiwiki/_mods/templates/kuro/templates/styles/kuroBK/tiki-show_page.tpl,v 1.2 2005-02-10 10:10:07 michael_davey Exp $ *}

<div class="wikitext">{if $structure eq 'y'}
<div class="tocnav">
<table>
<tr><td>
    {if $prev_info and $prev_info.page_ref_id}
		<a href="tiki-index.php?page_ref_id={$prev_info.page_ref_id}"><img src='img/icons2/nav_dot_right.gif' border='0' alt='{tr}Previous page{/tr}'
   			{if $prev_info.page_alias}
   				title='{$prev_info.page_alias}'
   			{else}
   				title='{$prev_info.pageName}'
   			{/if}/></a>{else}<img src='img/icons2/8.gif' border='0'/>{/if}
	{if $parent_info}
   	<a href="tiki-index.php?page_ref_id={$parent_info.page_ref_id}"><img src='img/icons2/nav_home.gif' border='0' alt='{tr}Parent page{/tr}'
        {if $parent_info.page_alias}
   	      title='{$parent_info.page_alias}'
        {else}
   	      title='{$parent_info.pageName}'
        {/if}/></a>{else}<img src='img/icons2/8.gif' border='0'/>{/if}
   	{if $next_info and $next_info.page_ref_id}
      <a href="tiki-index.php?page_ref_id={$next_info.page_ref_id}"><img src='img/icons2/nav_dot_left.gif' border='0' alt='{tr}Next page{/tr}'
		  {if $next_info.page_alias}
			  title='{$next_info.page_alias}'
		  {else}
			  title='{$next_info.pageName}'
		  {/if}/></a>{else}<img src='img/icons2/8.gif' border='0'/>
	{/if}
	{if $home_info}
   	<a href="tiki-index.php?page_ref_id={$home_info.page_ref_id}"><img src='img/icons2/home.gif' border='0' alt='TOC'
		  {if $home_info.page_alias}
			  title='{$home_info.page_alias}'
		  {else}
			  title='{$home_info.pageName}'
		  {/if}/></a>{/if}
  </td>
  <td>
    {section loop=$structure_path name=ix}
      {if $structure_path[ix].parent_id}->{/if}
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
{$parsed}
{if $pages > 1}
	<br/>
	<div align="center">
		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$first_page}"><img src='img/icons2/nav_first.gif' border='0' alt='{tr}First page{/tr}' title='{tr}First page{/tr}' /></a>
		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$prev_page}"><img src='img/icons2/nav_dot_right.gif' border='0' alt='{tr}Previous page{/tr}' title='{tr}Previous page{/tr}' /></a>
		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>
		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$next_page}"><img src='img/icons2/nav_dot_left.gif' border='0' alt='{tr}Next page{/tr}' title='{tr}Next page{/tr}' /></a>
		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$last_page}"><img src='img/icons2/nav_last.gif' border='0' alt='{tr}Last page{/tr}' title='{tr}Last page{/tr}' /></a>
	</div>
{/if}
</div>

{if $has_footnote eq 'y'}
<div class="wikitext">
{$footnote}
</div>
{/if}

<p class="editdate">
{tr}Contributors to this page{/tr}: {$lastUser|userlink}{section name=author loop=$contributors}{if !$smarty.section.author.last}, {else} {tr}and{/tr} {/if}{$contributors[author]|userlink}{/section}.<br />
{tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}.
</p>   
{if $wiki_extras eq 'y'}
<br/>
{include file=attachments.tpl}

{if $feature_wiki_comments eq 'y'}
{include file=comments.tpl}
{/if}
{/if}
{if $is_categorized eq 'y' and $feature_categories eq 'y' and $feature_categoryobjects eq 'y'}
{$display_catobjects}
{/if}
