<a class="pagetitle" href="tiki-referer_stats.php">referer stats</a><br/><br/>
<a class="link" href="tiki-referer_stats.php?clear=1">{tr}clear stats{/tr}</a><br/><br/>

<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-referer_stats.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="referer" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>


<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-referer_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'referer_desc'}referer_asc{else}referer_desc{/if}">{tr}term{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-referer_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}hits{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-referer_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'last_desc'}last_asc{else}last_desc{/if}">{tr}last{/tr}</a></td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].referer}</td>
<td class="odd">{$channels[user].hits}</td>
<td class="odd">{$channels[user].last|date_format:"%d of %b [%H:%M]"}</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].referer}</td>
<td class="even">{$channels[user].hits}</td>
<td class="even">{$channels[user].last|date_format:"%d of %b [%H:%M]"}</td>
</tr>
{/if}
{/section}
</table>
<br/>
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-referer_stats.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-referer_stats.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-referer_stats.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

