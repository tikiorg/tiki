<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

$access->check_feature('change_theme');
if (!empty($group_theme)) {
	$access->display_error(NULL, 'A group theme is defined.');
}

if (isset($_REQUEST['theme'])) {

	if (empty($_REQUEST['theme'])) {
		$_REQUEST['theme_option'] = '';
	}
	
	$tikilib->set_user_preference($user, 'theme', $_REQUEST['theme']); //save user's theme preference
	$tikilib->set_user_preference($user, 'theme_option', $_REQUEST['theme_option']);

}

if (isset($_SERVER['HTTP_REFERER'])) {
	$orig_url = $_SERVER['HTTP_REFERER'];
} else {
	$orig_url = $prefs['tikiIndex'];
}
header("location: $orig_url");
exit;
