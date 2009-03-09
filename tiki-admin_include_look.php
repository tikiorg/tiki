<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_look.php,v 1.1.2.8 2008-03-03 20:23:58 nyloth Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (!isset($prefs['site_style'])) {	// what am i missing here? shouldn't these get set earlier?
	$prefs = array_merge($prefs, $tikilib->get_preferences('site_style%'));
}
$a_style = $prefs['site_style'];

if (isset($_REQUEST["looksetup"])) {
    ask_ticket('admin-inc-look');

	if (isset($_REQUEST["site_style"])) {
	    check_ticket('admin-inc-general');
	    simple_set_value("site_style", "style");
		simple_set_value("site_style", "site_style");
		if (!isset($_REQUEST["site_style_option"]) || $_REQUEST["site_style_option"] == tra('None')) {	// style has no options
			$_REQUEST["site_style_option"] = '';
		}
	    check_ticket('admin-inc-general');
		simple_set_value("site_style_option", "style_option");
		simple_set_value("site_style_option", "site_style_option");
	}

    $pref_toggles = array(
	"feature_bot_bar",
	"feature_bot_bar_debug",
	"feature_bot_bar_icons",
	"feature_bot_bar_rss",
	'feature_bot_bar_power_by_tw',
	"feature_edit_templates",
	"feature_editcss",
	"feature_tabs",
	"feature_theme_control",
	"feature_top_bar",
	"feature_view_tpl",
	"layout_section",
	"feature_sitemycode",
	"feature_breadcrumbs",
    "feature_siteidentity",
	"feature_siteloclabel",
	"feature_sitelogo",
	"feature_sitesubtitle",
	"feature_sitenav",
	"feature_sitesearch",
	"feature_site_login",
	"feature_sitemenu",
	"feature_topbar_version",
	"feature_topbar_date",
	"feature_topbar_debug",
	"sitemycode_publish",
	"feature_bot_logo",
	'feature_menusfolderstyle',
	'direct_pagination',
	"nextprev_pagination",
	"pagination_firstlast",
	"pagination_icons",
	"pagination_fastmove_links",
	"use_context_menu_icon",
	"use_context_menu_text",
	"feature_site_report",
	"feature_site_send_link",
	"change_theme",
	"feature_jquery_ui",
	"feature_jquery_tooltips",
	'feature_jquery_autocomplete',
	'feature_jquery_superfish',
	'feature_jquery_reflection',
    'feature_jquery_sheet',
    );

    foreach ($pref_toggles as $toggle) {
        simple_set_toggle ($toggle);
    }

    $pref_simple_values = array(
	"sitelogo_src",
	"sitelogo_bgcolor",
	"sitelogo_bgstyle",
	"sitelogo_title",
	"sitelogo_alt",
	"sitetitle",
	"sitesubtitle",
	"sitemycode",
	"site_favicon",
	"site_favicon_type",
	"feature_topbar_id_menu",
	"feature_topbar_custom_code",
	"sitenav",
	"bot_logo_code",
	"transition_style_ver",
	"direct_pagination_max_middle_links",
	"direct_pagination_max_ending_links",
	'feature_site_report_email',
	'feature_endbody_code',
	'users_prefs_theme',
	'jquery_effect',
    'jquery_effect_direction',
    'jquery_effect_speed',
    'jquery_effect_tabs',
    'jquery_effect_tabs_direction',
    'jquery_effect_tabs_speed',
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
        "sitelogo_align",
    );

    foreach ($pref_byref_values as $britem) {
        byref_set_value ($britem);
    }
    
} else {	// just changed theme menu, so refill options
	if (isset($_REQUEST["site_style"]) && $_REQUEST["site_style"] != '') {
		$a_style = $_REQUEST["site_style"];
	}
}

$smarty->assign_by_ref( "styles", $tikilib->list_styles());
$smarty->assign('a_style', $a_style);
$smarty->assign_by_ref( "style_options", $tikilib->list_style_options($a_style));

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

if (isset($_REQUEST["looksetup"])) {
	for ($i = 0; $i < count($tikifeedback); $i++) {
		if (substr($tikifeedback[$i]['name'], 0, 10) == 'site_style') {	// if site_style or site_style_option
			// If the theme has changed, reload the page to use the new theme
			$location= 'location: tiki-admin.php?page=look';
			if ($prefs['feature_tabs'] == 'y') {
				$location .= "&cookietab=".$_COOKIE['tab'];
			}
			header($location);
			exit;
		}
	}
}
?>
