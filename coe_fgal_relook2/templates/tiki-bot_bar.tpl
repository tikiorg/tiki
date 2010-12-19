<div class="container">
{if $prefs.feature_bot_logo eq 'y'}<div id="custom_site_footer">{eval var=$prefs.bot_logo_code}</div>{/if}
{if ($prefs.feature_site_report eq 'y' && $tiki_p_site_report eq 'y') || ($prefs.feature_site_send_link eq 'y' and $prefs.feature_tell_a_friend eq 'y' and $tiki_p_tell_a_friend eq 'y')}
	<div id="site_report">
		{if $prefs.feature_site_report eq 'y'}
			<a href="tiki-tell_a_friend.php?report=y&amp;url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Report to Webmaster{/tr}</a>
		{/if}
		{if $prefs.feature_share eq 'y' }
			<a href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Share this page{/tr}</a>
		{/if}
		{if $prefs.feature_site_send_link eq 'y'}
			<a href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Email this page{/tr}</a>
		{/if}
	</div>
{/if}
{if $prefs.feature_bot_bar_icons eq 'y'}
	<div id="power_icons">
		<a href="http://tiki.org/" title="Tiki"><img alt="{tr}Powered by{/tr} Tiki" src="img/tiki/tikibutton2.png" /></a>
		<a href="http://php.net/" title="PHP"><img alt="{tr}Powered by{/tr} PHP" src="img/php.png" /></a>
		<a href="http://smarty.net/" title="Smarty"><img alt="{tr}Powered by{/tr} Smarty" src="img/smarty.gif"  /></a>
		<a href="http://www.w3.org/Style/CSS/" title="CSS"><img alt="{tr}Made with{/tr} CSS" src="img/css1.png" /></a>
		{if $prefs.feature_mobile eq 'y'}
		<a href="http://www.hawhaw.de/" title="HAWHAW"><img alt="{tr}powered by{/tr} HAWHAW" src="img/poweredbyhawhaw.gif"  /></a>		
		{/if}		
	</div>
{/if}
{if $prefs.feature_bot_bar_rss eq 'y'}
	<div id="rss" style="text-align: center">
		{if $prefs.feature_wiki eq 'y' and $prefs.feed_wiki eq 'y' and $tiki_p_view eq 'y'}
				<a title="{tr}Wiki RSS{/tr}" href="tiki-wiki_rss.php?ver={$prefs.feed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt="{tr}RSS feed{/tr}"}</a>
				<small>{tr}Wiki{/tr}</small>
		{/if}
		{if $prefs.feature_blogs eq 'y' and $prefs.feed_blogs eq 'y' and ($tiki_p_read_blog eq 'y' or $tiki_p_blog_view_ref eq 'y')}
				<a title="{tr}Blogs RSS{/tr}" href="tiki-blogs_rss.php?ver={$prefs.feed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt="{tr}RSS feed{/tr}"}</a>
				<small>{tr}Blogs{/tr}</small>
		{/if}
		{if $prefs.feature_articles eq 'y' and $prefs.feed_articles eq 'y' and ($tiki_p_read_article eq 'y' or $tiki_p_articles_read_heading eq 'y')}
				<a title="{tr}Articles RSS{/tr}" href="tiki-articles_rss.php?ver={$prefs.feed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt="{tr}RSS feed{/tr}"}</a>
				<small>{tr}Articles{/tr}</small>
		{/if}
		{if $prefs.feature_galleries eq 'y' and $prefs.feed_image_galleries eq 'y' and $tiki_p_view_image_gallery eq 'y'}
				<a title="{tr}Image Galleries RSS{/tr}" href="tiki-image_galleries_rss.php?ver={$prefs.feed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt="{tr}RSS feed{/tr}"}</a>
				<small>{tr}Image Galleries{/tr}</small>
		{/if}
		{if $prefs.feature_file_galleries eq 'y' and $prefs.feed_file_galleries eq 'y' and $tiki_p_view_file_gallery eq 'y'}
				<a title="{tr}File Galleries RSS{/tr}" href="tiki-file_galleries_rss.php?ver={$prefs.feed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt="{tr}RSS feed{/tr}"}</a>
				<small>{tr}File Galleries{/tr}</small>
		{/if}
		{if $prefs.feature_forums eq 'y' and $prefs.feed_forums eq 'y' and $tiki_p_forum_read eq 'y'}
				<a title="{tr}Forums RSS{/tr}" href="tiki-forums_rss.php?ver={$prefs.feed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt="{tr}RSS feed{/tr}"}</a>
				<small>{tr}Forums{/tr}</small>
		{/if}
		{if $prefs.feature_maps eq 'y' and $prefs.rss_mapfiles eq 'y' and $tiki_p_map_view eq 'y'}
				<a title="{tr}Maps RSS{/tr}" href="tiki-map_rss.php?ver={$prefs.feed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt="{tr}RSS feed{/tr}"}</a>
				<small>{tr}Maps{/tr}</small>
		{/if}
		{if $prefs.feature_directory eq 'y' and $prefs.feed_directories eq 'y' and $tiki_p_view_directory eq 'y'}
				<a href="tiki-directories_rss.php?ver={$prefs.feed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt="{tr}RSS feed{/tr}"}</a>
				<small>{tr}Directories{/tr}</small>
		{/if}
		{if $prefs.feature_calendar eq 'y' and $prefs.feed_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
				<a href="tiki-calendars_rss.php?ver={$prefs.feed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt="{tr}RSS feed{/tr}"}</a>
				<small>{tr}Calendars{/tr}</small>
		{/if}
		{if $prefs.feature_shoutbox eq 'y' and $prefs.feed_shoutbox eq 'y' and $tiki_p_view_shoutbox eq 'y'}
				<a href="tiki-shoutbox_rss.php?ver={$prefs.feed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt="{tr}RSS feed{/tr}"}</a>
				<small>{tr}Shoutbox{/tr}</small>
		{/if}
	</div>
{/if}
{if $prefs.feature_babelfish eq 'y' or $prefs.feature_babelfish_logo eq 'y'}
	{include file='babelfish.tpl'}
{/if}
<div id="power">
	{if $prefs.feature_bot_bar_power_by_tw ne 'n'}
		{tr}Powered by{/tr} <a href="http://tiki.org" title="&#169; 2002&#8211;{$smarty.now|date_format:"%Y"} {tr}The Tiki Community{/tr}">{tr}Tiki Wiki CMS Groupware{/tr}</a> {if $prefs.feature_topbar_version eq 'y'} v{$tiki_version} {if $tiki_uses_svn eq 'y'} (SVN){/if} -{$tiki_star}- {/if} | 
	{/if}
	<div id="credits">
		{include file='credits.tpl'}
	</div>
</div>
</div>
{if $prefs.feature_bot_bar_debug eq 'y'}
<div id="loadstats" style="text-align: center">
	<small>[ {tr}Execution time{/tr}: {elapsed} {tr}secs{/tr} ] &nbsp; [ {tr}Memory usage{/tr}: {memusage} ] &nbsp; [ {$num_queries} {tr}database queries used in {/tr} {$elapsed_in_db|truncate:3:''} {tr}secs{/tr} ]{if $server_load and $server_load ne '?'} &nbsp; [ {tr}Server load{/tr}: {$server_load} ]{/if}</small>
</div>
{/if}

{if !empty($lastup)}
<div class="cvsup" style="font-size:x-small;text-align:center;color:#999;">{tr}Last update from SVN{/tr} ({$tiki_version}): {$lastup|tiki_long_datetime}
{/if}
{if !empty($svnrev)}
 - REV {$svnrev}
{/if}
