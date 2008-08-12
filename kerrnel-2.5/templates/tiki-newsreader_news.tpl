{*Smarty template*}
<h1><a class="pagetitle" href="tiki-newsreader_news.php?serverId={$serverId}&amp;server={$server}&amp;port={$port}&amp;news_username={$news_username}&amp;password={$password}&amp;group={$group}">{tr}News from{/tr}:{$group}</a></h1>
{include file=tiki-mytiki_bar.tpl}
<br /><br />
<a class="linkbut" href="tiki-newsreader_servers.php">{tr}Back to servers{/tr}</a>
{if $serverId}<a class="linkbut" href="tiki-newsreader_groups.php?serverId={$serverId}">{tr}Back to groups{/tr}</a>{/if}
<a class="linkbut" href="tiki-newsreader_news.php?serverId={$serverId}&amp;server={$server}&amp;port={$port}&amp;news_username={$news_username}&amp;password={$password}&amp;group={$group}&amp;mark=1&amp;offset={$offset}">{tr}Save position{/tr}</a>
<br /><br />
<table class="normal">
{cycle values="odd,even" print=false}
<tr>
<td class="heading">{tr}From{/tr}</td>
<td class="heading">{tr}Subject{/tr}</td>
<td class="heading">{tr}Date{/tr}</td>
</tr>
{section loop=$articles name=ix}
<tr>
<td class="{cycle advance=false}" {if $articles[ix].status eq 'new'} style="font-weight:bold" {/if}>{$articles[ix].From}</td>
<td class="{cycle advance=false}" {if $articles[ix].status eq 'new'} style="font-weight:bold" {/if}><a class="link" href="tiki-newsreader_read.php?server={$server}&amp;port={$port}&amp;news_username={$news_username}&amp;password={$password}&amp;group={$group}&amp;offset={$offset}&amp;id={$articles[ix].loopid}&amp;serverId={$serverId}">{$articles[ix].Subject}</a></td>
<td class="{cycle}" {if $articles[ix].status eq 'new'} style="font-weight:bold" {/if}>{$articles[ix].Date|tiki_short_datetime}</td>
</tr>
{/section}
</table>

<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-newsreader_news.php?server={$server}&amp;port={$port}&amp;news_username={$news_username}&amp;password={$password}&amp;group={$group}&amp;offset={$prev_offset}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-newsreader_news.php?server={$server}&amp;port={$port}&amp;news_username={$news_username}&amp;password={$password}&amp;group={$group}&amp;offset={$next_offset}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{if $cant_pages < 20}
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-newsreader_news.php?server={$server}&amp;port={$port}&amp;news_username={$news_username}&amp;password={$password}&amp;group={$group}&amp;offset=selector_offset">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
{/if}
</div>
</div>
