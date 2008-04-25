<div id="loadstats" style="text-align: center">
</div>
<div id="power" style="text-align: center">
<small><a href="http://tikiwiki.org" title="Tikiwiki">{tr}Powered by{/tr} Tikiwiki</a> the ultimate CMS engine.
</div>
	<div id="rss" style="text-align: center">
		{if $prefs.feature_wiki eq 'y' and $prefs.rss_wiki eq 'y' and $tiki_p_view eq 'y'}
				<a title="{tr}Wiki RSS{/tr}" href="tiki-wiki_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' width='16' height='16' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' /></a>
				<small>{tr}Wiki{/tr}</small>
		{/if}
		{if $prefs.feature_blogs eq 'y' and $prefs.rss_blogs eq 'y' and $tiki_p_read_blog eq 'y'}
				<a title="{tr}Blogs RSS{/tr}" href="tiki-blogs_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' width='16' height='16' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' /></a>
				<small>{tr}Blogs{/tr}</small>
		{/if}
		{if $prefs.feature_articles eq 'y' and $prefs.rss_articles eq 'y' and $tiki_p_read_article eq 'y'}
				<a title="{tr}Articles RSS{/tr}" href="tiki-articles_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' width='16' height='16' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' /></a>
				<small>{tr}Articles{/tr}</small>
		{/if}
		{if $prefs.feature_galleries eq 'y' and $prefs.rss_image_galleries eq 'y' and $tiki_p_view_image_gallery eq 'y'}
				<a title="{tr}Image Galleries RSS{/tr}" href="tiki-image_galleries_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' width='16' height='16' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' /></a>
				<small>{tr}Image Galleries{/tr}</small>
		{/if}
		{if $prefs.feature_file_galleries eq 'y' and $prefs.rss_file_galleries eq 'y' and $tiki_p_view_file_gallery eq 'y'}
				<a title="{tr}File Galleries RSS{/tr}" href="tiki-file_galleries_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' width='16' height='16' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' /></a>
				<small>{tr}File Galleries{/tr}</small>
		{/if}
		{if $prefs.feature_forums eq 'y' and $prefs.rss_forums eq 'y' and $tiki_p_forum_read eq 'y'}
				<a title="{tr}Forums RSS{/tr}" href="tiki-forums_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' width='16' height='16' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' /></a>
				<small>{tr}Forums{/tr}</small>
		{/if}
		{if $prefs.feature_maps eq 'y' and $prefs.rss_mapfiles eq 'y' and $tiki_p_map_view eq 'y'}
				<a title="{tr}Maps RSS{/tr}" href="tiki-map_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' width='16' height='16' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' /></a>
				<small>{tr}Maps{/tr}</small>
		{/if}
		{if $prefs.feature_directory eq 'y' and $prefs.rss_directories eq 'y' and $tiki_p_view_directory eq 'y'}
				<a href="tiki-directories_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' width='16' height='16' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' /></a>
				<small>{tr}Directories{/tr}</small>
		{/if}
		{if $prefs.feature_calendar eq 'y' and $prefs.rss_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
				<a href="tiki-calendars_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' width='16' height='16' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' /></a>
				<small>{tr}Calendars{/tr}</small>
		{/if}
	</div>

{include file="babelfish.tpl"}

{if $prefs.feature_bot_bar_debug eq 'y'}
<div id="loadstats" style="text-align: center">
[ {tr}Execution time{/tr}: {elapsed} {tr}secs{/tr} ] &nbsp; [ {tr}Memory usage{/tr}: {memusage} ] &nbsp; [ {$num_queries} {tr}database queries used{/tr} ] &nbsp; [ GZIP {$gzip} ] &nbsp; [ {tr}Server load{/tr}: {$server_load} ]
</div>
{/if}