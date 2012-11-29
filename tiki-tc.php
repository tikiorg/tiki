<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));
if ($prefs['feature_theme_control'] == 'y') {
	// defined: $cat_type and cat_objid
	// search for theme for $cat_type
	// then search for theme for md5($cat_type.cat_objid)
	include_once ('lib/themecontrol/tcontrol.php');
	include_once ('lib/categories/categlib.php');
	global $tc_theme, $tc_theme_option;
	
	if (isset($tc_theme)) {
		$old_tc_theme = $tc_theme;
		$tc_theme = '';
	} else {
		$old_tc_theme = '';
	}
	if (isset($tc_theme_option)) {
		$old_tc_theme_option = $tc_theme_option;
		$tc_theme_option = '';
	} else {
		$old_tc_theme_option = '';
	}
	//SECTIONS
	if (isset($section)) {
		$tc_theme = $tcontrollib->tc_get_theme_by_section($section);
		list($tc_theme, $tc_theme_option) = $tcontrollib->parse_theme_option_string($tc_theme);
	}
	$tcontrollib->get_theme($cat_type, $cat_objid, $tc_theme, $tc_theme_option);
	if ($cat_type == 'trackeritem' && empty($tc_theme)) {
		$trackerId = $tcontrollib->table('tiki_tracker_items')->fetchOne('trackerId', array('itemId' => $cat_objid));
		$tcontrollib->get_theme('tracker', $trackerId, $tc_theme, $tc_theme_option);
	}
	
	if ($tc_theme) {
		if ($old_tc_theme) {
			$headerlib->drop_cssfile($tikilib->get_style_path('', '', $old_tc_theme));
			$headerlib->drop_cssfile($tikilib->get_style_path($old_tc_theme, $old_tc_theme_option, $old_tc_theme_option));
		}
		$headerlib->drop_cssfile($tikilib->get_style_path('', '', $prefs['style']));
		$headerlib->add_cssfile($tikilib->get_style_path('', '', $tc_theme), 51);

		$headerlib->drop_cssfile($tikilib->get_style_path($prefs['style'], $prefs['style_option'], $prefs['style_option']));
		if (!empty($tc_theme_option)) { // special handling for 'None' case
			$headerlib->add_cssfile($tikilib->get_style_path($tc_theme, $tc_theme_option, $tc_theme_option), 52);
		}
		// Reset IE specific CSS
		global $style_ie6_css, $style_ie7_css, $style_ie8_css, $style_base;
		$style_ie6_css = $tikilib->get_style_path($tc_theme, $tc_theme_option, 'ie6.css');
		$style_ie7_css = $tikilib->get_style_path($tc_theme, $tc_theme_option, 'ie7.css');
		$style_ie8_css = $tikilib->get_style_path($tc_theme, $tc_theme_option, 'ie8.css');
		
		$style_base = $tikilib->get_style_base($tc_theme);
	}
}
