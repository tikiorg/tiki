
	<div id="power" style="text-align: center">
		<a href="http://tikiwiki.org/TikiWiki" title="TikiWiki"><img style="border: 0; vertical-align: middle" alt="{tr}Powered by{/tr} TikiWiki" src="img/tiki/tikibutton2.png" /></a>
		<a href="http://www.w3.org/Style/CSS/"><img style="border: 0; vertical-align: middle" alt="{tr}Made with{/tr} CSS" src="img/css1.png" /></a>
		<a href="http://php.weblogs.com/adodb"><img style="border: 0; vertical-align: middle" alt="{tr}powered by{/tr} adodb" src="img/adodb.png" /></a>
		<a href="http://www.php.net"><img style="border: 0; vertical-align: middle" alt="{tr}powered by{/tr} PHP" src="img/php.png" /></a>
		<a href="http://smarty.php.net/"><img style="border: 0; vertical-align: middle" alt="{tr}powered by{/tr} smarty" src="img/smarty.gif"  /></a>
		<a href="http://www.w3.org/RDF/"><img style="border: 0; vertical-align: middle" alt="{tr}powered by{/tr} RDF" src="img/rdf.gif"  /></a>
	</div>

	<div id="rss" style="text-align: center">
		{if $feature_wiki eq 'y' and $rss_wiki eq 'y'}
				<a title="{tr}Wiki RSS{/tr}" href="tiki-wiki_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="RSS" style="border: 0; vertical-align: text-bottom;" src="img/rss.png" /></a>
				<small>{tr}Wiki{/tr}</small>
		{/if}
		{if $feature_blogs eq 'y' and $rss_blogs eq 'y'}
				<a title="{tr}Blogs RSS{/tr}" href="tiki-blogs_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="RSS" style="border: 0; vertical-align: text-bottom;" src="img/rss.png" /></a>
				<small>{tr}Blogs{/tr}</small>
		{/if}
		{if $feature_articles eq 'y' and $rss_articles eq 'y'}
				<a title="{tr}Articles RSS{/tr}" href="tiki-articles_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0; vertical-align: text-bottom;" src="img/rss.png" /></a>
				<small>{tr}Articles{/tr}</small>
		{/if}
		{if $feature_galleries eq 'y' and $rss_image_galleries eq 'y'}
				<a title="{tr}Image Galleries RSS{/tr}" href="tiki-image_galleries_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="RSS" style="border: 0; vertical-align: text-bottom;" src="img/rss.png" /></a>
				<small>{tr}Image Galleries{/tr}</small>
		{/if}
		{if $feature_file_galleries eq 'y' and $rss_file_galleries eq 'y'}
				<a title="{tr}File Galleries RSS{/tr}" href="tiki-file_galleries_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="RSS" style="border: 0; vertical-align: text-bottom;" src="img/rss.png" /></a>
				<small>{tr}File Galleries{/tr}</small>
		{/if}
		{if $feature_forums eq 'y' and $rss_forums eq 'y'}
				<a title="{tr}Forums RSS{/tr}" href="tiki-forums_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="RSS" style="border: 0; vertical-align: text-bottom;" src="img/rss.png" /></a>
				<small>{tr}Forums{/tr}</small>
		{/if}
		{if $feature_maps eq 'y' and $rss_mapfiles eq 'y'}
				<a title="{tr}Maps RSS{/tr}" href="tiki-map_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="RSS" style="border: 0; vertical-align: text-bottom;" src="img/rss.png" /></a>
				<small>{tr}Maps{/tr}</small>
		{/if}
		{if $feature_directory eq 'y' and $rss_directories eq 'y'}
				<a href="tiki-directories_rss.php?ver={$rssfeed_default_version}{$rssfeed_cssparam}"><img alt="rss" style="border: 0; vertical-align: text-bottom;" src="img/rss.png" /></a>
				<small>{tr}Directories{/tr}</small>
		{/if}
	</div>

{include file="babelfish.tpl"}

<div id="loadstats" style="text-align: center">
	<small>[ {tr}Execution time{/tr}: {elapsed} {tr}secs{/tr} ] &nbsp; [ {tr}Memory usage{/tr}: {memusage} ] &nbsp; [ {$num_queries} {tr}database queries used{/tr} ] &nbsp; [ GZIP {tr}{$gzip}{/tr} ] &nbsp; [ {tr}Server load{/tr}: {$server_load} ]</small>
</div>
