<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

$access->check_feature('change_theme');
if (!empty($group_style)) {
	$access->display_error(NULL, 'A group theme is defined.');
}

if (isset($_REQUEST['theme-option'])) {
	$themeOption = $_REQUEST['theme-option'];
}

if (isset($_REQUEST['theme-themegen'])) {
	$themeGenerator_theme = $_REQUEST['theme-themegen'];
}

if (isset($_REQUEST['theme'])) {
	$theme = $_REQUEST['theme'];

	if (empty($theme)) {
		$theme = '';
		$themeOption = '';
		$themeGenerator_theme = '';
	} elseif ($theme != $prefs['style']) { // use default theme option when changing main theme
		$themeOption = '';
		$themeGenerator_theme = '';
	}
	
	$tikilib->set_user_preference($user, 'theme', $theme);
}
if (isset($themeOption)) {
	  $tikilib->set_user_preference($user, 'theme-option', empty($themeOption) ? 'None' : $themeOption);
}

if (isset($themeGenerator_theme) && $prefs['themegenerator_feature'] === 'y') {
	$tikilib->set_user_preference($user, 'themegenerator_theme', $themeGenerator_theme);
}

if (isset($_SERVER['HTTP_REFERER'])) {
	$orig_url = $_SERVER['HTTP_REFERER'];
} else {
	$orig_url = $prefs['tikiIndex'];
}
header("location: $orig_url");
exit;
