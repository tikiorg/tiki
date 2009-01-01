{title help="polls" admpage="polls"}{tr}Polls{/tr}{/title}

{include file='find.tpl' _sort_mode='y'}

<table class="normal">
<tr>
<th><a href="tiki-old_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></th>
<th><a href="tiki-old_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'publishDate_desc'}publishDate_asc{else}publishDate_desc{/if}">{tr}Published{/tr}</a></th>
<th><a href="tiki-old_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'votes_desc'}votes_asc{else}votes_desc{/if}">{tr}Votes{/tr}</a></th>
<th>{tr}Action{/tr}</th>
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

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
