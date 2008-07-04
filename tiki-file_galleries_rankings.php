<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-file_galleries_rankings.php,v 1.16 2007-10-12 07:55:27 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/rankings/ranklib.php');

if ($prefs['feature_file_galleries'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_file_galleries");

	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_file_galleries_rankings'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_file_galleries_rankings");

	$smarty->display("error.tpl");
	die;
}

if ((isset($tiki_p_list_file_galleries) && $tiki_p_list_file_galleries != 'y') || (!isset($tiki_p_list_file_galleries) && $tiki_p_view_file_gallery != 'y')) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied you cannot view this section"));

	$smarty->display("error.tpl");
	die;
}

$allrankings = array(
	array(
	'name' => tra('Top visited file galleries'),
	'value' => 'filegal_ranking_top_galleries'
),
	array(
	'name' => tra('Most downloaded files'),
	'value' => 'filegal_ranking_top_files'
),
	array(
	'name' => tra('Last files'),
	'value' => 'filegal_ranking_last_files'
),
);

$smarty->assign('allrankings', $allrankings);

if (!isset($_REQUEST["which"])) {
	$which = 'filegal_ranking_top_files';
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
$smarty->assign('rpage', 'tiki-file_galleries_rankings.php');
ask_ticket('fgal-rankings');

// Display the template
$smarty->assign('mid', 'tiki-ranking.tpl');
$smarty->display("tiki.tpl");

?>
