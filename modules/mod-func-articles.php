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
function module_articles_info()
{
	return array(
		'name' => tra('Articles'),
		'description' => tra('Lists the specified number of published articles in the specified order.'),
		'prefs' => array('feature_articles'),
		'documentation' => 'Module articles',
		'params' => array(
			'showpubl' => array(
				'name' => tra('Show publication time'),
				'description' => tra('If set to "y", article publication times are shown.') . " " . tr('Default: "n".'),
				'filter' => 'word',
			),
			'showcreated' => array(
				'name' => tra('Show creation time'),
				'description' => tra('If set to "y", article creation times are shown.') . " " . tr('Default: "n".'),
				'filter' => 'word',
			),
			'show_rating_selector' => array(
				'name' => tra('Show rating selector'),
				'description' => tra('If set to "y", offers the user to filter articles based on a minimum and a maximum rating.') . " " . tr('Default: "n".'),
				'filter' => 'word',
			),
			'img' => array(
				'name' => tra('Image width'),
				'description' => tra('If set, displays an image for each article if one applies, with the given width (in pixels). The article\'s own image is used, with a fallback to the article\'s topic image.') . " " . tr('Not set by default.'),
				'filter' => 'int',
			),
			'categId' => array(
				'name' => tra('Category filter'),
				'description' => tra('If set to a category identifier, only lists the articles in the specified category.') . " " . tra('Example value: 13.') . " " . tr('Not set by default.'),
				'filter' => 'int',
				'profile_reference' => 'category',
			),
			'topic' => array(
				'name' => tra('Topic filter (by names)'),
				'description' => tra('If set to a list of article topic names separated by plus signs, only lists the articles in the specified article topics. If the string is preceded by an exclamation mark ("!"), the effect is reversed, i.e. articles in the specified article topics are not listed.') . " " . tra('Example values:') . ' Switching to Tiki, !Switching to Tiki, Tiki upgraded to version 6+Our project is one year old, !Tiki upgraded to version 6+Our project is one year old+Mr. Jones is appointed as CEO.' . " " . tr('Not set by default.'),
			),
			'topicId' => array(
				'name' => tra('Topic filter (by identifiers)'),
				'description' => tra('If set to a list of article topic identifiers separated by plus signs, only lists the articles in the specified article topics. If the string is preceded by an exclamation mark ("!"), the effect is reversed, i.e. articles in the specified article topics are not listed.') . " " . tra('Example values: 13, !13, 1+3, !1+5+7.') . " " . tra("If set to 0, will take the topicId of the article if in an article."). " " . tr('Not set by default.'),
				'profile_reference' => 'article_topic',
			),
			'type' => array(
				'name' => tra('Types filter'),
				'description' => tra('If set to a list of article type names separated by plus signs, only lists the articles of the specified types. If the string is preceded by an exclamation mark ("!"), the effect is reversed, i.e. articles of the specified article types are not listed.') . " " . tra('Example values: Event, !Event, Event+Review, !Event+Classified+Article.') . " " . tr('Not set by default.'),
			),
			'langfilter' => array(
				'name' => tra('Language filter'),
				'description' => tra('If set to a language code, only lists the articles in the specified language.') . " " . tra('Example values:') . ' en, fr.' . " " . tr('Not set by default.'),
			),
			'sort' => array(
				'name' => tra('Sort'),
				'description' => tra('Specifies how the articles should be sorted.') . " " . tra('Possible values include created and created_asc (equivalent), created_desc, author, rating, topicId, lang and title. Unless "_desc" is specified, the sort is ascending. "created" sorts on article creation date.')  . ' ' . tra('Default value:') . " publishDate_desc",
				'filter' => 'striptags',
			),
			'start' => array(
				'name' => tra('Offset'),
				'description' => tra('If set to an integer, offsets the articles list by the given number. For example, if the module was otherwise set to list the 10 articles most recently published, setting the offset to 10 would make the module list the 11th to 20th articles in descending order of publication time instead.') . " " . tra('Default value:') . " 0",
				'filter' => 'int',
			),
			'more' => array(
				'name' => tra('More'),
				'description' => tra('If set to "y", displays a button labelled "More" that links to a paginated view of the selected articles.') . " " . tr('Default: "n".'),
				'filter' => 'word',
			),
			'absurl' => array(
				'name' => tra('Absolute URL'),
				'description' => tra('If set to "y", some of the links use an absolute URL instead of a relative one. This can avoid broken links if the module is to be sent in a newsletter, for example.') . " " . tr('Default: "n".'),
			),
		),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_articles($mod_reference, $module_params)
{
	global $user;
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
	$artlib = TikiLib::lib('art');
	
	$urlParams = array(
		'topicId' => 'topic',
		'topic' => 'topicName',
		'categId' => 'categId',
		'type' => 'type',
		'langfilter' => 'lang',
		'start' => null,
		'sort' => null
	);
	if (isset($module_params['topicId']) && $module_params['topicId'] == 0 && ($object = current_object()) && $object['type'] == 'article') {
		$topicId = $smarty->getTemplateVars('topicId');
	}
	
	foreach ($urlParams as $p => $v) {
		if (isset($$p)) continue;
		$$p = isset($module_params[$p]) ? $module_params[$p] : '';
	}
	if ($start == '') $start = 0;
	if ($sort == '') $sort = 'publishDate_desc';

	$min_rating = isset($_REQUEST['min_rating']) ? $_REQUEST['min_rating'] : 0;
	$max_rating = isset($_REQUEST['max_rating']) ? $_REQUEST['max_rating'] : 10;
	$smarty->assign('min_rating', $min_rating);
	$smarty->assign('max_rating', $max_rating);
	
	$ranking = $artlib->list_articles($start, $mod_reference['rows'], $sort, '', '', '', $user, $type, $topicId, 'y', $topic, $categId, '', '', $langfilter, $min_rating, $max_rating, '', 'y');
	
	$smarty->assign_by_ref('urlParams', $urlParams);
	$smarty->assign('modArticles', $ranking["data"]);
	$smarty->assign('more', isset($module_params['more']) ? $module_params['more'] : 'n');
	$smarty->assign('absurl', isset($module_params["absurl"]) ? $module_params["absurl"] : 'n');
	$smarty->assign('showcreated', isset($module_params['showcreated']) ? $module_params['showcreated'] : 'n');
	$smarty->assign('showpubl', isset($module_params['showpubl']) ? $module_params['showpubl'] : 'n');
	$smarty->assign('show_rating_selector', isset($module_params['show_rating_selector']) ? $module_params['show_rating_selector'] : 'n');
}
