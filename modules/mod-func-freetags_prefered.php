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

function module_freetags_prefered_info() {
	return array(
		'name' => tra('My Preferred Tags'),
		'description' => tra('Displays to registered users the freetags they prefer, based on the number of objects they added the tag to. More preference is indicated by a larger font.'),
		'prefs' => array( 'feature_freetags' ),
		'params' => array(),
		'common_params' => array('rows')
	);
}

function module_freetags_prefered( $mod_reference, $module_params ) {
	global $user;
	global $smarty;
	if ($user) {
		global $freetaglib;	require_once("lib/freetag/freetaglib.php");
		$preferred_tags = $freetaglib->get_most_popular_tags($user, 0, $mod_reference["rows"]);
		$smarty->assign('preferred_tags', $preferred_tags);
		$smarty->assign('tpl_module_title', tra('My preferred tags'));
	}
}
