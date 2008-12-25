{title help="newsreader"}{tr}Reading article from{/tr}:{$group}{/title}

{include file=tiki-mytiki_bar.tpl}

{button href="tiki-newsreader_servers.php" _text="{tr}Back to servers{/tr}"}
{if $serverId}
	{button href="tiki-newsreader_groups.php?serverId=$serverId" _text="{tr}Back to groups{/tr}"}
{/if}
{button href="tiki-newsreader_news.php?serverId=$serverId&amp;server=$server&amp;port=$port&amp;news_username=$news_username&amp;password=$password&amp;group=$group&amp;offset=$offset" _text="{tr}Back to list of articles{/tr}"}

<table class="normal">
<tr><td class="formcolor" colspan="2">
<table ><tr><td>
<a class="link" href="tiki-newsreader_read.php?offset={$offset}&amp;id={$first}&amp;serverId={$serverId}&amp;server={$server}&amp;port={$port}&amp;news_username={$news_username}&amp;password={$password}&amp;group={$group}"><img src='img/icons2/nav_first.gif' alt='{tr}First{/tr}' title='{tr}First{/tr}' /></a>
{if $prev_article > 0}<a class="link" href="tiki-newsreader_read.php?offset={$offset}&amp;id={$prev_article}&amp;serverId={$serverId}&amp;server={$server}&amp;port={$port}&amp;news_username={$news_username}&amp;password={$password}&amp;group={$group}"><img src='img/icons2/nav_dot_right.gif' alt='{tr}Prev{/tr}' title='{tr}Prev{/tr}' /></a>{/if}
{if $prev_article > 0 and $next_article > 0}&nbsp;-&nbsp;{/if}
{if $next_article > 0}<a class="link" href="tiki-newsreader_read.php?offset={$offset}&amp;id={$next_article}&amp;serverId={$serverId}&amp;server={$server}&amp;port={$port}&amp;news_username={$news_username}&amp;password={$password}&amp;group={$group}"><img src='img/icons2/nav_dot_left.gif' alt='{tr}Next{/tr}' title={tr}Next{/tr}' /> </a>{/if}
<a class="link" href="tiki-newsreader_read.php?offset={$offset}&amp;id={$last}&amp;serverId={$serverId}&amp;server={$server}&amp;port={$port}&amp;news_username={$news_username}&amp;password={$password}&amp;group={$group}"><img src='img/icons2/nav_last.gif' alt='{tr}Last{/tr}' title='{tr}Last{/tr}' /></a>
</td>
<td style="text-align:right;">
	{if $user and $prefs.feature_notepad eq 'y'}
	<a title="{tr}Save to notepad{/tr}" href="tiki-newsreader_read.php?offset={$offset}&amp;id={$id}&amp;serverId={$serverId}&amp;server={$server}&amp;port={$port}&amp;news_username={$news_username}&amp;password={$password}&amp;group={$group}&amp;savenotepad=1">{html_image file='img/icons/ico_save.gif' alt='{tr}Save{/tr}'}</a>
	{/if}
</td></tr></table>
</td></tr>
<tr><td class="formcolor">{tr}Newsgroup{/tr}:</td><td class="formcolor">{$headers.Newsgroups}</td></tr>
<tr><td class="formcolor">{tr}From{/tr}:</td><td class="formcolor">{$headers.From}</td></tr>
<tr><td class="formcolor">{tr}Date{/tr}:</td><td class="formcolor">{$headers.Date|tiki_short_datetime}</td></tr>
<tr><td class="formcolor">{tr}Subject{/tr}:</td><td class="formcolor">{$headers.Subject}</td></tr>
<tr><td colspan="2" style="background-color:white;">{$body}</td></tr>
</table>
<br /><br /><br />
