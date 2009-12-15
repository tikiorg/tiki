<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-galleries_rankings.php,v 1.17.2.1 2008-03-15 21:11:15 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'galleries';
require_once ('tiki-setup.php');

include_once ('lib/rankings/ranklib.php');

if ($prefs['feature_galleries'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_galleries");

	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_gal_rankings'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_gal_rankings");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_list_image_galleries != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied you cannot view this section"));

	$smarty->display("error.tpl");
	die;
}

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

?>
