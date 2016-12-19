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
function module_article_topics_info()
{
	return array(
		'name' => tra('Article Topics'),
		'description' => tra('Lists all article topics with links to their articles.'),
		'prefs' => array('feature_articles'),
		'params' => array(),
		'common_params' => array('nonums')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_article_topics($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	$artlib = TikiLib::lib('art');
	
	$listTopics = $artlib->list_topics();
	/* To renumber array keys from 0 since smarty 3 doesn't seem to like arrays
	 * that start with other keys in a section loop, which this variable is used in
	 */
	$listTopics = array_values($listTopics);
	$smarty->assign('listTopics', $listTopics);
}
