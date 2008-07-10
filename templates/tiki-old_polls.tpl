<h1><a class="pagetitle" href="tiki-old_polls.php">{tr}Polls{/tr}</a></h1>
<div align="center">

{include file='find.tpl' _sort_mode='y'}

<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-old_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-old_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'publishDate_desc'}publishDate_asc{else}publishDate_desc{/if}">{tr}Published{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-old_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'votes_desc'}votes_asc{else}votes_desc{/if}">{tr}Votes{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;{$listpages[changes].title}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].publishDate|tiki_short_datetime}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].votes}&nbsp;</td>
<td class="odd">
<a class="link" href="tiki-poll_results.php?pollId={$listpages[changes].pollId}">{tr}Results{/tr}</a>
{if $tiki_p_vote_poll ne 'n'}
	<a class="link" href="tiki-poll_form.php?pollId={$listpages[changes].pollId}">{tr}Vote{/tr}</a>
{/if}
</td>
{else}
<td class="even">&nbsp;{$listpages[changes].title}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].publishDate|tiki_short_datetime}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].votes}&nbsp;</td>
<td class="even">
<a class="link" href="tiki-poll_results.php?pollId={$listpages[changes].pollId}">{tr}Results{/tr}</a>
{if $tiki_p_vote_poll ne 'n'}
	<a class="link" href="tiki-poll_form.php?pollId={$listpages[changes].pollId}">{tr}Vote{/tr}</a>
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
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-old_polls.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-old_polls.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-old_polls.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>
</div>
