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
function module_top_articles_info()
{
	return array(
		'name' => tra('Top Articles'),
		'description' => tra('Lists the specified number of articles with links to them, from the most visited one to the least.'),
		'prefs' => array('feature_articles'),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_top_articles($mod_reference, $module_params)
{
	global $user;
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
	$artlib = TikiLib::lib('art');

	$lang = '';
	if (isset($module_params['lang'])) {
		$lang = $module_params['lang'];
	}
	$ranking = $artlib->list_articles(0, $mod_reference['rows'], 'nbreads_desc', '', '', '', $user, '', '', '', '', '', '', '', $lang, '', '', '', 'y');
	
	$smarty->assign('modTopArticles', $ranking["data"]);
}
