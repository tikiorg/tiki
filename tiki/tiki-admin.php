<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin.php,v 1.94 2003-11-17 15:44:27 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/admin/adminlib.php');

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

function simple_set_toggle($feature) {
	global $_REQUEST, $tikilib, $smarty;

	if (isset($_REQUEST[$feature]) && $_REQUEST[$feature] == "on") {
		$tikilib->set_preference($feature, 'y');

		$smarty->assign($feature, 'y');
	} else {
		$tikilib->set_preference($feature, 'n');

		$smarty->assign($feature, 'n');
	}
}

function simple_set_value($feature) {
	global $_REQUEST, $tikilib, $smarty;

	if (isset($_REQUEST[$feature])) {
		$tikilib->set_preference($feature, $_REQUEST[$feature]);

		$smarty->assign($feature, $_REQUEST[$feature]);
	}
}

function byref_set_value($feature, $pref = "") {
	global $_REQUEST, $tikilib, $smarty;

	if (isset($_REQUEST[$feature])) {
		if (strlen($pref) > 0) {
			$tikilib->set_preference($pref, $_REQUEST[$feature]);
			// also assign the ref appareantly --gongo
			$smarty->assign_by_ref($pref, $_REQUEST[$feature]);
		} else {
			$tikilib->set_preference($feature, $_REQUEST[$feature]);
		}

		$smarty->assign_by_ref($feature, $_REQUEST[$feature]);
	}
}

$home_blog = $tikilib->get_preference("home_blog", 0);
$smarty->assign('home_blog', $home_blog);

$home_forum = $tikilib->get_preference("home_forum", 0);
$smarty->assign('home_forum', $home_forum);

$home_gallery = $tikilib->get_preference("home_gallery", 0);
$smarty->assign('home_gallery', $home_gallery);

$home_file_gallery = $tikilib->get_preference("home_file_gallery", 0);
$smarty->assign('home_file_gallery', $home_file_gallery);

if (isset($_REQUEST["page"])) {
	$adminPage = $_REQUEST["page"];

	if ($adminPage == "features") {
		include_once ('tiki-admin_include_features.php');
	} else if ($adminPage == "general") {
		include_once ('tiki-admin_include_general.php');
	} else if ($adminPage == "login") {
		include_once ('tiki-admin_include_login.php');
	} else if ($adminPage == "wiki") {
		include_once ('tiki-admin_include_wiki.php');
	} else if ($adminPage == "gal") {
		include_once ('tiki-admin_include_gal.php');
	} else if ($adminPage == "fgal") {
		include_once ('tiki-admin_include_fgal.php');
	} else if ($adminPage == "cms") {
		include_once ('tiki-admin_include_cms.php');
	} else if ($adminPage == "polls") {
		include_once ('tiki-admin_include_polls.php');
	} else if ($adminPage == "blogs") {
		include_once ('tiki-admin_include_blogs.php');
	} else if ($adminPage == "forums") {
		include_once ('tiki-admin_include_forums.php');
	} else if ($adminPage == "faqs") {
		include_once ('tiki-admin_include_faqs.php');
	} else if ($adminPage == "trackers") {
		include_once ('tiki-admin_include_trackers.php');
	} else if ($adminPage == "webmail") {
		include_once ('tiki-admin_include_webmail.php');
	} else if ($adminPage == "rss") {
		include_once ('tiki-admin_include_rss.php');
	} else if ($adminPage == "directory") {
		include_once ('tiki-admin_include_directory.php');
	} else if ($adminPage == "userfiles") {
		include_once ('tiki-admin_include_userfiles.php');
	} else if ($adminPage == "maps") {
		include_once ('tiki-admin_include_maps.php');
	}
}

// Display the template
$smarty->assign('mid', 'tiki-admin.tpl');
$smarty->display("tiki.tpl");

?>