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
function module_last_actions_info()
{
	return array(
		'name' => tra('Last Actions'),
		'description' => tra('Displays the specified number of last actions.'),
		'prefs' => array(),
		'params' => array(
			'showuser' => array(
				'name' => tra('Show user'),
				'description' => tra('If set to "y", user names are displayed in the module box.') . " " . tra('Default: "n"'),
				'filter' => 'word'
			),
			'showdate' => array(
				'name' => tra('Show date'),
				'description' => tra('If set to "y", action dates are displayed in the module box.') . " " . tra('Default: "n"'),
				'filter' => 'word'
			),
			'maxlen' => array(
				'name' => tra('Maximum length'),
				'description' => tra('Maximum number of characters in action descriptions before truncating.') . ' ' . tra('Default:') . ' 30',
				'filter' => 'int'
			)
		),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_last_actions($mod_reference, $module_params)
{
	global $tiki_p_admin, $user;
	$smarty = TikiLib::lib('smarty');
	if ($user) {
		$logslib = TikiLib::lib('logs');
		
		$results = $logslib->list_actions('', '', $tiki_p_admin == 'y' ? '' : $user, 0, $mod_reference["rows"]);
		$actions = $results['data'];
	
		$smarty->assign('modLastActions', $actions);
		$showuser = isset($module_params["showuser"]) ? $module_params["showuser"] : 'n';
		$showdate = isset($module_params["showdate"]) ? $module_params["showdate"] : 'n';
		$smarty->assign('showuser', $showuser);
		$smarty->assign('showdate', $showdate);
		$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : '30');
	}
}
