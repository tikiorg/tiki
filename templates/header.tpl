<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
{if $description ne ''}<meta name="description" content="{$description}" />{/if}

{* --- tikiwiki block --- *}
<script type="text/javascript" src="lib/tiki-js.js"></script>
<link rel="StyleSheet"  href="styles/{$style}" type="text/css" />
{include file="bidi.tpl"}
<title>
{$siteTitle}
{if $page ne ''} : {$page}
{elseif $headtitle} : {$headtitle}
{elseif $title ne ''} : {$title}
{elseif $thread_info.title ne ''} : {$thread_info.title}
{elseif $forum_info.name ne ''} : {$forum_info.name}
{/if}
</title>

{* --- jscalendar block --- *}
{if $feature_jscalendar eq 'y'}
<link rel="StyleSheet" href="lib/jscalendar/calendar-system.css" type="text/css"></link>
<script type="text/javascript" src="lib/jscalendar/calendar.js"></script>
<script type="text/javascript" src="lib/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="lib/jscalendar/calendar-setup.js"></script>
{/if}

{* --- phplayers block --- *}
{if $feature_phplayers eq 'y'}
<link rel="StyleSheet" href="lib/phplayers/layerstreemenu.css" type="text/css"></link>
<style type="text/css"><!-- @import url("lib/phplayers/layerstreemenu-hidden.css"); //--></style>
<script language="JavaScript" type="text/javascript"><!--
{php} include ("lib/phplayers/libjs/layersmenu-browser_detection.js"); {/php}
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
<link rel="stylesheet" href="lib/tabs/mozilla.css" type="text/css" />
<script src="lib/tabs/utils.js" type="text/javascript"></script>
<script src="lib/tabs/viewport.js" type="text/javascript"></script>
<script src="lib/tabs/global.js" type="text/javascript"></script>
<script src="lib/tabs/cookie.js" type="text/javascript"></script>
<script src="lib/tabs/tabs.js" type="text/javascript"></script>
<script language='Javascript' type='text/javascript'>
// <![CDATA[
TabParams = {literal}{{/literal}
	useClone         : false,
	alwaysShowClone  : false,
	eventType        : "click",
	tabTagName       : "span"
	{literal}}{/literal};
// ]]>
</script>
{/if}

{$trl}

</head>

<body {if $uses_tabs eq 'y'}onload="tabInit()"{/if} 
{if $user_dbl eq 'y' and $dblclickedit eq 'y' and $tiki_p_edit eq 'y'}ondblclick="location.href='tiki-editpage.php?page={$page|escape:"url"}';"{/if} >

{if $minical_reminders>100}
<iframe width='0' height='0' frameborder="0" src="tiki-minical_reminders.php"></iframe>
{/if}
