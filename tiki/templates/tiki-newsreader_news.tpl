{*Smarty template*}
<a class="pagetitle" href="tiki-newsreader_news.php?serverId={$serverId}&amp;server={$server}&amp;port={$port}&amp;username={$username}&amp;password={$password}&amp;group={$group}">{tr}Newss from{/tr}:{$group}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
<br/><br/>
[<a class="link" href="tiki-newsreader_servers.php">{tr}Back to servers{/tr}</a>
{if $serverId}| <a class="link" href="tiki-newsreader_groups.php?serverId={$serverId}">{tr}Back to groups{/tr}</a>{/if}]
<br/><br/>
<table class="normal">
{cycle values="odd,even" print=false}
<tr>
<td class="heading">{tr}From{/tr}</td>
<td class="heading">{tr}Subject{/tr}</td>
<td class="heading">{tr}Date{/tr}</td>
</tr>
{section loop=$articles name=ix}
<tr>
<td class="{cycle advance=false}">{$articles[ix].From}</td>
<td class="{cycle advance=false}"><a class="link" href="tiki-newsreader_read.php?server={$server}&amp;port={$port}&amp;username={$username}&amp;password={$password}&amp;group={$group}&amp;offset={$offset}&amp;id={$articles[ix].loopid}&amp;serverId={$serverId}">{$articles[ix].Subject}</a></td>
<td class="{cycle}">{$articles[ix].Date|tiki_short_datetime}</td>
</tr>
{/section}
</table>

<div class="mini">
<div align="center">
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
</div>