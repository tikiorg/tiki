{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/tiki-list_articles.tpl,v 1.1 2004-05-09 23:08:45 damosoft Exp $ *}

<a class="pagetitle" href="tiki-list_articles.php">{tr}Articles{/tr}</a>

{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=ArticleDoc" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}List Articles{/tr}">
<img border='0' src='img/icons/help.gif' alt='{tr}help{/tr}' /></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-list_articles.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}list articles tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='{tr}edit template{/tr}' /></a>
{/if}
<br /><br />[
{if $tiki_p_edit_article eq 'y'}
  <a class="linkbut" href="tiki-edit_article.php">{tr}Create new article|{/tr}</a>
{/if}
<a class="linkbut" href="tiki-view_articles.php">{tr}View articles{/tr}</a>]

{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=cms"><img src='img/icons/config.gif' border='0'  alt="{tr}configure listing{/tr}" title="{tr}configure listing{/tr}" /></a>
{/if}

<br/><br/>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Search{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_articles.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Go{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <select name="type">
     <option value='' {if $find_type eq ''}selected="selected"{/if}>{tr}all{/tr}</option>
     {section name=t loop=$types}
     <option value="{$types[t].type|escape}" {if $type eq $types[t].type}selected="selected"{/if}>{$types[t].type}</option>
     {/section}
     </select>
     <select name="topic">
     <option value='' {if $find_topic eq ''}selected="selected"{/if}>{tr}all{/tr}</option>
     {section name=ix loop=$topics}
     <option value="{$topics[ix].topicId|escape}" {if $find_topic eq $topics[ix].topicId}selected="selected"{/if}>{tr}{$topics[ix].name}{/tr}</option>
     {/section}
     </select>
   </form>
   </td>
</tr>
</table>

<table class="normal">
<tr>
{if $art_list_title eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></td>
{/if}
{if $art_list_type eq 'y'}	
	<td class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}Type{/tr}</a></td>
{/if}
{if $art_list_topic eq 'y'}	
	<td class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'topicName_desc'}topicName_asc{else}topicName_desc{/if}">{tr}Topic{/tr}</a></td>
{/if}
{if $art_list_date eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'publishDate_desc'}publishDate_asc{else}publishDate_desc{/if}">{tr}Publish Date{/tr}</a></td>
{/if}
{if $art_list_expire eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'expireDate_desc'}expireDate_asc{else}expireDate_desc{/if}">{tr}Expire Date{/tr}</a></td>
{/if}
{if $art_list_visible eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}Visible{/tr}</a></td>
{/if}
{if $art_list_author eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'authorName_desc'}authorName_asc{else}authorName_desc{/if}">{tr}Author Name{/tr}</a></td>
{/if}
{if $art_list_reads eq 'y'}
	<td style="text-align:right;" class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'reads_desc'}reads_asc{else}reads_desc{/if}">{tr}Reads{/tr}</a></td>
{/if}
{if $art_list_size eq 'y'}
	<td style="text-align:right;" class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}Size{/tr}</a></td>
{/if}
{if $art_list_img eq 'y'}
	<td class="heading">{tr}Image{/tr}</td>
{/if}
<td  class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$listpages}
<tr>
{if $art_list_title eq 'y'}
	<td class="{cycle advance=false}">
	{if $tiki_p_read_article eq 'y'}
		<a class="artname" href="tiki-read_article.php?articleId={$listpages[changes].articleId}">
	{/if}
	{$listpages[changes].title|truncate:20:"...":true}
	{if $listpages[changes].type eq 'Review'}(r){/if}
	{if $tiki_p_read_article eq 'y'}
		</a>
	{/if}
	</td>
{/if}
{if $art_list_type eq 'y'}	
	<td class="{cycle advance=false}">{tr}{$listpages[changes].type}{/tr}</td>
{/if}
{if $art_list_topic eq 'y'}	
	<td class="{cycle advance=false}">{$listpages[changes].topicName}</td>
{/if}
{if $art_list_date eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].publishDate|tiki_short_datetime}</td>
{/if}
{if $art_list_expire eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].expireDate|tiki_short_datetime}</td>
{/if}
{if $art_list_visible eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].disp_article}</td>
{/if}
{if $art_list_author eq 'y'}	
	<td class="{cycle advance=false}">{$listpages[changes].authorName}</td>
{/if}
{if $art_list_reads eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].reads}</td>
{/if}
{if $art_list_size eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].size|kbsize}</td>
{/if}
{if $art_list_img eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].hasImage}/{$listpages[changes].useImage}</td>
{/if}
<td class="{cycle}">
{if $tiki_p_edit_article eq 'y' or ($listpages[changes].author eq $user and $listpages[changes].creator_edit eq 'y')}
	<a class="link" href="tiki-edit_article.php?articleId={$listpages[changes].articleId}"><img src='img/icons/edit.gif' border='0' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' /></a>
{/if}
{if $tiki_p_remove_article eq 'y'}
	<a class="link" href="tiki-list_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].articleId}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this article?{/tr}')"><img src='img/icons2/delete.gif' border='0' alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' /></a>
{/if}
</td>
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="artprevnext" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="artprevnext" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>