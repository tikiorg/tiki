<a href="tiki-list_cache.php" class="pagetitle">{tr}Cache{/tr}</a><br/><br/>
<table class="findtable">
<tr><td class="findtitle">{tr}Find{/tr}</td>
   <td class="findtitle">
   <form method="get" action="tiki-list_cache.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<div  align="center">
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'refresh_desc'}refresh_asc{else}refresh_desc{/if}">{tr}Last updated{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;<a class="link" href="{$listpages[changes].url}">{$listpages[changes].url}</a>&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].refresh|date_format:"%d of %B [%H:%M]"}&nbsp;</td>
<td class="odd">&nbsp;<a class="link" target="_blank" href="tiki-view_cache.php?cacheId={$listpages[changes].cacheId}">view</a>&nbsp;<a class="link" href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].cacheId}">remove</a>&nbsp;<a class="link" href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;refresh={$listpages[changes].cacheId}">refresh</a>&nbsp;</td>
{else}
<td class="even">&nbsp;<a class="link" href="{$listpages[changes].url}">{$listpages[changes].url}</a>&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].refresh|date_format:"%d of %B [%H:%M]"}&nbsp;</td>
<td class="even">&nbsp;<a class="link" target="_blank" href="tiki-view_cache.php?cacheId={$listpages[changes].cacheId}">view</a>&nbsp;<a class="link" href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].cacheId}">remove</a>&nbsp;<a class="link" href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;refresh={$listpages[changes].cacheId}">refresh</a>&nbsp;</td>
</tr>
{/if}
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_cache.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_cache.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_cache.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
