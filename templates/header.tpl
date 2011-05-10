{if $base_uri and ($dir_level gt 0 or $prefs.feature_html_head_base_tag eq 'y')}
	<base href="{$base_uri|escape}" />
{/if}
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="Tiki Wiki CMS Groupware - http://tiki.org" />

{* --- Canonical URL --- *}
{include file="canonical.tpl"}	

{if !empty($forum_info.name) & $prefs.metatag_threadtitle eq 'y'}
	<meta name="keywords" content="{tr}Forum{/tr} {$forum_info.name|escape} {$thread_info.title|escape} {if $prefs.feature_freetags eq 'y'}{foreach from=$freetags.data item=taginfo}{$taginfo.tag|escape} {/foreach}{/if}" />
{elseif isset($galleryId) && $galleryId neq '' && $prefs.metatag_imagetitle neq 'n'}
	<meta name="keywords" content="{tr}Images Galleries{/tr} {$title|escape} {if $prefs.feature_freetags eq 'y'}{foreach from=$freetags.data item=taginfo}{$taginfo.tag|escape} {/foreach}{/if}" />
{elseif $prefs.metatag_keywords neq '' or !empty($metatag_local_keywords)}
	<meta name="keywords" content="{$prefs.metatag_keywords|escape} {if $prefs.feature_freetags eq 'y'}{foreach from=$freetags.data item="taginfo"}{$taginfo.tag|escape} {/foreach}{/if} {$metatag_local_keywords|escape}" />
{/if}
{if $prefs.metatag_author neq ''}
	<meta name="author" content="{$prefs.metatag_author|escape}" />
{/if}
{if $prefs.metatag_pagedesc eq 'y' and $description neq ''}
	<meta name="description" content="{$description|escape}" />
{elseif $prefs.metatag_description neq '' or (isset($description) and $description eq '')}
	<meta name="description" content="{$prefs.metatag_description|escape}" />
{/if}
{if $prefs.metatag_geoposition neq ''}
	<meta name="geo.position" content="{$prefs.metatag_geoposition|escape}" />
{/if}
{if $prefs.metatag_georegion neq ''}
	<meta name="geo.region" content="{$prefs.metatag_georegion|escape}" />
{/if}
{if $prefs.metatag_geoplacename neq ''}
	<meta name="geo.placename" content="{$prefs.metatag_geoplacename|escape}" />
{/if}
{if (isset($prefs.metatag_robots) and $prefs.metatag_robots neq '') and (!isset($metatag_robots) or $metatag_robots eq '')}
        <meta name="robots" content="{$prefs.metatag_robots|escape}" />
{/if}
{if (!isset($prefs.metatag_robots) or $prefs.metatag_robots eq '') and (isset($metatag_robots) and $metatag_robots neq '')}
        <meta name="robots" content="{$metatag_robots|escape}" />
{/if}
{if (isset($prefs.metatag_robots) and $prefs.metatag_robots neq '') and (isset($metatag_robots) and $metatag_robots neq '')}
        <meta name="robots" content="{$prefs.metatag_robots|escape}, {$metatag_robots|escape}" />
{/if}
{if $prefs.metatag_revisitafter neq ''}
	<meta name="revisit-after" content="{$prefs.metatag_revisitafter|escape}" />
{/if}

{* --- tiki block --- *}
<title>{strip}
	{if $prefs.site_title_location eq 'before'}{$prefs.browsertitle|tr_if|escape} {$prefs.site_nav_seper} {/if}
	{capture assign="page_description_title"}{strip}
		{if ($prefs.feature_breadcrumbs eq 'y' or $prefs.site_title_breadcrumb eq "desc") && isset($trail)}
			{breadcrumbs type=$prefs.site_title_breadcrumb loc="head" crumbs=$trail}
		{/if}
	{/strip}{/capture}
	{if !empty($page_description_title)}
		{$page_description_title}
	{else}
		{if !empty($tracker_item_main_value)}
			{$tracker_item_main_value|truncate:255|escape}
		{elseif !empty($title) and !is_array($title)}
			{$title|escape}
		{elseif !empty($page)}
			{if isset($beingStaged) and $beingStaged eq 'y' and $prefs.wikiapproval_hideprefix == 'y'}
				{$approvedPageName|escape}
			{else}
				{$page|escape}
			{/if}
		{elseif !empty($description)}{$description|escape}
		{* add $description|escape if you want to put the description + update breadcrumb_build replace return $crumbs->title; with return empty($crumbs->description)? $crumbs->title: $crumbs->description; *}
		{elseif !empty($arttitle)}
			{$arttitle|escape}
		{elseif !empty($thread_info.title)}
			{$thread_info.title|escape}
		{elseif !empty($forum_info.name)}
			{$forum_info.name|escape}
		{elseif !empty($categ_info.name)}
			{$categ_info.name|escape}
		{elseif !empty($userinfo.login)}
			{$userinfo.login|username}
		{elseif !empty($tracker_info.name)}
			{$tracker_info.name|escape}
		{elseif !empty($headtitle)}
			{$headtitle|escape}{* use $headtitle last if feature specific title not found *}
		{/if}
	{/if}
	{if $prefs.site_title_location eq 'after'} {$prefs.site_nav_seper} {$prefs.browsertitle|tr_if|escape}{/if}
{/strip}</title>

{if $prefs.site_favicon}
	<link rel="icon" href="{$prefs.site_favicon|escape}" />
{/if}

{* --- universaleditbutton.org --- *}
{if (isset($editable) and $editable) and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox' or $tiki_p_admin_wiki eq 'y' or $canEditStaging eq 'y')}
	<link rel="alternate" type="application/x-wiki" title="{tr}Edit this page!{/tr}" href="tiki-editpage.php?page={$page|escape:url}" />
{/if}

{* --- Firefox RSS icons --- *}
{if $prefs.feature_wiki eq 'y' and $prefs.feed_wiki eq 'y' and $tiki_p_view eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_wiki_title|escape|default:"{tr}RSS Wiki{/tr}"}' href="tiki-wiki_rss.php?ver={$prefs.feed_default_version|escape:'url'}" />
{/if}
{if $prefs.feature_blogs eq 'y' and $prefs.feed_blogs eq 'y' and $tiki_p_read_blog eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_blogs_title|escape|default:"{tr}RSS Blogs{/tr}"}' href="tiki-blogs_rss.php?ver={$prefs.feed_default_version|escape:'url'}" />
{/if}
{if $prefs.feature_articles eq 'y' and $prefs.feed_articles eq 'y' and $tiki_p_read_article eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_articles_title|escape|default:"{tr}RSS Articles{/tr}"}' href="tiki-articles_rss.php?ver={$prefs.feed_default_version|escape:'url'}" />
{/if}
{if $prefs.feature_galleries eq 'y' and $prefs.feed_image_galleries eq 'y' and $tiki_p_view_image_gallery eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_image_galleries_title|escape|default:"{tr}RSS Image Galleries{/tr}"}' href="tiki-image_galleries_rss.php?ver={$prefs.feed_default_version}" />
{/if}
{if $prefs.feature_file_galleries eq 'y' and $prefs.feed_file_galleries eq 'y' and $tiki_p_view_file_gallery eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_file_galleries_title|escape|default:"{tr}RSS File Galleries{/tr}"}' href="tiki-file_galleries_rss.php?ver={$prefs.feed_default_version|escape:'url'}" />
{/if}
{if $prefs.feature_forums eq 'y' and $prefs.feed_forums eq 'y' and $tiki_p_forum_read eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_forums_title|escape|default:"{tr}RSS Forums{/tr}"}' href="tiki-forums_rss.php?ver={$prefs.feed_default_version|escape:'url'}" />
{/if}
{if $prefs.feature_maps eq 'y' and $prefs.rss_mapfiles eq 'y' and $tiki_p_map_view eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_mapfiles|escape|default:"{tr}RSS Maps{/tr}"}' href="tiki-map_rss.php?ver={$prefs.feed_default_version|escape:'url'}" />
{/if}
{if $prefs.feature_directory eq 'y' and $prefs.feed_directories eq 'y' and $tiki_p_view_directory eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_directories_title|escape|default:"{tr}RSS Directories{/tr}"}' href="tiki-directories_rss.php?ver={$prefs.feed_default_version|escape:'url'}" />
{/if}

{if $prefs.feature_calendar eq 'y' and $prefs.feed_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_calendar_title|escape|default:"{tr}RSS Calendars{/tr}"}' href="tiki-calendars_rss.php?ver={$prefs.feed_default_version|escape:'url'}" />
{/if}

{if ($prefs.feature_blogs eq 'y' and $prefs.feature_blog_sharethis eq 'y') or ($prefs.feature_articles eq 'y' and $prefs.feature_cms_sharethis eq 'y')}
	{if $prefs.blog_sharethis_publisher neq "" and $prefs.article_sharethis_publisher neq ""}
		<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher={$prefs.blog_sharethis_publisher}&amp;type=website&amp;buttonText=&amp;onmouseover=false&amp;send_services=aim"></script>
	{elseif $prefs.blog_sharethis_publisher neq "" and $prefs.article_sharethis_publisher eq ""}
		<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher={$prefs.blog_sharethis_publisher}&amp;type=website&amp;buttonText=&amp;onmouseover=false&amp;send_services=aim"></script>
	{elseif $prefs.blog_sharethis_publisher eq "" and $prefs.article_sharethis_publisher neq ""}
		<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher={$prefs.article_sharethis_publisher}&amp;type=website&amp;buttonText=&amp;onmouseover=false&amp;send_services=aim"></script>
	{elseif $prefs.blog_sharethis_publisher eq "" and $prefs.article_sharethis_publisher eq ""}
		<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#type=website&amp;buttonText=&amp;onmouseover=false&amp;send_services=aim"></script>
	{/if}
{/if}

<!--[if lt IE 9]>{* according to http://remysharp.com/2009/01/07/html5-enabling-script/ *}
	<script src="lib/html5shim/html5.js" type="text/javascript"></script>
<![endif]-->

{if $headerlib}		{$headerlib->output_headers()}{/if}

{if $prefs.feature_custom_html_head_content}
	{eval var=$prefs.feature_custom_html_head_content}
{/if}
{* END of html head content *}
