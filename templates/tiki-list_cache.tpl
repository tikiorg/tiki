<h1>{tr}Cache{/tr}</h1>
<div  align="center">
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr><td>Find</td>
   <td>
   <form method="get" action="tiki-list_cache.php">
     <input type="text" name="find" />
     <input type="submit" value="find" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table border="1" width="97%" cellpadding="0" cellspacing="0">
<tr>
<td class="heading"><a class="link" href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'refresh_desc'}refresh_asc{else}refresh_desc{/if}">{tr}Last updated{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;<a class="wiki" href="{$listpages[changes].url}">{$listpages[changes].url}</a>&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].refresh|date_format:"%A %d of %B, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="odd">&nbsp;<a class="link" target="_blank" href="tiki-view_cache.php?cacheId={$listpages[changes].cacheId}">view cache</a>&nbsp;<a class="link" href="tiki-list_cache.php?remove={$listpages[changes].cacheId}">remove</a>&nbsp;<a class="link" href="tiki-list_cache.php?refresh={$listpages[changes].cacheId}">refresh</a>&nbsp;</td>
{else}
<td class="even">&nbsp;<a class="wiki" href="{$listpages[changes].url}">{$listpages[changes].url}</a>&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].refresh|date_format:"%A %d of %B, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="even">&nbsp;<a class="link" target="_blank" href="tiki-view_cache.php?cacheId={$listpages[changes].cacheId}">view cache</a>&nbsp;<a class="link" href="tiki-list_cache.php?remove={$listpages[changes].cacheId}">remove</a>&nbsp;<a class="link" href="tiki-list_cache.php?refresh={$listpages[changes].cacheId}">refresh</a>&nbsp;</td>
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
[<a href="tiki-list_cache.php?&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="tiki-list_cache.php?&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>
