<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'galleries';
require_once ('tiki-setup.php');
include_once ('lib/rankings/ranklib.php');
$access->check_feature(array('feature_galleries','feature_gal_rankings'));
$access->check_permission('tiki_p_list_image_galleries');

$allrankings = array(
	array(
	'name' => tra('Top galleries'),
	'value' => 'gal_ranking_top_galleries'
),
	array(
	'name' => tra('Top images'),
	'value' => 'gal_ranking_top_images'
),
	array(
	'name' => tra('Last images'),
	'value' => 'gal_ranking_last_images'
),
);

$smarty->assign('allrankings', $allrankings);

if (!isset($_REQUEST["which"])) {
	$which = 'gal_ranking_top_galleries';
} else {
	$which = $_REQUEST["which"];
}

$smarty->assign('which', $which);

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["limit"])) {
	$limit = 10;
} else {
	$limit = $_REQUEST["limit"];
}

$smarty->assign_by_ref('limit', $limit);

// Rankings:
// Top Pages
// Last pages
// Top Authors
$rankings = array();

$rk = $ranklib->$which($limit);
$rank["data"] = $rk["data"];
$rank["title"] = $rk["title"];
$rank["y"] = $rk["y"];
$rank["type"] = $rk["type"];
$rankings[] = $rank;

$smarty->assign_by_ref('rankings', $rankings);
$smarty->assign('rpage', 'tiki-galleries_rankings.php');

include_once ('tiki-section_options.php');
ask_ticket('galleries-rankings');

// Display the template
$smarty->assign('mid', 'tiki-ranking.tpl');
$smarty->display("tiki.tpl");
