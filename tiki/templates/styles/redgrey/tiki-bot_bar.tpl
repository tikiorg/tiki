<table width="100%">
<tr><td width="50%">
	<div id="power" style="text-align: left">
	</div></td><td width="50%">
	<div id="rss" style="text-align: right">
		{if $rss_wiki eq 'y'}
				<small>{tr}Wiki{/tr}</small> <a href="tiki-wiki_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0; vertical-align: middle;" src="img/rss.png" /></a>
				<br />
		{/if}
		{if $rss_blogs eq 'y'}
				<small>{tr}Blogs{/tr}</small> <a href="tiki-blogs_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0; vertical-align: middle;" src="img/rss.png" /></a>
				<br />
		{/if}
		{if $rss_articles eq 'y'}
				<small>{tr}Articles{/tr}</small> <a href="tiki-articles_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0; vertical-align: middle;" src="img/rss.png" /></a>
				<br />
		{/if}
		{if $rss_image_galleries eq 'y'}
				<small>{tr}Image galleries{/tr}</small> <a href="tiki-image_galleries_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0; vertical-align: middle;" src="img/rss.png" /></a>
				<br />
		{/if}
		{if $rss_file_galleries eq 'y'}
				<small>{tr}File galleries{/tr}</small> <a href="tiki-file_galleries_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0; vertical-align: middle;" src="img/rss.png" /></a>
				<br />
		{/if}
		{if $rss_forums eq 'y'}
				<small>{tr}Forums{/tr}</small> <a href="tiki-forums_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0; vertical-align: middle;" src="img/rss.png" /></a>
				<br />
		{/if}
		{if $rss_mapfiles eq 'y'}
				<small>{tr}Maps{/tr}</small> <a href="tiki-map_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0; vertical-align: middle;" src="img/rss.png" /></a>
				<br />
		{/if}
	</div></td></tr></table>
{include file="babelfish.tpl"}

<div id="loadstats" style="text-align: center">
{tr}Execution time{/tr}: {elapsed} {tr}secs{/tr} | {tr}Memory usage{/tr}: {memusage} | {$num_queries} {tr}database queries used{/tr} | GZIP {$gzip} | {tr}Server load{/tr}: {$server_load}<br />
This style works best in Mozilla based browsers - <a href="http://www.mozilla.org/" class="linkmenu">mozilla.org</a>
</div>
