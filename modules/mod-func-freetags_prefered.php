<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_freetags_prefered_info()
{
	return array(
		'name' => tra('My Preferred Tags'),
		'description' => tra('Displays to registered users the tags they prefer, based on the number of objects they tagged. Greater preference is indicated by a larger text size.'),
		'prefs' => array('feature_freetags'),
		'params' => array(),
		'common_params' => array('rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_freetags_prefered($mod_reference, $module_params)
{
	global $user;
	$smarty = TikiLib::lib('smarty');
	if ($user) {
		$freetaglib = TikiLib::lib('freetag');
		$preferred_tags = $freetaglib->get_most_popular_tags($user, 0, $mod_reference["rows"]);
		$smarty->assign('preferred_tags', $preferred_tags);
		$smarty->assign('tpl_module_title', tra('My preferred tags'));
	}
}
