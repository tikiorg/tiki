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
	
	//set prefs now if they have not been set before
	if (!isset($prefs['user_theme'])){
		$prefs['user_theme'] = '';
	}
	if (!isset($prefs['user_theme_option'])){
		$prefs['user_theme_option'] = '';
	}
	
	//first lets get the current theme.
	$current_theme = empty($tc_theme) ? $prefs['user_theme'] : $tc_theme;
	$current_theme_option = empty($tc_theme_option) ? !empty($tc_theme) ? $prefs['user_theme_option'] : '' : $tc_theme_option;
	
	$smarty->assign('tc_theme', $tc_theme);
	$smarty->assign('current_theme', $current_theme);
	$smarty->assign('current_theme_option', $current_theme_option);
	
	//get the list of available themes
	$available_themes = array();
	if (count($prefs['available_themes'] != 0) and !empty($prefs['available_themes'][0])) { //if pref['available_themes'] is set, than use it
		$available_themes = array_combine($prefs['available_themes'],$prefs['available_themes']);
	}
	else {
		$available_themes = $themelib->list_themes(); //else load all themes
		unset($available_themes['custom_url']); //remove Custom URL from the list
	}
	
	//collect options for the currently set theme
	$available_theme_options = $themelib->list_theme_options($prefs['user_theme']);
	
	$smarty->assign('available_themes', $available_themes);
	$smarty->assign('available_theme_options', $available_theme_options) ;

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
