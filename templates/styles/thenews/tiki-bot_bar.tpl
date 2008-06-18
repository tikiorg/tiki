{if $prefs.feature_bot_logo eq 'y'}{eval var=$prefs.bot_logo_code}{/if}
{if ($prefs.feature_site_report eq 'y' && $tiki_p_site_report eq 'y')}
	<div id="site_report">
		<a href="tiki-tell_a_friend.php?report=y&amp;url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Report to  Webmaster{/tr}</a>
	</div>
{/if}
{if $prefs.feature_bot_bar_icons eq 'y'}
	<div id="power" style="text-align: center">
		<a href="http://tikiwiki.org/" title="Tikiwiki"><img style="border: 0; vertical-align: middle" alt="{tr}Powered by{/tr} Tikiwiki" src="img/tiki/tikibutton2.png" /></a>
		<a href="http://www.php.net/" title="PHP"><img style="border: 0; vertical-align: middle" alt="{tr}Powered by{/tr} PHP" src="img/php.png" /></a>
		<a href="http://smarty.php.net/" title="Smarty"><img style="border: 0; vertical-align: middle" alt="{tr}Powered by{/tr} Smarty" src="img/smarty.gif"  /></a>
		<a href="http://adodb.sourceforge.net/" title="ADOdb"><img style="border: 0; vertical-align: middle" alt="{tr}Powered by{/tr} ADOdb" src="img/adodb.png" /></a>
		<a href="http://www.w3.org/Style/CSS/" title="CSS"><img style="border: 0; vertical-align: middle" alt="{tr}Made with{/tr} CSS" src="img/css1.png" /></a>
		<a href="http://www.w3.org/RDF/" title="RDF"><img style="border: 0; vertical-align: middle" alt="{tr}Powered by{/tr} RDF" src="img/rdf.gif"  /></a>
		{if $prefs.feature_phplayers eq 'y'}
		<a href="http://phplayersmenu.sourceforge.net/" title="PHP Layers Menu"><img style="border: 0; vertical-align: middle" alt="{tr}powered by{/tr} The PHP Layers Menu System" src="lib/phplayers/LOGOS/powered_by_phplm.png"  /></a>		
		{/if}
		{if $prefs.feature_mobile eq 'y'}
		<a href="http://www.hawhaw.de/" title="HAWHAW"><img style="border: 0; vertical-align: middle" alt="{tr}powered by{/tr} HAWHAW" src="img/poweredbyhawhaw.gif"  /></a>		
		{/if}		
	</div>
{/if}
{if $prefs.feature_bot_bar_rss eq 'y'}
	<div id="rss" style="text-align: center">
		{if $prefs.feature_wiki eq 'y' and $prefs.rss_wiki eq 'y' and $tiki_p_view eq 'y'}
				<a title="{tr}Wiki RSS{/tr}" href="tiki-wiki_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='border: 0; vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Wiki{/tr}</small>
		{/if}
		{if $prefs.feature_blogs eq 'y' and $prefs.rss_blogs eq 'y' and $tiki_p_read_blog eq 'y'}
				<a title="{tr}Blogs RSS{/tr}" href="tiki-blogs_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='border: 0; vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Blogs{/tr}</small>
		{/if}
		{if $prefs.feature_articles eq 'y' and $prefs.rss_articles eq 'y' and $tiki_p_read_article eq 'y'}
				<a title="{tr}Articles RSS{/tr}" href="tiki-articles_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='border: 0; vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Articles{/tr}</small>
		{/if}
		{if $prefs.feature_galleries eq 'y' and $prefs.rss_image_galleries eq 'y' and $tiki_p_view_image_gallery eq 'y'}
				<a title="{tr}Image Galleries RSS{/tr}" href="tiki-image_galleries_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='border: 0; vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Image Galleries{/tr}</small>
		{/if}
		{if $prefs.feature_file_galleries eq 'y' and $prefs.rss_file_galleries eq 'y' and $tiki_p_view_file_gallery eq 'y'}
				<a title="{tr}File Galleries RSS{/tr}" href="tiki-file_galleries_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='border: 0; vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}File Galleries{/tr}</small>
		{/if}
		{if $prefs.feature_forums eq 'y' and $prefs.rss_forums eq 'y' and $tiki_p_forum_read eq 'y'}
				<a title="{tr}Forums RSS{/tr}" href="tiki-forums_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='border: 0; vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Forums{/tr}</small>
		{/if}
		{if $prefs.feature_maps eq 'y' and $prefs.rss_mapfiles eq 'y' and $tiki_p_map_view eq 'y'}
				<a title="{tr}Maps RSS{/tr}" href="tiki-map_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='border: 0; vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Maps{/tr}</small>
		{/if}
		{if $prefs.feature_directory eq 'y' and $prefs.rss_directories eq 'y' and $tiki_p_view_directory eq 'y'}
				<a href="tiki-directories_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='border: 0; vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Directories{/tr}</small>
		{/if}
		{if $prefs.feature_calendar eq 'y' and $prefs.rss_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
				<a href="tiki-calendars_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='border: 0; vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Calendars{/tr}</small>
		{/if}
	</div>
{/if}
<div id="credits">
{include file="credits.tpl"}
{if $prefs.feature_topbar_date eq 'y'}
	{if $prefs.feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
	<a href="tiki-calendar.php">{$smarty.now|tiki_short_datetime}</a>
	{else}
	{$smarty.now|tiki_short_datetime}
	{/if}
{/if}
</div>
{if $prefs.feature_babelfish eq 'y' or $prefs.feature_babelfish_logo eq 'y'}
	{include file="babelfish.tpl"}
{/if}
{if $prefs.feature_bot_bar_debug eq 'y'}
<div id="loadstats" style="text-align: center">
	<small>[ {tr}Execution time{/tr}: {elapsed} {tr}secs{/tr} ] &nbsp; [ {tr}Memory usage{/tr}: {memusage} ] &nbsp; [ {$num_queries} {tr}database queries used in {/tr} {$elapsed_in_db|truncate:3:''} secs ] &nbsp; [ GZIP {$gzip} ] &nbsp; [ {tr}Server load{/tr}: {$server_load} ]</small>
</div>
{/if}
