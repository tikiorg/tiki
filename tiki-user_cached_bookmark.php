<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-user_cached_bookmark.php,v 1.12 2007-10-12 07:55:32 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'mytiki';
require_once ('tiki-setup.php');

include_once ('lib/bookmarks/bookmarklib.php');

if (!$user) {
	$smarty->assign('msg', tra("You must log in to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_user_bookmarks'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_user_bookmarks");

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["urlid"])) {
	$smarty->assign('msg', tra("No url indicated"));

	$smarty->display("error.tpl");
	die;
}

// Get a list of last changes to the Wiki database
$info = $bookmarklib->get_url($_REQUEST["urlid"]);
$smarty->assign_by_ref('info', $info);
$info["refresh"] = $info["lastUpdated"];
$smarty->assign('mid', 'tiki-view_cache.tpl');
$smarty->display('tiki-view_cache.tpl');

?>
