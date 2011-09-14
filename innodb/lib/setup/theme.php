<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if ( isset($_SESSION['try_style']) ) {
	$prefs['style'] = $_SESSION['try_style'];
} elseif ( $prefs['change_theme'] != 'y' && !isset($_SESSION['current_perspective'])) {
	// Use the site value instead of the user value if the user is not allowed to change the theme
	$prefs['style'] = $prefs['site_style'];
	$prefs['style_option'] = $prefs['site_style_option'];
}

if ($prefs['feature_fixed_width'] === 'y') {
	$headerlib->add_css('.fixed_width .fixedwidth, .fixed_width .fixedwidth .fixedwidth { width:' . (!empty($prefs['layout_fixed_width']) ? $prefs['layout_fixed_width'] : '990px') . '; }');
}

if ( $prefs['useGroupTheme'] == 'y' && $group_style = $userlib->get_user_group_theme()) {
	$prefs['style'] = $group_style;
	$smarty->assign_by_ref('group_style', $group_style);
}
if (empty($prefs['style']) || $tikilib->get_style_path('', '', $prefs['style']) == '') {
	$prefs['style'] = 'thenews.css';
}
		
$headerlib->add_cssfile($tikilib->get_style_path('', '', $prefs['style']), 51);
$style_base = $tikilib->get_style_base($prefs['style']);

// Allow to have a IE specific CSS files for the theme's specific hacks
$style_ie6_css = $tikilib->get_style_path($prefs['style'], $prefs['style_option'], 'ie6.css');
$style_ie7_css = $tikilib->get_style_path($prefs['style'], $prefs['style_option'], 'ie7.css');
$style_ie8_css = $tikilib->get_style_path($prefs['style'], $prefs['style_option'], 'ie8.css');

// include optional "options" cascading stylesheet if set
if ( !empty($prefs['style_option'])) {
	$style_option_css = $tikilib->get_style_path($prefs['style'], $prefs['style_option'], $prefs['style_option']);
	if (!empty($style_option_css)) {
		$headerlib->add_cssfile($style_option_css, 52);
	}
}

// include optional "custom" cascading stylesheet if there
$custom_css = $tikilib->get_style_path($prefs['style'], $prefs['style_option'], 'custom.css');;
if ( !empty($custom_css)) {
	$headerlib->add_cssfile($custom_css, 53);
}

