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
function module_old_articles_info()
{
	return array(
		'name' => tra('Old Articles'),
		'description' => tra('Displays the specified number of old articles (which do not show on articles home page anymore).'),
		'prefs' => array('feature_articles'),
		'params' => array(),
		'common_params' => array("rows", "nonums")
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_old_articles($mod_reference, $module_params)
{
	global $user, $prefs;
	$smarty = TikiLib::lib('smarty');
	$tikilib = TikiLib::lib('tiki');
	$artlib = TikiLib::lib('art');

	if (!isset($prefs['maxArticles']))
		$prefs['maxArticles'] = 0;
	
	$ranking = $artlib->list_articles($prefs['maxArticles'], $mod_reference["rows"], 'publishDate_desc', '', '', '', $user);
	$smarty->assign('modOldArticles', $ranking["data"]);
}
