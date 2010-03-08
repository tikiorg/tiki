<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/featured_links/flinkslib.php');
$access->check_feature('feature_featuredLinks');
$access->check_permission('tiki_p_admin');
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
	$access->check_authenticity();
	$flinkslib->remove_featured_link($_REQUEST["remove"]);
}
$links = $tikilib->get_featured_links(999999);
$smarty->assign_by_ref('links', $links);
ask_ticket('admin-links');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-admin_links.tpl');
$smarty->display("tiki.tpl");
