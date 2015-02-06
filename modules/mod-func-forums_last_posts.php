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
function module_forums_last_posts_info()
{
	return array(
		'name' => tra('Newest Forum Posts'),
		'description' => tra('Displays the latest forum posts.'),
		'prefs' => array('feature_forums'),
		'params' => array(
			'topics' => array(
				'name' => tra('Topics only'),
				'description' => tra('If set to "y", only displays topics.') . " " . tr('Not set by default.'),
				),
			'forumId' => array(
				'name' => tra('List of forum identifiers'),
				'description' => tra('If set to a list of forum identifiers, restricts the posts to those in the identified forums. Identifiers are separated by colons (":").') . " " . tra('Example values:') . '"13", "31:49". ' . tra('Not set by default.'),
				'separator' => ':',
				'profile_reference' => 'forum',
			),
			'date' => array(
				'name' => tra('Show date'),
				'description' => tra('If set to "y", show post date directly instead of as tooltip.') . ' ' . tra('Default:') . ' n',
			),
			'time' => array(
				'name' => tra('Show time'),
				'description' => tra('Show times after dates.') . ' ' . tra('Default:') . ' y',
			),
			'author' => array(
				'name' => tra('Show author'),
				'description' => tra('If set to "y", show post author directly instead of as tooltip.') . ' ' . tra('Default:') . ' n',
			),
		),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_forums_last_posts($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	global $ranklib; include_once ('lib/rankings/ranklib.php');
	$default = array('forumId'=>'', 'topics' => false);
	$module_params = array_merge($default, $module_params);
	$ranking = $ranklib->forums_ranking_last_posts($mod_reference['rows'], $module_params['topics'], $module_params['forumId']);
	
	$replyprefix = tra("Re:");
	
	$smarty->assign('modForumsLastPosts', $ranking["data"]);
	$smarty->assign('date', isset($module_params['date']) ? $module_params['date'] : 'n');
	$smarty->assign('author', isset($module_params['author']) ? $module_params['author'] : 'n');
}
