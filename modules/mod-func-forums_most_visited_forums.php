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
function module_forums_most_visited_forums_info()
{
	return array(
		'name' => tra('Top Visited Forums'),
		'description' => tra('Display the specified number of the forums with the most visits.'),
		'prefs' => array('feature_forums'),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_forums_most_visited_forums($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	global $ranklib; include_once ('lib/rankings/ranklib.php');
	
	$ranking = $ranklib->forums_ranking_most_visited_forums($mod_reference["rows"]);
	$smarty->assign('modForumsMostVisitedForums', $ranking["data"]);
}
