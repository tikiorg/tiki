<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_switch_theme_info() {
	return array(
		'name' => tra('Switch Theme'),
		'description' => tra('Enables to quickly change the theme.'),
		'prefs' => array( 'change_theme' ),
		'params' => array()
	);
}

function module_switch_theme( $mod_reference, $module_params ) {
	global $prefs, $user, $tikilib, $smarty, $tc_theme, $tc_theme_option;
	
	$current_style = empty($tc_theme) ? $prefs['style'] : $tc_theme;
	$current_style_option = empty($tc_theme_option) ? !empty($tc_theme) ? $prefs['style_option'] : '' : $tc_theme_option;

	if ( isset($_COOKIE['tiki-theme']) && !($prefs['feature_userPreferences'] == 'y' && $user && $prefs['change_theme'] == 'y') ){
		$current_style = $_COOKIE['tiki-theme'];
	}
	if ( isset($_COOKIE['tiki-theme-option']) && !($prefs['feature_userPreferences'] == 'y' && $user && $prefs['change_theme'] == 'y') ){
		$current_style_option = $_COOKIE['tiki-theme-option'];
	}
	
	$smarty->assign('tc_theme',$tc_theme);
	$smarty->assign('style',$current_style);
	$smarty->assign('style_option',$current_style_option);

	$smarty->assign('styleslist',$tikilib->list_styles());
	$smarty->assign( "style_options", $tikilib->list_style_options($current_style));

	if ($prefs['feature_themegenerator'] === 'y') {
		include_once 'lib/prefs/themegenerator.php';
		$p = prefs_themegenerator_list();
		if (!empty($p['themegenerator_theme']['options'])) {
			$smarty->assign('themegen_list', array_keys($p['themegenerator_theme']['options']));
			$smarty->assign('themegenerator_theme', $prefs['themegenerator_theme']);
		}
	}

	$smarty->clear_assign('tpl_module_title'); // TPL sets dynamic default title
}
