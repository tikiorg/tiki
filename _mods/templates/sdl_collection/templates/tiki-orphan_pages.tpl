{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/tiki-orphan_pages.tpl,v 1.1 2004-05-09 23:09:15 damosoft Exp $ *}

<a href="tiki-orphan_pages.php" class="pagetitle">{tr}Orphan Pages{/tr}</a><br/><br/>
<table class="findtable">
<tr><td class="findtitle">{tr}Search{/tr}</td>
   <td class="findtitle">
   <form method="get" action="tiki-orphan_pages.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" name="search" value="{tr}Go{/tr}" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<div align="center">
<table class="normal">
<tr>
{if $wiki_list_name eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-orphan_pages.php?find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'pageName_desc'}pageName_asc{else}pageName_desc{/if}">{tr}Page{/tr}</a></td>
{/if}
{if $wiki_list_hits eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-orphan_pages.php?find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></td>
{/if}	
{if $wiki_list_lastmodif eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-orphan_pages.php?find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last Modified{/tr}</a></td>
{/if}
{if $wiki_list_creator eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-orphan_pages.php?find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'creator_desc'}creator_asc{else}creator_desc{/if}">{tr}Creator{/tr}</a></td>
{/if}	

{if $wiki_list_user eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-orphan_pages.php?find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}Last Author{/tr}</a></td>
{/if}	
{if $wiki_list_lastver eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-orphan_pages.php?find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'version_desc'}version_asc{else}version_desc{/if}">{tr}Last Version{/tr}</a></td>
{/if}
{if $wiki_list_comment eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-orphan_pages.php?find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}">{tr}Comments{/tr}</a></td>
{/if}
{if $wiki_list_status eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-orphan_pages.php?find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'flag_desc'}flag_asc{else}flag_desc{/if}">{tr}Status{/tr}</a></td>
{/if}
{if $wiki_list_versions eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-orphan_pages.php?find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'versions_desc'}versions_asc{else}versions_desc{/if}">{tr}Version{/tr}</a></td>
{/if}
{if $wiki_list_links eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-orphan_pages.php?find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'links_desc'}links_asc{else}links_desc{/if}">{tr}Links{/tr}</a></td>
{/if}
{if $wiki_list_backlinks eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-orphan_pages.php?find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'backlinks_desc'}backlinks_asc{else}backlinks_desc{/if}">{tr}Backlinks{/tr}</a></td>
{/if}
{if $wiki_list_size eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-orphan_pages.php?find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}Size{/tr}</a></td>
{/if}
</tr>
{cycle values="even,odd" print=false}
{section name=changes loop=$listpages}
<tr>
{if $wiki_list_name eq 'y'}
	<td class="{cycle advance=false}"><a href="tiki-index.php?page={$listpages[changes].pageName|escape:"url"}" class="link" title="{$listpages[changes].pageName}">{$listpages[changes].pageName|truncate:20:"...":true}</a>
	{if $tiki_p_edit eq 'y'}
	<br/>(<a class="link" href="tiki-editpage.php?page={$listpages[changes].pageName|escape:"url"}">{tr}edit{/tr}</a>)
	{/if}
	</td>
{/if}
{if $wiki_list_hits eq 'y'}	
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].hits}</td>
{/if}
{if $wiki_list_lastmodif eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].lastModif|tiki_short_datetime}</td>
{/if}
{if $wiki_list_creator eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].creator|userlink}</td>
{/if}

{if $wiki_list_user eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].user|userlink}</td>
{/if}
{if $wiki_list_lastver eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].version}</td>
{/if}
{if $wiki_list_comment eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].comment}</td>
{/if}
{if $wiki_list_status eq 'y'}
	<td style="text-align:center;" class="{cycle advance=false}">
	{if $listpages[changes].flag eq 'locked'}
		<img src='img/icons/lock_topic.gif' alt='{tr}locked{/tr}' />
	{else}
		<img src='img/icons/unlock_topic.gif' alt='{tr}unlocked{/tr}' />
	{/if}
	</td>
{/if}
{if $wiki_list_versions eq 'y'}
	{if $feature_history eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}"><a class="link" href="tiki-pagehistory.php?page={$listpages[changes].pageName|escape:"url"}">{$listpages[changes].versions}</a></td>
	{else}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].versions}</td>
	{/if}
{/if}
{if $wiki_list_links eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].links}</td>
{/if}
{if $wiki_list_backlinks eq 'y'}
	{if $feature_backlinks eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}"><a class="link" href="tiki-backlinks.php?page={$listpages[changes].pageName|escape:"url"}">{$listpages[changes].backlinks}</a></td>
	{else}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].backlinks}</td>
	{/if}
{/if}
{if $wiki_list_size eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].len|kbsize}</td>
{/if}
       {cycle print=false}
</tr>
{sectionelse}
<tr><td colspan="16">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br/>
<!--
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-orphan_pages.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
[<a class="prevnext" href="tiki-orphan_pages.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-orphan_pages.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>
{/section}
{/if}
</div>
-->
</div>
