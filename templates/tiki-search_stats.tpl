<h1><a class="pagetitle" href="tiki-search_stats.php">{tr}Search stats{/tr}</a>
  
      {if $feature_help eq 'y'}
<a href="{$helpurl}SearchStats" target="tikihelp" class="tikihelp" title="{tr}Search Stats{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-search_stats.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}search stats tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}edit tpl{/tr}' /></a>{/if}</h1>

<a class="linkbut" href="tiki-search_stats.php?clear=1">{tr}clear stats{/tr}</a><br /><br />

<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-search_stats.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>


<table class="normal">
<tr>
<!-- term -->
<td class="heading"><a class="tableheading" href="tiki-search_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'term_desc'}term_asc{else}term_desc{/if}">{tr}term{/tr}</a></td>

<!-- searched -->
<td class="heading">
<a class="tableheading" href="tiki-search_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}searched{/tr}</a></td>

<!-- How can we increase the number of items displayed on a page? -->

</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].term}</td>
<td class="odd">{$channels[user].hits}</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].term}</td>
<td class="even">{$channels[user].hits}</td>
</tr>
{/if}
{/section}
</table>
<br />
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-search_stats.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-search_stats.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-search_stats.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

