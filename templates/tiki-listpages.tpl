<h1>{tr}Pages{/tr}</h1>
<div align="center">
<table border="1" width="90%" cellpadding="0" cellspacing="0">
<tr>
<td class="heading"><a class="link" href="tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'pageName_desc'}pageName_asc{else}pageName_desc{/if}">{tr}Page{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last modified{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}Last author{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'version_desc'}version_asc{else}version_desc{/if}">{tr}Last version{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}">{tr}Comment{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'flag_desc'}flag_asc{else}flag_desc{/if}">{tr}Status{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'versions_desc'}versions_asc{else}versions_desc{/if}">{tr}Versions{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'links_desc'}links_asc{else}links_desc{/if}">{tr}Links{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'backlinks_desc'}backlinks_asc{else}backlinks_desc{/if}">{tr}Backlinks{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}Size{/tr}</a></td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;<a href="tiki-index.php?page={$listpages[changes].pageName}" class="wiki">{$listpages[changes].pageName|truncate:20:"(...)":true}</a>&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].hits}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].lastModif|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].user}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].version}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].comment}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].flag}&nbsp;</td>
{if $feature_history eq 'y'}
<td class="odd">&nbsp;<a class="link" href="tiki-pagehistory.php?page={$listpages[changes].pageName}">{$listpages[changes].versions}</a>&nbsp;</td>
{else}
<td class="odd">&nbsp;{$listpages[changes].versions}&nbsp;</td>
{/if}
<td class="odd">&nbsp;{$listpages[changes].links}&nbsp;</td>
{if $feature_backlinks eq 'y'}
<td class="odd">&nbsp;<a class="link" href="tiki-backlinks.php?page={$listpages[changes].pageName}">{$listpages[changes].backlinks}</a>&nbsp;</td>
{else}
<td class="odd">&nbsp;{$listpages[changes].backlinks}&nbsp;</td>
{/if}
<td class="odd">&nbsp;{$listpages[changes].len}&nbsp;</td>
{else}
<td class="even">&nbsp;<a href="tiki-index.php?page={$listpages[changes].pageName}" class="wiki">{$listpages[changes].pageName|truncate:20:"(...)":true}</a>&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].hits}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].lastModif|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].user}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].version}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].comment}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].flag}&nbsp;</td>
{if $feature_history eq 'y'}
<td class="even">&nbsp;<a class="link" href="tiki-pagehistory.php?page={$listpages[changes].pageName}">{$listpages[changes].versions}</a>&nbsp;</td>
{else}
<td class="even">&nbsp;{$listpages[changes].versions}&nbsp;</td>
{/if}
<td class="even">&nbsp;{$listpages[changes].links}&nbsp;</td>
{if $feature_backlinks eq 'y'}
<td class="even">&nbsp;<a class="link" href="tiki-backlinks.php?page={$listpages[changes].pageName}">{$listpages[changes].backlinks}</a>&nbsp;</td>
{else}
<td class="even">&nbsp;{$listpages[changes].backlinks}&nbsp;</td>
{/if}
<td class="even">&nbsp;{$listpages[changes].len}&nbsp;</td>
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
[<a href="tiki-listpages.php?&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="tiki-listpages.php?&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>
