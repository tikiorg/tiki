<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-wiki_rankings.php,v 1.16.2.2 2007-12-09 19:17:57 nkoth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'wiki page';
require_once ('tiki-setup.php');

include_once ('lib/rankings/ranklib.php');

$smarty->assign('headtitle',tra('Rankings'));


if ($prefs['feature_wiki'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_wiki_rankings'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki_rankings");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_view != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied you cannot view this section"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["limit"])) {
	$limit = 10;
} else {
	$limit = $_REQUEST["limit"];
}

if (isset($_REQUEST["categId"]) && $_REQUEST["categId"] > 0) {
	$smarty->assign('categIdstr', $_REQUEST["categId"]);
	$categs = split(",",$_REQUEST["categId"]);
} else {
	$categs = array();	
}
$smarty->assign('categId',$categs);

$allrankings = array(
	array(
	'name' => tra('Top pages'),
	'value' => 'wiki_ranking_top_pages'
),
	array(
	'name' => tra('Last pages'),
	'value' => 'wiki_ranking_last_pages'
),
	array(
	'name' => tra('Most relevant pages'),
	'value' => 'wiki_ranking_top_pagerank'
),
	array(
	'name' => tra('Top authors'),
	'value' => 'wiki_ranking_top_authors'
)
);

$smarty->assign('allrankings', $allrankings);

if (!isset($_REQUEST["which"])) {
	$which = 'wiki_ranking_top_pages';
} else {
	$which = $_REQUEST["which"];
}

$smarty->assign('which', $which);

$smarty->assign_by_ref('limit', $limit);

// Rankings:
// Top Pages
// Last pages
// Top Authors
$rankings = array();

$rk = $ranklib->$which($limit, $categs);
$rank["data"] = $rk["data"];
$rank["title"] = $rk["title"];
$rank["y"] = $rk["y"];
$rank["type"] = $rk["type"];
$rankings[] = $rank;

$smarty->assign_by_ref('rankings', $rankings);
$smarty->assign('rpage', 'tiki-wiki_rankings.php');

include_once ('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-ranking.tpl');
$smarty->display("tiki.tpl");

?>
