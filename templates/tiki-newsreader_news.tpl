{*Smarty template*}
<a class="pagetitle" href="tiki-newsreader_news.php?server={$server}&amp;port={$port}&amp;username={$username}&amp;password={$password}&amp;group={$group}">{tr}Newss from{/tr}:{$group}</a><br/><br/>

<table class="normal">
{cycle values="odd,even" print=false}
<tr>
<td class="heading">{tr}id{/tr}</td>
<td class="heading">{tr}Date{/tr}</td>
</tr>
{section loop=$articles name=ix}
<tr>
<td class="{cycle}">{$articles[ix].loopid}</td>
<td class="{cycle}">{$articles[ix].Date}</td>
</tr>
{/section}
</table>

<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-newsreader_news.php?server={$server}&amp;port={$port}&amp;username={$username}&amp;password={$password}&amp;group={$group}&amp;offset={$prev_offset}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-newsreader_news.php?server={$server}&amp;port={$port}&amp;username={$username}&amp;password={$password}&amp;group={$group}&amp;offset={$next_offset}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{if $cant_pages < 20}
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-newsreader_news.php?server={$server}&amp;port={$port}&amp;username={$username}&amp;password={$password}&amp;group={$group}&amp;offset=selector_offset">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
{/if}
</div>