{* --- IMPORTANT: If you edit this (or any other TPL file) file via the Tiki built-in TPL editor (tiki-edit_templates.php), all the javascript will be stripped. This will cause problems. (Ex.: menus stop collapsing/expanding).

You should only modify header.tpl via a text editor through console, or ssh, or FTP edit commands. And only if you know what you are doing ;-)

You are most likely wanting to modify the top of your Tiki site. Please consider using Site Identity feature or modifying tiki-top_bar.tpl which you can do safely via the web-based interface.       --- *}<!DOCTYPE html PUBLIC
"-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
{if $prefs.metatag_keywords ne ''}
		<meta name="keywords" content="{$prefs.metatag_keywords}" />
{/if}
{if $prefs.metatag_author ne ''}
		<meta name="author" content="{$prefs.metatag_author|escape}" />
{/if}
{if $prefs.metatag_description ne ''}
		<meta name="description" content="{$prefs.metatag_description}" />
{/if}
{if $prefs.metatag_geoposition ne ''}
		<meta name="geo.position" content="{$prefs.metatag_geoposition}" />
{/if}
{if $prefs.metatag_georegion ne ''}
		<meta name="geo.region" content="{$prefs.metatag_georegion}" />
{/if}
{if $prefs.metatag_geoplacename ne ''}
		<meta name="geo.placename" content="{$prefs.metatag_geoplacename}" />
{/if}
{if $prefs.metatag_robots ne ''}
		<meta name="robots" content="{$prefs.metatag_robots}" />
{/if}
{if $prefs.metatag_revisitafter ne ''}
		<meta name="revisit-after" content="{$prefs.metatag_revisitafter}" />
{/if}

{* --- tikiwiki block --- *}		{php} include("lib/tiki-dynamic-js.php"); {/php}
		<script type="text/javascript" src="lib/tiki-js.js"></script>
{include file="bidi.tpl"}{* this is included for Right-to-left languages *}

{* --- page title block --- *}
		<title>{strip}{if $trail}{breadcrumbs type="fulltrail" loc="head" crumbs=$trail}{else}{$prefs.siteTitle}
{if $page ne ''} : {$page|escape}
{elseif $headtitle} : {$headtitle}
{elseif $arttitle ne ''} : {$arttitle}
{elseif $title ne ''} : {$title}
{elseif $thread_info.title ne ''} : {$thread_info.title}
{elseif $post_info.title ne ''} : {$post_info.title}
{elseif $forum_info.name ne ''} : {$forum_info.name}
{elseif $categ_info.name ne ''} : {$categ_info.name}
{elseif $userinfo.login ne ''} : {$userinfo.login}
{/if}{/if}{/strip}</title>

{* --- main CSS file --- *}		<link rel="StyleSheet" media="all" href="styles/{$prefs.style}" type="text/css" />

{* --- favicon file --- *}{if $prefs.site_favicon}		<link rel="icon" href="{$prefs.site_favicon}" />{/if}

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
{* ---- END of blocks ---- *}

{if $headerlib}{$headerlib->output_headers()}{/if}

	</head>

{* ---- BODY ---- *}
	<body{if $user_dbl eq 'y' and $prefs.dblclickedit eq 'y' and $tiki_p_edit eq 'y'} ondblclick="location.href='tiki-editpage.php?page={$page|escape:"url"}';"{/if} onload="{if $prefs.feature_tabs eq 'y'}tikitabs({if $cookietab neq ''}{$cookietab}{else}1{/if},5);{/if}{if $prefs.show_comzone eq 'y'} javascript:flip('comzone');{/if}">
{if $prefs.minical_reminders>100}{* TODO: replace the iframe with something xhtml strict compatible *}
		<iframe style="width: 0; height: 0; border: 0" src="tiki-minical_reminders.php"></iframe>
{/if}
	
{if $prefs.feature_community_mouseover}		{popup_init src="lib/overlib.js"}{/if}

{* main content follows here *}
		<div id="main"{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}><!-- START of main wrapper -->

			<!--  here can be placed start tag of an optional extra div wrapper, e.g. for look'n'feel fancy stuff -->

{if $prefs.feature_siteidentity eq 'y'}
{* Site identity header section *}
				<div id="header"><!-- START of header -->
	{include file="tiki-site_header.tpl"}
				</div><!-- END of header -->
{/if}

{if $prefs.feature_top_bar eq 'y'}
				<div id="tiki-top"><!-- START of Tiki top bar -->
	{include file="tiki-top_bar.tpl"}
				</div><!-- END of Tiki top bar -->
{/if}

{if $prefs.feature_left_column eq 'user' or $prefs.feature_right_column eq 'user'}
				<div id="tiki-columns"><!-- START of Tiki columns switchers -->
	{if $prefs.feature_left_column eq 'user'}
					<span style="float: left"><a class="flip" 
					href="#" onclick="toggleCols('col2','left'); return false">{tr}Show/Hide Left Menus{/tr}</a>
					</span>
	{/if}
	{if $prefs.feature_right_column eq 'user'}
					<span style="float: right"><a class="flip"
					href="#" onclick="toggleCols('col3','right'); return false">{tr}Show/Hide Right Menus{/tr}</a>
					</span>
	{/if}
					<br style='clear: both' />
				</div><!-- END of Tiki columns switchers -->
{/if}

				<div id="middle"><!-- START of middle part wrapper -->

					<div id="c1c2"><!-- START of column 1 and column 2 holder -->

						<div id="wrapper"><!-- START of column 1 wrapper -->
							<div id="col1" class="{if $prefs.feature_left_column ne 'n'} marginleft{/if}{if $prefs.feature_right_column ne 'n'} marginright{/if}">

								<div class="content">
{$mid_data}
								</div>

							</div><!-- END of column 1 -->
						</div><!-- END of column1 wrapper -->

	{if $prefs.feature_left_column ne 'n'}
						<div id="col2"><!-- START of column 2 -->
		{section name=homeix loop=$left_modules}
			{$left_modules[homeix].data}
		{/section}
		{if $prefs.feature_left_column eq 'user'}
			{literal}
							<script type="text/javascript">
								setfolderstate("leftcolumn");
							</script>
			{/literal}
		{/if}
						</div><!-- END of column 2 -->
	{/if}
					</div><!-- END of column 1 and column 2 holder -->

	{if $prefs.feature_right_column ne 'n'}
					<div id="col3"><!-- START of column 3 -->
		{section name=homeix loop=$right_modules}
			{$right_modules[homeix].data}
		{/section}
		{if $prefs.feature_right_column eq 'user'}
			{literal}
						<script type="text/javascript">
							setfolderstate("rightcolumn");
						</script>
			{/literal}
		{/if}
					</div><!-- END of column 3 -->
	{/if}
	
				</div><!-- END of middle part wrapper -->

	{if $prefs.feature_bot_bar eq 'y'}
				<div id="footer"><!-- START of footer -->
					<div class="footerbgtrap">
						<div class="content">
		{include file="tiki-bot_bar.tpl"}
						</div>
					</div>
				</div><!-- END of footer -->
	{/if}

			<!--  here can be end of an optional extra div wrapper, e.g. for look'n'feel fancy stuff -->
			
		</div><!-- END of main wrapper -->

	{if $tiki_p_admin eq 'y' and $prefs.feature_debug_console eq 'y'}
	{* Include debugging console. Note it should be processed as near as possible to the end of file *}

		{php} include_once("tiki-debug_console.php"); {/php}
		{include file="tiki-debug_console.tpl"}

	{/if}
	{if $lastup}
		<div style="font-size: x-small; text-align: center; color: #999;">{tr}Last update from CVS{/tr}: {$lastup|tiki_long_datetime}</div>
	{/if}
	</body>
</html>
