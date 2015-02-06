<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$wikilib = TikiLib::lib('wiki');
$access->check_feature(array('feature_wiki', 'feature_backlinks'));

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$smarty->assign('msg', tra("No page indicated"));
	$smarty->display("error.tpl");
	die;
} else {
	$page = $_REQUEST["page"];
	$smarty->assign_by_ref('page', $_REQUEST["page"]);
}
if (!($info = $tikilib->get_page_info($page))) {
	$smarty->assign('msg', tra('Page cannot be found'));
	$smarty->display('error.tpl');
	die;
}
// Now check permissions to access this page
$tikilib->get_perm_object($page, 'wiki page', $info);
$access->check_permission('tiki_p_view');

// If the page doesn't exist then display an error
if (!$tikilib->page_exists($page)) {
	$smarty->assign('msg', tra("The page cannot be found"));
	$smarty->display("error.tpl");
	die;
}
// Get the backlinks for the page "page"
$backlinks = $wikilib->get_backlinks($page);
$smarty->assign_by_ref('backlinks', $backlinks);
ask_ticket('backlinks');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-backlinks.tpl');
$smarty->display("tiki.tpl");
