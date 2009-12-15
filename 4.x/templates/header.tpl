{* $Id$ *}
{if $base_url and $dir_level gt 0}		<base href="{$base_url}" />{/if}
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="generator" content="TikiWiki CMS/Groupware - http://TikiWiki.org" />
{if !empty($forum_info.name) & $prefs.metatag_threadtitle eq 'y'}		<meta name="keywords" content="{tr}Forum{/tr} {$forum_info.name} {$thread_info.title} {if $prefs.feature_freetags eq 'y'}{foreach from=$freetags.data item=taginfo}{$taginfo.tag} {/foreach}{/if}" />
{elseif isset($galleryId) && $galleryId ne '' & $prefs.metatag_imagetitle ne 'n'}		<meta name="keywords" content="{tr}Images Galleries{/tr} {$title} {if $prefs.feature_freetags eq 'y'}{foreach from=$freetags.data item=taginfo}{$taginfo.tag} {/foreach}{/if}" />
{elseif $prefs.metatag_keywords ne ''}		<meta name="keywords" content="{$prefs.metatag_keywords} {if $prefs.feature_freetags eq 'y'}{foreach from=$freetags.data item=taginfo}{$taginfo.tag} {/foreach}{/if}" />
{/if}
{if $prefs.metatag_author ne ''}		<meta name="author" content="{$prefs.metatag_author}" />
{/if}
{if $prefs.metatag_pagedesc eq 'y' and $description ne ''}		<meta name="description" content="{$description}" />
{elseif $prefs.metatag_description ne '' or (isset($description) and $description eq '')}		<meta name="description" content="{$prefs.metatag_description}" />
{/if}
{if $prefs.metatag_geoposition ne ''}		<meta name="geo.position" content="{$prefs.metatag_geoposition}" />
{/if}
{if $prefs.metatag_georegion ne ''}		<meta name="geo.region" content="{$prefs.metatag_georegion}" />
{/if}
{if $prefs.metatag_geoplacename ne ''}		<meta name="geo.placename" content="{$prefs.metatag_geoplacename}" />
{/if}
{if $prefs.metatag_robots ne ''}		<meta name="robots" content="{$prefs.metatag_robots}" />
{/if}
{if $prefs.metatag_revisitafter ne ''}		<meta name="revisit-after" content="{$prefs.metatag_revisitafter}" />
{/if}

{* --- tikiwiki block --- *}
{include file='bidi.tpl'}
		<title>
{if isset($trail)}
	{breadcrumbs type=$prefs.site_title_breadcrumb loc="head" crumbs=$trail}
{else}
	{if $prefs.site_title_location eq 'before'}
		{$prefs.browsertitle|escape} : 
	{/if}
	{if !empty($headtitle)}{$headtitle}
	{elseif !empty($page)}{if $beingStaged eq 'y' and $prefs.wikiapproval_hideprefix == 'y'}{$approvedPageName|escape}{else}{$page|escape}{/if} {* add $description|escape if you want to put the description + update breadcrumb_build replace return $crumbs->title; with return empty($crumbs->description)? $crumbs->title: $crumbs->description; *}
	{elseif !empty($arttitle)}{$arttitle|escape}
	{elseif !empty($title)}{$title|escape}
	{elseif !empty($thread_info.title)}{$thread_info.title|escape}
	{elseif !empty($post_info.title)}{$post_info.title|escape}
	{elseif !empty($forum_info.name)}{$forum_info.name|escape}
	{elseif !empty($categ_info.name)}{$categ_info.name}
	{elseif !empty($userinfo.login)}{$userinfo.login|escape}
	{elseif !empty($tracker_item_main_value)}{$tracker_item_main_value|escape}
	{elseif !empty($tracker_info.name)}{$tracker_info.name|escape}
	{/if}
	{if $prefs.site_title_location eq 'after'}
		: {$prefs.browsertitle|escape} 
	{/if}
{/if}
		</title>

{if $prefs.site_favicon}		<link rel="icon" href="{$prefs.site_favicon}" />{/if}

{* --- phplayers block --- *}
{if $prefs.feature_phplayers eq 'y' and isset($phplayers_headers)}		{$phplayers_headers}{/if}

{*-- css menus block --*}
{if $prefs.feature_cssmenus eq 'y'}
		<link rel="stylesheet" href="css/cssmenus.css" type="text/css" />
{/if}

{* --- universaleditbutton.org --- *}
{if (isset($editable) and $editable) and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox' or $tiki_p_admin_wiki eq 'y' or $canEditStaging eq 'y')}
		<link rel="alternate" type="application/x-wiki" title="{tr}Edit this page!{/tr}" href="tiki-editpage.php?page={$page|escape:url}" />
{/if}

{* --- Firefox RSS icons --- *}
{if $prefs.feature_wiki eq 'y' and $prefs.rss_wiki eq 'y' and $tiki_p_view eq 'y'}
		<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_wiki|escape|default:"{tr}RSS Wiki{/tr}"}' href="tiki-wiki_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_blogs eq 'y' and $prefs.rss_blogs eq 'y' and $tiki_p_read_blog eq 'y'}
		<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_blogs|escape|default:"{tr}RSS Blogs{/tr}"}' href="tiki-blogs_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_articles eq 'y' and $prefs.rss_articles eq 'y' and $tiki_p_read_article eq 'y'}
		<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_articles|escape|default:"{tr}RSS Articles{/tr}"}' href="tiki-articles_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_galleries eq 'y' and $prefs.rss_image_galleries eq 'y' and $tiki_p_view_image_gallery eq 'y'}
		<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_image_galleries|escape|default:"{tr}RSS Image Galleries{/tr}"}' href="tiki-image_galleries_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_file_galleries eq 'y' and $prefs.rss_file_galleries eq 'y' and $tiki_p_view_file_gallery eq 'y'}
		<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_file_galleries|escape|default:"{tr}RSS File Galleries{/tr}"}' href="tiki-file_galleries_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_forums eq 'y' and $prefs.rss_forums eq 'y' and $tiki_p_forum_read eq 'y'}
		<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_forums|escape|default:"{tr}RSS Forums{/tr}"}' href="tiki-forums_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_maps eq 'y' and $prefs.rss_mapfiles eq 'y' and $tiki_p_map_view eq 'y'}
		<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_mapfiles|escape|default:"{tr}RSS Maps{/tr}"}' href="tiki-map_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_directory eq 'y' and $prefs.rss_directories eq 'y' and $tiki_p_view_directory eq 'y'}
		<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_directories|escape|default:"{tr}RSS Directories{/tr}"}' href="tiki-directories_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}

{if $prefs.feature_calendar eq 'y' and $prefs.rss_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
		<link rel="alternate" type="application/rss+xml" title='{$prefs.title_rss_calendar|escape|default:"{tr}RSS Calendars{/tr}"}' href="tiki-calendars_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}

{if $headerlib}		{$headerlib->output_headers()}{/if}

{if $prefs.javascript_enabled eq "y" and $prefs.feature_jquery eq "y"}
	{include file='header_jquery.tpl'}
{/if}

{if $prefs.feature_custom_html_head_content}
	{eval var=$prefs.feature_custom_html_head_content}
{/if}
{* END of html head content *}
