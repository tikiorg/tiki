
<div id="power" style="text-align: center">
<a title="{tr}CSS standards{/tr}" href="http://www.w3.org/Style/CSS/"><img style="border: 0" alt="{tr}Made with CSS{/tr}" src="img/css1.png" /></a>
<a title="{tr}Validate this page{/tr}" href="http://validator.w3.org/check/referer"><img style="border: 0" alt="{tr}Valid XHTML 1.0{/tr}" src="img/valid-xhtml10.png" /></a>
<a title="{tr}Pear website{/tr}" href="http://pear.php.net/"><img style="border: 0" alt="{tr}Powered by Pear{/tr}" src="img/pear.png" /></a>
<a title="{tr}PHP website{/tr}" href="http://www.php.net"><img style="border: 0" alt="{tr}Powered by PHP{/tr}" src="img/php.png" /></a>
<a title="{tr}Smarty website{/tr}" href="http://smarty.php.net/"><img style="border: 0" alt="{tr}Powered by Smarty{/tr}" src="img/smarty.gif"  /></a>
<a title="{tr}RDF standards{/tr}" href="http://www.w3.org/RDF/"><img style="border: 0" alt="{tr}powered by RDF{/tr}" src="img/rdf.gif" /></a>
</div>
	<div id="rss" style="text-align: center">
		{if $rss_wiki eq 'y'}
				<a title="{tr}Wiki RSS{/tr}" href="tiki-wiki_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="RSS" style="border: 0" src="img/rss.png" /></a>
				<small>{tr}Wiki{/tr}</small>
		{/if}
		{if $rss_blogs eq 'y'}
				<a title="{tr}Weblogs RSS{/tr}" href="tiki-blogs_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="RSS" style="border: 0" src="img/rss.png" /></a>
				<small>{tr}Weblogs{/tr}</small>
		{/if}
		{if $rss_articles eq 'y'}
				<a title="{tr}Articles RSS{/tr}" href="tiki-articles_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0" src="img/rss.png" /></a>
				<small>{tr}Articles{/tr}</small>
		{/if}
		{if $rss_image_galleries eq 'y'}
				<a title="{tr}Image Galleries RSS{/tr}" href="tiki-image_galleries_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="RSS" style="border: 0" src="img/rss.png" /></a>
				<small>{tr}Image galleries{/tr}</small>
		{/if}
		{if $rss_file_galleries eq 'y'}
				<a title="{tr}File Galleries RSS{/tr}" href="tiki-file_galleries_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="RSS" style="border: 0" src="img/rss.png" /></a>
				<small>{tr}File galleries{/tr}</small>
		{/if}
		{if $rss_forums eq 'y'}
				<a title="{tr}Forums RSS{/tr}" href="tiki-forums_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="RSS" style="border: 0" src="img/rss.png" /></a>
				<small>{tr}Forums{/tr}</small>
		{/if}
		{if $rss_mapfiles eq 'y'}
				<a title="{tr}Maps RSS{/tr}" href="tiki-map_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="RSS" style="border: 0" src="img/rss.png" /></a>
				<small>{tr}Maps{/tr}</small>
		{/if}
	</div>

{include file="babelfish.tpl"}

<div id="loadstats" style="text-align: center">
	<small>[ {tr}Execution time{/tr}: {elapsed} {tr}secs{/tr} ] &nbsp; [ {tr}Memory usage{/tr}: {memusage} ] &nbsp; [ {$num_queries} {tr}database queries used{/tr} ] &nbsp; [ GZIP {$gzip} ] &nbsp; [ {tr}Server load{/tr}: {$server_load} ]</small>
</div>
