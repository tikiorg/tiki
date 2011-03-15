<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/rss/rsslib.php');
$auto_query_args = array(
	'rssId',
	'offset',
	'maxRecords',
	'sort_mode',
	'find'
);
if (!isset($rsslib)) {
	$rsslib = new RssLib;
}
$access->check_permission('tiki_p_admin_rssmodules');

if (isset($_REQUEST["rssId"])) {
	$smarty->assign('rssId', $_REQUEST["rssId"]);
}
$smarty->assign('preview', 'n');
if (isset($_REQUEST["view"])) {
	$smarty->assign('preview', 'y');
	$data = $rsslib->get_rss_module($_REQUEST["view"]);
	
	if( $data['sitetitle'] ) {
		$smarty->assign('feedtitle', array(
			'title' => $data['sitetitle'],
			'link' => $data['siteurl']
		) );
	}

	$smarty->assign( 'items', $rsslib->get_feed_items( $_REQUEST['view'] ) );
}
if (isset($_REQUEST["rssId"])) {
	$info = $rsslib->get_rss_module($_REQUEST["rssId"]);
} else {
	$info = array();
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

if( isset($_REQUEST['article']) && $prefs['feature_articles'] == 'y' ) {
	if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		$rsslib->set_article_generator( $_REQUEST['article'], array(
			'active' => isset( $_POST['enable'] ),
			'expiry' => $jitPost->expiry->int(),
			'atype' => $jitPost->type->text(),
			'topic' => $jitPost->topic->int(),
			'future_publish' => $jitPost->future_publish->int(),
			'categories' => (array) $jitPost->cat_categories->int(),
			'rating' => $jitPost->rating->int(),
			'submission' => isset( $_POST['submission'] ),
		) );
	}

	$config = $rsslib->get_article_generator( $_REQUEST['article'] );
	$smarty->assign( 'articleConfig', $config );
	$smarty->assign( 'ratingOptions', range( 0, 10 ) );

	global $artlib; require_once 'lib/articles/artlib.php';
	$smarty->assign( 'topics', $artlib->list_topics() );
	$smarty->assign( 'types', $artlib->list_types() );

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
}
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
if (!isset($_REQUEST["offset"])) {
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
