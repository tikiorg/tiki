<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_look.php,v 1.1.2.4 2008-01-21 05:28:28 luciash Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["site_style"])) {
    check_ticket('admin-inc-general');
    simple_set_value("site_style", "style");
}

if (isset($_REQUEST["looksetup"])) {
ask_ticket('admin-inc-look');

    $pref_toggles = array(
	"feature_bot_bar",
	"feature_bot_bar_debug",
	"feature_bot_bar_icons",
	"feature_bot_bar_rss",
	"feature_edit_templates",
	"feature_editcss",
	"feature_tabs",
	"feature_theme_control",
	"feature_top_bar",
	"feature_view_tpl",
	"layout_section",
  			        "feature_sitemycode",
				"feature_breadcrumbs",
				"feature_siteloclabel",
				"feature_sitelogo",
				"feature_sitenav",
				"feature_sitead",
				"feature_sitesearch",
				"feature_sitemenu",
				"feature_topbar_version",
				"feature_topbar_date",
				"feature_topbar_debug",
				"sitemycode_publish",
				"sitead_publish",
				"feature_bot_logo"
    );

    foreach ($pref_toggles as $toggle) {
        simple_set_toggle ($toggle);
    }

 	$pref_simple_values = array(
				"sitelogo_src",
				"sitelogo_bgcolor",
				"sitelogo_title",
				"sitelogo_alt",
				"sitemycode",
				"sitead",
		        "site_favicon",
        		"site_favicon_type",
				"feature_topbar_id_menu",
				"sitenav",
				"bot_logo_code",
				"transition_style_ver"
    );

    foreach ($pref_simple_values as $svitem) {
        simple_set_value ($svitem);
    }

    $pref_byref_values = array(
	"feature_left_column",
	"feature_right_column",
    "slide_style",
    "feature_siteloc",
        "feature_sitetitle",
        "feature_sitedesc",
        "sitelogo_align"
    
    );

    foreach ($pref_byref_values as $britem) {
        byref_set_value ($britem);
    }
    
}

$llist = $tikilib->list_styles();
$smarty->assign_by_ref( "styles", $llist);

// Get list of available slideshow styles
$slide_styles = array();
$h = opendir("styles/slideshows");
while ($file = readdir($h)) {
    if (strstr($file, "css")) {
        $slide_styles[] = $file;
    }
}
closedir ($h);

$smarty->assign_by_ref("slide_styles", $slide_styles);

if ( isset($_REQUEST["site_style"]) ) {
	// If the theme has changed, reload the page to use the new theme
	header("location: tiki-admin.php?page=look");
	exit;
}
?>
