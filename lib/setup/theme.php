<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

//Initialize variables for the actual theme and theme option to be displayed
$theme_active = $prefs['theme'];
$theme_option_active = $prefs['theme_option'];

// User theme previously set up in lib/setup/user_prefs.php

//consider Group theme
if ($prefs['useGroupTheme'] == 'y') {
	$userlib = TikiLib::lib('user');
	$users_group_groupTheme = $userlib->get_user_group_theme();
	if (!empty($users_group_groupTheme)) {
		//group theme and option is stored in one column (groupTheme) in the users_groups table, so the theme and option value needs to be separated first
		list($group_theme, $group_theme_option) = $themelib->extract_theme_and_option($users_group_groupTheme); //for more info see list_themes_and_options() function in themelib

		//set active theme
		$theme_active = $group_theme;
		$theme_option_active = $group_theme_option;
		
		//set group_theme smarty variable so that it can be used elsewhere
		$smarty->assign_by_ref('group_theme', $users_group_groupTheme);
	}
}

//consider Admin Theme
if (!empty($prefs['theme_admin']) && ($section === 'admin' || empty($section))) {		// use admin theme if set
	$theme_active = $prefs['theme_admin'];
	$theme_option_active = $prefs['theme_option_admin'];								// and its option
}
	
//consider CSS Editor (tiki-edit_css.php) 
if (!empty($_SESSION['try_theme'])) {
	list($theme_active, $theme_option_active) = $themelib->extract_theme_and_option($_SESSION['try_theme']);
}

//START loading theme related items

//1) Always add default bootstrap JS and make some preference settings
$headerlib->add_jsfile('vendor/twitter/bootstrap/dist/js/bootstrap.js');
$headerlib->add_jsfile('lib/jquery_tiki/tiki-bootstrapmodalfix.js');

if ($prefs['feature_fixed_width'] === 'y') {
	$headerlib->add_css(
		'@media (min-width: 1200px) { .container { width:' .
		(!empty($prefs['layout_fixed_width']) ? $prefs['layout_fixed_width'] : '1170px') .
		'; } }'
	);
}

//2) Always add tiki_base.css. Add it first, so that it can be overriden in the custom themes
$headerlib->add_cssfile("themes/base_files/css/tiki_base.css");

//3) Always add bundled font-awesome css for the default icon fonts
$headerlib->add_cssfile('vendor/fortawesome/font-awesome/css/font-awesome.min.css');

//4) Add Addon custom css first, so it can be overridden by themes
foreach (TikiAddons::getPaths() as $path) {
	foreach (glob('addons/' . basename($path) . '/css/*.css') as $filename) {
		$headerlib->add_cssfile($filename);
	}
}

//5) Now add the theme or theme option
$themelib = TikiLib::lib('theme');

// compile a new CSS file using header_custom_less and using the real theme and the theme option
if (!empty($prefs['header_custom_less'])) {

	$cssfiles = $headerlib->compile_custom_less($prefs['header_custom_less'], $theme_active, $theme_option_active);
	foreach ($cssfiles as $cssfile) {
		$headerlib->add_cssfile($cssfile);
	}

} else if ($theme_active == 'custom_url' && !empty($prefs['theme_custom_url'])) { //custom URL, use only if file exists at the custom location
	$custom_theme = $prefs['theme_custom_url'];
	if (preg_match('/^(http(s)?:)?\/\//', $custom_theme)) { // Use external link if url begins with http://, https://, or // (auto http/https)
		$headerlib->add_cssfile($custom_theme, 'external');
	} else {
		$headerlib->add_cssfile($custom_theme);
	}
}
else {
	//first load the main theme css
	$theme_css = $themelib->get_theme_css($theme_active);
	if ($theme_css) {
		$headerlib->add_cssfile($theme_css);
		//than load the theme option css file if needed
		if (!empty($theme_option_active)) {
			$option_css = $themelib->get_theme_css($theme_active, $theme_option_active);
			$headerlib->add_cssfile($option_css);
		}
	} else {
		$theme_active = 'default';
		$theme_option_active = '';
		$theme_css = $themelib->get_theme_css($theme_active);
		$headerlib->add_cssfile($theme_css);
	}
}

//6) Allow to have a IE specific CSS files for the theme's specific hacks
$style_ie8_css = $themelib->get_theme_path($theme_active, $theme_option_active, 'ie8.css');
$style_ie9_css = $themelib->get_theme_path($theme_active, $theme_option_active, 'ie9.css');

//7) include optional custom.css if there. In case of theme option, first include main theme's custom.css, than the option's custom.css
if(!empty($theme_option_active)) {
	$main_theme_path = $themelib->get_theme_path($theme_active);
	$main_theme_custom_css = "{$main_theme_path}css/custom.css";
	if (is_readable($main_theme_custom_css)) {
		$headerlib->add_cssfile($main_theme_custom_css, 53);
	}
}

$custom_css = $themelib->get_theme_path($prefs['theme'], $prefs['theme_option'], 'custom.css');
if (empty($custom_css)) {
	$custom_css = $themelib->get_theme_path('', '', 'custom.css');
}
if (is_readable($custom_css)) {
	$headerlib->add_cssfile($custom_css, 53);
}

//8) produce $iconset to be used for generating icons
$iconset = TikiLib::lib('iconset')->getIconsetForTheme($theme_active, $theme_option_active);
// and add js support file
$headerlib->add_js('jqueryTiki.iconset = ' . json_encode($iconset->getJS()));
$headerlib->add_jsfile('lib/jquery_tiki/iconsets.js');

//9) set global variable and prefs so that they can be accessed elsewhere
$prefs['theme'] = $theme_active;
$prefs['theme_option'] = $theme_option_active;

//Note: if Theme Control is active, than tiki-tc.php can modify the active theme

//finish
$smarty->initializePaths();

