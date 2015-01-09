<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * @return array
 */
function module_switch_theme_info()
{
	return array(
		'name' => tra('Switch Theme'),
		'description' => tra('Enables to quickly change the theme for the user.'),
		'prefs' => array('change_theme'),
		'params' => array()
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_switch_theme($mod_reference, &$module_params)
{
	global $prefs, $section, $group_theme, $tc_theme, $tc_theme_option;
	$smarty = TikiLib::lib('smarty');
	$themelib = TikiLib::lib('theme');
	
	//first lets get the current theme.
	$current_theme = empty($tc_theme) ? isset($prefs['users_prefs_theme']) : $tc_theme;
	$current_theme_option = empty($tc_theme_option) ? !empty($tc_theme) ? isset($prefs['users_prefs_theme-option']) : '' : $tc_theme_option;
	
	$smarty->assign('tc_theme', $tc_theme);
	$smarty->assign('current_theme', $current_theme);
	$smarty->assign('current_theme_option', $current_theme_option);
	
	//get the list of available themes and options
	$available_themesandoptions = $themelib->get_available_themesandoptions();
	$smarty->assign('available_themesandoptions', $available_themesandoptions);
	
	//check if CSS Editor's try theme is on 
	if (!empty($_SESSION['try_theme'])) {
		list($css_theme, $css_theme_option) = $themelib->extract_theme_and_option($_SESSION['try_theme']);
		$smarty->assign('css_theme', $css_theme);
		$smarty->assign('css_theme_option', $css_theme_option);
	}
	
	//themegenerator
	if ($prefs['themegenerator_feature'] === 'y') {
		include_once 'lib/prefs/themegenerator.php';
		$p = prefs_themegenerator_list();
		if (!empty($p['themegenerator_theme']['options'])) {
			$smarty->assign('themegen_list', array_keys($p['themegenerator_theme']['options']));
			$smarty->assign('themegenerator_theme', $prefs['themegenerator_theme']);
		}
	}

	$smarty->clear_assign('tpl_module_title'); // TPL sets dynamic default title
}
