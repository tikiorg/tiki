<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
function module_most_commented_info()
{
	return array(
		'name' => tra('Most Commented'),
		'description' => tra('Display the most commented objects of a certain type.'),
		'prefs' => array(),
		'params' => array(
			'objectType' => array(
				'name' => tra('Object Type'),
				'description' => tra('Type of objects to consider.') . ' ' . tra('Possible values: wiki (Wiki pages), blog (blog posts), article (articles).') . ' ' . tra('Default:') . ' wiki'
			),
			'objectLanguageFilter' => array(
				'name' => tra('Language Filter'),
				'description' => tra('If set to a RFC1766 language tag, restricts the objects considered to those in the specified language.')
			)
		),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_most_commented($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');

	$type = 'wiki';
	if (isset($module_params['objectType'])) {
		$type = $module_params['objectType'];
		if ($type != 'article' && $type != 'blog' && $type != 'wiki') {
			//If parameter is not properly set then default to wiki
			$type = 'wiki';
		}
	}
	
	$result = TikiLib::lib('comments')->order_comments_by_count($type, isset($module_params['objectLanguageFilter']) ? $module_params['objectLanguageFilter'] : '', $mod_reference['rows']);
	if ($result === false) {
		$smarty->assign('module_error', tra('Feature disabled'));
		return;
	}
	$smarty->assign('modMostCommented', $result['data']);
	$smarty->assign('modContentType', $type);
}
