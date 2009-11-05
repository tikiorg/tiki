<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-tc.php,v 1.15.2.2 2007-12-18 01:06:44 nkoth Exp $
//this script may only be included - so its better to die if called directly.
//smarty is not there - we need setup
require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));
if ($prefs['feature_theme_control'] == 'y') {
	// defined: $cat_type and cat_objid
	// search for theme for $cat_type
	// then search for theme for md5($cat_type.cat_objid)
	include_once ('lib/themecontrol/tcontrol.php');
	include_once ('lib/categories/categlib.php');
	global $tc_theme;
	$tc_theme = '';
	//SECTIONS
	if (isset($section)) {
		$tc_theme = $tcontrollib->tc_get_theme_by_section($section);
		list($tc_theme, $tc_theme_option) = $tcontrollib->parse_theme_option_string($tc_theme);
	}
	// CATEGORIES
	if (isset($cat_type) && isset($cat_objid)) {
		$tc_categs = $categlib->get_object_categories($cat_type, $cat_objid);
		if (count($tc_categs)) {
			foreach($tc_categs as $cat) {
				if ($cat_theme = $tcontrollib->tc_get_theme_by_categ($cat)) {
					list($tc_theme, $tc_theme_option) = $tcontrollib->parse_theme_option_string($cat_theme);
					
					$catt = $categlib->get_category($cat);
					$smarty->assign_by_ref('category', $catt["name"]);
					break;
				}
			}
		}
	}
	// OBJECTS - if object has been particularly set, override SECTION or CATEGORIES $tc_theme
	// if not set, make sure we don't squash whatever $tc_theme may have been
	if (isset($cat_type) && isset($cat_objid)) {
		if ($obj_theme = $tcontrollib->tc_get_theme_by_object($cat_type, $cat_objid)) {
			list($tc_theme, $tc_theme_option) = $tcontrollib->parse_theme_option_string($obj_theme);
		}
	}
	if ($tc_theme) {
		if ($tikidomain and is_file("styles/$tikidomain/$tc_theme")) {
			$headerlib->drop_cssfile('styles/' . $tikidomain . '/' . $prefs['style']);
			$headerlib->add_cssfile('styles/' . $tikidomain . '/' . $tc_theme, 51);
		} else {
			$headerlib->drop_cssfile('styles/' . $prefs['style']);
			$headerlib->add_cssfile('styles/' . $tc_theme, 51);
		}
		$style_base = $tikilib->get_style_base($tc_theme);
		$site_style_base = $tikilib->get_style_base($prefs['style']);
		
		if (empty($tc_theme_option)) { // special handling for 'None' case
			if ($tikidomain) {
				$headerlib->drop_cssfile('styles/' . $tikidomain . $site_style_base . '/options/' . $prefs['style_option']);
			} else {
				$headerlib->drop_cssfile('styles/' . $site_style_base . '/options/' . $prefs['style_option']);
			}
		} else if ($tc_theme_option) {
			if ($tikidomain and (is_file("styles/$tikidomain/$style_base/options/$tc_theme_option"))) {
				$headerlib->drop_cssfile("styles/$tikidomain/$site_style_base/options/" . $prefs['style_option']);
				$headerlib->add_cssfile("styles/$tikidomain/$style_base/options/$tc_theme_option", 52);
			} else {
				$headerlib->drop_cssfile("styles/$site_style_base/options/" . $prefs['style_option']);
				$headerlib->add_cssfile("styles/$style_base/options/$tc_theme_option", 52);
			}
		}
	}
}
