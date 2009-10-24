<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_look.php,v 1.1.2.8 2008-03-03 20:23:58 nyloth Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
$a_style = $prefs['site_style'];
if (isset($_REQUEST["looksetup"])) {
	ask_ticket('admin-inc-look');
	if (isset($_REQUEST["site_style"])) {
		check_ticket('admin-inc-general');
		simple_set_value("site_style", "style");
		simple_set_value("site_style", "site_style");
		if (!isset($_REQUEST["site_style_option"]) || $_REQUEST["site_style_option"] == tra('None')) { // style has no options
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
		"feature_siteloclabel",
		"feature_sitelogo",
		"feature_sitenav",
		"feature_sitesearch",
		"feature_site_login",
		"feature_sitemenu",
		"feature_topbar_version",
		"feature_topbar_debug",
		"sitemycode_publish",
		"feature_bot_logo",
		'direct_pagination',
		"nextprev_pagination",
		"pagination_firstlast",
		"pagination_hide_if_one_page",
		"pagination_icons",
		"pagination_fastmove_links",
		"menus_items_icons",
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
		'feature_jquery_tablesorter',
		'feature_jquery_cycle',
		'feature_iepngfix',
		'feature_layoutshadows',
		'useGroupTheme'
	);
	foreach($pref_toggles as $toggle) {
		simple_set_toggle($toggle);
	}
	$pref_simple_values = array(
		"maxRecords",
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
		'feature_jquery_ui_theme',
		'available_styles',
		'iepngfix_selectors',
		'iepngfix_elements',
		'main_shadow_start',
		'main_shadow_end',
		'header_shadow_start',
		'header_shadow_end',
		'middle_shadow_start',
		'middle_shadow_end',
		'center_shadow_start',
		'center_shadow_end',
		'footer_shadow_start',
		'footer_shadow_end',
		'box_shadow_start',
		'box_shadow_end',
		'feature_custom_html_head_content',
		'feature_custom_center_column_header',
	);
	foreach($pref_simple_values as $svitem) {
		simple_set_value($svitem);
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
	foreach($pref_byref_values as $britem) {
		byref_set_value($britem);
	}
} else { // just changed theme menu, so refill options
	if (isset($_REQUEST["site_style"]) && $_REQUEST["site_style"] != '') {
		$a_style = $_REQUEST["site_style"];
	}
}
$styles = $tikilib->list_styles();
$smarty->assign_by_ref("styles", $styles);
$smarty->assign('a_style', $a_style);
$smarty->assign_by_ref("style_options", $tikilib->list_style_options($a_style));
/**
 * @param $stl - style file name (e.g. thenews.css)
 * @param $opt - optional option file name
 * @return string path to thumbnail file
 */
function get_thumbnail_file($stl, $opt = '') { // find thumbnail if there is one
	global $tikilib;
	if (!empty($opt) && $opt != tr('None')) {
		$filename = eregi_replace('\.css$', '.png', $opt); // change .css to .png
		
	} else {
		$filename = eregi_replace('\.css$', '.png', $stl); // change .css to .png
		$opt = '';
	}
	return $tikilib->get_style_path($stl, $opt, $filename);
}
// find thumbnail if there is one
$thumbfile = get_thumbnail_file($a_style, $prefs['site_style_option']);
if (!empty($thumbfile)) {
	$smarty->assign('thumbfile', $thumbfile);
}
if ($prefs['feature_jquery'] == 'y') {
	// hash of themes and their options and their thumbnail images
	$js = 'var style_options = {';
	foreach($styles as $s) {
		$js.= "\n'$s':['" . get_thumbnail_file($s, '') . '\',{';
		$options = $tikilib->list_style_options($s);
		if ($options) {
			foreach($options as $o) {
				$js.= "'$o':'" . get_thumbnail_file($s, $o) . '\',';
			}
			$js = substr($js, 0, strlen($js) - 1) . '}';
		} else {
			$js.= '}';
		}
		$js.= '],';
	}
	$js = substr($js, 0, strlen($js) - 1);
	$js.= '};';
	// JS to handle theme/option changes client-side
	// the var (style_options) has to be declared in the same block for AJAX call scope 
	$none = tr('None');
	$headerlib->add_js(<<<JS
$js

\$jq(document).ready( function() {
	// pick up theme drop-down change
	\$jq('#general-theme').change( function() {
		var ops = style_options[\$jq('#general-theme').val()];
		var none = true;
		\$jq('#general-theme-options').empty().attr('disabled','').attr('selectedIndex', 0);
		\$jq.each(ops[1], function(i, val) {
			\$jq('#general-theme-options').append(\$jq(document.createElement('option')).attr('value',i).text(i));
			none = false;
		});
		if (none) {
			\$jq('#general-theme-options').empty().attr('disabled','disabled').
					append(\$jq(document.createElement('option')).attr('value',"$none").text("$none"));
		}
	});
	\$jq('#general-theme').change( function() {
		var t = \$jq('#general-theme').val();
		var f = style_options[t][0];
		if (f) {
			\$jq('#style_thumb').fadeOut('fast').attr('src', f).fadeIn('fast').animate({'opacity': 1}, 'fast');
		} else {
			\$jq('#style_thumb').animate({'opacity': 0.3}, 'fast');
		}
	});
	\$jq('#general-theme-options').change( function() {
		var t = \$jq('#general-theme').val();
		var o = \$jq('#general-theme-options').val();
		var f = style_options[t][1][o];
		if (f) {
			\$jq('#style_thumb').fadeOut('fast').attr('src', f).fadeIn('fast').animate({'opacity': 1}, 'fast');
		} else {
			\$jq('#style_thumb').animate({'opacity': 0.3}, 'fast');
		}
	});
});
JS
	);
}
// Get list of available slideshow styles
$slide_styles = array();
$h = opendir("styles/slideshows");
while ($file = readdir($h)) {
    if (strstr($file, "css")) {
        $slide_styles[] = $file;
    }
}
closedir($h);
$smarty->assign_by_ref("slide_styles", $slide_styles);
if (isset($_REQUEST["looksetup"])) {
    for ($i = 0, $count_feedback = count($tikifeedback); $i < $count_feedback; $i++) {
        if (substr($tikifeedback[$i]['name'], 0, 10) == 'site_style') { // if site_style or site_style_option
            // If the theme has changed, reload the page to use the new theme
            $location = 'location: tiki-admin.php?page=look';
            if ($prefs['feature_tabs'] == 'y') {
                $location.= "&cookietab=" . $_COOKIE['tab'];
            }
            header($location);
            exit;
        }
    }
}
