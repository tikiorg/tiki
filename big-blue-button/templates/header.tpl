{* $Id$ *}
{if $base_url and $dir_level gt 0}
	<base href="{$base_url|escape}" />
{/if}
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="TikiWiki CMS/Groupware - http://TikiWiki.org" />
{if !empty($forum_info.name) & $prefs.metatag_threadtitle eq 'y'}
	<meta name="keywords" content="{tr}Forum{/tr} {$forum_info.name|escape} {$thread_info.title|escape} {if $prefs.feature_freetags eq 'y'}{foreach from=$freetags.data item=taginfo}{$taginfo.tag|escape} {/foreach}{/if}" />
{elseif isset($galleryId) && $galleryId ne '' & $prefs.metatag_imagetitle ne 'n'}
	<meta name="keywords" content="{tr}Images Galleries{/tr} {$title|escape} {if $prefs.feature_freetags eq 'y'}{foreach from=$freetags.data item=taginfo}{$taginfo.tag|escape} {/foreach}{/if}" />
{elseif $prefs.metatag_keywords ne '' or !empty($metatag_local_keywords)}
	<meta name="keywords" content="{$prefs.metatag_keywords|escape} {if $prefs.feature_freetags eq 'y'}{foreach from=$freetags.data item=taginfo}{$taginfo.tag|escape} {/foreach}{/if} {$metatag_local_keywords|escape}" />
{/if}
{if $prefs.metatag_author ne ''}
	<meta name="author" content="{$prefs.metatag_author|escape}" />
{/if}
{if $prefs.metatag_pagedesc eq 'y' and $description ne ''}
	<meta name="description" content="{$description|escape}" />
{elseif $prefs.metatag_description ne '' or (isset($description) and $description eq '')}
	<meta name="description" content="{$prefs.metatag_description|escape}" />
{/if}
{if $prefs.metatag_geoposition ne ''}
	<meta name="geo.position" content="{$prefs.metatag_geoposition|escape}" />
{/if}
{if $prefs.metatag_georegion ne ''}
	<meta name="geo.region" content="{$prefs.metatag_georegion|escape}" />
{/if}
{if $prefs.metatag_geoplacename ne ''}
	<meta name="geo.placename" content="{$prefs.metatag_geoplacename|escape}" />
{/if}
{if $prefs.metatag_robots ne ''}
	<meta name="robots" content="{$prefs.metatag_robots|escape}" />
{/if}
{if $prefs.metatag_revisitafter ne ''}
	<meta name="revisit-after" content="{$prefs.metatag_revisitafter|escape}" />
{/if}

{* --- tikiwiki block --- *}
<title>
	{if $prefs.site_title_location eq 'before'}
		{$prefs.browsertitle|escape} : 
	{/if}
	{if isset($trail)}
		{breadcrumbs type=$prefs.site_title_breadcrumb loc="head" crumbs=$trail}
	{else}
		{if !empty($headtitle)}
			{$headtitle|escape}
		{elseif !empty($page)}
			{if $beingStaged eq 'y' and $prefs.wikiapproval_hideprefix == 'y'}
				{$approvedPageName|escape}
			{else}
				{$page|escape}
			{/if}
		{elseif !empty($description)}{$description|escape}
		{* add $description|escape if you want to put the description + update breadcrumb_build replace return $crumbs->title; with return empty($crumbs->description)? $crumbs->title: $crumbs->description; *}
		{elseif !empty($arttitle)}
			{$arttitle|escape}
		{elseif !empty($title)}
			{$title|escape}
		{elseif !empty($thread_info.title)}
			{$thread_info.title|escape}
		{elseif !empty($post_info.title)}
			{$post_info.title|escape}
		{elseif !empty($forum_info.name)}
			{$forum_info.name|escape}
		{elseif !empty($categ_info.name)}
			{$categ_info.name|escape}
		{elseif !empty($userinfo.login)}
			{$userinfo.login|escape}
		{elseif !empty($tracker_item_main_value)}
			{$tracker_item_main_value|escape}
		{elseif !empty($tracker_info.name)}
			{$tracker_info.name|escape}
		{/if}
	{/if}
	{if $prefs.site_title_location eq 'after'}
		: {$prefs.browsertitle|escape} 
	{/if}
</title>

{if $prefs.site_favicon}
	<link rel="icon" href="{$prefs.site_favicon|escape}" />
{/if}

{* --- phplayers block --- *}
{if $prefs.feature_phplayers eq 'y' and isset($phplayers_headers)}
	{$phplayers_headers}
{/if}

{* --- universaleditbutton.org --- *}
{if (isset($editable) and $editable) and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox' or $tiki_p_admin_wiki eq 'y' or $canEditStaging eq 'y')}
	<link rel="alternate" type="application/x-wiki" title="{tr}Edit this page!{/tr}" href="tiki-editpage.php?page={$page|escape:url}" />
{/if}

{* --- Firefox RSS icons --- *}
{if $prefs.feature_wiki eq 'y' and $prefs.rss_wiki eq 'y' and $tiki_p_view eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_wiki|escape|default:"{tr}RSS Wiki{/tr}"}' href="tiki-wiki_rss.php?ver={$prefs.rssfeed_default_version|escape:'url'}" />
{/if}
{if $prefs.feature_blogs eq 'y' and $prefs.rss_blogs eq 'y' and $tiki_p_read_blog eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_blogs|escape|default:"{tr}RSS Blogs{/tr}"}' href="tiki-blogs_rss.php?ver={$prefs.rssfeed_default_version|escape:'url'}" />
{/if}
{if $prefs.feature_articles eq 'y' and $prefs.rss_articles eq 'y' and $tiki_p_read_article eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_articles|escape|default:"{tr}RSS Articles{/tr}"}' href="tiki-articles_rss.php?ver={$prefs.rssfeed_default_version|escape:'url'}" />
{/if}
{if $prefs.feature_galleries eq 'y' and $prefs.rss_image_galleries eq 'y' and $tiki_p_view_image_gallery eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_image_galleries|escape|default:"{tr}RSS Image Galleries{/tr}"}' href="tiki-image_galleries_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_file_galleries eq 'y' and $prefs.rss_file_galleries eq 'y' and $tiki_p_view_file_gallery eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_file_galleries|escape|default:"{tr}RSS File Galleries{/tr}"}' href="tiki-file_galleries_rss.php?ver={$prefs.rssfeed_default_version|escape:'url'}" />
{/if}
{if $prefs.feature_forums eq 'y' and $prefs.rss_forums eq 'y' and $tiki_p_forum_read eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_forums|escape|default:"{tr}RSS Forums{/tr}"}' href="tiki-forums_rss.php?ver={$prefs.rssfeed_default_version|escape:'url'}" />
{/if}
{if $prefs.feature_maps eq 'y' and $prefs.rss_mapfiles eq 'y' and $tiki_p_map_view eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_mapfiles|escape|default:"{tr}RSS Maps{/tr}"}' href="tiki-map_rss.php?ver={$prefs.rssfeed_default_version|escape:'url'}" />
{/if}
{if $prefs.feature_directory eq 'y' and $prefs.rss_directories eq 'y' and $tiki_p_view_directory eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_directories|escape|default:"{tr}RSS Directories{/tr}"}' href="tiki-directories_rss.php?ver={$prefs.rssfeed_default_version|escape:'url'}" />
{/if}

{if $prefs.feature_calendar eq 'y' and $prefs.rss_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_calendar|escape|default:"{tr}RSS Calendars{/tr}"}' href="tiki-calendars_rss.php?ver={$prefs.rssfeed_default_version|escape:'url'}" />
{/if}

{if $prefs.feature_blogs eq 'y' and $prefs.feature_blog_sharethis eq "y"}
	{if $prefs.blog_sharethis_publisher neq ""}
		<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher={$prefs.blog_sharethis_publisher}&amp;type=website&amp;buttonText=&amp;onmouseover=false&amp;send_services=aim"></script>
	{else}
		<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#type=website&amp;buttonText=&amp;onmouseover=false&amp;send_services=aim"></script>
	{/if}
{/if}

{if $headerlib}		{$headerlib->output_headers()}{/if}

{if $prefs.feature_custom_html_head_content}
	{eval var=$prefs.feature_custom_html_head_content}
{/if}
{* END of html head content *}
