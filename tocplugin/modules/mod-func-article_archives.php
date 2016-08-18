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
function module_article_archives_info()
{
	return array(
		'name' => tra('Article Archives'),
		'description' => tra('Shows links to the published articles for each month.'),
		'prefs' => array('feature_articles'),
		'params' => array(
			'more' => array(
				'name' => tra('More'),
				'description' => tra('If set to "y", displays a button labelled "More..." that links to a paginated view of the selected articles.') . " " . tr('Default: "n".'),
				'filter' => 'word',
			),
			'categId' => array(
				'name' => tra('Category filter'),
				'description' => tra('If set to a category identifier, only consider the articles in the specified category.') . " " . tra('Example value: 13.') . " " . tr('Not set by default.'),
				'filter' => 'int',
				'profile_reference' => 'category',
			),
			'topic' => array(
				'name' => tra('Topic filter (by names)'),
				'description' => tra('If set to a list of article topic names separated by plus signs, only consider the articles in the specified article topics. If the string is preceded by an exclamation mark ("!"), the effect is reversed, i.e. articles in the specified article topics are not considered.') . " " . tra('Example values:') . ' Switching to Tiki, !Switching to Tiki, Tiki upgraded to version 6+Our project is one year old, !Tiki upgraded to version 6+Our project is one year old+Mr. Jones is appointed as CEO.' . " " . tr('Not set by default.')
			),
			'topicId' => array(
				'name' => tra('Topic filter (by identifiers)'),
				'description' => tra('If set to a list of article topic identifiers separated by plus signs, only consider the articles in the specified article topics. If the string is preceded by an exclamation mark ("!"), the effect is reversed, i.e. articles in the specified article topics are not considered.') . " " . tra('Example values: 13, !13, 1+3, !1+5+7.') . " " . tr('Not set by default.'),
				'profile_reference' => 'article_topic',
			),
			'type' => array(
				'name' => tra('Types filter'),
				'description' => tra('If set to a list of article type names separated by plus signs, only consider the articles of the specified types. If the string is preceded by an exclamation mark ("!"), the effect is reversed, i.e. articles of the specified article types are not considered.') . " " . tra('Example values: Event, !Event, Event+Review, !Event+Classified+Article.') . " " . tr('Not set by default.'),
			),
			'langfilter' => array(
				'name' => tra('Language filter'),
				'description' => tra('If set to a language code, only consider the articles in the specified language.') . " " . tra('Example values:') . ' en, fr.' . " " . tr('Not set by default.'),
			),
		),
		'common_params' => array('nonums')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_article_archives($mod_reference, $module_params)
{
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
	$artlib = TikiLib::lib('art');
	
	$urlParams = array(
		'topicId' => 'topic',
		'topic' => 'topicName',
		'categId' => 'categId',
		'type' => 'type',
		'langfilter' => 'lang',
		'showImg' => NULL,
		'showDate' => NULL,
		'showHeading' => NULL,
	);
	
	foreach ($urlParams as $p => $v) {
		if (isset($$p)) continue;
		$$p = isset($module_params[$p]) ? $module_params[$p] : '';
	}
	
	foreach ($urlParams as $p => $v) $smarty->assign($p, $$p);
	
	$ranking = $artlib->list_articles(0, -1, 'publishDate_desc', '', '', date("U"), '', $type, $topicId, 'y', $topic, $categId, '', '', $langfilter);
	
	// filter the month from the data
	$artc_archive = array();
	foreach ($ranking['data'] as $key => &$rk_data) {
		if (isset($artc_archive[date('F Y', $rk_data['publishDate'])]))
			$artc_archive[date('F Y', $rk_data['publishDate'])]['item_count']++;
		else {
			$artc_archive[date('F Y', $rk_data['publishDate'])] = array(
				'title' => date('F Y', $rk_data['publishDate']),
				'start_month' => mktime(0, 0, 0, date('m', $rk_data['publishDate']), 1, date('Y', $rk_data['publishDate'])),
				'end_month' => mktime(0, 0, -1, date('m', $rk_data['publishDate'])+1, 1, date('Y', $rk_data['publishDate'])),
				'item_count' => 1);
		}
	}
	
	$smarty->assign('more', isset($module_params['more']) ? $module_params['more'] : 'n');
	$smarty->assign('modArticleArchives', $artc_archive);
	$smarty->assign('arch_count', 'y');
}
