<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
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
{$siteTitle}
{if $page ne ''} : {$page|escape}
{elseif $headtitle} : {$headtitle}
{elseif $title ne ''} : {$title}
{elseif $thread_info.title ne ''} : {$thread_info.title}
{elseif $post_info.title ne ''} : {$post_info.title}
{elseif $forum_info.name ne ''} : {$forum_info.name}
{/if}
</title>

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
{php} include_once ("lib/phplayers/libjs/layersmenu-browser_detection.js"); {/php}
// --></script>
<script language="JavaScript" type="text/javascript" src="lib/phplayers/libjs/layersmenu-library.js"></script>
<script language="JavaScript" type="text/javascript" src="lib/phplayers/libjs/layersmenu.js"></script>
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

<link rel="StyleSheet"  href="styles/{$style}" type="text/css" />

{$trl}

</head>

<body {if $user_dbl eq 'y' and $dblclickedit eq 'y' and $tiki_p_edit eq 'y'}ondblclick="location.href='tiki-editpage.php?page={$page|escape:"url"}';"{/if}>
{if $minical_reminders>100}
<iframe width='0' height='0' frameborder="0" src="tiki-minical_reminders.php"></iframe>
{/if}
