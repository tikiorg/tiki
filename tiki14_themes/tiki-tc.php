<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));
if ($prefs['feature_theme_control'] == 'y') {
	//we arrive here after lib/setup/theme.php has finished, so $prefs['theme_active'] and $prefs['theme_active_option'] are already set. Here we want to overwrite them according to the theme control setting
	// defined: $cat_type and cat_objid
	// search for theme for $cat_type
	// then search for theme for md5($cat_type.cat_objid)
	global $prefs, $iconset;
	$themelib = TikiLib::lib('theme');
	$themecontrollib = TikiLib::lib('themecontrol');
	$categlib = TikiLib::lib('categ');
	
	//Step 1: lets see if there is any theme control setting for the current object	
	if (!isset($cat_type) ) $cat_type = '';
	if (!isset($cat_objid) ) $cat_objid = '';
	
	list($tc_theme, $tc_theme_option) = $themecontrollib->get_tc_theme($cat_type, $cat_objid); //this function returns $tc_theme and $tc_theme_option
	
	if ($cat_type == 'trackeritem' && empty($tc_theme)) {
		$trackerId = $themecontrollib->table('tiki_tracker_items')->fetchOne('trackerId', array('itemId' => $cat_objid));
		$themecontrollib->get_tc_theme('tracker', $trackerId);
	}

	//Step 2: if at least tc_theme is not empty, than we have a setting, so continue
	if ($tc_theme) {
		if ($prefs['feature_theme_control_savesession'] == 'y' && !empty($tc_theme_option)) {
			$_SESSION['tc_theme'] = $tc_theme_option;
		}
		//DROP css files (theme, theme_option and custom.css) added by lib/setup/theme.php that became unnecessary now that we have tc_theme
		$themesetup_path = $themelib->get_theme_path($prefs['theme_active'], NULL, NULL);
		$headerlib->drop_cssfile("{$themesetup_path}css/tiki.css"); //drop main theme css
		if (!empty($prefs['theme_option_active'])){
			$themesetup_path = $themelib->get_theme_path($prefs['theme_active'], $prefs['theme_option_active'], NULL);
			$headerlib->drop_cssfile("{$themesetup_path}css/tiki.css"); //drop option css
		}
		$headerlib->drop_cssfile("{$themesetup_path}css/custom.css"); //drop custom css
		
		//ADD new css files (theme, theme_option and custom.css)
		$tc_theme_path = $themelib->get_theme_path($tc_theme , NULL, NULL);
		$headerlib->add_cssfile("{$tc_theme_path}css/tiki.css");
		if (!empty($tc_theme_option)){
			$tc_theme_path = $themelib->get_theme_path($tc_theme, $tc_theme_option, NULL);
			$headerlib->add_cssfile("{$theme_path}css/tiki.css");
		}
		$tc_custom_css = "{$tc_theme_path}css/custom.css";
		if (is_readable($tc_custom_css)) {
			$headerlib->add_cssfile($tc_custom_css, 53);
		}
		
		//RESET IE specific CSS
		global $style_ie8_css, $style_ie9_css, $style_base;
		$style_ie8_css = $themelib->get_theme_path($tc_theme, $tc_theme_option, 'ie8.css');
		$style_ie9_css = $themelib->get_theme_path($tc_theme, $tc_theme_option, 'ie9.css');

		//RESET $theme_path global smarty variable
		$smarty->assign_by_ref('theme_path', $tc_theme_path);
		
		//RESET $iconset according to the new theme
		$iconset = $themelib->get_iconset($tc_theme, $tc_theme_option);
		$smarty->assign_by_ref('iconset', $iconset);
		
		//RESET $theme_active prefs
		$prefs['theme_active'] = $tc_theme;
		$prefs['theme_option_active'] = $tc_theme_option;
		
		//TODO: get custom smarty templates (smarty.php already finished before tiki-tc.php started)
	}
}
