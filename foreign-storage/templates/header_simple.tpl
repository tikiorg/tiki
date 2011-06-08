{**
 * This file is simplified version of header.tpl intended to be used for pages such as popup windows, print page, etc.
 * $Id$
 *
 *}<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{if isset($pageLang)}{$pageLang}{else}{$prefs.language}{/if}" lang="{if isset($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
{if $base_url and $dir_level gt 0}		<base href="{$base_url}" />{/if}
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
{if $prefs.metatag_keywords ne '' or $metatag_local_keywords ne ''}<meta name="keywords" content="{$prefs.metatag_keywords|escape} {$metatag_local_keywords|escape}" />
{/if}
{if $prefs.metatag_author ne ''}<meta name="author" content="{$prefs.metatag_author|escape}" />
{/if}
{if $prefs.metatag_description ne ''}<meta name="description" content="{$prefs.metatag_description|escape}" />
{/if}
{if $prefs.metatag_robots ne '' && $metatag_robots eq ''}
        <meta name="robots" content="{$prefs.metatag_robots|escape}" />
{/if}
{if $prefs.metatag_robots eq '' && $metatag_robots ne ''}
        <meta name="robots" content="{$metatag_robots|escape}" />
{/if}
{if $prefs.metatag_robots eq '' && $metatag_robots eq ''}
        <meta name="robots" content="{$prefs.metatag_robots|escape}, {$metatag_robots|escape}" />
{/if}
{if $prefs.metatag_revisitafter ne ''}<meta name="revisit-after" content="{$prefs.metatag_revisitafter|escape}" />
{/if}

{* --- Canonical URL --- *}
{include file='canonical.tpl'}

{* --- Tiki block --- *}
{include file='bidi.tpl'}
<title>
{if isset($trail)}{breadcrumbs type="fulltrail" loc="head" crumbs=$trail}
{else}
{$prefs.browsertitle}
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

{if $headerlib}{$headerlib->output_headers()}{/if}

</head>
<body{html_body_attributes}>
