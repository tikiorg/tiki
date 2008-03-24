<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-stats.php,v 1.17 2007-10-12 07:55:32 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/stats/statslib.php');

if ($prefs['feature_stats'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_stats");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_view_stats != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["days"]))
	$_REQUEST["days"] = 7;

$smarty->assign('pv_chart', 'n');

if (isset($_REQUEST["pv_chart"])) {
	$smarty->assign('pv_chart', 'y');
}

$smarty->assign('days', $_REQUEST["days"]);

$smarty->assign('usage_chart', 'n');

if (isset($_REQUEST["chart"])) {
	$smarty->assign($_REQUEST["chart"] . "_chart", 'y');
}

if ($prefs['feature_wiki'] == "y") {
	$wiki_stats = $statslib->wiki_stats();
} else {
	$wiki_stats = false;
}
$smarty->assign_by_ref('wiki_stats', $wiki_stats);

if ($prefs['feature_galleries'] == 'y') {
	$igal_stats = $statslib->image_gal_stats();
} else {
	$igal_stats = false;
}
$smarty->assign_by_ref('igal_stats', $igal_stats);

if ($prefs['feature_file_galleries'] == 'y') {
	$fgal_stats = $statslib->file_gal_stats();
} else {
  $fgal_stats =	false;
}
$smarty->assign_by_ref('fgal_stats', $fgal_stats);

if ($prefs['feature_articles'] == 'y') {
	$cms_stats = $statslib->cms_stats();
} else {
  $cms_stats = false;
}
$smarty->assign_by_ref('cms_stats', $cms_stats);

if ($prefs['feature_forums'] == 'y') {
	$forum_stats = $statslib->forum_stats();
} else {
	$forum_stats = false;
}
$smarty->assign_by_ref('forum_stats', $forum_stats);

if ($prefs['feature_blogs'] == 'y') {
	$blog_stats = $statslib->blog_stats();
} else {
  $blog_stats =	false;
}
$smarty->assign_by_ref('blog_stats', $blog_stats);

if ($prefs['feature_polls'] == 'y') {
	$poll_stats = $statslib->poll_stats();
} else {
	$poll_stats = false;
}
$smarty->assign_by_ref('poll_stats', $poll_stats);

if ($prefs['feature_faqs'] == 'y') {
	$faq_stats = $statslib->faq_stats();
} else {
	$faq_stats = false;
}
$smarty->assign_by_ref('faq_stats', $faq_stats);

if ($prefs['feature_quizzes'] == 'y') {
	$quiz_stats = $statslib->quiz_stats();
} else {
	$quiz_stats = false;
}
$smarty->assign_by_ref('quiz_stats', $quiz_stats);


$user_stats = $statslib->user_stats();
$smarty->assign_by_ref('user_stats', $user_stats);

$site_stats = $statslib->site_stats();
$smarty->assign_by_ref('site_stats', $site_stats);

$best_objects_stats = $statslib->best_overall_object_stats(20);
$smarty->assign_by_ref('best_objects_stats', $best_objects_stats);

$best_objects_stats_lastweek = $statslib->best_overall_object_stats(20,7);
$smarty->assign_by_ref('best_objects_stats_lastweek', $best_objects_stats_lastweek);

ask_ticket('stats');

// Display the template
$smarty->assign('mid', 'tiki-stats.tpl');
$smarty->display("tiki.tpl");

?>
