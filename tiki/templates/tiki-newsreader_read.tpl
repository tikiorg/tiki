{*Smarty template*}
<a class="pagetitle" href="tiki-newsreader_read.php?offset={$offset}&amp;id={$id}&amp;serverId={$serverId}&amp;server={$server}&amp;port={$port}&amp;username={$username}&amp;password={$password}&amp;group={$group}">{tr}Reading article from{/tr}:{$group}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
<br/><br/>
[<a class="link" href="tiki-newsreader_servers.php">{tr}Back to servers{/tr}</a>
{if $serverId}| <a class="link" href="tiki-newsreader_groups.php?serverId={$serverId}">{tr}Back to groups{/tr}</a>{/if}
| <a class="link" href="tiki-newsreader_news.php?serverId={$serverId}&amp;server={$server}&amp;port={$port}&amp;username={$username}&amp;password={$password}&amp;group={$group}&amp;offset={$offset}">{tr}Back to list of articles{/tr}</a>]
<br/><br/>
<table class="normal">
<tr><td class="formcolor" colspan="2">
[<a class="link" href="tiki-newsreader_read.php?offset={$offset}&amp;id={$first}&amp;serverId={$serverId}&amp;server={$server}&amp;port={$port}&amp;username={$username}&amp;password={$password}&amp;group={$group}">{tr}First{/tr}</a>
| <a class="link" href="tiki-newsreader_read.php?offset={$offset}&amp;id={$last}&amp;serverId={$serverId}&amp;server={$server}&amp;port={$port}&amp;username={$username}&amp;password={$password}&amp;group={$group}">{tr}Last{/tr}</a>]
[{if $prev_article > 0}<a class="link" href="tiki-newsreader_read.php?offset={$offset}&amp;id={$prev_article}&amp;serverId={$serverId}&amp;server={$server}&amp;port={$port}&amp;username={$username}&amp;password={$password}&amp;group={$group}">{tr}Prev{/tr}</a>{/if}
{if $next_article > 0}| <a class="link" href="tiki-newsreader_read.php?offset={$offset}&amp;id={$next_article}&amp;serverId={$serverId}&amp;server={$server}&amp;port={$port}&amp;username={$username}&amp;password={$password}&amp;group={$group}">{tr}Next{/tr}</a>{/if}]
</td></tr>
<tr><td class="formcolor">{tr}Newsgroup{/tr}:</td><td class="formcolor">{$headers.Newsgroups}</td></tr>
<tr><td class="formcolor">{tr}From{/tr}:</td><td class="formcolor">{$headers.From}</td></tr>
<tr><td class="formcolor">{tr}Date{/tr}:</td><td class="formcolor">{$headers.Date|tiki_short_datetime}</td></tr>
<tr><td class="formcolor">{tr}Subject{/tr}:</td><td class="formcolor">{$headers.Subject}</td></tr>
<tr><td colspan="2">{$body}</td></tr>
</table>
<br/><br/><br/>