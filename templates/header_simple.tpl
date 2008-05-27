{**
 * This file is simplified version of header.tpl intended to be used for pages such as popup windows, print page, etc.
 * $Id$
 *
 *}<!DOCTYPE html PUBLIC
"-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{if isset($pageLang)}{$pageLang}{else}{$prefs.language}{/if}" lang="{if isset($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
{if $base_url and $dir_level gt 0}		<base href="{$base_url}" />{/if}
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
{if $prefs.metatag_keywords ne ''}<meta name="keywords" content="{$prefs.metatag_keywords}" />
{/if}
{if $prefs.metatag_author ne ''}<meta name="author" content="{$prefs.metatag_author}" />
{/if}
{if $prefs.metatag_description ne ''}<meta name="description" content="{$prefs.metatag_description}" />
{/if}
{if $prefs.metatag_robots ne ''}<meta name="robots" content="{$prefs.metatag_robots}" />
{/if}
{if $prefs.metatag_revisitafter ne ''}<meta name="revisit-after" content="{$prefs.metatag_revisitafter}" />
{/if}

{* --- tikiwiki block --- *}
<script type="text/javascript" src="lib/tiki-js.js"></script>
{include file="bidi.tpl"}
<title>
{if isset($trail)}{breadcrumbs type="fulltrail" loc="head" crumbs=$trail}
{else}
{$prefs.siteTitle}
{if !empty($headtitle)} : {$headtitle}
{elseif !empty($page)} : {if $beingStaged eq 'y' and $prefs.wikiapproval_hideprefix == 'y'}{$approvedPageName|escape}{else}{$page|escape}{/if} {* add $description|escape if you want to put the description + update breadcrumb_build replace return $crumbs->title; with return empty($crumbs->description)? $crumbs->title: $crumbs->description; *}
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

{if $prefs.site_favicon}<link rel="icon" href="{$prefs.site_favicon}" />{/if}
<!--[if lt IE 7]> <link rel="StyleSheet" href="css/ie6.css" type="text/css" /> <![endif]-->

{if $prefs.feature_mootools eq "y"}
<script type="text/javascript" src="lib/mootools/mootools.js"></script>
{if $mootools_windoo eq "y"}
<script type="text/javascript" src="lib/mootools/extensions/windoo/windoo.js"></script>
{/if}
{if $mootab eq "y"}
<script src="lib/mootools/extensions/tabs/SimpleTabs.js" type="text/javascript" ></script> 
{/if}
{/if}

{if $prefs.feature_swffix eq "y"}
<script type="text/javascript" src="lib/swffix/swffix.js"></script>
{/if}

{if $headerlib}{$headerlib->output_headers()}{/if}
{if ($mid eq 'tiki-editpage.tpl')}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
{literal}
  var needToConfirm = true;
  
  window.onbeforeunload = confirmExit;
  function confirmExit()
  {
    if (needToConfirm)
		{/literal}return "{tr interactive='n'}You are about to leave this page. If you have made any changes without Saving, your changes will be lost.  Are you sure you want to exit this page?{/tr}";{literal}
  }
{/literal}
//--><!]]>
</script>
{/if}

{if $prefs.feature_shadowbox eq 'y'}
<!-- Includes for Shadowbox script -->
	<link rel="stylesheet" type="text/css" href="lib/shadowbox/build/css/shadowbox.css" />

{if $prefs.feature_mootools eq "y"}
	<script type="text/javascript" src="lib/shadowbox/build/js/adapter/shadowbox-mootools.js" charset="utf-8"></script>
{else}
	<script type="text/javascript" src="lib/shadowbox/build/js/adapter/shadowbox-jquery.js" charset="utf-8"></script>
{/if}

	<script type="text/javascript" src="lib/shadowbox/build/js/shadowbox.js" charset="utf-8"></script>

	<script type="text/javascript">
<!--//--><![CDATA[//><!--
{if $prefs.feature_mootools eq "y"}
	{literal}
		window.addEvent('domready', function() {
	{/literal}
{else}
	{literal}
		$(document).ready(function() {
	{/literal}
{/if}
{literal}
			var options = {
				ext: {
					img:        ['png', 'jpg', 'jpeg', 'gif', 'bmp'],
					qt:         ['dv', 'mov', 'moov', 'movie', 'mp4'],
					wmp:        ['asf', 'wm', 'wmv'],
					qtwmp:      ['avi', 'mpg', 'mpeg'],
					iframe: ['asp', 'aspx', 'cgi', 'cfm', 'doc', 'htm', 'html', 'pdf', 'pl', 'php', 'php3', 'php4', 'php5', 'phtml', 'rb', 'rhtml', 'shtml', 'txt', 'vbs', 'xls']
				},
				handleUnsupported: 'remove',
				loadingImage: 'lib/shadowbox/images/loading.gif',
				overlayBgImage: 'lib/shadowbox/images/overlay-85.png',
				resizeLgImages: true,
				text: {
{/literal}
					cancel:   '{tr}Cancel{/tr}',
					loading:  '{tr}Loading{/tr}',
					close:    '{tr}\074span class="shortcut"\076C\074/span\076lose{/tr}',
					next:     '{tr}\074span class="shortcut"\076N\074/span\076ext{/tr}',
					prev:     '{tr}\074span class="shortcut"\076P\074/span\076revious{/tr}'
{literal}
				},
				keysClose:          ['c', 27], // c OR esc
				keysNext:           ['n', 39], // n OR arrow right
				keysPrev:           ['p', 37]  // p OR arrow left
			};

			Shadowbox.init(options);
		});
//--><!]]>
	</script>
{/literal}
{/if}
</head>

<body {if isset($section) and $section eq 'wiki page' and $prefs.user_dbl eq 'y' and $dblclickedit eq 'y' and $tiki_p_edit eq 'y'}ondblclick="location.href='tiki-editpage.php?page={$page|escape:"url"}';"{/if}
onload="{if $prefs.feature_tabs eq 'y'}tikitabs({if $cookietab neq ''}{$cookietab}{else}1{/if},5);{/if}{if $msgError} javascript:location.hash='msgError'{/if}"
{if $section} class="tiki_{$section}"{/if}>

{if $prefs.feature_community_mouseover eq 'y'}{popup_init src="lib/overlib.js"}{/if}
