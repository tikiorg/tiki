<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section_class="tiki_wiki_page print";
require_once ('tiki-setup.php');
include_once ('lib/wiki/wikilib.php');

$access->check_feature( array('feature_wiki', 'feature_wiki_print') );

// Create the HomePage if it doesn't exist
if (!$tikilib->page_exists($prefs['wikiHomePage'])) {
	$tikilib->create_page($prefs['wikiHomePage'], 0, '', $tikilib->now, 'Tiki initialization');
}
// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$page = $prefs['wikiHomePage'];
	$smarty->assign('page', $prefs['wikiHomePage']);
} else {
	$page = $_REQUEST["page"];
	$smarty->assign_by_ref('page', $_REQUEST["page"]);
}
// If the page doesn't exist then display an error
if (!($info = $tikilib->get_page_info($page))) {
	$smarty->assign('msg', tra('Page cannot be found'));
	$smarty->display('error.tpl');
	die;
}
$smarty->assign('page_id', $info['page_id']);

// Now check permissions to access this page
$tikilib->get_perm_object($page, 'wiki page', $info);
$access->check_permission('tiki_p_view');

// Now increment page hits since we are visiting this page
if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
	$tikilib->add_hit($page);
}
if (isset($prefs['wiki_feature_copyrights']) && $prefs['wiki_feature_copyrights'] == 'y' && isset($prefs['wikiLicensePage'])) {
	// insert license if wiki copyrights enabled
	$license_info = $tikilib->get_page_info($prefs['wikiLicensePage']);
	$tikilib->add_hit($prefs['wikiLicensePage']);
	$info["data"] = $info["data"] . "\n<HR>\n" . $license_info["data"];
	$_REQUEST['copyrightpage'] = $page;
}
// Verify lock status
if ($info["flag"] == 'L') {
	$smarty->assign('lock', true);
} else {
	$smarty->assign('lock', false);
}
if (isset($_REQUEST['page_ref_id'])) {
	// If a structure page has been requested
	$page_ref_id = $_REQUEST['page_ref_id'];
}
$pdata = $tikilib->parse_data($info["data"], array('is_html' => $info["is_html"], 'print' => 'y'));
$smarty->assign_by_ref('parsed', $pdata);
$smarty->assign_by_ref('lastModif', $info["lastModif"]);
if (empty($info["user"])) {
	$info["user"] = 'anonymous';
}
$smarty->assign_by_ref('lastVersion', $info["version"]);
$smarty->assign_by_ref('lastUser', $info["user"]);
if (isset($structure) && $structure == 'y' && isset($page_info['page_alias']) && $page_info['page_alias'] != '') $crumbpage = $page_info['page_alias'];
else $crumbpage = $page;
$crumbs[] = new Breadcrumb($crumbpage, $info["description"], 'tiki-index.php?page=' . urlencode($page), '', '');
ask_ticket('print');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the Index Template
$creator = $wikilib->get_creator($page);
$smarty->assign('creator', $creator);
$smarty->assign('print_page', 'y');
$smarty->assign('urlprefix', $base_url); // Obsolete, use base_url instead. This is for compatibility purposes only.
$smarty->assign('mid', 'tiki-show_page.tpl');
$smarty->assign('display', isset($_REQUEST['display']) ? $_REQUEST['display'] : '');
// Allow PDF export by installing a Mod that define an appropriate function
if (isset($_REQUEST['display']) && $_REQUEST['display'] == 'pdf') {
	require_once 'lib/pdflib.php';
	$generator = new PdfGenerator();
	$pdf = $generator->getPdf( 'tiki-print.php', array('page' => $page) );

	header('Cache-Control: private, must-revalidate');
	header('Pragma: private');
	header("Content-Description: File Transfer");
	header('Content-disposition: attachment; filename="'. $page. '.pdf"');
	header("Content-Type: application/pdf");
	header("Content-Transfer-Encoding: binary");
	header('Content-Length: '. strlen($pdf));
	echo $pdf;

} else {
	$smarty->display('tiki-print.tpl');
}
