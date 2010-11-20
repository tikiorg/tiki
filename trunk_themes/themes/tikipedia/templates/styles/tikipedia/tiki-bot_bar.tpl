{* $Id$ *}
{if $prefs.feature_bot_logo eq 'y'}{eval var=$prefs.bot_logo_code}{/if}
<a href="http://tikiwiki.org/" title="TikiWiki"><img style="border: 0; vertical-align: middle; float: right; margin-right: 10px;" alt="{tr}Powered by{/tr} TikiWiki" src="img/tiki/tikibutton2.png" /></a>
{* Moved here from tiki-show_page.tpl start *}
{if $page}
<div class="editdate" id="wiki-footer">
{* begin editdate *}
{if $wiki_authors_style neq 'none' || $prefs.wiki_feature_copyrights eq 'y'|| $print_page eq 'y'}
  <p class="editdate"> {* begining editdate *}
{/if}
{if isset($wiki_authors_style) && $wiki_authors_style eq 'business'}
  {tr}Last edited by{/tr} {$lastUser|userlink}
  {section name=author loop=$contributors}
   {if $smarty.section.author.first}, {tr}based on work by{/tr}
   {else}
    {if !$smarty.section.author.last},
    {else} {tr}and{/tr}
    {/if}
   {/if}
   {$contributors[author]|userlink}
  {/section}.<br />
  {tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}. {if $prefs.wiki_show_version eq 'y'}({tr}Version{/tr} {$lastVersion}){/if}
{elseif isset($wiki_authors_style) && $wiki_authors_style eq 'collaborative'}
<br />
  {tr}Contributors to this page{/tr}: {$lastUser|userlink}
  {section name=author loop=$contributors}
   {if !$smarty.section.author.last},
   {else} {tr}and{/tr}
   {/if}
   {$contributors[author]|userlink}
  {/section}.<br />
  {tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser|userlink}. {if $prefs.wiki_show_version eq 'y'}({tr}Version{/tr} {$lastVersion}){/if}
{elseif isset($wiki_authors_style) && $wiki_authors_style eq 'none'}
{elseif isset($wiki_authors_style) && $wiki_authors_style eq 'lastmodif'}
	{tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}
{else}
<br />
  {tr}Created by{/tr}: {$creator|userlink}.
  {tr}Last Modification{/tr}: {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser|userlink}. {if $prefs.wiki_show_version eq 'y'}({tr}Version{/tr} {$lastVersion}){/if}
{/if}
{if $prefs.wiki_feature_copyrights  eq 'y' and $prefs.wikiLicensePage}
  {if $prefs.wikiLicensePage == $page}
    {if $tiki_p_edit_copyrights eq 'y'}
      <br />
      {tr}To edit the copyright notices{/tr} <a href="copyrights.php?page={$copyrightpage}">{tr}Click Here{/tr}</a>.
    {/if}
  {else}
    <br />
    {tr}The content on this page is licensed under the terms of the{/tr} <a href="tiki-index.php?page={$prefs.wikiLicensePage}&amp;copyrightpage={$page|escape:"url"}">{$prefs.wikiLicensePage}</a>.
  {/if}
{/if}

{if $print_page eq 'y'}
    <br />
    {tr}The original document is available at{/tr} <a href="{$base_url}tiki-index.php?page={$page|escape:"url"}">{$base_url}tiki-index.php?page={$page|escape:"url"}</a>
{/if}

{* end editdate *}
</div>
{/if}
{* Moved here from tiki-show_page.tpl end *}
{if ($prefs.feature_site_report eq 'y' && $tiki_p_site_report eq 'y')}
	<div id="site_report">
		{if $prefs.feature_site_report eq 'y'}
			<a href="tiki-tell_a_friend.php?report=y&amp;url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Report to Webmaster{/tr}</a>
		{/if}
		{if $prefs.feature_site_send_link eq 'y'}
			<a href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Email this page{/tr}</a>
		{/if}
	</div>
{/if}
{if $prefs.feature_bot_bar_icons eq 'y'}
	<div id="power_icons">
		<a href="http://tikiwiki.org/" title="Tikiwiki"><img alt="{tr}Powered by{/tr} Tikiwiki" src="img/tiki/tikibutton2.png" /></a>
		<a href="http://www.php.net/" title="PHP"><img alt="{tr}Powered by{/tr} PHP" src="img/php.png" /></a>
		<a href="http://www.smarty.net/" title="Smarty"><img alt="{tr}Powered by{/tr} Smarty" src="img/smarty.gif"  /></a>
		<a href="http://adodb.sourceforge.net/" title="ADOdb"><img alt="{tr}Powered by{/tr} ADOdb" src="img/adodb.png" /></a>
		<a href="http://www.w3.org/Style/CSS/" title="CSS"><img alt="{tr}Made with{/tr} CSS" src="img/css1.png" /></a>
		<a href="http://www.w3.org/RDF/" title="RDF"><img alt="{tr}Powered by{/tr} RDF" src="img/rdf.gif"  /></a>
		{if $prefs.feature_phplayers eq 'y'}
		<a href="http://phplayersmenu.sourceforge.net/" title="PHP Layers Menu"><img alt="{tr}powered by{/tr} The PHP Layers Menu System" src="lib/phplayers/LOGOS/powered_by_phplm.png"  /></a>		
		{/if}
		{if $prefs.feature_mobile eq 'y'}
		<a href="http://www.hawhaw.de/" title="HAWHAW"><img alt="{tr}powered by{/tr} HAWHAW" src="img/poweredbyhawhaw.gif"  /></a>		
		{/if}		
	</div>
{/if}
{if $prefs.feature_bot_bar_rss eq 'y'}
	<div id="rss" style="text-align: center">
		{if $prefs.feature_wiki eq 'y' and $prefs.rss_wiki eq 'y' and $tiki_p_view eq 'y'}
				<a title="{tr}Wiki RSS{/tr}" href="tiki-wiki_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Wiki{/tr}</small>
		{/if}
		{if $prefs.feature_blogs eq 'y' and $prefs.rss_blogs eq 'y' and $tiki_p_read_blog eq 'y'}
				<a title="{tr}Blogs RSS{/tr}" href="tiki-blogs_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Blogs{/tr}</small>
		{/if}
		{if $prefs.feature_articles eq 'y' and $prefs.rss_articles eq 'y' and $tiki_p_read_article eq 'y'}
				<a title="{tr}Articles RSS{/tr}" href="tiki-articles_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Articles{/tr}</small>
		{/if}
		{if $prefs.feature_galleries eq 'y' and $prefs.rss_image_galleries eq 'y' and $tiki_p_view_image_gallery eq 'y'}
				<a title="{tr}Image Galleries RSS{/tr}" href="tiki-image_galleries_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Image Galleries{/tr}</small>
		{/if}
		{if $prefs.feature_file_galleries eq 'y' and $prefs.rss_file_galleries eq 'y' and $tiki_p_view_file_gallery eq 'y'}
				<a title="{tr}File Galleries RSS{/tr}" href="tiki-file_galleries_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}File Galleries{/tr}</small>
		{/if}
		{if $prefs.feature_forums eq 'y' and $prefs.rss_forums eq 'y' and $tiki_p_forum_read eq 'y'}
				<a title="{tr}Forums RSS{/tr}" href="tiki-forums_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Forums{/tr}</small>
		{/if}
		{if $prefs.feature_maps eq 'y' and $prefs.rss_mapfiles eq 'y' and $tiki_p_map_view eq 'y'}
				<a title="{tr}Maps RSS{/tr}" href="tiki-map_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Maps{/tr}</small>
		{/if}
		{if $prefs.feature_directory eq 'y' and $prefs.rss_directories eq 'y' and $tiki_p_view_directory eq 'y'}
				<a href="tiki-directories_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Directories{/tr}</small>
		{/if}
		{if $prefs.feature_calendar eq 'y' and $prefs.rss_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
				<a href="tiki-calendars_rss.php?ver={$prefs.rssfeed_default_version}">{icon style='vertical-align: text-bottom;' _id='feed' alt='{tr}RSS feed{/tr}'}</a>
				<small>{tr}Calendars{/tr}</small>
		{/if}
	</div>
{/if}
{if $prefs.feature_babelfish eq 'y' or $prefs.feature_babelfish_logo eq 'y'}
	{include file="babelfish.tpl"}
{/if}
<div id="power">
	{if $prefs.feature_bot_bar_power_by_tw ne 'n'}
		{tr}Powered by{/tr} <a href="http://info.tikiwiki.org" title="&#169; 2002&#8211;{$smarty.now|date_format:"%Y"} {tr}The TikiWiki Community{/tr}">{tr}TikiWiki CMS/Groupware{/tr}</a> {if $prefs.feature_topbar_version eq 'y'} v{$tiki_version} {if $tiki_uses_svn eq 'y'} (SVN){/if} -{$tiki_star}- {/if} | 
	{/if}
	<div id="credits">
		{include file="credits.tpl"}
	</div>
</div>

{if $prefs.feature_bot_bar_debug eq 'y'}
<div id="loadstats" style="text-align: center">
	<small>[ {tr}Execution time{/tr}: {elapsed} {tr}secs{/tr} ] &nbsp; [ {tr}Memory usage{/tr}: {memusage} ] &nbsp; [ {$num_queries} {tr}database queries used in {/tr} {$elapsed_in_db|truncate:3:''} secs ]{if $server_load and $server_load ne '?'} &nbsp; [ {tr}Server load{/tr}: {$server_load} ]{/if}</small>
</div>
{/if}

{if $lastup}
<div class="cvsup" style="font-size:x-small;text-align:center;color:#999;">{tr}Last update from SVN{/tr}({$tiki_version}): {$lastup|tiki_long_datetime}</div>
{/if}
