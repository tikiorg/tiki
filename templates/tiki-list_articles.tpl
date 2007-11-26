{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-list_articles.tpl,v 1.50.2.3 2007-11-26 15:18:22 sylvieg Exp $ *}

<h1><a class="pagetitle" href="tiki-list_articles.php">{tr}Articles{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Articles" target="tikihelp" class="tikihelp" title="{tr}List Articles{/tr}">
<img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>
{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-list_articles.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}List Articles Tpl{/tr}">
<img src="pics/icons/shape_square_edit.png" border="0" width="16" height="16" alt='{tr}Edit Template{/tr}' /></a>
{/if}
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=cms"><img src='pics/icons/wrench.png' border='0' width='16' height='16' alt="{tr}Admin Feature{/tr}" title="{tr}Admin Feature{/tr}" /></a>
{/if}
</h1>

<div class="navbar">
{if $tiki_p_edit_article eq 'y'}
  <a class="linkbut" href="tiki-edit_article.php">{tr}Edit New Article{/tr}</a>
{/if}
<a class="linkbut" href="tiki-view_articles.php">{tr}View Articles{/tr}</a>
{if $prefs.feature_submissions == 'y' && ($tiki_p_approve_submission == "y" || $tiki_p_remove_submission == "y" || $tiki_p_edit_submission == "y")}
<a class="linkbut" href="tiki-list_submissions.php">{tr}View submissions{/tr}</a>
{/if}
</div>

{include file="find.tpl"}

<table class="normal">
<tr>
{if $prefs.art_list_title eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}&amp;maxRecords={$maxRecords}">{tr}Title{/tr}</a></td>
{/if}
{if $prefs.art_list_type eq 'y'}	
	<td class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}&amp;maxRecords={$maxRecords}">{tr}Type{/tr}</a></td>
{/if}
{if $prefs.art_list_topic eq 'y'}	
	<td class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'topicName_desc'}topicName_asc{else}topicName_desc{/if}&amp;maxRecords={$maxRecords}">{tr}Topic{/tr}</a></td>
{/if}
{if $prefs.art_list_date eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'publishDate_desc'}publishDate_asc{else}publishDate_desc{/if}&amp;maxRecords={$maxRecords}">{tr}PublishDate{/tr}</a></td>
{/if}
{if $prefs.art_list_expire eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'expireDate_desc'}expireDate_asc{else}expireDate_desc{/if}&amp;maxRecords={$maxRecords}">{tr}ExpireDate{/tr}</a></td>
{/if}
{if $prefs.art_list_visible eq 'y'}
	<td class="heading"><span class="tableheading">{tr}Visible{/tr}</span></td>
{/if}
{if $prefs.art_list_author eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'authorName_desc'}authorName_asc{else}authorName_desc{/if}&amp;maxRecords={$maxRecords}">{tr}AuthorName{/tr}</a></td>
{/if}
{if $prefs.art_list_reads eq 'y'}
	<td style="text-align:right;" class="heading"><a class="tableheading" 
href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if 
$sort_mode eq 'nbreads_desc'}nbreads_asc{else}nbreads_desc{/if}&amp;maxRecords={$maxRecords}">{tr}Reads{/tr}</a></td>
{/if}
{if $prefs.art_list_size eq 'y'}
	<td style="text-align:right;" class="heading"><a class="tableheading" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}&amp;maxRecords={$maxRecords}">{tr}Size{/tr}</a></td>
{/if}
{if $prefs.art_list_img eq 'y'}
	<td class="heading">{tr}Img{/tr}</td>
{/if}
{if $tiki_p_edit_article eq 'y' or $tiki_p_remove_article eq 'y' or isset($oneEditPage) or $tiki_p_read_article}<td  class="heading">{tr}Action{/tr}</td>{/if}
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$listpages}
<tr>
{if $prefs.art_list_title eq 'y'}
	<td class="{cycle advance=false}">
	{if $tiki_p_read_article eq 'y'}
		<a class="artname" href="tiki-read_article.php?articleId={$listpages[changes].articleId}" title="{$listpages[changes].title|escape}">
	{/if}
	{$listpages[changes].title|truncate:$prefs.art_list_title_len:"...":true}
	{if $listpages[changes].type eq 'Review'}(r){/if}
	{if $tiki_p_read_article eq 'y'}
		</a>
	{/if}
	</td>
{/if}
{if $prefs.art_list_type eq 'y'}	
	<td class="{cycle advance=false}">{tr}{$listpages[changes].type}{/tr}</td>
{/if}
{if $prefs.art_list_topic eq 'y'}	
	<td class="{cycle advance=false}">{$listpages[changes].topicName}</td>
{/if}
{if $prefs.art_list_date eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].publishDate|tiki_short_datetime}</td>
{/if}
{if $prefs.art_list_expire eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].expireDate|tiki_short_datetime}</td>
{/if}
{if $prefs.art_list_visible eq 'y'}
	<td class="{cycle advance=false}">{tr}{$listpages[changes].disp_article}{/tr}</td>
{/if}
{if $prefs.art_list_author eq 'y'}	
	<td class="{cycle advance=false}">{$listpages[changes].authorName}</td>
{/if}
{if $prefs.art_list_reads eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].nbreads}</td>
{/if}
{if $prefs.art_list_size eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].size|kbsize}</td>
{/if}
{if $prefs.art_list_img eq 'y'}
	<td class="{cycle advance=false}">{tr}{$listpages[changes].hasImage}{/tr}/{tr}{$listpages[changes].useImage}{/tr}</td>
{/if}
<td class="{cycle}">
{if $tiki_p_read_article eq 'y'}
<a href="tiki-read_article.php?articleId={$listpages[changes].articleId}" title="{$listpages[changes].title|escape}"><img src='pics/icons/magnifier.png' border='0' alt='{tr}View{/tr}' title='{tr}View{/tr}' width='16' height='16' /></a>
{/if}
{if $tiki_p_edit_article eq 'y' or ($listpages[changes].author eq $user and $listpages[changes].creator_edit eq 'y')}
<a class="link" href="tiki-edit_article.php?articleId={$listpages[changes].articleId}"><img src='pics/icons/page_edit.png' border='0' width='16' height='16' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' /></a>{/if}
{if $tiki_p_admin_cms eq 'y' or $tiki_p_assign_perm_cms eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName={$listpages[changes].title|escape:'url'}&amp;objectType=article&amp;permType=cms&amp;objectId={$listpages[changes].articleId}"><img src='pics/icons/key.png' border='0' width='16' height='16' alt='{tr}Perms{/tr}' title='{tr}Perms{/tr}' /></a>
{/if}
{if $tiki_p_remove_article eq 'y'}
&nbsp;<a class="link" href="tiki-list_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].articleId}"><img src='pics/icons/cross.png' border='0' width='16' height='16' alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' /></a>{/if}
</td>
</tr>
{sectionelse}
<tr><td class="odd" colspan="11">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br />

{if count($listpages) > 0}
<div class="mini">
{if $prev_offset >= 0}
[<a class="artprevnext" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}&amp;maxRecords={$maxRecords}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="artprevnext" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}&amp;maxRecords={$maxRecords}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}&amp;maxRecords={$maxRecords}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
{/if}

