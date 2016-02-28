<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
	
	//get the list of available themes and options
	$smarty->assign('available_themes', $themelib->get_available_themes());
	$smarty->assign('available_options', $themelib->get_available_options($prefs['theme']));
	
	//check if CSS Editor's try theme is on
	if (!empty($_SESSION['try_theme'])) {
		list($css_theme, $css_theme_option) = $themelib->extract_theme_and_option($_SESSION['try_theme']);
	} else {
		$css_theme = '';
	}
	
	if (!empty($tc_theme) ||
		!empty($group_theme) ||
		(($section === 'admin' || empty($section)) && !empty($prefs['theme_admin'])) ||
		!empty($css_theme))
	{
		$info_title = tra('Not allowed here') . ':' .
			tra('Displayed theme') . ': ' . $prefs['theme'] . (!empty($prefs['theme_option']) ? '/' . $prefs['theme_option'] : '');

		if (!empty($css_theme)) {
			$info_title .= ' (' . tra('Edit CSS') . ')';
		} else if (!empty($tc_theme)) {
			$info_title .= ' (' . tra('Theme Control') . ')';
		} else if (($section === 'admin' || empty($section)) && !empty($prefs['theme_admin'])) {
			$info_title .= ' (' . tra('Admin Theme') . ')';
		} else if ($group_theme) {
			$info_title .= ' (' . tra('Group theme') . ')';
		}

		$smarty->assign('switchtheme_enabled', false);
		$smarty->assign('info_title', $info_title);
	} else {
		$smarty->assign('switchtheme_enabled', true);
		$smarty->assign('info_title', '');
	}

	$smarty->clear_assign('tpl_module_title'); // TPL sets dynamic default title
}
