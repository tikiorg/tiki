{* --- IMPORTANT: If you edit this (or any other TPL file) file via the Tiki built-in TPL editor (tiki-edit_templates.php), all the javascript will be stripped. This will cause problems. (Ex.: menus stop collapsing/expanding).

You should only modify header.tpl via a text editor through console, or ssh, or FTP edit commands. And only if you know what you are doing ;-)

You are most likely wanting to modify the top of your Tiki site. Please consider using Site Identity feature or modifying tiki-top_bar.tpl which you can do safely via the web-based interface.       --- *}<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
{* can be needed for sefurls with tiki not installed at the root server *}
{if $feature_server_name}<base href="{$feature_server_name}/" />{/if}
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
{if $metatag_keywords ne ''}<meta name="keywords" content="{$metatag_keywords}" />
{/if}
{if $metatag_author ne ''}<meta name="author" content="{$metatag_author}" />
{/if}
{if $metatag_description ne ''}<meta name="description" content="{$metatag_description}" />
{/if}
{if $metatag_geoposition ne ''}<meta name="geo.position" content="{$metatag_geoposition}" />
{/if}
{if $metatag_georegion ne ''}<meta name="geo.region" content="{$metatag_georegion}" />
{/if}
{if $metatag_geoplacename ne ''}<meta name="geo.placename" content="{$metatag_geoplacename}" />
{/if}
{if $metatag_robots ne ''}<meta name="robots" content="{$metatag_robots}" />
{/if}
{if $metatag_revisitafter ne ''}<meta name="revisit-after" content="{$metatag_revisitafter}" />
{/if}

{* --- tikiwiki block --- *}
{php} include("lib/tiki-dynamic-js.php"); {/php}
<script type="text/javascript" src="lib/tiki-js.js"></script>
{include file="bidi.tpl"}
<title>
{if $trail}{breadcrumbs type="fulltrail" loc="head" crumbs=$trail}
{else}
{$siteTitle}
{if $headtitle} : {$headtitle}
{elseif $page ne ''} : {$page|escape} {* add $description|escape if you want to put the description *}
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

{if $transition_style ne '' and $transition_style ne 'none' }
<link rel="StyleSheet"  href="{$feature_server_name}/styles/transitions/{$transition_style}" type="text/css" />
{/if}
<link rel="StyleSheet"  href="{$feature_server_name}/styles/{$style}" type="text/css" />
{if $favicon}<link rel="icon" href="{$favicon}" />{/if}
{* --- jscalendar block --- *}
{if $feature_jscalendar eq 'y' and $uses_jscalendar eq 'y'}
<link rel="StyleSheet" href="lib/jscalendar/calendar-system.css" type="text/css"></link>
<script language="JavaScript" type="text/javascript"><!--
{if $feature_phplayers eq 'y'}{php} include_once ("lib/phplayers/libjs/layersmenu-browser_detection.js"); {/php}{/if}
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
{if $feature_phplayers eq 'y'}
<link rel="StyleSheet" href="lib/phplayers/layerstreemenu.css" type="text/css"></link>
<style type="text/css"><!-- @import url("lib/phplayers/layerstreemenu-hidden.css"); //--></style>
<script language="JavaScript" type="text/javascript"><!--
{php} include_once ("lib/phplayers/libjs/layersmenu-browser_detection.js"); global $LayersMenu, $TreeMenu, $PHPTreeMenu, $PlainMenu;{/php}
// --></script>
<script language="JavaScript" type="text/javascript" src="lib/phplayers/libjs/layersmenu-library.js"></script>
{*
<script language="JavaScript" type="text/javascript" src="lib/phplayers/libjs/layersmenu.js"></script>
*}
<script language="JavaScript" type="text/javascript" src="lib/phplayers/libjs/layerstreemenu-cookies.js"></script>
{/if}

{* --- Integrator block --- *}
{if strlen($integrator_css_file) > 0}
<link rel="StyleSheet" href="{$integrator_css_file}" type="text/css" />
{/if}
    
{* --- tabs block (for myTiki, calendar, and more to come) --- *}
{if $uses_tabs eq 'y'}
{* tabs lib removed because non-free *}
{/if}

{* --- Firefox RSS icons --- *}
{if $feature_wiki eq 'y' and $rss_wiki eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS Wiki{/tr}" href="tiki-wiki_rss.php?ver={$rssfeed_default_version}" />
{/if}
{if $feature_blogs eq 'y' and $rss_blogs eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS Blogs{/tr}" href="tiki-blogs_rss.php?ver={$rssfeed_default_version}" />
{/if}
{if $feature_articles eq 'y' and $rss_articles eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS Articles{/tr}" href="tiki-articles_rss.php?ver={$rssfeed_default_version}" />
{/if}
{if $feature_galleries eq 'y' and $rss_image_galleries eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS Image Galleries{/tr}" href="tiki-image_galleries_rss.php?ver={$rssfeed_default_version}" />
{/if}
{if $feature_file_galleries eq 'y' and $rss_file_galleries eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS File Galleries{/tr}" href="tiki-file_galleries_rss.php?{$rssfeed_default_version}" />
{/if}
{if $feature_forums eq 'y' and $rss_forums eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS Forums{/tr}" href="tiki-forums_rss.php?ver={$rssfeed_default_version}" />
{/if}
{if $feature_maps eq 'y' and $rss_mapfiles eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS Maps{/tr}" href="tiki-map_rss.php?ver={$rssfeed_default_version}" />
{/if}
{if $feature_directory eq 'y' and $rss_directories eq 'y'}
<link rel="alternate" type="application/xml" title="{tr}RSS Directories{/tr}" href="tiki-directories_rss.php?ver={$rssfeed_default_version}" />
{/if}
{* ---- END ---- *}

{$trl}

</head>

<body id="body" {if $user_dbl eq 'y' and $dblclickedit eq 'y' and $tiki_p_edit eq 'y'}ondblclick="location.href='tiki-editpage.php?page={$page|escape:"url"}';"{/if}
{if $show_comzone eq 'y'}onload="javascript:flip('comzone');"{/if}{if $section} class="tiki_{$section}"{/if}>
{if $minical_reminders>100}
	<iframe width='0' height='0' frameborder="0" src="tiki-minical_reminders.php"></iframe>
{/if}

{if $feature_community_mouseover}{popup_init src="lib/overlib.js"}{/if}
{if $feature_siteidentity eq 'y'}
{* Site identity header section *}
	<div id="siteheader">
		{include file="tiki-site_header.tpl"}
	</div>
{/if}
