<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_links.php,v 1.9 2003-10-08 03:53:08 dheltzel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/featured_links/flinkslib.php');

if ($feature_featuredLinks != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_featuredLinks");

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

// PERMISSIONS: NEEDS p_admin
if ($user != 'admin') {
	if ($tiki_p_admin != 'y') {
		$smarty->assign('msg', tra("You dont have permission to use this feature"));

		$smarty->display("styles/$style_base/error.tpl");
		die;
	}
}

$smarty->assign('title', '');
$smarty->assign('type', 'f');
$smarty->assign('position', 1);

if (isset($_REQUEST["generate"])) {
	$flinkslib->generate_featured_links_positions();
}

if (!isset($_REQUEST["editurl"])) {
	$_REQUEST["editurl"] = 'n';
}

if ($_REQUEST["editurl"] != 'n') {
	$info = $flinkslib->get_featured_link($_REQUEST["editurl"]);

	if (!$info) {
		$smarty->assign('msg', tra("Unexistant link"));

		$smarty->display("styles/$style_base/error.tpl");
		die;
	}

	$smarty->assign('title', $info["title"]);
	$smarty->assign('position', $info["position"]);
	$smarty->assign('type', $info["type"]);
}

$smarty->assign('editurl', $_REQUEST["editurl"]);

if (isset($_REQUEST["add"])) {
	if (!empty($_REQUEST["url"]) && !empty($_REQUEST["url"])) {
		if ($_REQUEST["editurl"] == 0) {
			$flinkslib->add_featured_link($_REQUEST["url"], $_REQUEST["title"], '', $_REQUEST["position"], $_REQUEST["type"]);
		} else {
			$flinkslib->update_featured_link($_REQUEST["url"], $_REQUEST["$title"], '', $_REQUEST["position"], $_REQUEST["type"]);
		}
	}
}

if (isset($_REQUEST["remove"])) {
	$flinkslib->remove_featured_link($_REQUEST["remove"]);
}

$links = $tikilib->get_featured_links(999999);
$smarty->assign_by_ref('links', $links);

$smarty->assign('mid', 'tiki-admin_links.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
