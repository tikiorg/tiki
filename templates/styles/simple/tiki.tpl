{* --- IMPORTANT: If you edit this (or any other TPL file) file via the Tiki built-in TPL editor (tiki-edit_templates.php), all the javascript will be stripped. This will cause problems. (Ex.: menus stop collapsing/expanding).

You should only modify header.tpl via a text editor through console, or ssh, or FTP edit commands. And only if you know what you are doing ;-)

You are most likely wanting to modify the top of your Tiki site. Please consider using Site Identity feature or modifying tiki-top_bar.tpl which you can do safely via the web-based interface.       --- *}<!DOCTYPE html PUBLIC
"-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
{if $metatag_keywords ne ''}		<meta name="keywords" content="{$metatag_keywords}" />{/if}
{if $metatag_author ne ''}		<meta name="author" content="{$metatag_author|escape}" />{/if}
{if $metatag_description ne ''}		<meta name="description" content="{$metatag_description}" />{/if}
{if $metatag_geoposition ne ''}		<meta name="geo.position" content="{$metatag_geoposition}" />{/if}
{if $metatag_georegion ne ''}		<meta name="geo.region" content="{$metatag_georegion}" />{/if}
{if $metatag_geoplacename ne ''}		<meta name="geo.placename" content="{$metatag_geoplacename}" />{/if}
{if $metatag_robots ne ''}		<meta name="robots" content="{$metatag_robots}" />{/if}
{if $metatag_revisitafter ne ''}		<meta name="revisit-after" content="{$metatag_revisitafter}" />{/if}

{* --- tikiwiki block --- *}
{php} include("lib/tiki-dynamic-js.php"); {/php}
		<script type="text/javascript" src="lib/tiki-js.js"></script>
{include file="bidi.tpl"}{* this is included for Right-to-left languages *}

{* --- page title block --- *}
{strip}
		<title>{if $trail}{breadcrumbs type="fulltrail" loc="head" crumbs=$trail}{else}{$siteTitle}
{if $page ne ''} : {$page|escape}
{elseif $headtitle} : {$headtitle}
{elseif $arttitle ne ''} : {$arttitle}
{elseif $title ne ''} : {$title}
{elseif $thread_info.title ne ''} : {$thread_info.title}
{elseif $post_info.title ne ''} : {$post_info.title}
{elseif $forum_info.name ne ''} : {$forum_info.name}
{elseif $categ_info.name ne ''} : {$categ_info.name}
{elseif $userinfo.login ne ''} : {$userinfo.login}
{/if}{/if}
		</title>
{/strip}

{* --- main CSS file --- *}
		<link rel="StyleSheet" media="all" href="styles/{$style}" type="text/css" />

{* --- favicon file --- *}
{if $favicon}		<link rel="icon" href="{$favicon}" />{/if}

{* --- phplayers block --- *}
{if $feature_phplayers eq 'y'}
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
{if $feature_trackers eq 'y' and $rss_tracker eq 'y'}
		<link rel="alternate" type="application/xml" title="{tr}RSS Trackers{/tr}" href="tiki-tracker_rss.php?ver={$rssfeed_default_version}" />
{/if}
{* ---- END of blocks ---- *}

{if $headerlib}{$headerlib->output_headers()}{/if}

</head>

{* ---- BODY ---- *}
	<body{if $user_dbl eq 'y' and $dblclickedit eq 'y' and $tiki_p_edit eq 'y'} ondblclick="location.href='tiki-editpage.php?page={$page|escape:"url"}';"{/if}>
{if $minical_reminders>100}
		<iframe style="width: 0; height: 0; border: 0" src="tiki-minical_reminders.php"></iframe>
{/if}
	
{if $feature_community_mouseover}{popup_init src="lib/overlib.js"}{/if}

{if $feature_siteidentity eq 'y'}
{* Site identity header section *}
		<div id="header">
			{include file="tiki-site_header.tpl"}
		</div>
{/if}

{* main content follows here *}
{if $feature_bidi eq 'y'}
		<div dir="rtl">
{/if}
			<div id="main"><!-- START of main content, please don't remove otherwise it breaks the layout in FF -->
  {if $feature_top_bar eq 'y'}
				<div id="tiki-top">
    {include file="tiki-top_bar.tpl"}
				</div>
  {/if}

  {if $feature_left_column eq 'user' or $feature_right_column eq 'user'}
			<div id="tiki-columns">
      {if $feature_left_column eq 'user'}
				<span style="float: left"><a class="flip" href="javascript:icntoggle('col2');">
					<img name="leftcolumnicn" class="colflip" src="img/icons/ofo.gif" border="0" alt="+/-" />&nbsp;{tr}Show/Hide Left Menus{/tr}&nbsp;</a>
				</span>
      {/if}
      {if $feature_right_column eq 'user'}
				<span style="float: right"><a class="flip" href="javascript:icntoggle('col3');">
					<img name="rightcolumnicn" class="colflip" src="img/icons/ofo.gif" border="0" alt="+/-" />&nbsp;{tr}Show/Hide Right Menus{/tr}&nbsp;</a>
				</span>
      {/if}
				<br style='clear: both' />
			</div>
  {/if}

		<div id="wrapper">

			<div id="col1">
				<div class="colwrapper">
					<div class="content{if $feature_left_column ne 'n'} marginleft{/if}{if $feature_right_column ne 'n'} marginright{/if}">
      {$mid_data}
					</div>
				</div>
			</div>

      {if $feature_left_column ne 'n'}
			<div id="col2">
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
          {if $feature_left_column eq 'user'}
            {literal}
							<script type="text/javascript">
								setfolderstate("leftcolumn");
							</script>
            {/literal}
          {/if}
			</div>
      {/if}

		</div><!-- END of wrapper -->

      {if $feature_right_column ne 'n'}
				<div id="col3">
      {section name=homeix loop=$right_modules}
      {$right_modules[homeix].data}
      {/section}
          {if $feature_right_column eq 'user'}
					<img src="images/none.gif" height="0" />
            {literal}
							<script type="text/javascript">
								setfolderstate("rightcolumn");
							</script>
            {/literal}
          {/if}
				</div>
      {/if}

	</div><!-- END of main -->

  {if $feature_bot_bar eq 'y'}
	<div id="footer">
		<div class="footerbgtrap">
			<div class="content">
    {include file="tiki-bot_bar.tpl"}
			</div>
		</div>
	</div>
  {/if}
{if $feature_bidi eq 'y'}
</div>
{/if}

{if $tiki_p_admin eq 'y' and $feature_debug_console eq 'y'}
  {* Include debugging console. Note it should be processed as near as possible to the end of file *}

  {php}  include_once("tiki-debug_console.php"); {/php}
  {include file="tiki-debug_console.tpl"}

{/if}
{if $lastup}
		<div style="font-size:x-small;text-align:center;color:#999;">{tr}Last update from CVS{/tr}: {$lastup|tiki_long_datetime}</div>
{/if}
	</body>
</html>
