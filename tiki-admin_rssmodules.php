<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
$rsslib = TikiLib::lib('rss');
//get_strings tra('External Feeds')
$auto_query_args = [
	'rssId',
	'offset',
	'maxRecords',
	'sort_mode',
	'find'
];

$access->check_permission('tiki_p_admin_rssmodules');

if (isset($_REQUEST["rssId"])) {
	$smarty->assign('rssId', $_REQUEST["rssId"]);
	$cookietab = 2;
}
$smarty->assign('preview', 'n');
if (isset($_REQUEST["view"])) {
	$smarty->assign('preview', 'y');
	$data = $rsslib->get_rss_module($_REQUEST["view"]);

	if ($data['sitetitle']) {
		$smarty->assign(
			'feedtitle',
			[
				'title' => $data['sitetitle'],
				'link' => $data['siteurl']
			]
		);
	}

	$smarty->assign('items', $rsslib->get_feed_items($_REQUEST['view']));
}
if (isset($_REQUEST["rssId"])) {
	$info = $rsslib->get_rss_module($_REQUEST["rssId"]);
} else {
	$info = [];
	// default for new rss feed:
	$info["name"] = '';
	$info["description"] = '';
	$info["url"] = '';
	$info["refresh"] = 1800;
	$info["showTitle"] = 'n';
	$info["showPubDate"] = 'n';
}
$smarty->assign('name', $info["name"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('url', $info["url"]);
$smarty->assign('refresh', $info["refresh"]);
$smarty->assign('showTitle', $info["showTitle"]);
$smarty->assign('showPubDate', $info["showPubDate"]);
if (isset($_REQUEST["refresh"])) {
	$rsslib->refresh_rss_module($_REQUEST["refresh"]);
}
if (isset($_REQUEST['clear'])) {
	$rsslib->clear_rss_cache($_REQUEST['clear']);
}
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$rsslib->remove_rss_module($_REQUEST["remove"]);
}

if (isset($_REQUEST['article']) && $prefs['feature_articles'] == 'y') {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$rsslib->set_article_generator(
			$_REQUEST['article'],
			[
				'active' => isset($_POST['enable']),
				'expiry' => $jitPost->expiry->int(),
				'atype' => $jitPost->type->text(),
				'custom_atype' => $jitPost->asArray('custom_atype'),
				'topic' => $jitPost->topic->int(),
				'custom_topic' => $jitPost->asArray('custom_topic'),
				'future_publish' => $jitPost->future_publish->int(),
				'categories' => (array) $jitPost->cat_categories->int(),
				'rating' => $jitPost->rating->int(),
				'custom_rating' => $jitPost->asArray('custom_rating'),
				'submission' => isset($_POST['submission']),
				'custom_priority' => $jitPost->asArray('custom_priority'),
				'a_lang' => $jitPost->a_lang->word(),
			]
		);
		$cookietab = 1;
	} else {
		$cookietab = 3;
	}

	$config = $rsslib->get_article_generator($_REQUEST['article']);
	$smarty->assign('articleConfig', $config);
	$smarty->assign('ratingOptions', array_map('strval', range(0, 10)));

	$sourcecats = $rsslib->get_feed_source_categories($_REQUEST["article"]);
	$smarty->assign('sourcecats', $sourcecats);
	$article_custom_info = $rsslib->get_article_custom_info($_REQUEST["article"]);
	$smarty->assign('article_custom_info', $article_custom_info);

	$artlib = TikiLib::lib('art');
	$smarty->assign('topics', $artlib->list_topics());
	$smarty->assign('types', $artlib->list_types());

	$cat_type = 'null';
	$cat_objid = 'null';
	$_REQUEST['cat_categorize'] = 'on';
	$_REQUEST['cat_categories'] = $config['categories'];
	include 'categorize_list.php';
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-rssmodules');
	if (isset($_REQUEST['showTitle']) == 'on') {
		$smarty->assign('showTitle', 'y');
		$info["showTitle"] = 'y';
	} else {
		$smarty->assign('showTitle', 'n');
		$info["showTitle"] = 'n';
	}
	if (isset($_REQUEST['showPubDate']) == 'on') {
		$smarty->assign('showPubDate', 'y');
		$info["showPubDate"] = 'y';
	} else {
		$smarty->assign('showPubDate', 'n');
		$info["showPubDate"] = 'n';
	}
	$rsslib->replace_rss_module($_REQUEST["rssId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["url"], $_REQUEST["refresh"], $info["showTitle"], $info["showPubDate"]);
	$smarty->assign('rssId', 0);
	$smarty->assign('name', '');
	$smarty->assign('description', '');
	$smarty->assign('url', '');
	$smarty->assign('refresh', 900);
	$smarty->assign('showTitle', 'n');
	$smarty->assign('showPubDate', 'n');
	$cookietab = 1;
}
if (! isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
if (! isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
if ($prefs['feature_multilingual'] == 'y') {
	$languages = [];
	$langLib = TikiLib::lib('language');
	$languages = $langLib->list_languages();
	$smarty->assign_by_ref('languages', $languages);
}
$smarty->assign('find', $find);
$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $rsslib->list_rss_modules($offset, $maxRecords, $sort_mode, $find);
$cant = $channels['cant'];
$smarty->assign_by_ref('cant', $cant);
$temp_max = count($channels["data"]);
$smarty->assign_by_ref('channels', $channels["data"]);
ask_ticket('admin-rssmodules');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_rssmodules.tpl');
$smarty->display("tiki.tpl");
