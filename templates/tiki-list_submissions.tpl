<h1><a class="pagetitle" href="tiki-list_submissions.php">{tr}Submissions{/tr}</a></h1>

<div class="navbar">
<a class="linkbut" href="tiki-edit_submission.php">{tr}Edit New Submission{/tr}</a>
{if $tiki_p_read_article eq 'y'}
<a class="linkbut" href="tiki-list_articles.php">{tr}List articles{/tr}</a>
{/if}
</div>

<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_submissions.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>

<table class="normal">
<tr>
{if $prefs.art_list_title eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></td>
{/if}
{if $prefs.art_list_topic eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'topicName_desc'}topicName_asc{else}topicName_desc{/if}">{tr}Topic{/tr}</a></td>
{/if}
{if $prefs.art_list_date eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'publishDate_desc'}publishDate_asc{else}publishDate_desc{/if}">{tr}PublishDate{/tr}</a></td>
{/if}
{if $prefs.art_list_size eq 'y'}
	<td style="text-align:right;" class="heading"><a class="tableheading" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}Size{/tr}</a></td>
{/if}
{if $prefs.art_list_img eq 'y'}
<td class="heading">{tr}Img{/tr}</td>
{/if}
{if $prefs.art_list_author eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'author_desc'}author_asc{else}author_desc{/if}">{tr}User{/tr}</a></td>
{/if}
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$listpages}
<tr>
{if $prefs.art_list_title eq 'y'}
	<td class="{cycle advance=false}"><a class="link" title="{$listpages[changes].title}" href="tiki-edit_submission.php?subId={$listpages[changes].subId}">{$listpages[changes].title|truncate:$prefs.art_list_title_len:"...":true}</a>
	{*if $listpages[changes].type eq 'Review'}(r){/if*}
	</td>
{/if}
{if $prefs.art_list_topic eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].topicName}</td>
{/if}
{if $prefs.art_list_date eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].publishDate|tiki_short_datetime}</td>
{/if}
{if $prefs.art_list_size eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].size|kbsize}</td>
{/if}
{if $prefs.art_list_img eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].hasImage}/{$listpages[changes].useImage}</td>
{/if}
{if $prefs.art_list_author eq 'y'}
<td class="{cycle advance=false}">{$listpages[changes].author}</td>
{/if}
<td class="{cycle}" >
	{if $tiki_p_edit_submission eq 'y' or ($listpages[changes].author eq $user and $user)}
		<a class="link" href="tiki-edit_submission.php?subId={$listpages[changes].subId}">{icon _id='page_edit'}</a>
	{/if}
	{if $tiki_p_remove_submission eq 'y'}
		<a class="link" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].subId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
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
[<a class="prevnext" href="tiki-list_submissions.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_submissions.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-list_submissions.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
