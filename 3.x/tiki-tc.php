<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-tc.php,v 1.15.2.2 2007-12-18 01:06:44 nkoth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

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
	}

	// CATEGORIES
	if (isset($cat_type) && isset($cat_objid)) {
		$tc_categs = $categlib->get_object_categories($cat_type, $cat_objid);

		if (count($tc_categs)) {
			$cat_themes = array();	// collect all the category themes
			foreach ($tc_categs as $cat) {
				$ct = $tcontrollib->tc_get_theme_by_categ($cat);
				if (!empty($ct) && !in_array($ct, $cat_themes)) {
					$cat_themes[] = $ct;
					if (!isset($catt)) {
						$catt=$categlib->get_category($cat);
						$smarty->assign_by_ref('category', $catt["name"]);
						//break;
					}
				}
			}
			if (count($cat_themes) == 1) {	// only use category theme if there is exactly one set
				$p = strpos($cat_themes[0], '/');	// theme option starts after a / char
				if ($p === false) {
					$tc_theme = $cat_themes[0];
					$tc_theme_option = '';
				} else {
					$tc_theme = substr($cat_themes[0], 0, $p);
					$tc_theme_option = substr($cat_themes[0], $p+1);
				}	
			}
		}
	}

    // OBJECTS - if object has been particularly set, override SECTION or CATEGORIES $tc_theme
	// if not set, make sure we don't squash whatever $tc_theme may have been
    if (isset($cat_type) && isset($cat_objid)) {
        if( $obj_theme = $tcontrollib->tc_get_theme_by_object($cat_type, $cat_objid) ) {
            $tc_theme = $obj_theme;
        }
    }
										
	if ($tc_theme) {
		if ($tikidomain and is_file("styles/$tikidomain/$tc_theme")) {
			$headerlib->drop_cssfile('styles/'.$tikidomain.'/'.$prefs['style']);
			$headerlib->add_cssfile('styles/'.$tikidomain.'/'.$tc_theme,51);
		} else {
			$headerlib->drop_cssfile('styles/'.$prefs['style']);
			$headerlib->add_cssfile('styles/'.$tc_theme,51);
		}
		
		$style_base = $tikilib->get_style_base($tc_theme);
	
		if (empty($tc_theme_option)) {	// special handling for 'None' case
			if ($tikidomain) {
				$headerlib->drop_cssfile('styles/'.$style_base.'/options/'.$tikidomain.'/'.$prefs['style_option']);
			} else {
				$headerlib->drop_cssfile('styles/'.$style_base.'/options/'.$prefs['style_option']);
			}
		} else if ($tc_theme_option) {
			if ($tikidomain and (is_file("styles/$tikidomain/$style_base/options/$tc_theme_option" || $tc_theme_option == ''))) {
				$headerlib->drop_cssfile("styles/$tikidomain/".$prefs['style_option']);
				$headerlib->add_cssfile("styles/$tikidomain/$style_base/options/$tc_theme_option",52);
			} else {
				$headerlib->drop_cssfile("styles/$style_base/options/".$prefs['style_option']);
				$headerlib->add_cssfile("styles/$style_base/options/$tc_theme_option",52);
			}
		}
		// Reset IE specific CSS
		global $style_ie6_css, $style_ie7_css, $style_ie8_css;
		$style_ie6_css = $tikilib->get_style_path($tc_theme, $tc_theme_option, 'ie6.css');
		$style_ie7_css = $tikilib->get_style_path($tc_theme, $tc_theme_option, 'ie7.css');
		$style_ie8_css = $tikilib->get_style_path($tc_theme, $tc_theme_option, 'ie8.css');
	}
}

?>