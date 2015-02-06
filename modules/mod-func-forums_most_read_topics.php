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
function module_forums_most_read_topics_info()
{
	return array(
		'name' => tra('Top Visited Forum Topics'),
		'description' => tra('Display the specified number of the forum topics with the most reads.'),
		'prefs' => array('feature_forums'),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_forums_most_read_topics($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	global $ranklib; include_once ('lib/rankings/ranklib.php');
	
	if (isset($module_params['forumId'])) {
		if (strstr($module_params['forumId'], ':')) {
			$forumId = explode(':', $module_params['forumId']);
		} else {
			$forumId = $module_params['forumId'];
		}
	} else {
		$forumId = '';
	}
	
	$ranking = $ranklib->forums_ranking_most_read_topics($mod_reference["rows"], $forumId);
	$smarty->assign('modForumsMostReadTopics', $ranking["data"]);
}
