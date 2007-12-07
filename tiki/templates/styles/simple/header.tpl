{* --- IMPORTANT: If you edit this (or any other TPL file) file via the Tiki built-in TPL editor (tiki-edit_templates.php), all the javascript will be stripped. This will cause problems. (Ex.: menus stop collapsing/expanding).

You should only modify header.tpl via a text editor through console, or ssh, or FTP edit commands. And only if you know what you are doing ;-)

You are most likely wanting to modify the top of your Tiki site. Please consider using Site Identity feature or modifying tiki-top_bar.tpl which you can do safely via the web-based interface.       --- *}<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
{if $prefs.metatag_keywords ne ''}		<meta name="keywords" content="{$prefs.metatag_keywords}" />{/if}
{if $prefs.metatag_author ne ''}		<meta name="author" content="{$prefs.metatag_author|escape}" />{/if}
{if $prefs.metatag_description ne ''}		<meta name="description" content="{$prefs.metatag_description}" />{/if}
{if $prefs.metatag_geoposition ne ''}		<meta name="geo.position" content="{$prefs.metatag_geoposition}" />{/if}
{if $prefs.metatag_georegion ne ''}		<meta name="geo.region" content="{$prefs.metatag_georegion}" />{/if}
{if $prefs.metatag_geoplacename ne ''}		<meta name="geo.placename" content="{$prefs.metatag_geoplacename}" />{/if}
{if $prefs.metatag_robots ne ''}		<meta name="robots" content="{$prefs.metatag_robots}" />{/if}
{if $prefs.metatag_revisitafter ne ''}		<meta name="revisit-after" content="{$prefs.metatag_revisitafter}" />{/if}

{* --- tikiwiki block --- *}
{php} include("lib/tiki-dynamic-js.php"); {/php}
<script type="text/javascript" src="lib/tiki-js.js"></script>
{include file="bidi.tpl"}{* this is included for Right-to-left languages *}
{strip}
		<title>
{if $trail}{breadcrumbs type="fulltrail" loc="head" crumbs=$trail}
{else}
{$prefs.siteTitle}
{if $page ne ''} : {$page|escape}
{elseif $headtitle} : {$headtitle}
{elseif $arttitle ne ''} : {$arttitle}
{elseif $title ne ''} : {$title}
{elseif $thread_info.title ne ''} : {$thread_info.title}
{elseif $post_info.title ne ''} : {$post_info.title}
{elseif $forum_info.name ne ''} : {$forum_info.name}
{elseif $categ_info.name ne ''} : {$categ_info.name}
{elseif $userinfo.login ne ''} : {$userinfo.login}
{/if}
{/if}
		</title>
{/strip}

{if $transition_style ne '' and $transition_style ne 'none' }
<link rel="StyleSheet"  href="styles/transitions/{$transition_style}" type="text/css" />
{/if}
<link rel="StyleSheet" media="all" href="styles/{$prefs.style}" type="text/css" />
{if $prefs.site_favicon}<link rel="icon" href="{$prefs.site_favicon}" />{/if}
{* --- jscalendar block --- *}
{if $prefs.feature_jscalendar eq 'y' and $uses_jscalendar eq 'y'}
<link rel="StyleSheet" href="lib/jscalendar/calendar-system.css" type="text/css"></link>
<script type="text/javascript"><!--
{if $prefs.feature_phplayers eq 'y'}{php} include_once ("lib/phplayers/libjs/layersmenu-browser_detection.js"); {/php}{/if}
// --></script>
<script type="text/javascript" src="lib/jscalendar/calendar.js"></script>
{if $jscalendar_langfile}
<script type="text/javascript" src="lib/jscalendar/lang/calendar-{$jscalendar_langfile}.js"></script>
{else}
<script type="text/javascript" src="lib/jscalendar/lang/calendar-en.js"></script>
{/if}
<script type="text/javascript" src="lib/jscalendar/calendar-setup.js"></script>
{/if}

{* --- phplayers block --- *}
{if $prefs.feature_phplayers eq 'y'}
<link rel="StyleSheet" href="lib/phplayers/layerstreemenu.css" type="text/css"></link>
<style type="text/css"><!-- @import url("lib/phplayers/layerstreemenu-hidden.css"); //--></style>
<script type="text/javascript"><!--
{php} include_once ("lib/phplayers/libjs/layersmenu-browser_detection.js"); {/php}
// --></script>
<script type="text/javascript" src="lib/phplayers/libjs/layersmenu-library.js"></script>
{*
<script type="text/javascript" src="lib/phplayers/libjs/layersmenu.js"></script>
*}
<script type="text/javascript" src="lib/phplayers/libjs/layerstreemenu-cookies.js"></script>
{/if}

{* --- Firefox RSS icons --- *}
{if $prefs.feature_wiki eq 'y' and $prefs.rss_wiki eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS Wiki{/tr}" href="tiki-wiki_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_blogs eq 'y' and $prefs.rss_blogs eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS Blogs{/tr}" href="tiki-blogs_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_articles eq 'y' and $prefs.rss_articles eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS Articles{/tr}" href="tiki-articles_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_galleries eq 'y' and $prefs.rss_image_galleries eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS Image Galleries{/tr}" href="tiki-image_galleries_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_file_galleries eq 'y' and $prefs.rss_file_galleries eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS File Galleries{/tr}" href="tiki-file_galleries_rss.php?{$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_forums eq 'y' and $prefs.rss_forums eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS Forums{/tr}" href="tiki-forums_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_maps eq 'y' and $prefs.rss_mapfiles eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS Maps{/tr}" href="tiki-map_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_directory eq 'y' and $prefs.rss_directories eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS Directories{/tr}" href="tiki-directories_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{if $prefs.feature_trackers eq 'y' and $prefs.rss_tracker eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS Trackers{/tr}" href="tiki-tracker_rss.php?ver={$prefs.rssfeed_default_version}" />
{/if}
{* ---- END ---- *}

{if $headerlib}{$headerlib->output_headers()}{/if}

</head>
<body {if isset($section) and $section eq 'wiki page' and $prefs.user_dbl eq 'y' and $dblclickedit eq 'y' and $tiki_p_edit eq 'y'} ondblclick="location.href='tiki-editpage.php?page={$page|escape:"url"}';"{/if}{if $prefs.show_comzone eq 'y'}onload="javascript:flip('comzone');"{/if}>
{if $prefs.minical_reminders>100}
	<iframe style="width: 0; height: 0; border: 0" src="tiki-minical_reminders.php"></iframe>
{/if}
	
{if $prefs.feature_community_mouseover}{popup_init src="lib/overlib.js"}{/if}
{if $prefs.feature_siteidentity eq 'y'}
{* Site identity header section *}
	<div id="siteheader">
		{include file="tiki-site_header.tpl"}
	</div>
{/if}
