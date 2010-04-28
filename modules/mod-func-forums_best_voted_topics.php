<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_forums_best_voted_topics_info() {
	return array(
		'name' => tra('Best rated topics'),
		'description' => tra('Displays the specified number of the forum topics with the best ratings.'),
		'prefs' => array('feature_forums'),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_forums_best_voted_topics( $mod_reference, $module_params ) {
	global $smarty;
	global $ranklib; include_once ('lib/rankings/ranklib.php');
	
	$ranking = $ranklib->forums_ranking_top_topics($mod_reference["rows"]);
	$smarty->assign('modForumsTopTopics', $ranking["data"]);
}
