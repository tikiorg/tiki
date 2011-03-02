<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
$section_class = "tiki_wiki_page manage";	// This will be body class instead of $section
require_once ('tiki-setup.php');
include_once ('lib/wiki/wikilib.php');
$access->check_feature('feature_wiki');
$access->check_feature('feature_likePages');
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
$tikilib->get_perm_object($page, 'wiki page', $info);
$access->check_permission('tiki_p_view');
$likepages = $wikilib->get_like_pages($page);
// If the page doesn't exist then display an error
if (!$tikilib->page_exists($page)) {
	if (count($likepages) == 1) {
		header("Status: 402 Found"); /* PHP3 */
		header("HTTP/1.0 402 Found"); /* PHP4 */
		header("Location: tiki-index.php?page=$likepages[0]");
		die;
	}
	$smarty->assign('page_exists', 'n');
	if (count($likepages) < 1) {
		header("Status: 404 Not Found"); /* PHP3 */
		header("HTTP/1.0 404 Not Found"); /* PHP4 */
		$smarty->assign('headtitle', tra("Page cannot be found"));
		$smarty->assign('errortitle', tra("Page cannot be found") . " (404)");
		$smarty->assign('errortype', '404');
		$smarty->display("error.tpl");
		die;
	}
}
$smarty->assign_by_ref('likepages', $likepages);
ask_ticket('likepages');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
include_once ('tiki-section_options.php');
// Display the template
$smarty->assign('mid', 'tiki-likepages.tpl');
$smarty->display("tiki.tpl");
