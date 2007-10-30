<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <link rel="StyleSheet"  href="styles/{$prefs.style}" type="text/css" />
		{if $prefs.site_favicon}<link rel="icon" href="{$prefs.site_favicon}" />{/if}
    {include file="bidi.tpl"}

<title>
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
</title>

{if $prefs.feature_community_mouseover}{popup_init src="lib/overlib.js"}{/if}

    {literal}
	<script type="text/javascript" src="lib/tiki-js.js">
	</script>
	{/literal}
	{if $uses_tabs eq 'y'}
		{literal}
			<link rel="stylesheet" href="lib/tabs/mozilla.css" type="text/css" />
			<script src="lib/tabs/utils.js" type="text/javascript"></script>
			<script src="lib/tabs/viewport.js" type="text/javascript"></script>
			<script src="lib/tabs/global.js" type="text/javascript"></script>
			<script src="lib/tabs/cookie.js" type="text/javascript"></script>
			<script src="lib/tabs/tabs.js" type="text/javascript"></script>
			<script language='Javascript' type='text/javascript'>
				// <![CDATA[
			
			
				TabParams = {
					useClone         : false,
					alwaysShowClone  : false,
					eventType        : "click",
					tabTagName       : "span"
					};
			

				// ]]>
			</script>

		{/literal}
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

<body{if $uses_tabs eq 'y'} onload="javascript:tabInit();"{/if}{if isset($section) and $section eq 'wiki page' and $prefs.user_dbl eq 'y' and $dblclickedit eq 'y' and $tiki_p_edit eq 'y'} ondblclick="location.href='tiki-editpage.php?page={$page}';"{/if}>  
{if $prefs.minical_reminders>100}
<iframe width='0' height='0' frameborder="0" src="tiki-minical_reminders.php"></iframe>
{/if}  
