<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-blog_rankings.php,v 1.18.2.1 2007-11-08 21:38:33 ricks99 Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'blogs';
require_once ('tiki-setup.php');

include_once ('lib/rankings/ranklib.php');

$smarty->assign('headtitle',tra('Rankings'));

if ($prefs['feature_blogs'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_blogs");

	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_blog_rankings'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_blog_rankings");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_read_blog != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot view this section"));

	$smarty->display("error.tpl");
	die;
}

$allrankings = array(
	array(
	'name' => tra('Top visited blogs'),
	'value' => 'blog_ranking_top_blogs'
),
	array(
	'name' => tra('Last posts'),
	'value' => 'blog_ranking_last_posts'
),
	array(
	'name' => tra('Top active blogs'),
	'value' => 'blog_ranking_top_active_blogs'
)
);

$smarty->assign('allrankings', $allrankings);

if (!isset($_REQUEST["which"])) {
	$which = 'blog_ranking_top_blogs';
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

include_once ('tiki-section_options.php');

$smarty->assign_by_ref('rankings', $rankings);
$smarty->assign('rpage', 'tiki-blog_rankings.php');
ask_ticket('blog-rankings');

// Display the template
$smarty->assign('mid', 'tiki-ranking.tpl');
$smarty->display("tiki.tpl");

?>
