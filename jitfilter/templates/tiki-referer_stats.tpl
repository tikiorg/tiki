{title help="Referer+Stats"}{tr}Referer stats{/tr}{/title}

<span class="button2"><a href="tiki-referer_stats.php?clear=1">{tr}Clear Stats{/tr}</a></span>
<br /><br />

{include file='find.tpl' _sort_mode='y'}

<table class="normal">
  <tr>
  <th>
    <a href="tiki-referer_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'referer_desc'}referer_asc{else}referer_desc{/if}">{tr}Word{/tr}</a>
  </th>
  <th>
    <a href="tiki-referer_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a>
  </th>
  <th>
    <a href="tiki-referer_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'last_desc'}last_asc{else}last_desc{/if}">{tr}Last{/tr}</a>
  </th>
  </tr>
  {cycle values="odd,even" print=false}
  {section name=user loop=$channels}
    <tr>
      <td class="{cycle advance=false}">{$channels[user].referer}</td>
      <td class="{cycle advance=false}">{$channels[user].hits}</td>
      <td class="{cycle}">{$channels[user].last|tiki_short_datetime}</td>
    </tr>
  {/section}
</table>

<br />

<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-referer_stats.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-referer_stats.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-referer_stats.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>

