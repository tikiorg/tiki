<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
require_once ('tiki-setup.php');

include_once ('lib/structures/structlib.php');
include_once ('lib/wiki/wikilib.php');
include_once ('lib/wiki-plugins/wikiplugin_slideshow.php');

if ($prefs['feature_wiki'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("error_raw.tpl");
	die;
}

// Create the HomePage if it doesn't exist
if (!$tikilib->page_exists($prefs['wikiHomePage'])) {
	$tikilib->create_page($prefs['wikiHomePage'], 0, '', date("U"), 'Tiki initialization');
}

if (!isset($_SESSION["thedate"])) {
	$thedate = date("U");
} else {
	$thedate = $_SESSION["thedate"];
}

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$_REQUEST["page"] = $wikilib->get_default_wiki_page();
}
$page = $_REQUEST['page'];
$smarty->assign('page', $page);

// If the page doesn't exist then display an error
if (!($info = $tikilib->page_exists($page))) {
	include_once ('tiki-index.php');
	die;
}

if (isset($_REQUEST['theme'])) {
	print_r(getSlideshowTheme($_REQUEST['theme'], true));
	die; 
}

// Now check permissions to access this page
$tikilib->get_perm_object( $page, 'wiki page', $info);
if ($tiki_p_view != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied. You cannot view this page."));

	$smarty->display("error_raw.tpl");
	die;
}

// BreadCrumbNavigation here
// Remember to reverse the array when posting the array

if (!isset($_SESSION["breadCrumb"])) {
	$_SESSION["breadCrumb"] = array();
}

if (!in_array($page, $_SESSION["breadCrumb"])) {
	if (count($_SESSION["breadCrumb"]) > $prefs['userbreadCrumb']) {
		array_shift ($_SESSION["breadCrumb"]);
	}

	array_push($_SESSION["breadCrumb"], $page);
} else {
	// If the page is in the array move to the last position
	$pos = array_search($page, $_SESSION["breadCrumb"]);

	unset ($_SESSION["breadCrumb"][$pos]);
	array_push($_SESSION["breadCrumb"], $page);
}

// Now increment page hits since we are visiting this page
if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
	$tikilib->add_hit($page);
}

// Get page data
$info = $tikilib->get_page_info($page);
$pdata = $tikilib->parse_data_raw($info["data"]);

if (!isset($_REQUEST['pagenum']))
	$_REQUEST['pagenum'] = 1;

$pages = $wikilib->get_number_of_pages($pdata);
$pdata = $wikilib->get_page($pdata, $_REQUEST['pagenum']);
$smarty->assign('pages', $pages);

// Put ~pp~, ~np~ and <pre> back. --rlpowell, 24 May 2004
$tikilib->replace_preparse( $info["data"], $preparsed, $noparsed );
$tikilib->replace_preparse( $pdata, $preparsed, $noparsed );

$smarty->assign_by_ref('parsed', $pdata);
//$smarty->assign_by_ref('lastModif',date("l d of F, Y  [H:i:s]",$info["lastModif"]));
$smarty->assign_by_ref('lastModif', $info["lastModif"]);

if (empty($info["user"])) {
	$info["user"] = 'anonymous';
}

$smarty->assign_by_ref('lastUser', $info["user"]);

include_once ('tiki-section_options.php');

$headerlib->add_cssfile( 'lib/jquery/jquery.s5/jquery.s5.css' );
$headerlib->add_jsfile( 'lib/jquery/jquery.s5/jquery.s5.js' );
$headerlib->add_jq_onready( '
	window.s5Settings = (window.s5Settings ? window.s5Settings : {});
	$.s5.start($.extend(window.s5Settings, {
		menu: function() {
			return $("#tiki_slideshow_buttons").show();
		},
		noteMenu: function() {
			return $("#tiki_slideshowNote_buttons").clone().show();
		}
	}));
	$("#main").hide();
	
	if (window.s5Settings.listItemHighlightColor) {
		$.s5.slides().find("li").hover(function() {
			$(this)
				.css("color", window.s5Settings.listItemHighlightColor)
				.stop()
				.animate({
					fontSize: $.s5.sizeDetector.width() * 1.2
				});
		}, function() {
			$(this)
				.css("color", "")
				.stop()
				.animate({
					fontSize: "1em"
				});
		});
	}
	alert(window.s5Settings.themeName);
	$("#tiki-slideshow-theme")
		.val(window.s5Settings.themeName)
		.change(function() {
			var theme = $(this).val();
			if (theme) {
				$.get("tiki-slideshow.php", {theme: theme}, function(o) {
					theme = $.parseJSON(o);
					$.s5.makeTheme(theme.slidefontcolor, theme.headerfontcolor, theme.backgroundcolor, theme.backgroundimage);
				}); 
			}
		});
	
');

ask_ticket('index-raw');

$smarty->assign('is_slideshow' , 'y');
// Display the Index Template
$smarty->assign('dblclickedit', 'y');
$smarty->assign('mid','tiki-show_page_raw.tpl');

// use tiki_full to include include CSS and JavaScript
$smarty->display("tiki_full.tpl");
