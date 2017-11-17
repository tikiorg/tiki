<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
$access->check_permission('tiki_p_admin');
$smarty->assign('title', '');
$smarty->assign('type', 'f');
$smarty->assign('position', 1);
if (isset($_REQUEST["generate"])) {
	check_ticket('admin-links');
	$flinkslib->generate_featured_links_positions();
}


	$smarty->assign('title', $info["title"]);
	$smarty->assign('position', $info["position"]);
	$smarty->assign('type', $info["type"]);

	$flinkslib->add_featured_link($_REQUEST["url"], $_REQUEST["title"], '', $_REQUEST["position"], $_REQUEST["type"]);
$smarty->assign('editurl', $_REQUEST["editurl"]);

if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$flinkslib->remove_featured_link($_REQUEST["remove"]);
}
$smarty->assign_by_ref('links', $links);
ask_ticket('admin-links');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-admin_links.tpl');
$smarty->display("tiki.tpl");
