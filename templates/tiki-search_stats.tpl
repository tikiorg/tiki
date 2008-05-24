{* $Id$ *}
<h1><a class="pagetitle" href="tiki-search_stats.php">{tr}Search stats{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}SearchStats" target="tikihelp" class="tikihelp" title="{tr}Search Stats{/tr}">
{icon _id='help'}</a>{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-search_stats.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}search stats tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>{/if}</h1>

<div class="navbar">
<a class="linkbut" href="tiki-search_stats.php?clear=1">{tr}Clear Stats{/tr}</a>
</div>

<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-search_stats.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>


<table class="normal">
<tr>
<!-- term -->
<td class="heading"><a class="tableheading" href="tiki-search_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'term_desc'}term_asc{else}term_desc{/if}">{tr}Word{/tr}</a></td>

<!-- searched -->
<td class="heading">
<a class="tableheading" href="tiki-search_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Searched{/tr}</a></td>

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

