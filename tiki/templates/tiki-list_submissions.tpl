<h1><a class="wiki" href="tiki-list_submissions.php">{tr}Submissions{/tr}</a></h1>
<a class="link" href="tiki-edit_submission.php">edit submission</a>
<br/><br/>
<div align="center">
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr><td>Find</td>
   <td>
   <form method="get" action="tiki-list_submissions.php">
     <input type="text" name="find" />
     <input type="submit" value="find" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table  border="1" width="97%" cellpadding="0" cellspacing="0">
<tr>
<td class="heading"><a class="link" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'topicName_desc'}topicName_asc{else}topicName_desc{/if}">{tr}Topic{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'publishDate_desc'}publishDate_asc{else}publishDate_desc{/if}">{tr}PublishDate{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'authorName_desc'}authorName_asc{else}authorName_desc{/if}">{tr}AuthorName{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'reads_desc'}reads_asc{else}reads_desc{/if}">{tr}Reads{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}Size{/tr}</a></td>
<td class="heading">{tr}HasImg{/tr}</td>
<td class="heading"><a class="link" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'useImage_desc'}useImage_asc{else}useImage_desc{/if}">{tr}UseImg{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'author_desc'}author_asc{else}author_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;{$listpages[changes].title|truncate:20:"(...)":true}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].topicName}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].publishDate|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].authorName}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].reads}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].size}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].hasImage}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].useImage}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].author}&nbsp;</td>
<td class="odd">
{if $tiki_p_edit_submission eq 'y'}
<a class="link" href="tiki-edit_submission.php?subId={$listpages[changes].subId}">{tr}Edit{/tr}</a>
{/if}
{if $tiki_p_remove_submission eq 'y'}
<a class="link" href="tiki-list_submissions.php?remove={$listpages[changes].subId}">{tr}Remove{/tr}</a>
{/if}
{if $tiki_p_approve_submission eq 'y'}
<a class="link" href="tiki-list_submissions.php?approve={$listpages[changes].subId}">{tr}Approve{/tr}</a>
{/if}
</td>
{else}
<td class="even">&nbsp;{$listpages[changes].title|truncate:20:"(...)":true}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].topicName}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].publishDate|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].authorName}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].reads}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].size}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].hasImage}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].useImage}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].author}&nbsp;</td>
<td class="even">
{if $tiki_p_edit_submission eq 'y'}
<a class="link" href="tiki-edit_submission.php?subId={$listpages[changes].subId}">{tr}Edit{/tr}</a>
{/if}
{if $tiki_p_remove_submission eq 'y'}
<a class="link" href="tiki-list_submissions.php?remove={$listpages[changes].subId}">{tr}Remove{/tr}</a>
{/if}
{if $tiki_p_approve_submission eq 'y'}
<a class="link" href="tiki-list_submissions.php?approve={$listpages[changes].subId}">{tr}Approve{/tr}</a>
{/if}
</td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a href="tiki-list_submissions.php?&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="tiki-list_submissions.php?&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>
