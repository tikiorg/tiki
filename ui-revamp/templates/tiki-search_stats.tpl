{* $Id$ *}

{title help="SearchStats"}{tr}Search stats{/tr}{/title}

<div class="navbar">
	{button href="?clear=1" _text="{tr}Clear Stats{/tr}"}
</div>

{include file='find.tpl' _sort_mode='y'}

<table class="normal">
<tr>
<!-- term -->
<th><a href="tiki-search_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'term_desc'}term_asc{else}term_desc{/if}">{tr}Word{/tr}</a></th>

<!-- searched -->
<th>
<a href="tiki-search_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Searched{/tr}</a></th>

<!-- How can we increase the number of items displayed on a page? -->

</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
  <tr>
    <td class="{cycle advance=false}">{$channels[user].term}</td>
    <td class="{cycle}">{$channels[user].hits}</td>
  </tr>
{/section}
</table>
<br />
{if $cant_pages gt 0}<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-search_stats.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-search_stats.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-search_stats.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>{/if}

