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
function module_random_pages_info()
{
	return array(
		'name' => tra('Random Pages'),
		'description' => tra('Displays the specified number of random wiki pages.'),
		'prefs' => array('feature_wiki'),
		'documentation' => 'Module random_pages',
		'params' => array(),
		'common_params' => array("rows", "nonums")
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_random_pages($mod_reference, $module_params)
{
	$tikilib = TikiLib::lib('tiki');
	$pages=$tikilib->list_pages(0, $mod_reference["rows"], "random", '', '', true, true);
	$smarty = TikiLib::lib('smarty');
	$smarty->assign('modRandomPages', $pages["data"]);
}
