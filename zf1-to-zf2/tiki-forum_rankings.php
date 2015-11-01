<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'forums';
require_once ('tiki-setup.php');

include_once ('lib/rankings/ranklib.php');

$access->check_feature('feature_forums');
$access->check_feature('feature_forum_rankings');
$access->check_permission('tiki_p_forum_read');

$allrankings = array(
	array(
	'name' => tra('Last forum topics'),
	'value' => 'forums_ranking_last_topics'
),
	array(
	'name' => tra('Last replied forum topics'),
	'value' => 'forums_ranking_last_replied_topics'
),
	array(
	'name' => tra('Most-Read Forum Topics'),
	'value' => 'forums_ranking_most_read_topics'
),
	array(
	'name' => tra('Top topics'),
	'value' => 'forums_ranking_top_topics'
),
	array(
	'name' => tra('Forum posts'),
	'value' => 'forums_ranking_most_commented_forum'
),
	array(
	'name' => tra('Most-Visited Forums'),
	'value' => 'forums_ranking_most_visited_forums'
)
);

$smarty->assign('allrankings', $allrankings);

if (!isset($_REQUEST["which"])) {
	$which = 'forums_ranking_last_topics';
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
$smarty->assign('rpage', 'tiki-forum_rankings.php');

include_once ('tiki-section_options.php');
ask_ticket('forum-rankings');

// Display the template
$smarty->assign('mid', 'tiki-ranking.tpl');
$smarty->display("tiki.tpl");
