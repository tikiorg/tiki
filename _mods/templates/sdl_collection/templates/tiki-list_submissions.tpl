<a class="pagetitle" href="tiki-list_submissions.php">{tr}Submissions{/tr}</a><br/><br/>
<a class="linkbut" href="tiki-edit_submission.php">[{tr}Create new submission{/tr}]</a>
<br/><br/>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Search{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_submissions.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Go{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
{if $art_list_title eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></td>
{/if}
{if $art_list_topic eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'topicName_desc'}topicName_asc{else}topicName_desc{/if}">{tr}Topic{/tr}</a></td>
{/if}
{if $art_list_date eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'publishDate_desc'}publishDate_asc{else}publishDate_desc{/if}">{tr}Publish Date{/tr}</a></td>
{/if}
{if $art_list_size eq 'y'}
	<td style="text-align:right;" class="heading"><a class="tableheading" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}Size{/tr}</a></td>
{/if}
{if $art_list_img eq 'y'}
<td class="heading">{tr}Image{/tr}</td>
{/if}
{if $art_list_author eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'author_desc'}author_asc{else}author_desc{/if}">{tr}User{/tr}</a></td>
{/if}
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$listpages}
<tr>
{if $art_list_title eq 'y'}
	<td class="{cycle advance=false}"><a class="link" title="{$listpages[changes].title}" href="tiki-edit_submission.php?subId={$listpages[changes].subId}">{$listpages[changes].title|truncate:20:"...":true}</a>
	{*if $listpages[changes].type eq 'Review'}(r){/if*}
	</td>
{/if}
{if $art_list_topic eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].topicName}</td>
{/if}
{if $art_list_date eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].publishDate|tiki_short_datetime}</td>
{/if}
{if $art_list_size eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].size|kbsize}</td>
{/if}
{if $art_list_img eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].hasImage}/{$listpages[changes].useImage}</td>
{/if}
{if $art_list_author eq 'y'}
<td class="{cycle advance=false}">{$listpages[changes].author}</td>
{/if}
<td class="{cycle}" >
	{if $tiki_p_edit_submission eq 'y' or $listpages[changes].author eq $user}
		<a class="link" href="tiki-edit_submission.php?subId={$listpages[changes].subId}"><img src='img/icons/edit.gif' border='0' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' /></a>
	{/if}
	{if $tiki_p_remove_submission eq 'y'}
		<a class="link" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].subId}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this article?{/tr}')"><img src='img/icons2/delete.gif' alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' border='0' /></a>
	{/if}
	{if $tiki_p_approve_submission eq 'y'}
		<a class="link" href="tiki-list_submissions.php?approve={$listpages[changes].subId}"><img src='img/icons2/post.gif' border='0' alt='{tr}Approve{/tr}' title='{tr}Approve{/tr}' /></a>
	{/if}
</td>
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_submissions.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_submissions.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_submissions.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>