<div id="loadstats" style="text-align: center">
</div>
<div id="power" style="text-align: center">
<small><a href="http://tikiwiki.org" title="TikiWiki">{tr}Powered by{/tr} TikiWiki</a> the ultimate CMS engine.
</div>
	<div id="rss" style="text-align: center">
		{if $rss_wiki eq 'y'}
				<a href="tiki-wiki_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0" src="img/rss.png" /></a>
				<small>{tr}Wiki{/tr}</small>
		{/if}
		{if $rss_blogs eq 'y'}
				<a href="tiki-blogs_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0" src="img/rss.png" /></a>
				<small>{tr}Blogs{/tr}</small>
		{/if}
		{if $rss_articles eq 'y'}
				<a href="tiki-articles_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0" src="img/rss.png" /></a>
				<small>{tr}Articles{/tr}</small>
		{/if}
		{if $rss_image_galleries eq 'y'}
				<a href="tiki-image_galleries_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0" src="img/rss.png" /></a>
				<small>{tr}Image galleries{/tr}</small>
		{/if}
		{if $rss_file_galleries eq 'y'}
				<a href="tiki-file_galleries_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0" src="img/rss.png" /></a>
				<small>{tr}File galleries{/tr}</small>
		{/if}
		{if $rss_forums eq 'y'}
				<a href="tiki-forums_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0" src="img/rss.png" /></a>
				<small>{tr}Forums{/tr}</small>
		{/if}
		{if $rss_mapfiles eq 'y'}
				<a href="tiki-map_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0" src="img/rss.png" /></a>
				<small>{tr}Maps{/tr}</small>
		{/if}
	</div>

{include file="babelfish.tpl"}

<div id="loadstats" style="text-align: center">
[ {tr}Execution time{/tr}: {elapsed} {tr}secs{/tr} ] &nbsp; [ {tr}Memory usage{/tr}: {memusage} ] &nbsp; [ {$num_queries} {tr}database queries used{/tr} ] &nbsp; [ GZIP {$gzip} ] &nbsp; [ {tr}Server load{/tr}: {$server_load} ]
</div>
