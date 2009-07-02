<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_links.php,v 1.21 2007-10-12 07:55:24 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/featured_links/flinkslib.php');

if ($prefs['feature_featuredLinks'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_featuredLinks");

	$smarty->display("error.tpl");
	die;
}

// PERMISSIONS: NEEDS p_admin
if ($user != 'admin') {
	if ($tiki_p_admin != 'y') {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to use this feature"));

		$smarty->display("error.tpl");
		die;
	}
}

$smarty->assign('title', '');
$smarty->assign('type', 'f');
$smarty->assign('position', 1);

if (isset($_REQUEST["generate"])) {
	check_ticket('admin-links');
	$flinkslib->generate_featured_links_positions();
}

if (!isset($_REQUEST["editurl"])) {
	$_REQUEST["editurl"] = 'n';
}

if ($_REQUEST["editurl"] != 'n') {
	$info = $flinkslib->get_featured_link($_REQUEST["editurl"]);

	if (!$info) {
		$smarty->assign('msg', tra("Non-existent link"));

		$smarty->display("error.tpl");
		die;
	}

	$smarty->assign('title', $info["title"]);
	$smarty->assign('position', $info["position"]);
	$smarty->assign('type', $info["type"]);
}

$smarty->assign('editurl', $_REQUEST["editurl"]);

if (isset($_REQUEST["add"])) {
	check_ticket('admin-links');
	if (!empty($_REQUEST["url"]) && !empty($_REQUEST["url"])) {
		if ($_REQUEST["editurl"] == 0) {
			$flinkslib->add_featured_link($_REQUEST["url"], $_REQUEST["title"], '', $_REQUEST["position"], $_REQUEST["type"]);
		} else {
			$flinkslib->update_featured_link($_REQUEST["url"], $_REQUEST["$title"], '', $_REQUEST["position"], $_REQUEST["type"]);
		}
	}
}

if (isset($_REQUEST["remove"])) {
  $area = 'delfeaturedlink';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$flinkslib->remove_featured_link($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
}

$links = $tikilib->get_featured_links(999999);
$smarty->assign_by_ref('links', $links);
ask_ticket('admin-links');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-admin_links.tpl');
$smarty->display("tiki.tpl");

?>
