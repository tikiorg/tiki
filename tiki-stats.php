<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-stats.php,v 1.6 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/stats/statslib.php');

if ($feature_stats != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($tiki_p_view_stats != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
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

$wiki_stats = $statslib->wiki_stats();
$smarty->assign_by_ref('wiki_stats', $wiki_stats);
$igal_stats = $statslib->image_gal_stats();
$smarty->assign_by_ref('igal_stats', $igal_stats);
$fgal_stats = $statslib->file_gal_stats();
$smarty->assign_by_ref('fgal_stats', $fgal_stats);
$cms_stats = $statslib->cms_stats();
$smarty->assign_by_ref('cms_stats', $cms_stats);
$forum_stats = $statslib->forum_stats();
$smarty->assign_by_ref('forum_stats', $forum_stats);
$blog_stats = $statslib->blog_stats();
$smarty->assign_by_ref('blog_stats', $blog_stats);
$poll_stats = $statslib->poll_stats();
$smarty->assign_by_ref('poll_stats', $poll_stats);
$faq_stats = $statslib->faq_stats();
$smarty->assign_by_ref('faq_stats', $faq_stats);
$user_stats = $statslib->user_stats();
$smarty->assign_by_ref('user_stats', $user_stats);
$site_stats = $statslib->site_stats();
$smarty->assign_by_ref('site_stats', $site_stats);
$quiz_stats = $statslib->quiz_stats();
$smarty->assign_by_ref('quiz_stats', $quiz_stats);

// Display the template
$smarty->assign('mid', 'tiki-stats.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>