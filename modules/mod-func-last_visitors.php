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
function module_last_visitors_info()
{
	return array(
		'name' => tra('Last Visitors'),
		'description' => tra('Displays information about the specified number of users in decreasing order of last login time.'),
		'params' => array(
			'showavatars' => array(
				'name' => tra('Show profile pictures'),
				'description' => tra('If set to "y", show user profile pictures.') . ' ' . tra('Default:') . ' "n"'
			),
			'maxlen' => array(
				'name' => tra('Maximum length'),
				'description' => tra('Maximum number of characters in user names allowed before truncating.'),
				'filter' => 'int'
			),
			'nodate' => array(
				'name' => tra("Don't show date"),
				'description' => tra('If set to "y", it will hide date.'),
			)
		),
		'common_params' => array('nonums', 'rows'),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_last_visitors($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	$userlib = TikiLib::lib('user');

	$last_visitors = $userlib->get_users(0, $mod_reference["rows"], 'currentLogin_desc');
	$smarty->assign('modLastVisitors', $last_visitors['data']);
	$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 0);
	$smarty->assign('showavatars', isset($module_params["showavatars"]) ? $module_params["showavatars"] : 'n');
	$smarty->assign('nodate', isset($module_params["nodate"]) ? $module_params["nodate"] : 'n');
}
