<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'cms';
//get_strings tra('Articles Home');
require_once ('tiki-setup.php');
$artlib = TikiLib::lib('art');
if ($prefs['feature_freetags'] == 'y') {
	$freetaglib = TikiLib::lib('freetag');
}
if ($prefs['feature_categories'] == 'y') {
	$categlib = TikiLib::lib('categ');
}

$access->check_feature('feature_articles');
$access->check_permission_either(array('tiki_p_read_article', 'tiki_p_articles_read_heading'));

if (isset($_REQUEST["remove"])) {
	$access->check_permission('tiki_p_remove_article');
	$access->check_authenticity();
	$artlib->remove_article($_REQUEST["remove"]);
}
// This script can receive the threshold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if (empty($_REQUEST["sort_mode"])) {
	$sort_mode = $prefs['art_sort_mode'];
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);
// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST['date_min']) || isset($_REQUEST['date_max'])) {
	$date_min = isset($_REQUEST['date_min']) ? $_REQUEST['date_min'] : 0;
	$date_max = isset($_REQUEST['date_max']) ? $_REQUEST['date_max'] : $tikilib->now;
} elseif (isset($_SESSION["thedate"])) {
	$date_min = 0;
	if ($_SESSION["thedate"] < $tikilib->now) {
		$date_max = $_SESSION["thedate"];
	} else {
		if ($tiki_p_admin == 'y' || $tiki_p_admin_cms == 'y') {
			$date_max = $_SESSION["thedate"];
		} else {
			$date_max = $tikilib->now;
		}
	}
} else {
	$date_min = 0;
	$date_max = $tikilib->now;
}
//Keep track of month of last viewed article for article months_links module foldable display
$_SESSION['cms_last_viewed_month'] = TikiLib::date_format("%Y-%m", $date_max);
$min_rating = isset($_REQUEST['min_rating']) ? $_REQUEST['min_rating'] : '';
$max_rating = isset($_REQUEST['max_rating']) ? $_REQUEST['max_rating'] : '';
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign_by_ref('find', $find);
if (isset($_REQUEST["type"])) {
	$type = $_REQUEST["type"];
} else {
	$type = '';
}
if (isset($_REQUEST["topic"])) {
	$topic = $_REQUEST["topic"];
} else {
	$topic = '';
}
if (isset($_REQUEST['topicName'])) {
	$topicName = $_REQUEST['topicName'];
} else {
	$topicName = '';
}
if (isset($_REQUEST["categId"])) {
	$categId = $_REQUEST["categId"];
} else {
	$categId = '';
}
$smarty->assign_by_ref('categId', $categId);
if (!isset($_REQUEST['lang'])) {
	$_REQUEST['lang'] = '';
}
$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);
$smarty->assign_by_ref('type', $type);
$smarty->assign('maxArticles', $prefs['maxArticles']);

include_once ('tiki-section_options.php');

// Display the template
$smarty->assign('mid', TikiLib::custom_template('lists/articles.tpl',$type));
$smarty->display("tiki.tpl");