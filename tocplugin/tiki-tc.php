<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));
if ($prefs['feature_theme_control'] == 'y' && empty($_SESSION['try_theme'])) {
	//we arrive here after lib/setup/theme.php has finished, so $prefs['theme'] and $prefs['theme_active_option'] are already set. Here we want to overwrite them according to the theme control setting
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
		$themesetup_path = $themelib->get_theme_path($prefs['theme']);
		$headerlib->drop_cssfile("{$themesetup_path}css/{$prefs['theme']}.css"); //drop main theme css
		$headerlib->drop_cssfile("{$themesetup_path}css/custom.css"); //drop main theme custom css
		if (!empty($prefs['theme_option'])){
			$themesetup_path = $themelib->get_theme_path($prefs['theme'], $prefs['theme_option']);
			$headerlib->drop_cssfile("{$themesetup_path}css/{$prefs['theme_option']}.css"); //drop option css
			$headerlib->drop_cssfile("{$themesetup_path}css/custom.css"); //drop option custom css
		}
		
		//ADD new css files (theme, theme_option and custom.css)
		$tc_theme_path = $themelib->get_theme_path($tc_theme);
		$headerlib->add_cssfile("{$tc_theme_path}css/{$tc_theme}.css"); //add main theme css
		if (!empty($tc_theme_option)){ //add theme option css
			$tc_theme_path = $themelib->get_theme_path($tc_theme, $tc_theme_option);
			$headerlib->add_cssfile("{$tc_theme_path}css/{$tc_theme_option}.css");
		}
		if (!empty($tc_theme_option)){ //add main theme custom css in case of theme option
			$tc_main_theme_path = $themelib->get_theme_path($tc_theme);
			$tc_main_custom_css = "{$tc_main_theme_path}css/custom.css";
			if (is_readable($tc_main_custom_css)) {
				$headerlib->add_cssfile($tc_main_custom_css, 53);
			}
		}
		$tc_custom_css = "{$tc_theme_path}css/custom.css"; //add custom css (can be a main theme or theme option)
		if (is_readable($tc_custom_css)) {
			$headerlib->add_cssfile($tc_custom_css, 53);
		}
		
		//RESET IE specific CSS
		global $style_ie8_css, $style_ie9_css;
		$style_ie8_css = $themelib->get_theme_path($tc_theme, $tc_theme_option, 'ie8.css');
		$style_ie9_css = $themelib->get_theme_path($tc_theme, $tc_theme_option, 'ie9.css');

		//RESET $theme_path global variable
		$theme_path = $tc_theme_path;

		//RESET $iconset according to the new theme
		$iconset = TikiLib::lib('iconset')->getIconsetForTheme($tc_theme, $tc_theme_option);

		//RESET theme prefs
		$prefs['theme'] = $tc_theme;
		$prefs['theme_option'] = $tc_theme_option;
	}
}
