{php}header('Content-Type: text/html; charset=utf-8');{/php}{* --- IMPORTANT: If you edit this (or any other TPL file) file via the Tiki built-in TPL editor (tiki-edit_templates.php), all the javascript will be stripped. This will cause problems. (Ex.: menus stop collapsing/expanding).

You should only modify header.tpl via a text editor through console, or ssh, or FTP edit commands. And only if you know what you are doing ;-)

You are most likely wanting to modify the top of your Tiki site. Please consider using Site Identity feature or modifying tiki-top_bar.tpl which you can do safely via the web-based interface.       --- *}<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
{* can be needed for sefurls with tiki not installed at the root server {if $feature_server_name}<base href="{$feature_server_name}" />{/if} *}
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
<script type="text/javascript" src="lib/tiki-js.js"></script>
{include file="bidi.tpl"}
<title>
{if isset($trail)}{breadcrumbs type="fulltrail" loc="head" crumbs=$trail}
{else}
{$siteTitle}
{if !empty($headtitle)} : {$headtitle}
{elseif !empty($page)} : {$page|escape} {* add $description|escape if you want to put the description *}
{elseif !empty($arttitle)} : {$arttitle}
{elseif !empty($title)} : {$title}
{elseif !empty($thread_info.title)} : {$thread_info.title}
{elseif !empty($post_info.title)} : {$post_info.title}
{elseif !empty($forum_info.name)} : {$forum_info.name}
{elseif !empty($categ_info.name)} : {$categ_info.name}
{elseif !empty($userinfo.login)} : {$userinfo.login}
{elseif !empty($tracker_item_main_value)} : {$tracker_item_main_value}
{elseif !empty($tracker_info.name)} : {$tracker_info.name}
{/if}
{/if}
</title>

{if $favicon}<link rel="icon" href="{$favicon}" />{/if}

{* --- phplayers block --- *}
{if $feature_phplayers eq 'y'}
<link rel="StyleSheet" href="lib/phplayers/layerstreemenu.css" type="text/css"></link>
<style type="text/css"><!-- @import url("lib/phplayers/layerstreemenu-hidden.css"); //--></style>
<script type="text/javascript"><!--
{php} include_once ("lib/phplayers/libjs/layersmenu-browser_detection.js"); global $LayersMenu, $TreeMenu, $PHPTreeMenu, $PlainMenu;{/php}
// --></script>
<script type="text/javascript" src="lib/phplayers/libjs/layersmenu-library.js"></script>
{* lets try this *}
<script type="text/javascript" src="lib/phplayers/libjs/layersmenu.js"></script>
{* will it work now ? (luci) *}
<script type="text/javascript" src="lib/phplayers/libjs/layerstreemenu-cookies.js"></script>
{/if}

{* --- Firefox RSS icons --- *}
{if $feature_wiki eq 'y' and $rss_wiki eq 'y' and $tiki_p_view eq 'y'}
<link rel="alternate" type="application/rss+xml" title="{tr}RSS Wiki{/tr}" href="tiki-wiki_rss.php?ver={$rssfeed_default_version}" />
{/if}
{if $feature_blogs eq 'y' and $rss_blogs eq 'y' and $tiki_p_read_blog eq 'y'}
<link rel="alternate" type="application/rss+xml" title="{tr}RSS Blogs{/tr}" href="tiki-blogs_rss.php?ver={$rssfeed_default_version}" />
{/if}
{if $feature_articles eq 'y' and $rss_articles eq 'y' and $tiki_p_read_article eq 'y'}
<link rel="alternate" type="application/rss+xml" title="{tr}RSS Articles{/tr}" href="tiki-articles_rss.php?ver={$rssfeed_default_version}" />
{/if}
{if $feature_galleries eq 'y' and $rss_image_galleries eq 'y' and $tiki_p_view_image_gallery eq 'y'}
<link rel="alternate" type="application/rss+xml" title="{tr}RSS Image Galleries{/tr}" href="tiki-image_galleries_rss.php?ver={$rssfeed_default_version}" />
{/if}
{if $feature_file_galleries eq 'y' and $rss_file_galleries eq 'y' and $tiki_p_view_file_gallery eq 'y'}
<link rel="alternate" type="application/rss+xml" title="{tr}RSS File Galleries{/tr}" href="tiki-file_galleries_rss.php?ver={$rssfeed_default_version}" />
{/if}
{if $feature_forums eq 'y' and $rss_forums eq 'y' and $tiki_p_forum_read eq 'y'}
<link rel="alternate" type="application/rss+xml" title="{tr}RSS Forums{/tr}" href="tiki-forums_rss.php?ver={$rssfeed_default_version}" />
{/if}
{if $feature_maps eq 'y' and $rss_mapfiles eq 'y' and $tiki_p_map_view eq 'y'}
<link rel="alternate" type="application/rss+xml" title="{tr}RSS Maps{/tr}" href="tiki-map_rss.php?ver={$rssfeed_default_version}" />
{/if}
{if $feature_directory eq 'y' and $rss_directories eq 'y' and $tiki_p_view_directory eq 'y'}
<link rel="alternate" type="application/rss+xml" title="{tr}RSS Directories{/tr}" href="tiki-directories_rss.php?ver={$rssfeed_default_version}" />
{/if}

{if $feature_calendar eq 'y' and $rss_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
<link rel="alternate" type="application/rss+xml" title="{tr}RSS Calendars{/tr}" href="tiki-calendars_rss.php?ver={$rssfeed_default_version}" />
{/if}

{if $headerlib}{$headerlib->output_headers()}{/if}

</head>

<body {if isset($section) and $section eq 'wiki page' and $user_dbl eq 'y' and $dblclickedit eq 'y' and $tiki_p_edit eq 'y'}ondblclick="location.href='tiki-editpage.php?page={$page|escape:"url"}';"{/if}
{if $msgError} onload="javascript:location.hash='msgError'" {/if}>
{if $minical_reminders>100}
<iframe width='0' height='0' frameborder="0" src="tiki-minical_reminders.php"></iframe>
{/if}

{if $feature_community_mouseover eq 'y'}{popup_init src="lib/overlib.js"}{/if}
{if $feature_siteidentity eq 'y'}
{* Site identity header section *}
	<div id="siteheader">
		{include file="tiki-site_header.tpl"}
	</div>
{/if}

{if $feature_fullscreen eq 'y'}
{if $smarty.session.fullscreen eq 'y'}
<a href="{$smarty.server.SCRIPT_NAME}{if $fsquery}?{$fsquery}&amp;{else}?{/if}fullscreen=n" style="float:right;padding:0 10px;font-size:80%;" class="menulink" id="fullscreenbutton">{tr}Cancel Fullscreen{/tr}</a>
{else}
<a href="{$smarty.server.SCRIPT_NAME}{if $fsquery}?{$fsquery}&amp;{else}?{/if}fullscreen=y" style="float:right;padding:0 10px;font-size:80%;" class="menulink" id="fullscreenbutton">{tr}Fullscreen{/tr}</a>
{/if}
{/if}

